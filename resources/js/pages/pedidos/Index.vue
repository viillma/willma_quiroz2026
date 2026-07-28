<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Plus, Eye } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import pedidos from '@/routes/pedidos';
import type { Team } from '@/types';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage();
const teamSlug = ref<string>('');

watch(
    () => page.props.currentTeam,
    (team) => {
        teamSlug.value = (team as Team | null)?.slug ?? '';
    },
    { immediate: true },
);

const statusLabels: Record<string, string> = {
    pendiente: 'Pendiente',
    en_diseno: 'En Diseño',
    en_revision: 'En Revisión',
    aprobado: 'Aprobado',
    entregado: 'Entregado',
};

const statusColors: Record<string, string> = {
    pendiente: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    en_diseno: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    en_revision: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
    aprobado: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    entregado: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
};

interface OrderItem {
    id: number;
    code: string;
    client: { id: number; name: string } | null;
    designer: { id: number; name: string } | null;
    status: string;
    total: string;
    balance: number;
    is_paid: boolean;
    services_count: number;
    created_at: string;
}

const orders_data = computed(() => (page.props.orders || []) as OrderItem[]);
</script>

<template>
    <Head title="Pedidos" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Heading title="Pedidos" description="Registro de pedidos de diseño" />
            <Button v-if="teamSlug" as-child class="btn-gradient-blue">
                <Link :href="pedidos.create({ current_team: teamSlug }).url">
                    <Plus class="mr-2 h-4 w-4" />
                    Nuevo Pedido
                </Link>
            </Button>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="kpi-blue rounded-2xl p-5 kpi-card shadow-lg glow-blue animate-slide-up">
                <p class="text-xs font-medium uppercase tracking-wider text-white/70">Total Pedidos</p>
                <p class="mt-1 text-2xl font-bold text-white">{{ orders_data.length }}</p>
            </div>
            <div class="kpi-orange rounded-2xl p-5 kpi-card shadow-lg glow-orange animate-slide-up" style="animation-delay: 0.1s">
                <p class="text-xs font-medium uppercase tracking-wider text-white/70">Pendientes</p>
                <p class="mt-1 text-2xl font-bold text-white">{{ orders_data.filter(o => o.status === 'pendiente').length }}</p>
            </div>
            <div class="kpi-purple rounded-2xl p-5 kpi-card shadow-lg glow-purple animate-slide-up" style="animation-delay: 0.2s">
                <p class="text-xs font-medium uppercase tracking-wider text-white/70">En Diseño</p>
                <p class="mt-1 text-2xl font-bold text-white">{{ orders_data.filter(o => o.status === 'en_diseno').length }}</p>
            </div>
            <div class="kpi-green rounded-2xl p-5 kpi-card shadow-lg glow-green animate-slide-up" style="animation-delay: 0.3s">
                <p class="text-xs font-medium uppercase tracking-wider text-white/70">Completados</p>
                <p class="mt-1 text-2xl font-bold text-white">{{ orders_data.filter(o => o.status === 'aprobado' || o.status === 'entregado').length }}</p>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table-glow w-full">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">Código</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">Cliente</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">Diseñador</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">Servicios</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">Total</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">Estado</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-white/50">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template v-if="teamSlug && orders_data.length > 0">
                            <tr v-for="order in orders_data" :key="order.id" class="transition-colors hover:bg-white/5">
                                <td class="px-5 py-4 text-sm font-medium">{{ order.code }}</td>
                                <td class="px-5 py-4 text-sm">{{ order.client?.name || '-' }}</td>
                                <td class="px-5 py-4 text-sm text-muted-foreground">{{ order.designer?.name || 'Sin asignar' }}</td>
                                <td class="px-5 py-4 text-sm">{{ order.services_count }} servicio(s)</td>
                                <td class="px-5 py-4 text-sm">S/ {{ order.total }}</td>
                                <td class="px-5 py-4">
                                    <span :class="'badge-status-' + order.status" class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium transition-all duration-200">
                                        {{ statusLabels[order.status] || order.status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button size="sm" as-child class="btn-gradient-ghost">
                                            <Link
                                                :href="
                                                    pedidos.show({
                                                        current_team: teamSlug,
                                                        pedido: order.id,
                                                    }).url
                                                "
                                            >
                                                <Eye class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr v-else>
                            <td colspan="7" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="h-12 w-12 text-white/30" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6"/><path d="m3.5 3.5 7.5 5 7.5-5"/><path d="M11 21v-7.5"/><path d="M21 15v6"/><path d="M15 18h6"/></svg>
                                    <p class="text-lg font-medium text-white/60">
                                        {{ teamSlug ? 'No hay pedidos registrados' : 'Cargando...' }}
                                    </p>
                                    <p v-if="teamSlug" class="text-sm text-white/40">Crea tu primer pedido para comenzar</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4 md:hidden">
            <template v-if="teamSlug && orders_data.length > 0">
                <div v-for="order in orders_data" :key="order.id" class="glass-card rounded-2xl p-4 animate-slide-up">
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-sm font-semibold text-white">{{ order.code }}</span>
                        <span :class="'badge-status-' + order.status" class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium transition-all duration-200">
                            {{ statusLabels[order.status] || order.status }}
                        </span>
                    </div>
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-white/50">Cliente:</span>
                            <span class="text-white/80">{{ order.client?.name || '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white/50">Diseñador:</span>
                            <span class="text-white/80">{{ order.designer?.name || 'Sin asignar' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white/50">Servicios:</span>
                            <span class="text-white/80">{{ order.services_count }} servicio(s)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white/50">Total:</span>
                            <span class="font-medium text-white">S/ {{ order.total }}</span>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-end">
                        <Button size="sm" as-child class="btn-gradient-ghost">
                            <Link
                                :href="
                                    pedidos.show({
                                        current_team: teamSlug,
                                        pedido: order.id,
                                    }).url
                                "
                            >
                                <Eye class="mr-1 h-4 w-4" />
                                Ver detalle
                            </Link>
                        </Button>
                    </div>
                </div>
            </template>
            <div v-else class="flex flex-col items-center gap-3 py-16 text-center">
                <svg class="h-12 w-12 text-white/30" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6"/><path d="m3.5 3.5 7.5 5 7.5-5"/><path d="M11 21v-7.5"/><path d="M21 15v6"/><path d="M15 18h6"/></svg>
                <p class="text-lg font-medium text-white/60">
                    {{ teamSlug ? 'No hay pedidos registrados' : 'Cargando...' }}
                </p>
                <p v-if="teamSlug" class="text-sm text-white/40">Crea tu primer pedido para comenzar</p>
            </div>
        </div>
    </div>
</template>
