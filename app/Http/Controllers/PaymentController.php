<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentFormRequest;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(string $currentTeam, Order $order): Response
    {
        $order->load(['payments.confirmer', 'client']);

        return Inertia::render('pagos/Index', [
            'order' => $order,
            'balance' => $order->balance,
        ]);
    }

    public function store(PaymentFormRequest $request, string $currentTeam, Order $order): RedirectResponse
    {
        $validated = $request->validated();

        if ($validated['amount'] > $order->balance) {
            return back()->withErrors(['amount' => 'El monto supera el saldo pendiente ('.$order->balance.')']);
        }

        Payment::create([
            'order_id' => $order->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference' => $validated['reference'] ?? null,
        ]);

        return to_route('pagos.index', [
            'current_team' => $currentTeam,
            'order' => $order->id,
        ]);
    }

    public function confirm(string $currentTeam, Order $order, Payment $payment): RedirectResponse
    {
        $payment->update([
            'status' => 'confirmado',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        return to_route('pagos.index', [
            'current_team' => $currentTeam,
            'order' => $order->id,
        ]);
    }

    public function reject(string $currentTeam, Order $order, Payment $payment): RedirectResponse
    {
        $payment->update([
            'status' => 'rechazado',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        return to_route('pagos.index', [
            'current_team' => $currentTeam,
            'order' => $order->id,
        ]);
    }

    public function destroy(string $currentTeam, Order $order, Payment $payment): RedirectResponse
    {
        $payment->delete();

        return to_route('pagos.index', [
            'current_team' => $currentTeam,
            'order' => $order->id,
        ]);
    }
}
