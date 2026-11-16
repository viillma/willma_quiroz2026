<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

import { dashboard } from '@/routes';
import reportes from '@/routes/reportes';

const page = usePage<{
    start: string;
    end: string;
    total_income: number;
    total_orders: number;
    pending_orders: number;
    completed_orders: number;
    payments_by_method: Record<string, number>;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard().url },
            { title: 'Reportes', href: reportes.index().url },
            { title: 'Ingresos', href: '#' },
        ],
    }),
});
const start = ref(page.props.start);
const end = ref(page.props.end);

const totalIncome = computed(() => page.props.total_income ?? 0);
const totalOrders = computed(() => page.props.total_orders ?? 0);
const pendingOrders = computed(() => page.props.pending_orders ?? 0);
const completedOrders = computed(() => page.props.completed_orders ?? 0);
const paymentsByMethod = computed(() => page.props.payments_by_method ?? {});

function filter() {
    const url = reportes.ingresos.url({
        query: { start: start.value, end: end.value },
    });
    window.location.href = url;
}

const paymentMethodLabels: Record<string, string> = {
    efectivo: 'Efectivo',
    transferencia: 'Transferencia',
    yape: 'Yape',
    plin: 'Plin',
};
</script>

<template>
    <Head title="Reporte de Ingresos" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <Heading
            title="Reporte de Ingresos"
            description="Resumen financiero por período"
        />

        <div class="flex items-end gap-4">
            <div class="space-y-1">
                <Label for="start" class="text-white/80">Fecha inicio</Label>
                <Input id="start" v-model="start" type="date" class="glass" />
            </div>
            <div class="space-y-1">
                <Label for="end" class="text-white/80">Fecha fin</Label>
                <Input id="end" v-model="end" type="date" class="glass" />
            </div>
            <Button @click="filter" class="btn-gradient-blue btn-shimmer"
                >Filtrar</Button
            >

            <Button variant="ghost" class="btn-gradient-ghost" as-child>
                <Link :href="reportes.index().url">Volver</Link>
            </Button>
        </div>

        <div class="grid gap-6 md:grid-cols-4">
            <div
                class="kpi-green glass-card animate-scale-in rounded-2xl p-5 transition-all duration-300 hover:scale-[1.02]"
            >
                <p class="text-sm text-white/60">Total Ingresos</p>
                <p class="mt-1 text-3xl font-bold text-white">
                    S/ {{ totalIncome.toFixed(2) }}
                </p>
            </div>
            <div
                class="kpi-blue glass-card animate-scale-in rounded-2xl p-5 transition-all duration-300 hover:scale-[1.02]"
            >
                <p class="text-sm text-white/60">Total Pedidos</p>
                <p class="mt-1 text-3xl font-bold text-white">
                    {{ totalOrders }}
                </p>
            </div>
            <div
                class="kpi-orange glass-card animate-scale-in rounded-2xl p-5 transition-all duration-300 hover:scale-[1.02]"
            >
                <p class="text-sm text-white/60">Pedidos Pendientes</p>
                <p class="mt-1 text-3xl font-bold text-white">
                    {{ pendingOrders }}
                </p>
            </div>
            <div
                class="kpi-teal glass-card animate-scale-in rounded-2xl p-5 transition-all duration-300 hover:scale-[1.02]"
            >
                <p class="text-sm text-white/60">Pedidos Completados</p>
                <p class="mt-1 text-3xl font-bold text-white">
                    {{ completedOrders }}
                </p>
            </div>
        </div>

        <div class="glass-card card-glow rounded-2xl p-6">
            <div class="mb-6 flex items-center gap-3">
                <span
                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-cyan-500 to-blue-500 px-3 py-1.5 text-xs font-bold text-white"
                    >S/</span
                >
                <h3 class="text-lg font-semibold text-white">
                    Ingresos por Método de Pago
                </h3>
            </div>
            <div class="divider-gradient mb-4"></div>
            <div class="space-y-3">
                <div
                    v-for="(amount, method) in paymentsByMethod"
                    :key="method"
                    class="glass animate-slide-up flex items-center justify-between rounded-xl p-4 transition-all duration-200 hover:bg-white/5"
                >
                    <span
                        class="flex items-center gap-3 font-medium text-white/80"
                    >
                        <span
                            class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-white/10 to-white/5 text-xs font-bold text-white/40"
                            >{{
                                Object.keys(paymentsByMethod).indexOf(method) +
                                1
                            }}</span
                        >
                        <span
                            class="inline-block h-2 w-2 rounded-full bg-gradient-to-r from-cyan-400 to-blue-500"
                        ></span>
                        {{ paymentMethodLabels[method] || method }}
                    </span>
                    <span class="badge-green px-3 py-1.5 font-semibold"
                        >S/ {{ Number(amount).toFixed(2) }}</span
                    >
                </div>
                <div
                    v-if="Object.keys(paymentsByMethod).length === 0"
                    class="py-4 text-center text-white/50"
                >
                    No hay pagos confirmados en este período.
                </div>
            </div>
        </div>
    </div>
</template>
