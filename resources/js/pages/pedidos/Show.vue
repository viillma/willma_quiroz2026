<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Pencil, Palette, DollarSign, FileDown } from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import pedidos from '@/routes/pedidos';
import disenos from '@/routes/disenos';
import pagos from '@/routes/pagos';
import { dashboard } from '@/routes';
import type { Team } from '@/types';

const page = usePage<{
    currentTeam?: Team | null;
    order: {
        id: number;
        code: string;
        client: { id: number; name: string; phone: string; email: string; company: string } | null;
        designer: { id: number; name: string } | null;
        status: string;
        notes: string;
        total: string;
        delivery_date: string | null;
        created_at: string;
        servicios: Array<{
            id: number;
            nombre: string;
            pivot: { quantity: number; unit_price: string; subtotal: string };
        }>;
        designFiles: Array<{
            id: number;
            version: number;
            file_path: string;
            file_type: string;
            comments: string;
            is_approved: boolean;
            uploader: { id: number; name: string };
            created_at: string;
        }>;
        payments: Array<{
            id: number;
            amount: string;
            payment_method: string;
            status: string;
            reference: string;
            confirmed_by: number | null;
            created_at: string;
        }>;
    };
}>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            { title: 'Dashboard', href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/' },
            { title: 'Pedidos', href: props.currentTeam ? pedidos.index(props.currentTeam.slug) : '#' },
            { title: 'Pedido ' + (props as any).order?.code || '', href: '#' },
        ],
    }),
});

const currentTeam = page.props.currentTeam as Team | null;
const order = computed(() => page.props.order);

const statusLabels: Record<string, string> = {
    pendiente: 'Pendiente', en_diseno: 'En Diseño', en_revision: 'En Revisión',
    aprobado: 'Aprobado', entregado: 'Entregado',
};
const statusColors: Record<string, string> = {
    pendiente: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    en_diseno: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    en_revision: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
    aprobado: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    entregado: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
};

const totalPaid = computed(() =>
    order.value.payments.filter((p) => p.status === 'confirmado').reduce((sum, p) => sum + Number(p.amount), 0),
);
const balance = computed(() => Number(order.value.total) - totalPaid.value);
</script>

