<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderFormRequest;
use App\Models\Client;
use App\Models\Order;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(): Response
    {
        $orders = Order::with(['client', 'designer', 'servicios', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'code' => $order->code,
                    'client' => $order->client,
                    'designer' => $order->designer,
                    'status' => $order->status,
                    'total' => $order->total,
                    'balance' => $order->balance,
                    'is_paid' => $order->is_paid,
                    'delivery_date' => $order->delivery_date,
                    'services_count' => $order->servicios->count(),
                    'created_at' => $order->created_at,
                ];
            });

        return Inertia::render('pedidos/Index', [
            'orders' => $orders,
        ]);
    }

    public function create(): Response
    {
        $designerRole = 'disenador';
        $designers = User::whereHas('roles', function ($q) use ($designerRole) {
            $q->where('nombre', $designerRole);
        })->get(['id', 'name']);

        return Inertia::render('pedidos/Create', [
            'clients' => Client::orderBy('name')->get(['id', 'name', 'phone', 'company']),
            'servicios' => Servicio::where('estado', 'activo')->get(['id', 'nombre', 'precio']),
            'designers' => $designers,
        ]);
    }

    public function store(OrderFormRequest $request, string $currentTeam): RedirectResponse
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
                'total' => 0,
            ]);

            $total = 0;
            foreach ($data['servicios'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $total += $subtotal;
                $order->servicios()->attach($item['servicio_id'], [
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total' => $total]);

            return $order;
        });

        return to_route('pedidos.show', [
            'current_team' => $currentTeam,
            'pedido' => $order->id,
        ]);
    }

    public function show(string $currentTeam, Order $order): Response
    {
        $order->load(['client', 'designer', 'servicios', 'designFiles.uploader', 'payments']);

        return Inertia::render('pedidos/Show', [
            'order' => $order,
        ]);
    }

    public function edit(string $currentTeam, Order $order): Response
    {
        $order->load(['client', 'designer', 'servicios']);

        $designerRole = 'disenador';
        $designers = User::whereHas('roles', function ($q) use ($designerRole) {
            $q->where('nombre', $designerRole);
        })->get(['id', 'name']);

        return Inertia::render('pedidos/Edit', [
            'order' => $order,
            'clients' => Client::orderBy('name')->get(['id', 'name', 'phone', 'company']),
            'servicios' => Servicio::where('estado', 'activo')->get(['id', 'nombre', 'precio']),
            'designers' => $designers,
        ]);
    }

    public function update(OrderFormRequest $request, string $currentTeam, Order $order): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($order, $data) {
            $order->update([
                'client_id' => $data['client_id'],
                'designer_id' => $data['designer_id'] ?? null,
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
                'delivery_date' => $data['delivery_date'] ?? null,
            ]);

            $order->servicios()->detach();
            $total = 0;
            foreach ($data['servicios'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $total += $subtotal;
                $order->servicios()->attach($item['servicio_id'], [
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total' => $total]);
        });

        return to_route('pedidos.show', [
            'current_team' => $currentTeam,
            'pedido' => $order->id,
        ]);
    }

    public function destroy(string $currentTeam, Order $order): RedirectResponse
    {
        $order->delete();

        return to_route('pedidos.index', [
            'current_team' => $currentTeam,
        ]);
    }
}
