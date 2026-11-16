<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import Chart from 'chart.js/auto';
import {
    TrendingUp,
    BarChart3,
    Users,
    Palette,
    AlertTriangle,
    ArrowRight,
    Download,
    FileSpreadsheet,
    FileText,
    Calendar,
    Wallet,
    Package,
    Receipt,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import type { Component } from 'vue';
import Heading from '@/components/Heading.vue';
import { formatMoney } from '@/lib/money';
import { dashboard } from '@/routes';
import reportes from '@/routes/reportes';

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard().url },
            { title: 'Reportes', href: reportes.index().url },
        ],
    }),
});

type Summary = {
    total_income: number;
    total_orders: number;
    ticket_average: number;
    pending_balance: number;
    quotes_accepted: number;
    quotes_rejected: number;
};

type TopService = {
    id: number;
    nombre: string;
    total_quantity: number;
    total_revenue: number;
};
type TopClient = {
    id: number;
    name: string;
    orders_count: number;
    total: number;
};
type PendingOrder = {
    id: number;
    code: string;
    client_name: string | null;
    total: number;
    balance: number;
    delivery_date: string | null;
};

const page = usePage<{
    periodo: string;
    start: string;
    end: string;
    summary: Summary;
    payments_by_method: Record<string, number>;
    sales_by_month: Record<string, number>;
    orders_by_status: Record<string, number>;
    top_services: TopService[];
    top_clients: TopClient[];
    pending_orders: PendingOrder[];
}>();

const summary = page.props.summary ?? ({} as Summary);
const salesByMonth = page.props.sales_by_month ?? {};
const ordersByStatus = page.props.orders_by_status ?? {};
const paymentsByMethod = page.props.payments_by_method ?? {};
const topServices = page.props.top_services ?? [];
const topClients = page.props.top_clients ?? [];
const pendingOrders = page.props.pending_orders ?? [];

const statusMeta: Record<string, { label: string; color: string }> = {
    nuevo: { label: 'Nuevo', color: '#7bc8f4' },
    confirmado: { label: 'Confirmado', color: '#7b5ee0' },
    en_proceso: { label: 'En proceso', color: '#f4a15d' },
    en_revision: { label: 'En revisión', color: '#f9d34e' },
    terminado: { label: 'Terminado', color: '#5ec4a0' },
    entregado: { label: 'Entregado', color: '#17805a' },
    cancelado: { label: 'Cancelado', color: '#f06868' },
};

const methodLabels: Record<string, string> = {
    efectivo: 'Efectivo',
    qr: 'QR',
    transferencia: 'Transferencia',
    tarjeta: 'Tarjeta',
    otro: 'Otro',
};

const periods = [
    { value: 'hoy', label: 'Hoy' },
    { value: 'semana', label: 'Semana' },
    { value: 'mes', label: 'Mes' },
    { value: 'ano', label: 'Año' },
    { value: 'personalizado', label: 'Personalizado' },
];

const periodo = ref(page.props.periodo ?? 'mes');
const start = ref(page.props.start ?? '');
const end = ref(page.props.end ?? '');

function applyPeriod(value: string) {
    periodo.value = value;
    const query: Record<string, string> = { periodo: value };

    if (value === 'personalizado') {
        query.start = start.value;
        query.end = end.value;
    }

    router.get(
        reportes.index.url({ query }),
        {},
        { preserveState: true, preserveScroll: true },
    );
}

function applyCustomRange() {
    applyPeriod('personalizado');
}

const salesChart = ref<HTMLCanvasElement | null>(null);
const statusChart = ref<HTMLCanvasElement | null>(null);
const methodChart = ref<HTMLCanvasElement | null>(null);
let salesInstance: Chart | null = null;
let statusInstance: Chart | null = null;
let methodInstance: Chart | null = null;

const gridColor = 'rgba(23, 26, 43, 0.08)';
const tickColor = 'rgba(23, 26, 43, 0.5)';

const salesLabels = computed(() => Object.keys(salesByMonth).sort());
const salesValues = computed(() =>
    salesLabels.value.map((k) => salesByMonth[k] ?? 0),
);

