<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Cotizacion;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Servicio;
use App\Models\User;
use App\Support\XlsxWriter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private function range(Request $request): array
    {
        $period = $request->input('periodo', 'mes');
        $now = now();

        return match ($period) {
            'hoy' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'semana' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'ano' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'personalizado' => [
                $request->date('start', $now->copy()->startOfMonth()),
                $request->date('end', $now->copy()->endOfMonth()),
            ],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }

    public function index(Request $request): Response
    {
        [$start, $end] = $this->range($request);

        $orders = Order::with(['client', 'payments', 'servicios'])
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->get();

        $payments = Payment::where('status', 'confirmado')
            ->whereBetween('confirmed_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()]);

        $cotizaciones = Cotizacion::whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])->get();

        $totalIncome = (float) $payments->sum('amount');
        $totalOrders = $orders->count();
        $ticketAverage = $totalOrders > 0 ? round($totalIncome / $totalOrders, 2) : 0;
        $pendingBalance = (float) Order::whereNotIn('status', ['cancelado'])
            ->get()->sum('balance');

        $paymentsByMethod = $payments->get()->groupBy('payment_method')
            ->map(fn ($items) => (float) $items->sum('amount'));

        $salesByMonth = Payment::where('status', 'confirmado')
            ->whereBetween('confirmed_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->get()
            ->groupBy(fn ($p) => $p->confirmed_at?->format('Y-m'))
            ->map(fn ($items) => (float) $items->sum('amount'));

        $ordersByStatus = $orders->groupBy('status')->map(fn ($items) => $items->count());

        $topServices = DB::table('order_service')
            ->join('servicios', 'servicios.id', '=', 'order_service.servicio_id')
            ->join('orders', 'orders.id', '=', 'order_service.order_id')
            ->whereBetween('orders.created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->select('servicios.id', 'servicios.nombre', DB::raw('SUM(order_service.quantity) as total_quantity'), DB::raw('SUM(order_service.subtotal) as total_revenue'))
            ->groupBy('servicios.id', 'servicios.nombre')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'nombre' => $s->nombre,
                'total_quantity' => (int) $s->total_quantity,
                'total_revenue' => (float) $s->total_revenue,
            ]);

        $topClients = Client::with(['orders' => fn ($q) => $q->whereBetween('orders.created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])])
            ->withCount(['orders as orders_count' => fn ($q) => $q->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])])
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'orders_count' => $c->orders_count,
                'total' => (float) $c->orders->sum('total'),
            ])
            ->sortByDesc('total')
            ->take(10)
            ->values();

        $pendingOrders = Order::whereNotIn('status', ['entregado', 'cancelado'])
            ->with('client')
            ->get()
            ->filter(fn ($o) => $o->balance > 0)
            ->values()
            ->map(fn ($o) => [
                'id' => $o->id,
                'code' => $o->code,
                'client_name' => $o->client?->name,
                'total' => (float) $o->total,
                'balance' => (float) $o->balance,
                'delivery_date' => $o->delivery_date?->toDateString(),
            ]);

        return Inertia::render('reportes/Index', [
            'periodo' => $period = $request->input('periodo', 'mes'),
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'summary' => [
                'total_income' => $totalIncome,
                'total_orders' => $totalOrders,
                'ticket_average' => $ticketAverage,
                'pending_balance' => $pendingBalance,
                'quotes_accepted' => $cotizaciones->where('estado', 'aceptada')->count(),
                'quotes_rejected' => $cotizaciones->where('estado', 'rechazada')->count(),
            ],
            'payments_by_method' => $paymentsByMethod,
            'sales_by_month' => $salesByMonth,
            'orders_by_status' => $ordersByStatus,
            'top_services' => $topServices,
            'top_clients' => $topClients,
            'pending_orders' => $pendingOrders,
        ]);
    }

    public function income(Request $request): Response
    {
        [$start, $end] = $this->range($request);

        $payments = Payment::where('status', 'confirmado')
            ->whereBetween('confirmed_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->get();

        $orders = Order::whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])->get();

        return Inertia::render('reportes/Income', [
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'total_income' => (float) $payments->sum('amount'),
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
            ->whereNotIn('status', ['entregado', 'cancelado'])
            ->get()
            ->filter(fn ($o) => $o->balance > 0)
            ->values();

        return Inertia::render('reportes/Morosos', [
            'orders' => $orders,
        ]);
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        [$start, $end] = $this->range($request);

        $type = $request->input('tipo', 'ingresos');
        $dataset = $this->dataset($type, $start, $end);

        return match ($request->input('formato', 'csv')) {
            'pdf' => $this->exportPdf($type, $start, $end, $dataset),
            'xlsx' => $this->exportXlsx($type, $start, $end, $dataset),
            default => $this->exportCsv($type, $start, $end, $dataset),
        };
    }

    /**
     * Build the dataset (headers + rows) for a report type.
     *
     * @return array{headers: array<int, string>, rows: array<int, array<int, mixed>>}
     */
    private function dataset(string $type, $start, $end): array
    {
        return match ($type) {
            'ingresos' => $this->dataIngresos($start, $end),
            'pedidos' => $this->dataPedidos($start, $end),
            'servicios' => $this->dataServicios($start, $end),
            'clientes' => $this->dataClientes($start, $end),
            'pagos' => $this->dataPagos($start, $end),
            'saldos' => $this->dataSaldos(),
            'cotizaciones' => $this->dataCotizaciones($start, $end),
            default => $this->dataIngresos($start, $end),
        };
    }

    private function exportCsv(string $type, $start, $end, array $dataset): StreamedResponse
    {
        $filename = 'reporte-'.$type.'-'.$start->format('Ymd').'-'.$end->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($type, $start, $end, $dataset) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['CREAPIX - Reporte '.strtoupper($type)]);
            fputcsv($out, ['Periodo', $start->toDateString().' - '.$end->toDateString()]);
            fputcsv($out, []);
            fputcsv($out, $dataset['headers']);

            foreach ($dataset['rows'] as $row) {
                fputcsv($out, $row);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function exportXlsx(string $type, $start, $end, array $dataset): \Symfony\Component\HttpFoundation\Response
    {
        $filename = 'reporte-'.$type.'-'.$start->format('Ymd').'-'.$end->format('Ymd').'.xlsx';

        $content = XlsxWriter::generate($dataset['headers'], $dataset['rows'], 'Reporte '.$type);

        $tmp = tempnam(sys_get_temp_dir(), 'creapix');
        file_put_contents($tmp, $content);

        return response()->download($tmp, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function exportPdf(string $type, $start, $end, array $dataset): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = Pdf::setOptions([
            'defaultFont' => 'dejavu sans',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);

        $dompdf = $pdf->loadView('pdf.reporte', [
            'type' => $type,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'headers' => $dataset['headers'],
            'rows' => $dataset['rows'],
        ])->setPaper('a4', 'landscape');

        return $dompdf->download('reporte-'.$type.'-'.$start->format('Ymd').'-'.$end->format('Ymd').'.pdf');
    }

    private function dataIngresos($start, $end): array
    {
        $headers = ['Fecha', 'Pedido', 'Cliente', 'Método', 'Monto'];
        $rows = [];

        $payments = Payment::where('status', 'confirmado')
            ->whereBetween('confirmed_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->with(['order.client'])
            ->get();

        foreach ($payments as $p) {
            $rows[] = [
                $p->confirmed_at?->toDateString(),
                $p->order?->code,
                $p->order?->client?->name,
                $p->payment_method,
                number_format((float) $p->amount, 2),
            ];
        }

        return ['headers' => $headers, 'rows' => $rows];
    }

    private function dataPedidos($start, $end): array
    {
        $headers = ['Código', 'Cliente', 'Estado', 'Total', 'Saldo', 'Fecha de entrega'];
        $rows = [];

        $orders = Order::with('client')->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])->get();
        foreach ($orders as $o) {
            $rows[] = [
                $o->code,
                $o->client?->name,
                Order::STATUSES[$o->status] ?? $o->status,
                number_format((float) $o->total, 2),
                number_format((float) $o->balance, 2),
                $o->delivery_date?->toDateString(),
            ];
        }

        return ['headers' => $headers, 'rows' => $rows];
    }

    private function dataServicios($start, $end): array
    {
        $headers = ['Servicio', 'Cantidad vendida', 'Ingresos'];
        $rows = [];

        $services = DB::table('order_service')
            ->join('servicios', 'servicios.id', '=', 'order_service.servicio_id')
            ->join('orders', 'orders.id', '=', 'order_service.order_id')
            ->whereBetween('orders.created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->select('servicios.nombre', DB::raw('SUM(order_service.quantity) as total_quantity'), DB::raw('SUM(order_service.subtotal) as total_revenue'))
            ->groupBy('servicios.nombre')
            ->orderByDesc('total_revenue')
            ->get();

        foreach ($services as $s) {
            $rows[] = [$s->nombre, (int) $s->total_quantity, number_format((float) $s->total_revenue, 2)];
        }

        return ['headers' => $headers, 'rows' => $rows];
    }

    private function dataClientes($start, $end): array
    {
        $headers = ['Cliente', 'Pedidos', 'Total'];
        $rows = [];

        $clients = Client::with('orders')->get()
            ->map(fn ($c) => [
                'name' => $c->name,
                'orders_count' => $c->orders->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])->count(),
                'total' => (float) $c->orders->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])->sum('total'),
            ])
            ->sortByDesc('total')
            ->take(20);

        foreach ($clients as $c) {
            $rows[] = [$c['name'], $c['orders_count'], number_format($c['total'], 2)];
        }

        return ['headers' => $headers, 'rows' => $rows];
    }

    private function dataPagos($start, $end): array
    {
        $headers = ['Fecha', 'Pedido', 'Método', 'Tipo', 'Monto', 'Estado'];
        $rows = [];

        $payments = Payment::whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->with('order')
            ->get();

        foreach ($payments as $p) {
            $rows[] = [
                $p->created_at?->toDateString(),
                $p->order?->code,
                $p->payment_method,
                $p->tipo_pago,
                number_format((float) $p->amount, 2),
                $p->status,
            ];
        }

        return ['headers' => $headers, 'rows' => $rows];
    }

    private function dataSaldos(): array
    {
        $headers = ['Pedido', 'Cliente', 'Total', 'Pagado', 'Saldo'];
        $rows = [];

        $orders = Order::with('client')->whereNotIn('status', ['entregado', 'cancelado'])->get()
            ->filter(fn ($o) => $o->balance > 0);

        foreach ($orders as $o) {
            $rows[] = [
                $o->code,
                $o->client?->name,
                number_format((float) $o->total, 2),
                number_format((float) $o->total_pagado, 2),
                number_format((float) $o->balance, 2),
            ];
        }

        return ['headers' => $headers, 'rows' => $rows];
    }

    private function dataCotizaciones($start, $end): array
    {
        $headers = ['Número', 'Cliente', 'Fecha', 'Estado', 'Total'];
        $rows = [];

        $cotizaciones = Cotizacion::with('client')
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->get();

        foreach ($cotizaciones as $c) {
            $rows[] = [
                $c->numero,
                $c->client?->name,
                $c->fecha?->toDateString(),
                Cotizacion::STATUSES[$c->estado] ?? $c->estado,
                number_format((float) $c->total, 2),
            ];
        }

        return ['headers' => $headers, 'rows' => $rows];
    }
}