<template>
    <Head :title="'Pedido ' + order.code" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 space-y-6">
        <div class="flex items-center justify-between">
            <Heading :title="'Pedido ' + order.code" :description="'Registrado el ' + new Date(order.created_at).toLocaleDateString()" />
            <div class="flex gap-2">
                <Button v-if="currentTeam" size="sm" class="btn-gradient-ghost" as-child>
                    <Link :href="pedidos.edit({ current_team: currentTeam.slug, pedido: order.id }).url">
                        <Pencil class="mr-1 h-4 w-4" /> Editar
                    </Link>
                </Button>
                <Button v-if="currentTeam" size="sm" class="btn-gradient-ghost" as-child>
                    <Link :href="pedidos.index.url({ current_team: currentTeam.slug })">Volver</Link>
                </Button>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="glass-card rounded-2xl p-6">
                    <h3 class="mb-4 text-lg font-semibold text-white">Información del Pedido</h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm text-white/50">Cliente</p>
                            <p class="font-medium text-white">{{ order.client?.name || '-' }}</p>
                            <p v-if="order.client?.company" class="text-sm text-white/50">{{ order.client.company }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-white/50">Diseñador</p>
                            <p class="font-medium text-white">{{ order.designer?.name || 'Sin asignar' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-white/50">Estado</p>
                            <span :class="'badge-status-' + order.status" class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium">{{ statusLabels[order.status] || order.status }}</span>
                        </div>
                        <div>
                            <p class="text-sm text-white/50">Fecha de Entrega</p>
                            <p class="font-medium text-white">{{ order.delivery_date ? new Date(order.delivery_date).toLocaleDateString() : 'No definida' }}</p>
                        </div>
                    </div>
                    <div v-if="order.notes" class="mt-4">
                        <p class="text-sm text-white/50">Notas</p>
                        <p class="text-sm text-white/80">{{ order.notes }}</p>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">Servicios</h3>
                    </div>
                    <div class="overflow-x-auto">
                    <table class="table-glow w-full">
                        <thead>
                            <tr class="border-b border-white/10 text-sm text-white/50">
                                <th class="pb-2 text-left">Servicio</th>
                                <th class="pb-2 text-center">Cant.</th>
                                <th class="pb-2 text-right">Precio Unit.</th>
                                <th class="pb-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="servicio in order.servicios" :key="servicio.id" class="border-b border-white/5 text-sm text-white/80 hover:bg-white/5 transition-colors">
                                <td class="py-2">{{ servicio.nombre }}</td>
                                <td class="py-2 text-center">{{ servicio.pivot.quantity }}</td>
                                <td class="py-2 text-right">S/ {{ servicio.pivot.unit_price }}</td>
                                <td class="py-2 text-right font-medium text-white">S/ {{ servicio.pivot.subtotal }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="text-lg font-semibold text-white">
                                <td colspan="3" class="pt-4 text-right">Total:</td>
                                <td class="pt-4 text-right">S/ {{ order.total }}</td>
                            </tr>
                        </tfoot>
                    </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">Diseños</h3>
                        <Button v-if="currentTeam" size="sm" class="btn-gradient-ghost" as-child>
                            <Link :href="disenos.index.url({ current_team: currentTeam.slug, order: order.id })">
                                <Palette class="mr-1 h-4 w-4" /> Ver Diseños
                            </Link>
                        </Button>
                    </div>
                    <p v-if="order.designFiles.length === 0" class="text-sm text-white/50">Sin diseños subidos aún</p>
                    <div v-else class="space-y-2">
                        <div v-for="file in order.designFiles" :key="file.id" class="flex items-center justify-between rounded-lg glass p-2 text-sm text-white/80">
                            <span>V{{ file.version }} - {{ file.file_type?.toUpperCase() }}</span>
                            <span v-if="file.is_approved" class="badge-green inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">Aprobado</span>
                        </div>
                    </div>
                </div>
                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">Pagos</h3>
                        <Button v-if="currentTeam" size="sm" class="btn-gradient-ghost" as-child>
                            <Link :href="pagos.index.url({ current_team: currentTeam.slug, order: order.id })">
                                <DollarSign class="mr-1 h-4 w-4" /> Ver Pagos
                            </Link>
                        </Button>
                    </div>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-white/50">Total:</span>
                            <span class="font-medium text-white">S/ {{ order.total }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white/50">Pagado:</span>
                            <span class="font-medium text-green-400">S/ {{ totalPaid.toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between border-t border-white/10 pt-1">
                            <span class="font-medium text-white">Saldo:</span>
                            <span :class="balance > 0 ? 'text-red-400 font-medium' : 'text-green-400 font-medium'">
                                S/ {{ balance.toFixed(2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div v-if="order.status === 'aprobado' && currentTeam" class="glass-card rounded-2xl p-6 border border-green-500/30 glow-green">
                    <h3 class="mb-2 text-lg font-semibold text-green-300">Entregar Trabajo</h3>
                    <p class="mb-4 text-sm text-green-400/70">El diseño está aprobado. Puedes proceder con la entrega.</p>
                    <Button class="btn-gradient-blue w-full" @click="() => {
                        if (currentTeam && confirm('¿Marcar pedido como entregado?')) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = pedidos.update.url({ current_team: currentTeam.slug, pedido: order.id });
                            const methodInput = document.createElement('input');
                            methodInput.name = '_method';
                            methodInput.value = 'PUT';
                            form.appendChild(methodInput);
                            const statusInput = document.createElement('input');
                            statusInput.name = 'status';
                            statusInput.value = 'entregado';
                            form.appendChild(statusInput);
                            const clientInput = document.createElement('input');
                            clientInput.name = 'client_id';
                            clientInput.value = String(order.client?.id || '');
                            form.appendChild(clientInput);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    }">
                        <FileDown class="mr-2 h-4 w-4" /> Marcar como Entregado
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
