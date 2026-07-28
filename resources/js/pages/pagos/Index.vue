<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { CheckCircle, XCircle, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import pedidos from '@/routes/pedidos';
import pagos from '@/routes/pagos';
import { dashboard } from '@/routes';
import type { Team } from '@/types';

const page = usePage<{
    currentTeam?: Team | null;
    order: {
        id: number;
        code: string;
        client: { id: number; name: string } | null;
        total: string;
        payments: Array<{
            id: number;
            amount: string;
            payment_method: string;
            status: string;
            reference: string;
            confirmer: { id: number; name: string } | null;
            confirmed_at: string | null;
            created_at: string;
        }>;
    };
    balance: number;
}>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            { title: 'Dashboard', href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/' },
            { title: 'Pedidos', href: props.currentTeam ? pedidos.index(props.currentTeam.slug) : '#' },
            { title: 'Pagos', href: '#' },
        ],
    }),
});

const currentTeam = page.props.currentTeam as Team | null;
const order = computed(() => page.props.order);
const balance = computed(() => page.props.balance);

const paymentMethods: Record<string, string> = {
    efectivo: 'Efectivo', transferencia: 'Transferencia', yape: 'Yape', plin: 'Plin',
};

const totalPaid = computed(() =>
    order.value.payments.filter((p) => p.status === 'confirmado').reduce((s, p) => s + Number(p.amount), 0),
);

const payFormBind = computed(() => {
    if (!currentTeam) return {};
    return {
        action: pagos.store.url({ current_team: currentTeam.slug, order: order.value.id }),
        method: 'post' as const,
    };
});
</script>

<template>
    <Head :title="'Pagos - ' + order.code" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 space-y-6 relative z-10">
        <div class="flex items-center justify-between">
            <div class="animate-slide-up">
                <Heading
                    :title="'Pagos - ' + order.code"
                    :description="'Cliente: ' + (order.client?.name || 'Sin cliente')"
                />
            </div>
            <Button v-if="currentTeam" variant="ghost" size="sm" class="btn-gradient-ghost" as-child>
                <Link :href="pedidos.show({ current_team: currentTeam.slug, pedido: order.id }).url">
                    Volver al Pedido
                </Link>
            </Button>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="glass-card rounded-2xl p-6 animate-scale-in">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-8 w-1 bg-gradient-to-b from-green-400 to-cyan-500 rounded-full"></div>
                        <h3 class="text-lg font-semibold text-white">Historial de Pagos</h3>
                    </div>

                    <div v-if="order.payments.length === 0" class="glass-card rounded-2xl p-8 text-center">
                        <p class="text-white/50">No hay pagos registrados.</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="table-glow w-full">
                        <thead>
                            <tr class="border-b border-white/10 text-sm text-white/50">
                                <th class="pb-3 text-left font-semibold">Fecha</th>
                                <th class="pb-3 text-left font-semibold">Monto</th>
                                <th class="pb-3 text-left font-semibold">Método</th>
                                <th class="pb-3 text-left font-semibold">Referencia</th>
                                <th class="pb-3 text-left font-semibold">Estado</th>
                                <th class="pb-3 text-right font-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="payment in order.payments" :key="payment.id" class="border-b border-white/5 text-sm text-white/70 hover:bg-white/5 transition-colors">
                                <td class="py-3">{{ new Date(payment.created_at).toLocaleDateString() }}</td>
                                <td class="py-3 font-medium text-white">S/ {{ payment.amount }}</td>
                                <td class="py-3 text-white/60">{{ paymentMethods[payment.payment_method] || payment.payment_method }}</td>
                                <td class="py-3 text-white/40">{{ payment.reference || '-' }}</td>
                                <td class="py-3">
                                    <Badge :class="{
                                        'badge-orange': payment.status === 'pendiente',
                                        'badge-green': payment.status === 'confirmado',
                                        'badge-red': payment.status === 'rechazado',
                                    }">
                                        {{ payment.status }}
                                    </Badge>
                                </td>
                                <td class="py-3 text-right">
                                    <div class="flex justify-end gap-1">
                                        <Button
                                            v-if="currentTeam && payment.status === 'pendiente'"
                                            variant="ghost"
                                            size="sm"
                                            class="btn-gradient-ghost glow-green"
                                            @click="router.post(pagos.confirm.url({ current_team: currentTeam.slug, order: order.id, payment: payment.id }))"
                                        >
                                            <CheckCircle class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            v-if="currentTeam && payment.status === 'pendiente'"
                                            variant="ghost"
                                            size="sm"
                                            class="btn-gradient-ghost text-red-400 hover:text-red-300"
                                            @click="router.post(pagos.reject.url({ current_team: currentTeam.slug, order: order.id, payment: payment.id }))"
                                        >
                                            <XCircle class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            v-if="currentTeam"
                                            variant="ghost"
                                            size="sm"
                                            class="btn-gradient-ghost text-red-400 hover:text-red-300"
                                            @click="router.delete(pagos.destroy.url({ current_team: currentTeam.slug, order: order.id, payment: payment.id }))"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>

                    <div class="mt-6 space-y-3 border-t border-white/10 pt-6">
                        <div class="flex justify-between text-sm text-white/70">
                            <span>Total del pedido:</span>
                            <span class="font-medium text-white">S/ {{ order.total }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-white/70">
                            <span>Total pagado:</span>
                            <span class="font-medium text-green-400">S/ {{ totalPaid.toFixed(2) }}</span>
                        </div>
                        <div class="divider-gradient"></div>
                        <div class="flex justify-between text-base font-bold">
                            <span class="text-white/80">Saldo pendiente:</span>
                            <span :class="balance > 0 ? 'text-orange-400' : 'text-green-400'">
                                S/ {{ balance.toFixed(2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="glass-card rounded-2xl p-6 animate-slide-up">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-8 w-1 bg-gradient-to-b from-purple-400 to-pink-500 rounded-full"></div>
                        <h3 class="text-lg font-semibold text-white">Registrar Pago</h3>
                    </div>

                    <Form
                        v-if="currentTeam"
                        v-bind="payFormBind"
                        class="space-y-4"
                        #default="{ errors, processing }"
                    >
                        <div class="space-y-2">
                            <Label for="amount" class="text-white/80">Monto * (S/)</Label>
                            <Input id="amount" name="amount" type="number" step="0.01" min="0.01" required :max="balance" class="glass" />
                            <p v-if="errors.amount" class="text-sm text-red-400">{{ errors.amount }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="payment_method" class="text-white/80">Método de Pago</Label>
                            <Select name="payment_method" default-value="efectivo" required>
                                <SelectTrigger class="glass"><SelectValue placeholder="Seleccionar" /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="efectivo">Efectivo</SelectItem>
                                    <SelectItem value="transferencia">Transferencia</SelectItem>
                                    <SelectItem value="yape">Yape</SelectItem>
                                    <SelectItem value="plin">Plin</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="space-y-2">
                            <Label for="reference" class="text-white/80">Referencia</Label>
                            <Input id="reference" name="reference" type="text" placeholder="N° de operación" class="glass" />
                        </div>

                        <Button type="submit" :disabled="processing" class="btn-gradient-purple w-full btn-shimmer">Registrar Pago</Button>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>
