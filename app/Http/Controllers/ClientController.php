<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientFormRequest;
use App\Models\Client;
use App\Models\Cotizacion;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(): Response
    {
        $clients = Client::withCount(['orders', 'cotizaciones'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($c) => $this->toArray($c));

        return Inertia::render('clientes/Index', [
            'clients' => $clients,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('clientes/Create');
    }

    public function store(ClientFormRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = $data['code'] ?? Client::generateCode();
        $data['activo'] = $data['activo'] ?? true;

        Client::create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Cliente creado correctamente.']);

        return to_route('clientes.index');
    }

    public function show(Client $cliente): Response
    {
        $cliente->load([
            'orders.servicios',
            'orders.payments',
            'cotizaciones.servicios',
        ]);

        return Inertia::render('clientes/Show', [
            'client' => $this->toArray($cliente),
            'orders' => $cliente->orders->map(fn ($o) => [
                'id' => $o->id,
                'code' => $o->code,
                'status' => $o->status,
                'status_label' => Order::STATUSES[$o->status] ?? $o->status,
                'total' => (float) $o->total,
                'balance' => (float) $o->balance,
                'payment_state' => $o->payment_state,
                'delivery_date' => $o->delivery_date?->toDateString(),
                'services_names' => $o->servicios->pluck('nombre')->implode(', '),
                'created_at' => $o->created_at?->toISOString(),
            ]),
            'cotizaciones' => $cliente->cotizaciones->map(fn ($c) => [
                'id' => $c->id,
                'numero' => $c->numero,
                'estado' => $c->estado,
                'estado_label' => Cotizacion::STATUSES[$c->estado] ?? $c->estado,
                'total' => (float) $c->total,
                'fecha' => $c->fecha?->toDateString(),
                'fecha_validez' => $c->fecha_validez?->toDateString(),
                'services_names' => $c->servicios->pluck('nombre')->implode(', '),
            ]),
            'pagos' => $cliente->orders
                ->flatMap(fn ($o) => $o->payments)
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'order_code' => $p->order?->code,
                    'amount' => (float) $p->amount,
                    'payment_method' => $p->payment_method,
                    'tipo_pago' => $p->tipo_pago,
                    'status' => $p->status,
                    'confirmed_at' => $p->confirmed_at?->toISOString(),
                    'created_at' => $p->created_at?->toISOString(),
                ]),
            'summary' => [
                'total_comprado' => (float) $cliente->orders->sum(fn ($o) => (float) $o->total),
                'total_pagado' => (float) $cliente->orders->flatMap(fn ($o) => $o->payments)
                    ->where('status', 'confirmado')->sum(fn ($p) => (float) $p->amount),
                'saldo_pendiente' => (float) $cliente->orders->sum(fn ($o) => (float) $o->balance),
            ],
        ]);
    }

    public function edit(Client $cliente): Response
    {
        return Inertia::render('clientes/Edit', [
            'client' => $this->toArray($cliente),
        ]);
    }

    public function update(ClientFormRequest $request, Client $cliente): RedirectResponse
    {
        $cliente->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Cliente actualizado.']);

        return to_route('clientes.show', [
            'cliente' => $cliente->id,
        ]);
    }

    public function toggle(Client $cliente): RedirectResponse
    {
        $cliente->update(['activo' => ! $cliente->activo]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $cliente->activo ? 'Cliente activado.' : 'Cliente desactivado.',
        ]);

        return to_route('clientes.index');
    }

    public function destroy(Client $cliente): RedirectResponse
    {
        $cliente->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Cliente eliminado.']);

        return to_route('clientes.index');
    }

    private function toArray(Client $client): array
    {
        return [
            'id' => $client->id,
            'code' => $client->code,
            'name' => $client->name,
            'tipo_cliente' => $client->tipo_cliente,
            'ci_nit' => $client->ci_nit,
            'phone' => $client->phone,
            'whatsapp' => $client->whatsapp,
            'email' => $client->email,
            'direccion' => $client->direccion,
            'company' => $client->company,
            'notes' => $client->notes,
            'activo' => $client->activo,
            'orders_count' => $client->orders_count ?? $client->orders()->count(),
            'cotizaciones_count' => $client->cotizaciones_count ?? $client->cotizaciones()->count(),
            'created_at' => $client->created_at?->toISOString(),
        ];
    }
}
