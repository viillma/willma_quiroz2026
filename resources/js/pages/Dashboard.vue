<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import Chart from 'chart.js/auto';
import gsap from 'gsap';
import {
    Users,
    Package,
    TrendingUp,
    Wallet,
    Sparkles,
    ArrowRight,
    Activity,
    BarChart3,
    CreditCard,
    FileText,
    FolderKanban,
    Clock,
    Film,
} from 'lucide-vue-next';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { usePermissions } from '@/composables/usePermissions';
import { formatMoney, timeAgo } from '@/lib/money';
import { dashboard } from '@/routes';

import cotizaciones from '@/routes/cotizaciones';
import pedidos from '@/routes/pedidos';

defineOptions({
    layout: () => ({
        breadcrumbs: [{ title: 'Dashboard', href: dashboard().url }],
    }),
});

type OrderRow = {
    id: number;
    code: string;
    client: { id: number; name: string } | null;
    status: string;
    total: number;
    balance: number;
    delivery_date: string | null;
    services_names: string;
    services_count: number;
    created_at: string;
};

type PaymentRow = {
    id: number;
    amount: number;
    method: string;
    order_code: string | null;
    client_name: string | null;
    confirmed_at: string | null;
};

const page = usePage<{
    kpis: Record<string, number>;
    latestOrders: OrderRow[];
    dueSoon: OrderRow[];
    incomeByMonth: Array<{ label: string; value: number }>;
    salesByMonth: Array<{ label: string; value: number }>;
    customersByMonth: Array<{ label: string; value: number }>;
    ordersByStatus: Array<{ status: string; label: string; value: number }>;
    topServices: Array<{
        id: number;
        nombre: string;
        total_quantity: number;
        total_revenue: number;
    }>;
    recentPayments: PaymentRow[];
    canManageOrders: boolean;
    canManageClients: boolean;
    canManageQuotes: boolean;
    canManagePayments: boolean;
}>();
const { can } = usePermissions();

const kpis = page.props.kpis ?? {};
const latestOrders = page.props.latestOrders ?? [];
const dueSoon = page.props.dueSoon ?? [];
const incomeByMonth = page.props.incomeByMonth ?? [];
const salesByMonth = page.props.salesByMonth ?? [];
const customersByMonth = page.props.customersByMonth ?? [];
const ordersByStatus = page.props.ordersByStatus ?? [];
const topServices = page.props.topServices ?? [];
const recentPayments = page.props.recentPayments ?? [];

const statusMeta: Record<string, { label: string; badge: string }> = {
    nuevo: { label: 'Nuevo', badge: 'badge-cyan' },
    confirmado: { label: 'Confirmado', badge: 'badge-blue' },
    en_proceso: { label: 'En proceso', badge: 'badge-purple' },
    en_revision: { label: 'En revisión', badge: 'badge-yellow' },
    terminado: { label: 'Terminado', badge: 'badge-cyan' },
    entregado: { label: 'Entregado', badge: 'badge-green' },
    cancelado: { label: 'Cancelado', badge: 'badge-red' },
};

let mm: ReturnType<typeof gsap.matchMedia> | null = null;
const rootRef = ref<HTMLElement | null>(null);

const salesCanvas = ref<HTMLCanvasElement | null>(null);
const statusCanvas = ref<HTMLCanvasElement | null>(null);
const customersCanvas = ref<HTMLCanvasElement | null>(null);
let salesChart: Chart | null = null;
let statusChart: Chart | null = null;
let customersChart: Chart | null = null;

const statusColors: Record<string, string> = {
    nuevo: '#7bc8f4',
    confirmado: '#7b5ee0',
    en_proceso: '#f4a15d',
    en_revision: '#f9d34e',
    terminado: '#5ec4a0',
    entregado: '#17805a',
    cancelado: '#f06868',
};

const gridColor = 'rgba(23, 26, 43, 0.08)';
const tickColor = 'rgba(23, 26, 43, 0.5)';

