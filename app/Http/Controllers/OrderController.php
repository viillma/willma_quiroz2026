<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderFormRequest;
use App\Models\Client;
use App\Models\Order;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Order::with(['client:id,name,code', 'designer:id,name', 'servicios:id,nombre', 'payments']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('servicios', fn ($s) => $s->where('nombre', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('estado')) {
            $query->where('status', $request->string('estado'));
        }

        if ($request->filled('cliente')) {
            $query->where('client_id', $request->integer('cliente'));
        }

        if ($request->filled('filtro')) {
            match ($request->string('filtro')) {
                'pendientes' => $query->whereIn('status', ['nuevo', 'confirmado', 'en_proceso', 'en_revision']),
                'terminados' => $query->whereIn('status', ['terminado', 'entregado']),
                'vencidos' => $query->where('delivery_date', '<', now()->toDateString())
                    ->whereNotIn('status', ['entregado', 'cancelado']),
                default => null,
            };
        }

        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->string('fecha'));
        }

        $orders = $query->get()->map(fn (Order $o) => $this->toArray($o));

        return Inertia::render('pedidos/Index', [
            'orders' => $orders,
            'filters' => [
                'search' => $request->input('search') ?? '',
                'estado' => $request->input('estado') ?? '',
                'cliente' => $request->integer('cliente', 0) ?: '',
                'filtro' => $request->input('filtro') ?? '',
                'fecha' => $request->input('fecha') ?? '',
            ],
            'clients' => Client::orderBy('name')->get(['id', 'name']),
            'statuses' => Order::STATUSES,
        ]);
    }

    public function create(): Response
    {
        $designers = User::whereHas('roles', fn ($q) => $q->where('nombre', 'disenador'))->get(['id', 'name']);

        return Inertia::render('pedidos/Create', [
            'clients' => Client::orderBy('name')->get(['id', 'name', 'phone', 'company', 'code']),
            'servicios' => Servicio::where('estado', 'activo')->get(['id', 'nombre', 'codigo', 'precio', 'unidad']),
            'designers' => $designers,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function store(OrderFormRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $order = DB::transaction(function () use ($data) {
            $order = Order::create([
                'code' => Order::generateCode(),
                'client_id' => $data['client_id'],
                'designer_id' => $data['designer_id'] ?? null,
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
                'delivery_date' => $data['delivery_date'] ?? null,
                'descuento' => $data['descuento'] ?? 0,
                'adelanto' => $data['adelanto'] ?? 0,
                'forma_pago' => $data['forma_pago'] ?? 'efectivo',
                'responsable' => $data['responsable'] ?? null,
                'total' => 0,
            ]);

            $subtotal = 0;
            foreach ($data['servicios'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemSubtotal;
                $order->servicios()->attach($item['servicio_id'], [
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $itemSubtotal,
                ]);
            }

            $order->update(['total' => max(0, $subtotal - ($data['descuento'] ?? 0))]);

            $order->histories()->create([
                'user_id' => auth()->id(),
                'from_status' => null,
                'to_status' => $order->status,
                'note' => 'Pedido creado.',
            ]);

            return $order;
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Pedido creado correctamente.']);

        return to_route('pedidos.show', [
            'pedido' => $order->id,
        ]);
    }

    public function show(Order $pedido): Response
    {
        $pedido->load(['client', 'designer', 'servicios', 'designFiles.uploader', 'payments.confirmer', 'cotizacion.client', 'histories.user']);

        return Inertia::render('pedidos/Show', [
            'order' => $this->toArray($pedido, true),
            'statuses' => Order::STATUSES,
        ]);
    }

    public function edit(Order $pedido): Response
    {
        $pedido->load(['client', 'designer', 'servicios']);

        $designers = User::whereHas('roles', fn ($q) => $q->where('nombre', 'disenador'))->get(['id', 'name']);

        return Inertia::render('pedidos/Edit', [
            'order' => $this->toArray($pedido, true),
            'clients' => Client::orderBy('name')->get(['id', 'name', 'phone', 'company', 'code']),
            'servicios' => Servicio::where('estado', 'activo')->get(['id', 'nombre', 'codigo', 'precio', 'unidad']),
            'designers' => $designers,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function update(OrderFormRequest $request, Order $pedido): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($pedido, $data) {
            $previousStatus = $pedido->status;
            $previousDesigner = $pedido->designer_id;
            $previousDelivery = $pedido->delivery_date?->toDateString();

            $pedido->update([
                'client_id' => $data['client_id'],
                'designer_id' => $data['designer_id'] ?? null,
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
                'delivery_date' => $data['delivery_date'] ?? null,
                'descuento' => $data['descuento'] ?? 0,
                'adelanto' => $data['adelanto'] ?? 0,
                'forma_pago' => $data['forma_pago'] ?? 'efectivo',
                'responsable' => $data['responsable'] ?? null,
            ]);

            $pedido->servicios()->detach();
            $subtotal = 0;
            foreach ($data['servicios'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemSubtotal;
                $pedido->servicios()->attach($item['servicio_id'], [
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $itemSubtotal,
                ]);
            }

            $pedido->update(['total' => max(0, $subtotal - ($data['descuento'] ?? 0))]);

            $changes = [];

            if ($previousStatus !== $pedido->status) {
                $pedido->histories()->create([
                    'user_id' => auth()->id(),
                    'from_status' => $previousStatus,
                    'to_status' => $pedido->status,
                    'note' => 'Estado actualizado a '.Order::STATUSES[$pedido->status].'.',
                ]);
            }

            if ((int) $previousDesigner !== (int) $pedido->designer_id) {
                $designerName = $pedido->designer?->name ?? 'Sin asignar';
                $changes[] = 'Diseñador: '.$designerName;
            }

            if ($previousDelivery !== $pedido->delivery_date?->toDateString()) {
                $changes[] = 'Entrega: '.($pedido->delivery_date?->toDateString() ?? 'Sin fecha');
            }

            if ($changes !== []) {
                $pedido->histories()->create([
                    'user_id' => auth()->id(),
                    'from_status' => $pedido->status,
                    'to_status' => $pedido->status,
                    'note' => implode(' · ', $changes),
                ]);
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Pedido actualizado.']);

        return to_route('pedidos.show', [
            'pedido' => $pedido->id,
        ]);
    }

    public function destroy(Order $pedido): RedirectResponse
    {
        $pedido->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Pedido eliminado.']);

        return to_route('pedidos.index');
    }

    private function toArray(Order $order, bool $detailed = false): array
    {
        $data = [
            'id' => $order->id,
            'code' => $order->code,
            'client' => $order->client,
            'designer' => $order->designer,
            'status' => $order->status,
            'status_label' => Order::STATUSES[$order->status] ?? $order->status,
            'notes' => $order->notes,
            'total' => (float) $order->total,
            'descuento' => (float) ($order->descuento ?? 0),
            'adelanto' => (float) ($order->adelanto ?? 0),
            'forma_pago' => $order->forma_pago,
            'responsable' => $order->responsable,
            'balance' => (float) $order->balance,
            'total_pagado' => (float) $order->total_pagado,
            'payment_state' => $order->payment_state,
            'is_paid' => $order->is_paid,
            'is_overdue' => $order->isOverdue(),
            'delivery_date' => $order->delivery_date?->toDateString(),
            'delivered_at' => $order->delivered_at?->toISOString(),
            'created_at' => $order->created_at?->toISOString(),
            'subtotal' => (float) $order->servicios->sum(fn ($s) => (float) $s->pivot->subtotal),
            'services_count' => $order->servicios->count(),
        ];

        if ($detailed) {
            $data['servicios'] = $order->servicios->map(fn ($s) => [
                'id' => $s->id,
                'nombre' => $s->nombre,
                'codigo' => $s->codigo,
                'quantity' => (int) $s->pivot->quantity,
                'unit_price' => (float) $s->pivot->unit_price,
                'subtotal' => (float) $s->pivot->subtotal,
            ])->values();
            $data['designFiles'] = $order->designFiles->map(fn ($df) => [
                'id' => $df->id,
                'version' => $df->version,
                'file_path' => $df->file_path,
                'file_type' => $df->file_type,
                'comments' => $df->comments,
                'is_approved' => $df->is_approved,
                'uploaded_by_name' => $df->uploader?->name,
                'created_at' => $df->created_at?->toISOString(),
            ]);
            $data['payments'] = $order->payments->map(fn ($p) => [
                'id' => $p->id,
                'amount' => (float) $p->amount,
                'payment_method' => $p->payment_method,
                'tipo_pago' => $p->tipo_pago,
                'status' => $p->status,
                'reference' => $p->reference,
                'confirmed_by_name' => $p->confirmer?->name,
                'confirmed_at' => $p->confirmed_at?->toISOString(),
                'created_at' => $p->created_at?->toISOString(),
            ]);
            $data['cotizacion'] = $order->cotizacion ? [
                'id' => $order->cotizacion->id,
                'numero' => $order->cotizacion->numero,
                'estado' => $order->cotizacion->estado,
            ] : null;
            $data['histories'] = $order->histories->map(fn ($h) => [
                'id' => $h->id,
                'from_status' => $h->from_status,
                'from_status_label' => $h->from_status ? (Order::STATUSES[$h->from_status] ?? $h->from_status) : null,
                'to_status' => $h->to_status,
                'to_status_label' => Order::STATUSES[$h->to_status] ?? $h->to_status,
                'note' => $h->note,
                'user_name' => $h->user?->name ?? 'Sistema',
                'created_at' => $h->created_at?->toISOString(),
            ])->values();
        }

        return $data;
    }
}
