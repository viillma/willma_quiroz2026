<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import reportes from '@/routes/reportes';
import { dashboard } from '@/routes';
import type { Team } from '@/types';

const page = usePage<{
    currentTeam?: Team | null;
    start: string;
    end: string;
    total_income: number;
    total_orders: number;
    pending_orders: number;
    completed_orders: number;
    payments_by_method: Record<string, number>;
}>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            { title: 'Dashboard', href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/' },
            { title: 'Reportes', href: props.currentTeam ? reportes.index(props.currentTeam.slug).url : '#' },
            { title: 'Ingresos', href: '#' },
        ],
    }),
});

const currentTeam = page.props.currentTeam as Team | null;
const start = ref(page.props.start);
const end = ref(page.props.end);

function filter() {
    if (currentTeam) {
        const url = reportes.income.url({ current_team: currentTeam.slug, query: { start: start.value, end: end.value } });
        window.location.href = url;
    }
}

const paymentMethodLabels: Record<string, string> = {
    efectivo: 'Efectivo', transferencia: 'Transferencia', yape: 'Yape', plin: 'Plin',
};
</script>

<template>
    <Head title="Reporte de Ingresos" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 space-y-6 relative">
        <Heading title="Reporte de Ingresos" description="Resumen financiero por período" />

        <div class="flex items-end gap-4">
            <div class="space-y-1">
                <Label for="start" class="text-white/80">Fecha inicio</Label>
                <Input id="start" v-model="start" type="date" class="glass" />
            </div>
            <div class="space-y-1">
                <Label for="end" class="text-white/80">Fecha fin</Label>
                <Input id="end" v-model="end" type="date" class="glass" />
            </div>
            <Button @click="filter" class="btn-gradient-blue btn-shimmer">Filtrar</Button>

            <Button v-if="currentTeam" variant="ghost" class="btn-gradient-ghost" as-child>
                <Link :href="reportes.index(currentTeam.slug).url">Volver</Link>
            </Button>
        </div>

        <div class="grid gap-6 md:grid-cols-4">
            <div class="kpi-green glass-card rounded-2xl p-5 animate-scale-in hover:scale-[1.02] transition-all duration-300">
                <p class="text-sm text-white/60">Total Ingresos</p>
                <p class="mt-1 text-3xl font-bold text-white">S/ {{ total_income.toFixed(2) }}</p>
            </div>
            <div class="kpi-blue glass-card rounded-2xl p-5 animate-scale-in hover:scale-[1.02] transition-all duration-300">
                <p class="text-sm text-white/60">Total Pedidos</p>
                <p class="mt-1 text-3xl font-bold text-white">{{ total_orders }}</p>
            </div>
            <div class="kpi-orange glass-card rounded-2xl p-5 animate-scale-in hover:scale-[1.02] transition-all duration-300">
                <p class="text-sm text-white/60">Pedidos Pendientes</p>
                <p class="mt-1 text-3xl font-bold text-white">{{ pending_orders }}</p>
            </div>
            <div class="kpi-teal glass-card rounded-2xl p-5 animate-scale-in hover:scale-[1.02] transition-all duration-300">
                <p class="text-sm text-white/60">Pedidos Completados</p>
                <p class="mt-1 text-3xl font-bold text-white">{{ completed_orders }}</p>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 card-glow">
            <div class="flex items-center gap-3 mb-6">
                <span class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-cyan-500 to-blue-500 px-3 py-1.5 text-xs font-bold text-white">S/</span>
                <h3 class="text-lg font-semibold text-white">Ingresos por Método de Pago</h3>
            </div>
            <div class="divider-gradient mb-4"></div>
            <div class="space-y-3">
                <div
                    v-for="(amount, method, index) in payments_by_method"
                    :key="method"
                    class="glass rounded-xl p-4 flex items-center justify-between hover:bg-white/5 transition-all duration-200 animate-slide-up"
                >
                    <span class="font-medium text-white/80 flex items-center gap-3">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-white/10 to-white/5 text-xs font-bold text-white/40">{{ index + 1 }}</span>
                        <span class="inline-block h-2 w-2 rounded-full bg-gradient-to-r from-cyan-400 to-blue-500"></span>
                        {{ paymentMethodLabels[method] || method }}
                    </span>
                    <span class="badge-green px-3 py-1.5 font-semibold">S/ {{ Number(amount).toFixed(2) }}</span>
                </div>
                <div v-if="Object.keys(payments_by_method).length === 0" class="py-4 text-center text-white/50">
                    No hay pagos confirmados en este período.
                </div>
            </div>
        </div>
    </div>
</template>