function initCharts() {
    if (typeof window === 'undefined' || !window.devicePixelRatio) {
        return;
    }

    if (salesCanvas.value) {
        salesChart = new Chart(salesCanvas.value, {
            type: 'bar',
            data: {
                labels: incomeByMonth.map((i) => i.label),
                datasets: [
                    {
                        label: 'Ventas',
                        data: salesByMonth.map((i) => i.value),
                        backgroundColor: '#F43F7A',
                        hoverBackgroundColor: '#f25f91',
                        borderRadius: 10,
                        borderSkipped: false,
                        maxBarThickness: 26,
                    },
                    {
                        label: 'Ingresos',
                        data: incomeByMonth.map((i) => i.value),
                        backgroundColor: '#DCCBFF',
                        hoverBackgroundColor: '#cdb8f8',
                        borderRadius: 10,
                        borderSkipped: false,
                        maxBarThickness: 26,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 10,
                            boxHeight: 10,
                            usePointStyle: true,
                            color: '#171A2B',
                            font: { weight: 600 },
                        },
                    },
                    tooltip: {
                        backgroundColor: '#171A2B',
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        callbacks: {
                            label: (ctx) =>
                                ` ${ctx.dataset.label}: ${formatMoney(Number(ctx.parsed.y))}`,
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

    if (statusCanvas.value) {
        statusChart = new Chart(statusCanvas.value, {
            type: 'doughnut',
            data: {
                labels: ordersByStatus.map((s) => s.label),
                datasets: [
                    {
                        data: ordersByStatus.map((s) => s.value),
                        backgroundColor: ordersByStatus.map(
                            (s) => statusColors[s.status] ?? '#7b5ee0',
                        ),
                        hoverOffset: 6,
                        borderWidth: 0,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
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
                        callbacks: {
                            label: (ctx) =>
                                ` ${ctx.label}: ${ctx.parsed} pedido${ctx.parsed === 1 ? '' : 's'}`,
                        },
                    },
                },
            },
        });
    }

    if (customersCanvas.value) {
        customersChart = new Chart(customersCanvas.value, {
            type: 'line',
            data: {
                labels: customersByMonth.map((i) => i.label),
                datasets: [
                    {
                        label: 'Clientes nuevos',
                        data: customersByMonth.map((i) => i.value),
                        borderColor: '#17805a',
                        backgroundColor: 'rgba(23, 128, 90, 0.14)',
                        fill: true,
                        tension: 0.45,
                        pointRadius: 4,
                        pointBackgroundColor: '#17805a',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#171A2B',
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        callbacks: {
                            label: (ctx) =>
                                ` ${ctx.parsed.y} cliente${ctx.parsed.y === 1 ? '' : 's'}`,
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
                        ticks: { color: tickColor, precision: 0 },
                        beginAtZero: true,
                    },
                },
            },
        });
    }
}

function destroyCharts() {
    salesChart?.destroy();
    statusChart?.destroy();
    customersChart?.destroy();
    salesChart = statusChart = customersChart = null;
}

function animateCharts() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const canvases =
        rootRef.value?.querySelectorAll<HTMLCanvasElement>('.dash-canvas');

    if (canvases?.length) {
        gsap.from(canvases, {
            opacity: 0,
            y: 18,
            duration: 0.9,
            ease: 'power2.out',
            stagger: 0.1,
            scrollTrigger: { trigger: '.dash-chart', start: 'top 90%' },
            onStart: () => initCharts(),
        });
    }

    const spark = rootRef.value?.querySelectorAll('.dash-spark');

    if (spark?.length) {
        gsap.fromTo(
            spark,
            { scaleY: 0 },
            {
                scaleY: 1,
                duration: 0.9,
                ease: 'power2.out',
                stagger: 0.04,
                scrollTrigger: { trigger: '.dash-kpis', start: 'top 90%' },
            },
        );
    }

    const rows = rootRef.value?.querySelectorAll('.dash-row');

    if (rows?.length) {
        gsap.fromTo(
            rows,
            { opacity: 0, x: -14 },
            {
                opacity: 1,
                x: 0,
                duration: 0.6,
                ease: 'power2.out',
                stagger: 0.08,
                scrollTrigger: { trigger: '.dash-activity', start: 'top 85%' },
            },
        );
    }
}

onMounted(() => {
    mm = gsap.matchMedia();
    mm.add('(min-width: 0px)', () => animateCharts());
});

onBeforeUnmount(() => {
    destroyCharts();
    mm?.revert();
});
</script>

<template>
    <Head title="Dashboard · CREAPIX" />

    <div ref="rootRef" class="relative space-y-6 p-4 md:p-6 lg:p-8">
        <!-- Hero -->
        <div
            class="animate-slide-up relative overflow-hidden rounded-3xl border border-ink/5 bg-gradient-to-br from-blush/60 via-lavender/60 to-peach/50 p-6 md:p-8"
        >
            <div
                class="pointer-events-none absolute -top-24 -right-24 h-64 w-64 rounded-full bg-lilac/50 blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute -bottom-24 -left-16 h-56 w-56 rounded-full bg-mint/70 blur-3xl"
            ></div>

            <div
                class="relative flex flex-col justify-between gap-6 md:flex-row md:items-center"
            >
                <div class="flex items-start gap-4">
                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/70 text-pix shadow-lg shadow-pix/10 backdrop-blur"
                    >
                        <Sparkles class="h-7 w-7" />
                    </div>
                    <div>
                        <h1
                            class="font-display text-2xl font-extrabold tracking-tight text-ink md:text-3xl"
                        >
                            Panel de Control
                        </h1>
                        <p
                            class="mt-1 max-w-md text-sm text-ink/60 md:text-base"
                        >
                            El pulso de tu estudio creativo: pedidos, ingresos y
                            clientes en un solo lugar.
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div
                        class="flex items-center gap-2.5 rounded-2xl bg-white/70 px-4 py-2.5 shadow-sm backdrop-blur"
                    >
                        <span class="relative flex h-2.5 w-2.5">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-mint opacity-75"
                            ></span>
                            <span
                                class="relative inline-flex h-2.5 w-2.5 rounded-full bg-[#17805a]"
                            ></span>
                        </span>
                        <span class="text-xs font-semibold text-ink/60"
                            >Estudio activo</span
                        >
                    </div>
                    <Link
                        v-if="can('pedidos.crear')"
                        :href="pedidos.create().url"
                        class="group inline-flex items-center gap-2 rounded-2xl bg-pix px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-pix/25 transition-all duration-300 hover:-translate-y-0.5 hover:bg-pix-2"
                    >
                        Nuevo Pedido
                        <ArrowRight
                            class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
                        />
                    </Link>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="dash-kpis grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div
                class="group relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.16)] transition-all duration-500 hover:-translate-y-1.5 hover:shadow-[0_22px_50px_-20px_rgba(23,26,43,0.22)]"
            >
                <div
                    class="pointer-events-none absolute -top-10 -right-10 h-36 w-36 rounded-full bg-gradient-to-br from-mint/60 to-lavender/50 opacity-70 blur-2xl transition-opacity duration-500 group-hover:opacity-100"
                ></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p
                            class="text-[11px] font-bold tracking-[0.14em] text-ink/45 uppercase"
                        >
                            Ingresos del mes
                        </p>
                        <h2
                            class="mt-2 font-display text-3xl font-extrabold tracking-tight text-ink"
                        >
                            {{ formatMoney(kpis.ingresos_del_mes) }}
                        </h2>
                        <p
                            class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-green-700"
                        >
                            <TrendingUp class="h-3 w-3" />
                            {{ kpis.pagos_recibidos_mes ?? 0 }} pagos
                            confirmados
                        </p>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#17805a]/10 text-[#17805a] transition-transform duration-500 group-hover:scale-110"
                    >
                        <CreditCard class="h-6 w-6" />
                    </div>
                </div>
                <div class="relative mt-5 flex h-10 items-end gap-1.5">
                    <span
                        v-for="(bar, i) in [35, 55, 65, 50, 80, 90]"
                        :key="i"
                        class="dash-spark w-full origin-bottom rounded-full bg-[#17805a]/20 opacity-90"
                        :style="{ height: `${bar}%` }"
                    ></span>
                </div>
            </div>

            <div
                class="group relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.16)] transition-all duration-500 hover:-translate-y-1.5 hover:shadow-[0_22px_50px_-20px_rgba(23,26,43,0.22)]"
            >
                <div
                    class="pointer-events-none absolute -top-10 -right-10 h-36 w-36 rounded-full bg-gradient-to-br from-blush/60 to-lavender/60 opacity-70 blur-2xl transition-opacity duration-500 group-hover:opacity-100"
                ></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p
                            class="text-[11px] font-bold tracking-[0.14em] text-ink/45 uppercase"
                        >
                            Pedidos activos
                        </p>
                        <h2
                            class="mt-2 font-display text-3xl font-extrabold tracking-tight text-ink"
                        >
                            {{ kpis.pedidos_activos ?? 0 }}
                        </h2>
                        <p
                            class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-[#7b5ee0]"
                        >
                            <Clock class="h-3 w-3" />
                            {{ kpis.pedidos_pendientes ?? 0 }} sin iniciar
                        </p>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#7b5ee0]/10 text-[#7b5ee0] transition-transform duration-500 group-hover:scale-110"
                    >
                        <Package class="h-6 w-6" />
                    </div>
                </div>
                <div class="relative mt-5 flex h-10 items-end gap-1.5">
                    <span
                        v-for="(bar, i) in [40, 65, 45, 80, 60, 95]"
                        :key="i"
                        class="dash-spark w-full origin-bottom rounded-full bg-[#7b5ee0]/20 opacity-90"
                        :style="{ height: `${bar}%` }"
                    ></span>
                </div>
            </div>

            <div
                class="group relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.16)] transition-all duration-500 hover:-translate-y-1.5 hover:shadow-[0_22px_50px_-20px_rgba(23,26,43,0.22)]"
            >
                <div
                    class="pointer-events-none absolute -top-10 -right-10 h-36 w-36 rounded-full bg-gradient-to-br from-lilac/50 to-lavender/60 opacity-70 blur-2xl transition-opacity duration-500 group-hover:opacity-100"
                ></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p
                            class="text-[11px] font-bold tracking-[0.14em] text-ink/45 uppercase"
                        >
                            Clientes
                        </p>
                        <h2
                            class="mt-2 font-display text-3xl font-extrabold tracking-tight text-ink"
                        >
                            {{ kpis.total_clientes ?? 0 }}
                        </h2>
                        <p
                            class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-[#cf6c0b]"
                        >
                            <Users class="h-3 w-3" />
                            base registrada
                        </p>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#cf6c0b]/10 text-[#cf6c0b] transition-transform duration-500 group-hover:scale-110"
                    >
                        <Users class="h-6 w-6" />
                    </div>
                </div>
                <div class="relative mt-5 flex h-10 items-end gap-1.5">
                    <span
                        v-for="(bar, i) in [30, 50, 40, 70, 85, 75]"
                        :key="i"
                        class="dash-spark w-full origin-bottom rounded-full bg-[#cf6c0b]/20 opacity-90"
                        :style="{ height: `${bar}%` }"
                    ></span>
                </div>
            </div>

            <div
                class="group relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.16)] transition-all duration-500 hover:-translate-y-1.5 hover:shadow-[0_22px_50px_-20px_rgba(23,26,43,0.22)]"
            >
                <div
                    class="pointer-events-none absolute -top-10 -right-10 h-36 w-36 rounded-full bg-gradient-to-br from-peach/50 to-blush/60 opacity-70 blur-2xl transition-opacity duration-500 group-hover:opacity-100"
                ></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p
                            class="text-[11px] font-bold tracking-[0.14em] text-ink/45 uppercase"
                        >
                            Por cobrar
                        </p>
                        <h2
                            class="mt-2 font-display text-3xl font-extrabold tracking-tight text-ink"
                        >
                            {{ formatMoney(kpis.total_por_cobrar) }}
                        </h2>
                        <p
                            class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-red-600"
                        >
                            <Wallet class="h-3 w-3" />
                            saldo pendiente
                        </p>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-500/10 text-red-500 transition-transform duration-500 group-hover:scale-110"
                    >
                        <Wallet class="h-6 w-6" />
                    </div>
                </div>
                <div class="relative mt-5 flex h-10 items-end gap-1.5">
                    <span
                        v-for="(bar, i) in [50, 45, 75, 60, 90, 100]"
                        :key="i"
                        class="dash-spark w-full origin-bottom rounded-full bg-red-500/20 opacity-90"
                        :style="{ height: `${bar}%` }"
                    ></span>
                </div>
            </div>
        </div>

        <!-- Secondary stats strip -->
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6">
            <div class="kpi-blue kpi-card glow-blue rounded-2xl p-5 shadow-lg">
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Ventas del mes
                </p>
                <p class="mt-1 text-xl font-bold text-ink">
                    {{ formatMoney(kpis.ventas_del_mes) }}
                </p>
            </div>
            <div
                class="kpi-green kpi-card glow-green rounded-2xl p-5 shadow-lg"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Clientes nuevos
                </p>
                <p class="mt-1 text-xl font-bold text-ink">
                    {{ kpis.clientes_nuevos_mes ?? 0 }}
                </p>
            </div>
            <div
                class="kpi-orange kpi-card glow-orange rounded-2xl p-5 shadow-lg"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Terminados este mes
                </p>
                <p class="mt-1 text-xl font-bold text-ink">
                    {{ kpis.pedidos_terminados_mes ?? 0 }}
                </p>
            </div>
            <div class="kpi-purple kpi-card rounded-2xl p-5 shadow-lg">
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Servicios vendidos
                </p>
                <p class="mt-1 text-xl font-bold text-ink">
                    {{ kpis.servicios_vendidos ?? 0 }}
                </p>
            </div>
            <div class="kpi-cyan kpi-card rounded-2xl p-5 shadow-lg">
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Diseños realizados
                </p>
                <p class="mt-1 text-xl font-bold text-ink">
                    {{ kpis.disenos_realizados ?? 0 }}
                </p>
            </div>
            <div class="kpi-pink kpi-card rounded-2xl p-5 shadow-lg">
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Cotizaciones pend.
                </p>
                <p class="mt-1 text-xl font-bold text-ink">
                    {{ kpis.cotizaciones_pendientes ?? 0 }}
                </p>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Latest orders -->
            <div
                class="dash-activity relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)] lg:col-span-2"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2
                            class="font-display text-lg font-extrabold tracking-tight text-ink"
                        >
                            Últimos pedidos
                        </h2>
                        <p class="text-sm text-ink/50">
                            Movimientos recientes del estudio
                        </p>
                    </div>
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blush/60 text-pix"
                    >
                        <Activity class="h-5 w-5" />
                    </div>
                </div>

                <div
                    v-if="latestOrders.length === 0"
                    class="py-10 text-center text-sm text-ink/50"
                >
                    Aún no hay pedidos registrados.
                </div>

                <div v-else class="space-y-3">
                    <Link
                        v-for="order in latestOrders"
                        :key="order.id"
                        :href="pedidos.show({ pedido: order.id }).url"
                        class="dash-row group flex items-center justify-between rounded-2xl border border-ink/5 bg-warm p-4 transition-all duration-300 hover:translate-x-1 hover:border-pix/15 hover:bg-blush/25"
                    >
                        <div class="flex items-center gap-4">
                            <div
                                class="relative flex h-11 w-11 items-center justify-center rounded-xl bg-blush/70 text-pix"
                            >
                                <Package class="h-5 w-5" />
                                <span
                                    class="absolute -top-0.5 -right-0.5 h-2.5 w-2.5 rounded-full ring-2 ring-white"
                                    :class="
                                        statusMeta[order.status]?.badge ||
                                        'badge-gray'
                                    "
                                ></span>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-ink">
                                    {{ order.code }} ·
                                    {{ order.client?.name || 'Sin cliente' }}
                                </h3>
                                <p class="text-xs text-ink/50">
                                    {{
                                        order.services_names ||
                                        `${order.services_count} servicios`
                                    }}
                                </p>
                            </div>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <span
                                class="hidden rounded-full px-3 py-1 text-xs font-semibold sm:inline-flex"
                                :class="
                                    statusMeta[order.status]?.badge ||
                                    'badge-gray'
                                "
                            >
                                {{
                                    statusMeta[order.status]?.label ||
                                    order.status
                                }}
                            </span>
                            <span
                                class="shrink-0 rounded-full bg-white px-3 py-1 text-xs font-bold text-ink/70 shadow-sm"
                            >
                                {{ formatMoney(order.total) }}
                            </span>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Orders status -->
            <div
                class="dash-chart relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
            >
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2
                            class="font-display text-lg font-extrabold tracking-tight text-ink"
                        >
                            Estado de pedidos
                        </h2>
                        <p class="text-sm text-ink/50">
                            Distribución actual de tu flujo
                        </p>
                    </div>
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blush/60 text-pix"
                    >
                        <BarChart3 class="h-5 w-5" />
                    </div>
                </div>

                <div class="relative h-64">
                    <canvas
                        ref="statusCanvas"
                        class="dash-canvas"
                        aria-label="Estado de pedidos"
                    ></canvas>
                </div>

                <div
                    class="mt-4 rounded-2xl bg-gradient-to-br from-lavender/80 to-blush/50 p-5"
                >
                    <div class="flex items-center gap-3">
                        <div
                            v-if="can('cotizaciones.ver')"
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/80 text-pix"
                        >
                            <FileText class="h-4 w-4" />
                        </div>
                        <h3
                            class="font-display text-base font-extrabold text-ink"
                        >
                            Cotizaciones y ventas
                        </h3>
                    </div>
                    <p class="mt-2 text-sm text-ink/55">
                        {{ kpis.cotizaciones_pendientes ?? 0 }} cotizaciones
                        esperando aprobación. Convierte cotizaciones aceptadas
                        en pedidos.
                    </p>
                    <Link
                        v-if="can('cotizaciones.ver')"
                        :href="cotizaciones.index().url"
                        class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-pix"
                    >
                        Ir a cotizaciones <ArrowRight class="h-3 w-3" />
                    </Link>
                </div>
            </div>
        </div>

        <!-- Sales & customers charts -->
        <div class="grid gap-6 lg:grid-cols-2">
            <div
                class="dash-chart relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
            >
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2
                            class="font-display text-lg font-extrabold tracking-tight text-ink"
                        >
                            Ventas e ingresos
                        </h2>
                        <p class="text-sm text-ink/50">Últimos 6 meses · Bs</p>
                    </div>
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-mint/60 text-[#17805a]"
                    >
                        <TrendingUp class="h-5 w-5" />
                    </div>
                </div>

                <div class="relative h-60">
                    <canvas
                        ref="salesCanvas"
                        class="dash-canvas"
                        aria-label="Ventas e ingresos por mes"
                    ></canvas>
                </div>
            </div>

            <div
                class="dash-chart relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
            >
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2
                            class="font-display text-lg font-extrabold tracking-tight text-ink"
                        >
                            Clientes nuevos
                        </h2>
                        <p class="text-sm text-ink/50">
                            Registro mensual de la base de clientes
                        </p>
                    </div>
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#17805a]/10 text-[#17805a]"
                    >
                        <Users class="h-5 w-5" />
                    </div>
                </div>

                <div class="relative h-60">
                    <canvas
                        ref="customersCanvas"
                        class="dash-canvas"
                        aria-label="Clientes nuevos por mes"
                    ></canvas>
                </div>
            </div>
        </div>

        <!-- Bottom grid -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Due soon -->
            <div
                class="rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2
                            class="font-display text-lg font-extrabold tracking-tight text-ink"
                        >
                            Por entregar
                        </h2>
                        <p class="text-sm text-ink/50">Próximos 7 días</p>
                    </div>
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-orange-500/10 text-orange-600"
                    >
                        <Clock class="h-5 w-5" />
                    </div>
                </div>
                <div
                    v-if="dueSoon.length === 0"
                    class="py-8 text-center text-sm text-ink/50"
                >
                    Sin entregas cercanas.
                </div>
                <div v-else class="space-y-2.5">
                    <Link
                        v-for="order in dueSoon"
                        :key="order.id"
                        :href="pedidos.show({ pedido: order.id }).url"
                        class="flex items-center justify-between rounded-xl border border-ink/5 bg-warm p-3 transition-colors hover:bg-orange-50"
                    >
                        <div>
                            <p class="text-sm font-bold text-ink">
                                {{ order.code }} · {{ order.client?.name }}
                            </p>
                            <p class="text-xs text-ink/50">
                                {{ order.delivery_date || 'Sin fecha' }}
                            </p>
                        </div>
                        <span
                            class="rounded-full bg-white px-3 py-1 text-xs font-bold text-orange-600 shadow-sm"
                            >{{ formatMoney(order.balance) }}</span
                        >
                    </Link>
                </div>
            </div>

            <!-- Top services -->
            <div
                class="rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2
                            class="font-display text-lg font-extrabold tracking-tight text-ink"
                        >
                            Servicios top
                        </h2>
                        <p class="text-sm text-ink/50">
                            Por ingresos generados
                        </p>
                    </div>
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-lilac/40 text-[#7b5ee0]"
                    >
                        <FolderKanban class="h-5 w-5" />
                    </div>
                </div>
                <div
                    v-if="topServices.length === 0"
                    class="py-8 text-center text-sm text-ink/50"
                >
                    Sin datos de servicios aún.
                </div>
                <div v-else class="space-y-3">
                    <div
                        v-for="(s, i) in topServices"
                        :key="s.id"
                        class="flex items-center gap-3"
                    >
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-ink/5 text-xs font-bold text-ink/60"
                            >{{ i + 1 }}</span
                        >
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink">
                                {{ s.nombre }}
                            </p>
                            <p class="text-xs text-ink/50">
                                {{ s.total_quantity }} vendidos
                            </p>
                        </div>
                        <span class="text-sm font-bold text-ink/80">{{
                            formatMoney(s.total_revenue)
                        }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent payments -->
            <div
                class="dash-activity rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2
                            class="font-display text-lg font-extrabold tracking-tight text-ink"
                        >
                            Pagos recientes
                        </h2>
                        <p class="text-sm text-ink/50">
                            Últimas confirmaciones
                        </p>
                    </div>
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-green-500/10 text-green-700"
                    >
                        <Film class="h-5 w-5" />
                    </div>
                </div>
                <div
                    v-if="recentPayments.length === 0"
                    class="py-8 text-center text-sm text-ink/50"
                >
                    Sin pagos confirmados aún.
                </div>
                <div v-else class="space-y-3">
                    <div
                        v-for="p in recentPayments"
                        :key="p.id"
                        class="dash-row flex items-center justify-between rounded-xl border border-ink/5 bg-warm p-3 transition-colors hover:bg-green-50"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-green-500/10 text-green-700"
                            >
                                <TrendingUp class="h-4 w-4" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-ink">
                                    {{ p.client_name || 'Sin cliente' }}
                                </p>
                                <p class="text-xs text-ink/50">
                                    {{ p.order_code || '' }} ·
                                    {{ timeAgo(p.confirmed_at) }}
                                </p>
                            </div>
                        </div>
                        <span
                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700"
                            >{{ formatMoney(p.amount) }}</span
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