const monthLabel = (key: string) => {
    const [year, month] = key.split('-');
    const date = new Date(Number(year), Number(month) - 1, 1);

    return date.toLocaleDateString('es-BO', {
        month: 'short',
        year: '2-digit',
    });
};

function initCharts() {
    if (typeof window === 'undefined') {
        return;
    }

    if (salesChart.value) {
        salesInstance = new Chart(salesChart.value, {
            type: 'bar',
            data: {
                labels: salesLabels.value.map(monthLabel),
                datasets: [
                    {
                        label: 'Ingresos',
                        data: salesValues.value,
                        backgroundColor: '#F43F7A',
                        borderRadius: 10,
                        borderSkipped: false,
                        maxBarThickness: 30,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#171A2B',
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        callbacks: {
                            label: (ctx) =>
                                ` ${formatMoney(Number(ctx.parsed.y))}`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: tickColor },
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: {
                            color: tickColor,
                            callback: (v) => `Bs ${Number(v).toLocaleString()}`,
                        },
                        beginAtZero: true,
                    },
                },
            },
        });
    }

    if (statusChart.value) {
        const keys = Object.keys(ordersByStatus);
        statusInstance = new Chart(statusChart.value, {
            type: 'doughnut',
            data: {
                labels: keys.map((k) => statusMeta[k]?.label ?? k),
                datasets: [
                    {
                        data: keys.map((k) => ordersByStatus[k]),
                        backgroundColor: keys.map(
                            (k) => statusMeta[k]?.color ?? '#7b5ee0',
                        ),
                        borderWidth: 0,
                        hoverOffset: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            boxHeight: 10,
                            usePointStyle: true,
                            color: tickColor,
                            font: { size: 11, weight: 600 },
                        },
                    },
                    tooltip: {
                        backgroundColor: '#171A2B',
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                    },
                },
            },
        });
    }

    if (methodChart.value) {
        const keys = Object.keys(paymentsByMethod);
        methodInstance = new Chart(methodChart.value, {
            type: 'bar',
            data: {
                labels: keys.map((k) => methodLabels[k] ?? k),
                datasets: [
                    {
                        label: 'Monto',
                        data: keys.map((k) => paymentsByMethod[k]),
                        backgroundColor: '#DCCBFF',
                        hoverBackgroundColor: '#cdb8f8',
                        borderRadius: 10,
                        borderSkipped: false,
                        maxBarThickness: 28,
                    },
                ],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#171A2B',
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        callbacks: {
                            label: (ctx) =>
                                ` ${formatMoney(Number(ctx.parsed.x))}`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: {
                            color: tickColor,
                            callback: (v) => `Bs ${Number(v).toLocaleString()}`,
                        },
                        beginAtZero: true,
                    },
                    y: {
                        grid: { display: false },
                        ticks: { color: tickColor },
                    },
                },
            },
        });
    }
}

function destroyCharts() {
    salesInstance?.destroy();
    statusInstance?.destroy();
    methodInstance?.destroy();
    salesInstance = statusInstance = methodInstance = null;
}

const exportTipos = [
    { value: 'ingresos', label: 'Ingresos' },
    { value: 'pedidos', label: 'Pedidos' },
    { value: 'servicios', label: 'Servicios' },
    { value: 'clientes', label: 'Clientes' },
    { value: 'pagos', label: 'Pagos' },
    { value: 'saldos', label: 'Saldos' },
    { value: 'cotizaciones', label: 'Cotizaciones' },
];
const exportTipo = ref('ingresos');

function exportUrl(formato: string) {
    return reportes.export.url({
        query: { tipo: exportTipo.value, formato, periodo: periodo.value },
    });
}

type ReportRouteName =
    | 'ingresos'
    | 'servicios'
    | 'clientes'
    | 'disenadores'
    | 'morosos';

