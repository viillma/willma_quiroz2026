<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentFormRequest;
use App\Models\Client;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function globalIndex(Request $request): Response
    {
        $query = Payment::with(['order.client']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('order', fn ($o) => $o->where('code', 'like', "%{$search}%"))
                    ->orWhereHas('order.client', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('estado')) {
            $query->where('status', $request->string('estado'));
        }

        if ($request->filled('metodo')) {
            $query->where('payment_method', $request->string('metodo'));
        }

        if ($request->filled('cliente')) {
            $query->whereHas('order', fn ($o) => $o->where('client_id', $request->integer('cliente')));
        }

        $payments = $query->orderByDesc('created_at')->get()->map(fn (Payment $p) => [
            'id' => $p->id,
            'amount' => (float) $p->amount,
            'payment_method' => $p->payment_method,
            'tipo_pago' => $p->tipo_pago,
            'status' => $p->status,
            'reference' => $p->reference,
            'order' => $p->order ? [
                'id' => $p->order->id,
                'code' => $p->order->code,
                'client_name' => $p->order->client?->name,
            ] : null,
            'created_at' => $p->created_at?->toISOString(),
            'confirmed_at' => $p->confirmed_at?->toISOString(),
        ]);

        return Inertia::render('pagos/Registro', [
            'payments' => $payments,
            'clients' => Client::orderBy('name')->get(['id', 'name']),
            'filters' => [
                'search' => $request->input('search') ?? '',
                'estado' => $request->input('estado') ?? '',
                'metodo' => $request->input('metodo') ?? '',
                'cliente' => $request->input('cliente') ?? '',
            ],
        ]);
    }

    public function index(Order $pedido): Response
    {
        $pedido->load(['payments.confirmer', 'client']);

        return Inertia::render('pagos/Index', [
            'order' => $this->orderToArray($pedido),
            'balance' => $pedido->balance,
        ]);
    }

    public function store(PaymentFormRequest $request, Order $pedido): RedirectResponse
    {
        $validated = $request->validated();

        if ($validated['amount'] > $pedido->balance) {
            return back()->withErrors(['amount' => 'El monto supera el saldo pendiente ('.number_format($pedido->balance, 2).').']);
        }

        Payment::create([
            'order_id' => $pedido->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'tipo_pago' => $validated['tipo_pago'] ?? 'pago_parcial',
            'reference' => $validated['reference'] ?? null,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Pago registrado.']);

        return to_route('pagos.index', [
            'order' => $pedido->id,
        ]);
    }

    public function confirm(Order $order, Payment $payment): RedirectResponse
    {
        $payment->update([
            'status' => 'confirmado',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Pago confirmado.']);

        return to_route('pagos.index', [
            'order' => $order->id,
        ]);
    }

    public function reject(Order $order, Payment $payment): RedirectResponse
    {
        $payment->update([
            'status' => 'rechazado',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'info', 'message' => 'Pago rechazado.']);

        return to_route('pagos.index', [
            'order' => $order->id,
        ]);
    }

    public function destroy(Order $pedido, Payment $pago): RedirectResponse
    {
        $pago->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Pago eliminado.']);

        return to_route('pagos.index', [
            'order' => $pedido->id,
        ]);
    }

    private function orderToArray(Order $order): array
    {
        return [
            'id' => $order->id,
            'code' => $order->code,
            'client' => $order->client,
            'status' => $order->status,
            'status_label' => Order::STATUSES[$order->status] ?? $order->status,
            'total' => (float) $order->total,
            'descuento' => (float) ($order->descuento ?? 0),
            'adelanto' => (float) ($order->adelanto ?? 0),
            'balance' => (float) $order->balance,
            'total_pagado' => (float) $order->total_pagado,
            'payment_state' => $order->payment_state,
            'forma_pago' => $order->forma_pago,
            'payments' => $order->payments->map(fn ($p) => [
                'id' => $p->id,
                'amount' => (float) $p->amount,
                'payment_method' => $p->payment_method,
                'tipo_pago' => $p->tipo_pago,
                'status' => $p->status,
                'reference' => $p->reference,
                'confirmed_by_name' => $p->confirmer?->name,
                'confirmed_at' => $p->confirmed_at?->toISOString(),
                'created_at' => $p->created_at?->toISOString(),
            ]),
        ];
    }
}
