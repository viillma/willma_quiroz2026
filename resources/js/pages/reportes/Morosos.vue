<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import reportes from '@/routes/reportes';
import { dashboard } from '@/routes';
import type { Team } from '@/types';

const page = usePage<{
    currentTeam?: Team | null;
    orders: Array<{
        id: number;
        code: string;
        client: { id: number; name: string } | null;
        total: string;
        status: string;
        created_at: string;
        balance: number;
    }>;
}>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            { title: 'Dashboard', href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/' },
            { title: 'Reportes', href: props.currentTeam ? reportes.index(props.currentTeam.slug).url : '#' },
            { title: 'Morosos', href: '#' },
        ],
    }),
});

const currentTeam = page.props.currentTeam as Team | null;

const totalDeuda = computed(() =>
    page.props.orders.reduce((sum, o) => sum + o.balance, 0),
);
</script>

<template>
    <Head title="Clientes Morosos" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 space-y-6 relative">
        <div class="flex items-center justify-between">
            <Heading title="Clientes Morosos" description="Pedidos con saldo pendiente" />
            <Button v-if="currentTeam" variant="ghost" size="sm" class="btn-gradient-ghost" as-child>
                <Link :href="reportes.index(currentTeam.slug).url">Volver</Link>
            </Button>
        </div>

        <div class="kpi-orange glass-card rounded-2xl p-5 animate-slide-up">
            <div class="flex items-center gap-3 mb-2">
                <span class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-red-500 to-orange-500 p-2">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
                <p class="text-lg font-semibold text-white">Deuda Total</p>
            </div>
            <p class="text-3xl font-bold text-white glow-green drop-shadow-lg">S/ {{ totalDeuda.toFixed(2) }}</p>
            <p class="mt-1 text-sm text-white/60">
                <span class="inline-flex items-center gap-1">
                    <span class="badge-red px-2 py-0.5 text-xs">{{ orders.length }}</span>
                    pedido(s) con saldo pendiente
                </span>
            </p>
        </div>

        <div class="glass-card rounded-2xl overflow-hidden card-glow">
            <table class="table-glow w-full">
                <thead>
                    <tr class="border-b border-white/10 bg-gradient-to-r from-red-500/10 to-orange-500/5">
                        <th class="px-4 py-3.5 text-left text-sm font-semibold text-white/60">Código</th>
                        <th class="px-4 py-3.5 text-left text-sm font-semibold text-white/60">Cliente</th>
                        <th class="px-4 py-3.5 text-left text-sm font-semibold text-white/60">Total</th>
                        <th class="px-4 py-3.5 text-left text-sm font-semibold text-white/60">Saldo</th>
                        <th class="px-4 py-3.5 text-left text-sm font-semibold text-white/60">Estado</th>
                        <th class="px-4 py-3.5 text-left text-sm font-semibold text-white/60">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr v-for="order in orders" :key="order.id" class="transition-all duration-200 text-white/70 hover:bg-white/5">
                        <td class="px-4 py-3.5"><span class="badge-gray text-sm font-medium px-2.5 py-1">{{ order.code }}</span></td>
                        <td class="px-4 py-3.5 text-sm font-medium text-white">{{ order.client?.name || '-' }}</td>
                        <td class="px-4 py-3.5 text-sm text-white/50">S/ {{ order.total }}</td>
                        <td class="px-4 py-3.5"><span class="badge-red text-sm font-semibold px-2.5 py-1">S/ {{ order.balance.toFixed(2) }}</span></td>
                        <td class="px-4 py-3.5">
                            <Badge class="badge-red">{{ order.status }}</Badge>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-white/40">
                            {{ new Date(order.created_at).toLocaleDateString() }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