const reportCards: Array<{
    title: string;
    description: string;
    icon: Component;
    routeName: ReportRouteName;
    color: string;
}> = [
    {
        title: 'Ingresos',
        description: 'Detalle de ingresos por período',
        icon: TrendingUp,
        routeName: 'ingresos',
        color: 'from-[#17805a] to-[#5ec4a0]',
    },
    {
        title: 'Servicios más solicitados',
        description: 'Ranking de servicios más vendidos',
        icon: BarChart3,
        routeName: 'servicios',
        color: 'from-[#7b5ee0] to-[#a78bfa]',
    },
    {
        title: 'Clientes frecuentes',
        description: 'Top clientes con más pedidos',
        icon: Users,
        routeName: 'clientes',
        color: 'from-[#F43F7A] to-[#f97316]',
    },
    {
        title: 'Diseñadores',
        description: 'Productividad por diseñador',
        icon: Palette,
        routeName: 'disenadores',
        color: 'from-[#f4a15d] to-[#f9d34e]',
    },
    {
        title: 'Clientes Morosos',
        description: 'Pedidos con saldo pendiente',
        icon: AlertTriangle,
        routeName: 'morosos',
        color: 'from-[#f06868] to-[#F43F7A]',
    },
];

onMounted(() => initCharts());
onBeforeUnmount(() => destroyCharts());
</script>

