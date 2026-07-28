<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('reportes/Index');
    }

    public function income(Request $request): Response
    {
        $start = $request->input('start', now()->startOfMonth()->toDateString());
        $end = $request->input('end', now()->endOfMonth()->toDateString());

        $payments = Payment::where('status', 'confirmado')
            ->whereBetween('confirmed_at', [$start.' 00:00:00', $end.' 23:59:59'])
            ->get();

        $orders = Order::whereBetween('created_at', [$start.' 00:00:00', $end.' 23:59:59'])->get();

        return Inertia::render('reportes/Income', [
            'start' => $start,
            'end' => $end,
            'total_income' => $payments->sum('amount'),
            'total_orders' => $orders->count(),
            'pending_orders' => $orders->whereNotIn('status', ['entregado'])->count(),
            'completed_orders' => $orders->where('status', 'entregado')->count(),
            'payments_by_method' => $payments->groupBy('payment_method')->map(fn ($items) => $items->sum('amount')),
        ]);
    }

    public function services(): Response
    {
        $services = Servicio::withCount(['orders'])->orderBy('orders_count', 'desc')->get();

        return Inertia::render('reportes/Services', [
            'services' => $services,
        ]);
    }

    public function clients(): Response
    {
        $topClients = Client::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(10)
            ->get();

        return Inertia::render('reportes/Clients', [
            'topClients' => $topClients,
        ]);
    }

    public function designers(): Response
    {
        $designers = User::whereHas('roles', fn ($q) => $q->where('nombre', 'disenador'))
            ->withCount(['orders as orders_count'])
            ->get();

        return Inertia::render('reportes/Designers', [
            'designers' => $designers,
        ]);
    }

    public function morosos(): Response
    {
        $orders = Order::with('client')
            ->where('status', '!=', 'entregado')
            ->get()
            ->filter(fn ($o) => $o->balance > 0)
            ->values();

        return Inertia::render('reportes/Morosos', [
            'orders' => $orders,
        ]);
    }
}
