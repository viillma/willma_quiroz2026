<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Models\Client;
use App\Models\Cotizacion;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Portafolio;
use App\Models\Servicio;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $now = now();

        $monthOrders = Order::whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
        $paidThisMonth = Payment::where('status', 'confirmado')
            ->whereBetween('confirmed_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);

        $income = (float) $paidThisMonth->sum('amount');

        $orders = Order::with(['client', 'servicios', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        $activeOrders = $orders->whereNotIn('status', ['entregado', 'cancelado']);
        $totalPorCobrar = (float) $orders->sum('balance');

        $kpis = [
            'ventas_del_mes' => (float) $monthOrders->sum('total'),
            'ingresos_del_mes' => $income,
            'pedidos_activos' => $activeOrders->count(),
            'total_por_cobrar' => $totalPorCobrar,
            'total_clientes' => Client::count(),
            'clientes_nuevos_mes' => Client::whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->count(),
            'pedidos_pendientes' => $orders->where('status', 'nuevo')->count() + $orders->where('status', 'confirmado')->count(),
            'pedidos_terminados_mes' => $orders->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
                ->whereIn('status', ['terminado', 'entregado'])->count(),
            'cotizaciones_pendientes' => Cotizacion::whereIn('estado', ['borrador', 'enviada'])->count(),
            'servicios_activos' => Servicio::where('estado', 'activo')->count(),
            'servicios_vendidos' => (int) DB::table('order_service')->sum('quantity'),
            'disenos_realizados' => Portafolio::count(),
            'pagos_recibidos_mes' => (int) $paidThisMonth->count(),
        ];

        $latestOrders = $orders->take(8)->map(fn (Order $o) => $this->orderToArray($o));

        $dueSoon = $orders
            ->filter(fn (Order $o) => $o->delivery_date !== null
                && ! in_array($o->status, ['entregado', 'cancelado'])
                && $o->delivery_date->between($now->copy()->subDay(), $now->copy()->addDays(7)))
            ->sortBy(fn (Order $o) => $o->delivery_date)
            ->take(8)
            ->values()
            ->map(fn (Order $o) => $this->orderToArray($o));

        $incomeByMonth = $this->incomeByMonth();
        $salesByMonth = $this->salesByMonth();
        $customersByMonth = $this->customersByMonth();

        $ordersByStatus = $orders->groupBy('status')->map(fn ($items) => $items->count())
            ->map(fn ($value, $status) => [
                'status' => $status,
                'label' => Order::STATUSES[$status] ?? $status,
                'value' => $value,
            ])->values();

        $topServices = DB::table('order_service')
            ->join('servicios', 'servicios.id', '=', 'order_service.servicio_id')
            ->select('servicios.id', 'servicios.nombre', DB::raw('SUM(order_service.quantity) as total_quantity'), DB::raw('SUM(order_service.subtotal) as total_revenue'))
            ->groupBy('servicios.id', 'servicios.nombre')
            ->orderByDesc('total_revenue')
            ->take(6)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'nombre' => $s->nombre,
                'total_quantity' => (int) $s->total_quantity,
                'total_revenue' => (float) $s->total_revenue,
            ]);

        $recentPayments = Payment::with(['order.client'])
            ->where('status', 'confirmado')
            ->orderBy('confirmed_at', 'desc')
            ->take(6)
            ->get()
            ->map(fn (Payment $p) => [
                'id' => $p->id,
                'amount' => (float) $p->amount,
                'method' => $p->payment_method,
                'order_code' => $p->order?->code,
                'client_name' => $p->order?->client?->name,
                'confirmed_at' => $p->confirmed_at?->toISOString(),
            ]);

        return Inertia::render('Dashboard', [
            'kpis' => $kpis,
            'latestOrders' => $latestOrders,
            'dueSoon' => $dueSoon,
            'incomeByMonth' => $incomeByMonth,
            'salesByMonth' => $salesByMonth,
            'customersByMonth' => $customersByMonth,
            'ordersByStatus' => $ordersByStatus,
            'topServices' => $topServices,
            'recentPayments' => $recentPayments,
            'canManageOrders' => auth()->user()?->hasPermission(Permission::PedidosGestionar->value),
            'canManageClients' => auth()->user()?->hasPermission(Permission::ClientesGestionar->value),
            'canManageQuotes' => auth()->user()?->hasPermission(Permission::CotizacionesGestionar->value),
            'canManagePayments' => auth()->user()?->hasPermission(Permission::PagosGestionar->value),
        ]);
    }

    private function orderToArray(Order $order): array
    {
        return [
            'id' => $order->id,
            'code' => $order->code,
            'client' => $order->client ? ['id' => $order->client->id, 'name' => $order->client->name] : null,
            'status' => $order->status,
            'total' => (float) $order->total,
            'balance' => (float) $order->balance,
            'delivery_date' => $order->delivery_date?->toDateString(),
            'services_names' => $order->servicios->pluck('nombre')->implode(', '),
            'services_count' => $order->servicios->count(),
            'created_at' => $order->created_at?->toISOString(),
        ];
    }

    private function incomeByMonth(): array
    {
        $months = collect(range(5, 0))->map(function ($offset) {
            $start = now()->startOfMonth()->subMonths($offset);
            $end = $start->copy()->endOfMonth();

            return [
                'label' => $start->format('M'),
                'year' => $start->format('Y'),
                'start' => $start,
                'end' => $end,
            ];
        });

        $payments = Payment::where('status', 'confirmado')
            ->whereBetween('confirmed_at', [
                $months->first()['start']->copy()->startOfDay(),
                $months->last()['end']->copy()->endOfDay(),
            ])
            ->get()
            ->groupBy(fn (Payment $p) => Carbon::parse($p->confirmed_at)->format('Y-m'));

        return $months->map(function ($month) use ($payments) {
            $key = $month['year'].'-'.$month['start']->format('m');

            return [
                'label' => $month['label'],
                'value' => (float) ($payments->get($key)?->sum('amount') ?? 0),
            ];
        })->values()->toArray();
    }

    private function salesByMonth(): array
    {
        $months = $this->monthWindow();

        $orders = Order::whereBetween('created_at', [
            $months->first()['start']->copy()->startOfDay(),
            $months->last()['end']->copy()->endOfDay(),
        ])
            ->get()
            ->groupBy(fn (Order $o) => Carbon::parse($o->created_at)->format('Y-m'));

        return $months->map(function ($month) use ($orders) {
            $key = $month['year'].'-'.$month['start']->format('m');

            return [
                'label' => $month['label'],
                'value' => (float) ($orders->get($key)?->sum('total') ?? 0),
            ];
        })->values()->toArray();
    }

    private function customersByMonth(): array
    {
        $months = $this->monthWindow();

        $clients = Client::whereBetween('created_at', [
            $months->first()['start']->copy()->startOfDay(),
            $months->last()['end']->copy()->endOfDay(),
        ])
            ->get()
            ->groupBy(fn (Client $c) => Carbon::parse($c->created_at)->format('Y-m'));

        return $months->map(function ($month) use ($clients) {
            $key = $month['year'].'-'.$month['start']->format('m');

            return [
                'label' => $month['label'],
                'value' => (int) ($clients->get($key)?->count() ?? 0),
            ];
        })->values()->toArray();
    }

    /**
     * Last six months window (label, year, start, end).
     *
     * @return Collection<int, array{label: string, year: string, start: Carbon, end: Carbon}>
     */
    private function monthWindow(): Collection
    {
        return collect(range(5, 0))->map(function ($offset) {
            $start = now()->startOfMonth()->subMonths($offset);
            $end = $start->copy()->endOfMonth();

            return [
                'label' => $start->format('M'),
                'year' => $start->format('Y'),
                'start' => $start,
                'end' => $end,
            ];
        });
    }
}