<template>
    <Head title="Reportes" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <div
            class="flex flex-col justify-between gap-4 lg:flex-row lg:items-end"
        >
            <Heading
                title="Reportes"
                description="Indicadores, estadísticas y exportación del negocio"
            />

            <!-- Period filter -->
            <div
                class="flex flex-wrap items-center gap-2 rounded-2xl border border-ink/5 bg-white p-2 shadow-sm"
            >
                <div class="flex items-center gap-1.5 px-2 text-ink/50">
                    <Calendar class="h-4 w-4" />
                </div>
                <button
                    v-for="p in periods"
                    :key="p.value"
                    type="button"
                    class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all duration-200"
                    :class="
                        periodo === p.value
                            ? 'bg-pix text-white shadow-md shadow-pix/25'
                            : 'text-ink/55 hover:bg-warm hover:text-ink'
                    "
                    @click="applyPeriod(p.value)"
                >
                    {{ p.label }}
                </button>
            </div>
        </div>

        <!-- Custom range -->
        <div
            v-if="periodo === 'personalizado'"
            class="flex flex-wrap items-end gap-3 rounded-2xl border border-ink/5 bg-white p-4 shadow-sm"
        >
            <div class="space-y-1.5">
                <label
                    class="text-xs font-bold tracking-wide text-ink/50 uppercase"
                    >Desde</label
                >
                <input
                    v-model="start"
                    type="date"
                    class="h-10 rounded-xl border border-ink/10 bg-warm px-3 text-sm text-ink focus:border-pix/40 focus:outline-none"
                />
            </div>
            <div class="space-y-1.5">
                <label
                    class="text-xs font-bold tracking-wide text-ink/50 uppercase"
                    >Hasta</label
                >
                <input
                    v-model="end"
                    type="date"
                    class="h-10 rounded-xl border border-ink/10 bg-warm px-3 text-sm text-ink focus:border-pix/40 focus:outline-none"
                />
            </div>
            <button
                type="button"
                class="h-10 rounded-xl bg-pix px-5 text-sm font-bold text-white shadow-lg shadow-pix/25 transition hover:bg-pix-2"
                @click="applyCustomRange"
            >
                Aplicar
            </button>
        </div>

        <!-- Summary -->
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div
                class="relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.16)]"
            >
                <div
                    class="pointer-events-none absolute -top-10 -right-10 h-32 w-32 rounded-full bg-gradient-to-br from-mint/60 to-lavender/50 blur-2xl"
                ></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p
                            class="text-[11px] font-bold tracking-[0.14em] text-ink/45 uppercase"
                        >
                            Ingresos
                        </p>
                        <h2
                            class="mt-2 font-display text-3xl font-extrabold text-ink"
                        >
                            {{ formatMoney(summary.total_income) }}
                        </h2>
                        <p class="mt-1 text-xs font-semibold text-[#17805a]">
                            {{ start }} — {{ end }}
                        </p>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#17805a]/10 text-[#17805a]"
                    >
                        <Wallet class="h-6 w-6" />
                    </div>
                </div>
            </div>

            <div
                class="relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.16)]"
            >
                <div class="relative flex items-start justify-between">
                    <div>
                        <p
                            class="text-[11px] font-bold tracking-[0.14em] text-ink/45 uppercase"
                        >
                            Pedidos
                        </p>
                        <h2
                            class="mt-2 font-display text-3xl font-extrabold text-ink"
                        >
                            {{ summary.total_orders ?? 0 }}
                        </h2>
                        <p class="mt-1 text-xs font-semibold text-[#7b5ee0]">
                            en el período
                        </p>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#7b5ee0]/10 text-[#7b5ee0]"
                    >
                        <Package class="h-6 w-6" />
                    </div>
                </div>
            </div>

            <div
                class="relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.16)]"
            >
                <div class="relative flex items-start justify-between">
                    <div>
                        <p
                            class="text-[11px] font-bold tracking-[0.14em] text-ink/45 uppercase"
                        >
                            Ticket promedio
                        </p>
                        <h2
                            class="mt-2 font-display text-3xl font-extrabold text-ink"
                        >
                            {{ formatMoney(summary.ticket_average) }}
                        </h2>
                        <p class="mt-1 text-xs font-semibold text-pix">
                            por pedido
                        </p>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blush/60 text-pix"
                    >
                        <Receipt class="h-6 w-6" />
                    </div>
                </div>
            </div>

            <div
                class="relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.16)]"
            >
                <div class="relative flex items-start justify-between">
                    <div>
                        <p
                            class="text-[11px] font-bold tracking-[0.14em] text-ink/45 uppercase"
                        >
                            Por cobrar
                        </p>
                        <h2
                            class="mt-2 font-display text-3xl font-extrabold text-ink"
                        >
                            {{ formatMoney(summary.pending_balance) }}
                        </h2>
                        <p class="mt-1 text-xs font-semibold text-red-500">
                            {{ summary.quotes_accepted ?? 0 }} cotiz. aceptadas
                        </p>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-500/10 text-red-500"
                    >
                        <AlertTriangle class="h-6 w-6" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Export bar -->
        <div
            class="flex flex-col justify-between gap-3 rounded-2xl border border-ink/5 bg-gradient-to-r from-lavender/60 via-lavender/30 to-blush/40 p-4 md:flex-row md:items-center"
        >
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/80 text-pix"
                >
                    <Download class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-sm font-bold text-ink">Exportar reporte</p>
                    <p class="text-xs text-ink/50">
                        Descarga el período seleccionado en el formato que
                        prefieras
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <select
                    v-model="exportTipo"
                    class="h-10 rounded-xl border border-ink/10 bg-white px-3 text-sm font-semibold text-ink focus:border-pix/40 focus:outline-none"
                >
                    <option
                        v-for="t in exportTipos"
                        :key="t.value"
                        :value="t.value"
                    >
                        {{ t.label }}
                    </option>
                </select>
                <a
                    :href="exportUrl('csv')"
                    target="_blank"
                    class="inline-flex h-10 items-center gap-2 rounded-xl bg-white px-4 text-sm font-bold text-ink shadow-sm transition hover:-translate-y-0.5"
                >
                    <FileText class="h-4 w-4 text-ink/60" /> CSV
                </a>
                <a
                    :href="exportUrl('xlsx')"
                    target="_blank"
                    class="inline-flex h-10 items-center gap-2 rounded-xl bg-[#17805a] px-4 text-sm font-bold text-white shadow-lg shadow-[#17805a]/25 transition hover:-translate-y-0.5"
                >
                    <FileSpreadsheet class="h-4 w-4" /> Excel
                </a>
                <a
                    :href="exportUrl('pdf')"
                    target="_blank"
                    class="inline-flex h-10 items-center gap-2 rounded-xl bg-pix px-4 text-sm font-bold text-white shadow-lg shadow-pix/25 transition hover:-translate-y-0.5"
                >
                    <FileText class="h-4 w-4" /> PDF
                </a>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid gap-6 lg:grid-cols-3">
            <div
                class="relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)] lg:col-span-2"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2
                            class="font-display text-lg font-extrabold tracking-tight text-ink"
                        >
                            Ingresos por mes
                        </h2>
                        <p class="text-sm text-ink/50">
                            Pagos confirmados en el período
                        </p>
                    </div>
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blush/60 text-pix"
                    >
                        <TrendingUp class="h-5 w-5" />
                    </div>
                </div>
                <div
                    v-if="salesLabels.length === 0"
                    class="py-14 text-center text-sm text-ink/40"
                >
                    Sin ingresos en el período.
                </div>
                <div v-else class="relative h-64">
                    <canvas
                        ref="salesChart"
                        aria-label="Ingresos por mes"
                    ></canvas>
                </div>
            </div>

            <div
                class="relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2
                            class="font-display text-lg font-extrabold tracking-tight text-ink"
                        >
                            Pedidos por estado
                        </h2>
                        <p class="text-sm text-ink/50">
                            Distribución del período
                        </p>
                    </div>
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-mint/60 text-[#17805a]"
                    >
                        <BarChart3 class="h-5 w-5" />
                    </div>
                </div>
                <div
                    v-if="Object.keys(ordersByStatus).length === 0"
                    class="py-14 text-center text-sm text-ink/40"
                >
                    Sin pedidos.
                </div>
                <div v-else class="relative h-64">
                    <canvas
                        ref="statusChart"
                        aria-label="Pedidos por estado"
                    ></canvas>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div
                class="relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
            >
                <div class="mb-5">
                    <h2
                        class="font-display text-lg font-extrabold tracking-tight text-ink"
                    >
                        Pagos por método
                    </h2>
                    <p class="text-sm text-ink/50">Cómo pagan tus clientes</p>
                </div>
                <div
                    v-if="Object.keys(paymentsByMethod).length === 0"
                    class="py-14 text-center text-sm text-ink/40"
                >
                    Sin pagos.
                </div>
                <div v-else class="relative h-64">
                    <canvas
                        ref="methodChart"
                        aria-label="Pagos por método"
                    ></canvas>
                </div>
            </div>

            <!-- Top services -->
            <div
                class="relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)] lg:col-span-2"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2
                            class="font-display text-lg font-extrabold tracking-tight text-ink"
                        >
                            Servicios más solicitados
                        </h2>
                        <p class="text-sm text-ink/50">Top del período</p>
                    </div>
                    <Link
                        :href="
                            reportes.servicios.url({
                                query: { periodo: periodo },
                            })
                        "
                        class="inline-flex items-center gap-1 text-xs font-bold text-pix"
                    >
                        Ver detalle <ArrowRight class="h-3 w-3" />
                    </Link>
                </div>
                <div
                    v-if="topServices.length === 0"
                    class="py-10 text-center text-sm text-ink/40"
                >
                    Sin datos.
                </div>
                <div v-else class="space-y-3">
                    <div
                        v-for="(s, i) in topServices.slice(0, 6)"
                        :key="s.id"
                        class="flex items-center gap-4"
                    >
                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-warm text-xs font-bold text-ink/60"
                            >{{ i + 1 }}</span
                        >
                        <div class="min-w-0 flex-1">
                            <div
                                class="flex items-center justify-between gap-3"
                            >
                                <p class="truncate text-sm font-bold text-ink">
                                    {{ s.nombre }}
                                </p>
                                <p class="shrink-0 text-sm font-bold text-ink">
                                    {{ formatMoney(s.total_revenue) }}
                                </p>
                            </div>
                            <div
                                class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-warm"
                            >
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-pix to-lilac"
                                    :style="{
                                        width: `${topServices[0]?.total_revenue ? (s.total_revenue / topServices[0].total_revenue) * 100 : 0}%`,
                                    }"
                                ></div>
                            </div>
                        </div>
                        <span
                            class="shrink-0 rounded-full bg-lavender/70 px-2.5 py-1 text-xs font-bold text-ink/70"
                            >{{ s.total_quantity }}×</span
                        >
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Top clients -->
            <div
                class="relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)] lg:col-span-2"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2
                            class="font-display text-lg font-extrabold tracking-tight text-ink"
                        >
                            Clientes frecuentes
                        </h2>
                        <p class="text-sm text-ink/50">
                            Mayor volumen en el período
                        </p>
                    </div>
                    <Link
                        :href="
                            reportes.clientes.url({
                                query: { periodo: periodo },
                            })
                        "
                        class="inline-flex items-center gap-1 text-xs font-bold text-pix"
                    >
                        Ver detalle <ArrowRight class="h-3 w-3" />
                    </Link>
                </div>
                <div
                    v-if="topClients.length === 0"
                    class="py-10 text-center text-sm text-ink/40"
                >
                    Sin datos.
                </div>
                <table v-else class="w-full text-left text-sm">
                    <thead>
                        <tr
                            class="text-[11px] tracking-wide text-ink/40 uppercase"
                        >
                            <th class="pb-2">Cliente</th>
                            <th class="pb-2 text-center">Pedidos</th>
                            <th class="pb-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="c in topClients.slice(0, 8)"
                            :key="c.id"
                            class="border-t border-ink/5"
                        >
                            <td class="py-3 font-semibold text-ink">
                                {{ c.name }}
                            </td>
                            <td class="py-3 text-center">
                                <span
                                    class="rounded-full bg-lavender/70 px-2.5 py-1 text-xs font-bold text-ink/70"
                                    >{{ c.orders_count }}</span
                                >
                            </td>
                            <td class="py-3 text-right font-bold text-ink">
                                {{ formatMoney(c.total) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pending / morosos -->
            <div
                class="relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2
                            class="font-display text-lg font-extrabold tracking-tight text-ink"
                        >
                            Saldos pendientes
                        </h2>
                        <p class="text-sm text-ink/50">
                            {{ pendingOrders.length }} pedido(s)
                        </p>
                    </div>
                    <Link
                        :href="
                            reportes.morosos.url({
                                query: { periodo: periodo },
                            })
                        "
                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-red-500/10 text-red-500"
                    >
                        <AlertTriangle class="h-5 w-5" />
                    </Link>
                </div>
                <div
                    v-if="pendingOrders.length === 0"
                    class="py-10 text-center text-sm text-ink/40"
                >
                    Todo cobrado.
                </div>
                <div v-else class="max-h-80 space-y-2.5 overflow-y-auto pr-1">
                    <Link
                        v-for="o in pendingOrders.slice(0, 10)"
                        :key="o.id"
                        :href="
                            reportes.morosos.url({
                                query: { periodo: periodo },
                            })
                        "
                        class="flex items-center justify-between rounded-xl border border-ink/5 bg-warm p-3 transition hover:bg-red-50"
                    >
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-ink">
                                {{ o.code }}
                            </p>
                            <p class="truncate text-xs text-ink/50">
                                {{ o.client_name || 'Sin cliente' }}
                            </p>
                        </div>
                        <span
                            class="shrink-0 rounded-full bg-red-500/10 px-2.5 py-1 text-xs font-bold text-red-600"
                            >{{ formatMoney(o.balance) }}</span
                        >
                    </Link>
                </div>
            </div>
        </div>

        <!-- Detail reports -->
        <div>
            <h2
                class="mb-3 font-display text-lg font-extrabold tracking-tight text-ink"
            >
                Reportes detallados
            </h2>
            <div
                class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5"
            >
                <Link
                    v-for="card in reportCards"
                    :key="card.routeName"
                    :href="
                        reportes[card.routeName].url({
                            query: { periodo: periodo },
                        })
                    "
                    class="group rounded-2xl border border-ink/5 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg"
                >
                    <div
                        class="mb-3 inline-flex rounded-xl bg-gradient-to-r p-2.5"
                        :class="card.color"
                    >
                        <component :is="card.icon" class="h-5 w-5 text-white" />
                    </div>
                    <h3 class="text-sm font-bold text-ink group-hover:text-pix">
                        {{ card.title }}
                    </h3>
                    <p class="mt-1 text-xs text-ink/50">
                        {{ card.description }}
                    </p>
                </Link>
            </div>
        </div>
    </div>
</template>
