<?php

namespace App\Http\Controllers;

use App\Http\Requests\CotizacionFormRequest;
use App\Models\BusinessSetting;
use App\Models\Client;
use App\Models\Cotizacion;
use App\Models\Order;
use App\Models\Servicio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CotizacionController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Cotizacion::with(['client:id,name,code', 'servicios:id,nombre']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                    ->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->string('estado'));
        }

        if ($request->filled('cliente')) {
            $query->where('client_id', $request->integer('cliente'));
        }

        $cotizaciones = $query->orderByDesc('created_at')->get()->map(fn ($c) => $this->toArray($c, false));

        return Inertia::render('cotizaciones/Index', [
            'cotizaciones' => $cotizaciones,
            'clients' => Client::orderBy('name')->get(['id', 'name']),
            'statuses' => Cotizacion::STATUSES,
            'filters' => [
                'search' => $request->input('search') ?? '',
                'estado' => $request->input('estado') ?? '',
                'cliente' => $request->input('cliente') ?? '',
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('cotizaciones/Create', [
            'clients' => Client::orderBy('name')->get(['id', 'name', 'phone', 'company', 'code']),
            'servicios' => Servicio::where('estado', 'activo')->get(['id', 'nombre', 'codigo', 'precio', 'unidad']),
            'statuses' => Cotizacion::STATUSES,
        ]);
    }

    public function store(CotizacionFormRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $cotizacion = DB::transaction(function () use ($data) {
            $cotizacion = Cotizacion::create([
                'numero' => Cotizacion::generateNumber(),
                'client_id' => $data['client_id'],
                'fecha' => $data['fecha'],
                'fecha_validez' => $data['fecha_validez'] ?? null,
                'descuento' => $data['descuento'] ?? 0,
                'observaciones' => $data['observaciones'] ?? null,
                'condiciones' => $data['condiciones'] ?? null,
                'estado' => $data['estado'],
                'total' => 0,
            ]);

            $subtotal = 0;
            foreach ($data['servicios'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemSubtotal;
                $cotizacion->servicios()->attach($item['servicio_id'], [
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $itemSubtotal,
                ]);
            }

            $cotizacion->update(['total' => max(0, $subtotal - ($data['descuento'] ?? 0))]);

            return $cotizacion;
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Cotización creada.']);

        return to_route('cotizaciones.show', [
            'cotizacion' => $cotizacion->id,
        ]);
    }

    public function show(Cotizacion $cotizacion): Response
    {
        $cotizacion->load(['client', 'servicios', 'order']);

        return Inertia::render('cotizaciones/Show', [
            'cotizacion' => $this->toArray($cotizacion, true),
        ]);
    }

    public function pdf(Cotizacion $cotizacion): \Symfony\Component\HttpFoundation\Response
    {
        $cotizacion->load(['client', 'servicios']);

        $business = [
            'name' => BusinessSetting::get('business_name') ?: 'CREAPIX',
            'logo' => $this->logoPath(),
            'nit' => BusinessSetting::get('business_nit'),
            'phone' => BusinessSetting::get('business_phone'),
            'whatsapp' => BusinessSetting::get('business_whatsapp'),
            'email' => BusinessSetting::get('business_email'),
            'address' => BusinessSetting::get('business_address'),
            'city' => BusinessSetting::get('business_city'),
            'country' => BusinessSetting::get('business_country'),
            'currency_symbol' => BusinessSetting::get('currency_symbol') ?: 'Bs',
        ];

        $dompdf = Pdf::setOptions([
            'defaultFont' => 'dejavu sans',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ])->loadView('pdf.cotizacion', [
            'cotizacion' => $cotizacion,
            'business' => $business,
        ])->setPaper('a4', 'portrait');

        $filename = 'cotizacion-'.$cotizacion->numero.'.pdf';

        return $dompdf->download($filename);
    }

    private function logoPath(): ?string
    {
        $logo = BusinessSetting::get('business_logo');

        if (! $logo) {
            return null;
        }

        $path = public_path('storage/'.$logo);

        return file_exists($path) ? $path : null;
    }

    public function edit(Cotizacion $cotizacion): Response
    {
        $cotizacion->load(['client', 'servicios']);

        $cotizacion->is_expired = $cotizacion->isExpired();

        return Inertia::render('cotizaciones/Edit', [
            'cotizacion' => $this->toArray($cotizacion, true),
            'clients' => Client::orderBy('name')->get(['id', 'name', 'phone', 'company', 'code']),
            'servicios' => Servicio::where('estado', 'activo')->get(['id', 'nombre', 'codigo', 'precio', 'unidad']),
            'statuses' => Cotizacion::STATUSES,
        ]);
    }

    public function update(CotizacionFormRequest $request, Cotizacion $cotizacion): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($cotizacion, $data) {
            $cotizacion->update([
                'client_id' => $data['client_id'],
                'fecha' => $data['fecha'],
                'fecha_validez' => $data['fecha_validez'] ?? null,
                'descuento' => $data['descuento'] ?? 0,
                'observaciones' => $data['observaciones'] ?? null,
                'condiciones' => $data['condiciones'] ?? null,
                'estado' => $data['estado'],
            ]);

            $cotizacion->servicios()->detach();
            $subtotal = 0;
            foreach ($data['servicios'] as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemSubtotal;
                $cotizacion->servicios()->attach($item['servicio_id'], [
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $itemSubtotal,
                ]);
            }

            $cotizacion->update(['total' => max(0, $subtotal - ($data['descuento'] ?? 0))]);
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Cotización actualizada.']);

        return to_route('cotizaciones.show', [
            'cotizacion' => $cotizacion->id,
        ]);
    }

    public function duplicate(Cotizacion $cotizacion): RedirectResponse
    {
        $copy = DB::transaction(function () use ($cotizacion) {
            $copy = Cotizacion::create([
                'numero' => Cotizacion::generateNumber(),
                'client_id' => $cotizacion->client_id,
                'fecha' => now()->toDateString(),
                'fecha_validez' => null,
                'descuento' => $cotizacion->descuento,
                'observaciones' => $cotizacion->observaciones,
                'condiciones' => $cotizacion->condiciones,
                'estado' => 'borrador',
                'total' => $cotizacion->total,
            ]);

            foreach ($cotizacion->servicios as $servicio) {
                $copy->servicios()->attach($servicio->id, [
                    'quantity' => $servicio->pivot->quantity,
                    'unit_price' => $servicio->pivot->unit_price,
                    'subtotal' => $servicio->pivot->subtotal,
                ]);
            }

            return $copy;
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Cotización duplicada.']);

        return to_route('cotizaciones.edit', [
            'cotizacion' => $copy->id,
        ]);
    }

    public function convertToOrder(Cotizacion $cotizacion): RedirectResponse
    {
        if ($cotizacion->estado !== 'aceptada') {
            return back()->withErrors(['estado' => 'Solo las cotizaciones aceptadas pueden convertirse en pedido.']);
        }

        if ($cotizacion->order_id) {
            return back()->withErrors(['estado' => 'Esta cotización ya fue convertida en pedido.']);
        }

        $order = DB::transaction(function () use ($cotizacion) {
            $order = Order::create([
                'code' => Order::generateCode(),
                'client_id' => $cotizacion->client_id,
                'status' => 'nuevo',
                'notes' => 'Creado desde la cotización '.$cotizacion->numero,
                'delivery_date' => null,
                'descuento' => $cotizacion->descuento,
                'adelanto' => 0,
                'forma_pago' => 'efectivo',
                'total' => 0,
            ]);

            foreach ($cotizacion->servicios as $servicio) {
                $order->servicios()->attach($servicio->id, [
                    'quantity' => $servicio->pivot->quantity,
                    'unit_price' => $servicio->pivot->unit_price,
                    'subtotal' => $servicio->pivot->subtotal,
                ]);
            }

            $order->update(['total' => (float) $cotizacion->total]);
            $cotizacion->update(['order_id' => $order->id]);

            return $order;
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Cotización convertida en pedido.']);

        return to_route('pedidos.show', [
            'pedido' => $order->id,
        ]);
    }

    public function destroy(Cotizacion $cotizacion): RedirectResponse
    {
        $cotizacion->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Cotización eliminada.']);

        return to_route('cotizaciones.index');
    }

    private function toArray(Cotizacion $cotizacion, bool $detailed): array
    {
        $data = [
            'id' => $cotizacion->id,
            'numero' => $cotizacion->numero,
            'client' => $cotizacion->client,
            'fecha' => $cotizacion->fecha?->toDateString(),
            'fecha_validez' => $cotizacion->fecha_validez?->toDateString(),
            'descuento' => (float) ($cotizacion->descuento ?? 0),
            'total' => (float) $cotizacion->total,
            'estado' => $cotizacion->estado,
            'estado_label' => Cotizacion::STATUSES[$cotizacion->estado] ?? $cotizacion->estado,
            'is_expired' => $cotizacion->isExpired(),
            'created_at' => $cotizacion->created_at?->toISOString(),
        ];

        if ($detailed) {
            $data['observaciones'] = $cotizacion->observaciones;
            $data['condiciones'] = $cotizacion->condiciones;
            $data['servicios'] = $cotizacion->servicios->map(fn ($s) => [
                'id' => $s->id,
                'nombre' => $s->nombre,
                'codigo' => $s->codigo,
                'quantity' => (int) $s->pivot->quantity,
                'unit_price' => (float) $s->pivot->unit_price,
                'subtotal' => (float) $s->pivot->subtotal,
            ])->values();
            $data['subtotal'] = (float) $cotizacion->servicios->sum(fn ($s) => (float) $s->pivot->subtotal);
            $data['order'] = $cotizacion->order ? [
                'id' => $cotizacion->order->id,
                'code' => $cotizacion->order->code,
            ] : null;
        }

        return $data;
    }
}
