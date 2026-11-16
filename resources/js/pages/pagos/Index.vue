<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { CheckCircle, XCircle, Trash2, Loader2 } from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { formatMoney, formatDate } from '@/lib/money';
import pagosAcciones from '@/routes/pagos';
import pedidos from '@/routes/pedidos';
import pagos from '@/routes/pedidos/pagos';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage<{
    order: {
        id: number;
        code: string;
        client: { id: number; name: string } | null;
        total: number;
        total_pagado: number;
        balance: number;
        payment_state: string;
        payments: Array<{
            id: number;
            amount: number;
            payment_method: string;
            tipo_pago: string | null;
            status: string;
            reference: string | null;
            confirmed_by_name: string | null;
            confirmed_at: string | null;
            created_at: string;
        }>;
    };
    balance: number;
}>();

const order = computed(() => page.props.order);
const balance = computed(() => page.props.balance);

const paymentMethods: Record<string, string> = {
    efectivo: 'Efectivo',
    qr: 'QR',
    transferencia: 'Transferencia',
    tarjeta: 'Tarjeta',
    otro: 'Otro',
};
const tipoPagoOptions: Record<string, string> = {
    adelanto: 'Adelanto',
    pago_parcial: 'Pago parcial',
    pago_total: 'Pago total',
};
const statusBadge: Record<string, string> = {
    pendiente: 'badge-orange',
    confirmado: 'badge-green',
    rechazado: 'badge-red',
};

const form = useForm({
    amount: '',
    payment_method: 'efectivo',
    tipo_pago: 'pago_parcial',
    reference: '',
});

const totalPaid = computed(() => order.value.total_pagado ?? 0);

function submit() {
    form.post(pagos.store.url({ pedido: order.value.id }), {
        preserveScroll: true,
    });
}

function confirmPayment(payment: { id: number }) {
    router.post(
        pagosAcciones.confirm.url({
            order: order.value.id,
            payment: payment.id,
        }),
    );
}

function rejectPayment(payment: { id: number }) {
    router.post(
        pagosAcciones.reject.url({
            order: order.value.id,
            payment: payment.id,
        }),
    );
}

function destroyPayment(payment: { id: number }) {
    if (window.confirm('¿Eliminar este pago?')) {
        router.delete(
            pagos.destroy.url({
                pedido: order.value.id,
                pago: payment.id,
            }),
        );
    }
}
</script>

<template>
    <Head :title="'Pagos - ' + order.code" />

    <div class="animate-fade-in space-y-6 p-4 md:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <Heading
                :title="'Pagos - ' + order.code"
                :description="
                    'Cliente: ' + (order.client?.name || 'Sin cliente')
                "
            />
            <Button
                variant="ghost"
                size="sm"
                class="btn-gradient-ghost"
                as-child
            >
                <Link :href="pedidos.show({ pedido: order.id }).url"
                    >Volver al Pedido</Link
                >
            </Button>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="glass-card rounded-2xl p-6">
                    <h3 class="mb-6 text-lg font-bold text-ink">
                        Historial de Pagos
                    </h3>

                    <p
                        v-if="order.payments.length === 0"
                        class="py-6 text-center text-ink/60"
                    >
                        No hay pagos registrados.
                    </p>

                    <template v-else>
                        <div class="hidden overflow-x-auto md:block">
                            <table class="table-glow w-full">
                                <thead>
                                    <tr
                                        class="border-b border-ink/10 text-sm text-ink/60"
                                    >
                                        <th class="pb-3 text-left">Fecha</th>
                                        <th class="pb-3 text-left">Monto</th>
                                        <th class="pb-3 text-left">Método</th>
                                        <th class="pb-3 text-left">Tipo</th>
                                        <th class="pb-3 text-left">
                                            Referencia
                                        </th>
                                        <th class="pb-3 text-left">Estado</th>
                                        <th class="pb-3 text-right">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="payment in order.payments"
                                        :key="payment.id"
                                        class="border-b border-ink/5 text-sm text-ink/75 transition-colors hover:bg-pink-50/70"
                                    >
                                        <td class="py-3">
                                            {{ formatDate(payment.created_at) }}
                                        </td>
                                        <td class="py-3 font-semibold text-ink">
                                            {{ formatMoney(payment.amount) }}
                                        </td>
                                        <td class="py-3 text-ink/70">
                                            {{
                                                paymentMethods[
                                                    payment.payment_method
                                                ] || payment.payment_method
                                            }}
                                        </td>
                                        <td class="py-3 text-ink/70">
                                            {{
                                                tipoPagoOptions[
                                                    payment.tipo_pago || ''
                                                ] ||
                                                payment.tipo_pago ||
                                                '—'
                                            }}
                                        </td>
                                        <td class="py-3 text-ink/50">
                                            {{ payment.reference || '-' }}
                                        </td>
                                        <td class="py-3">
                                            <span
                                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                                :class="
                                                    statusBadge[
                                                        payment.status
                                                    ] || 'badge-gray'
                                                "
                                            >
                                                {{ payment.status }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-right">
                                            <div class="flex justify-end gap-1">
                                                <Button
                                                    v-if="
                                                        payment.status ===
                                                        'pendiente'
                                                    "
                                                    variant="ghost"
                                                    size="sm"
                                                    class="btn-gradient-ghost glow-green"
                                                    @click="
                                                        confirmPayment(payment)
                                                    "
                                                >
                                                    <CheckCircle
                                                        class="h-4 w-4"
                                                    />
                                                </Button>
                                                <Button
                                                    v-if="
                                                        payment.status ===
                                                        'pendiente'
                                                    "
                                                    variant="ghost"
                                                    size="sm"
                                                    class="btn-gradient-ghost text-red-600 hover:text-red-700"
                                                    @click="
                                                        rejectPayment(payment)
                                                    "
                                                >
                                                    <XCircle class="h-4 w-4" />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    class="btn-gradient-ghost text-red-600 hover:text-red-700"
                                                    @click="
                                                        destroyPayment(payment)
                                                    "
                                                >
                                                    <Trash2 class="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="space-y-3 md:hidden">
                            <div
                                v-for="payment in order.payments"
                                :key="payment.id"
                                class="glass rounded-xl p-4"
                            >
                                <div class="flex items-center justify-between">
                                    <p class="font-bold text-ink">
                                        {{ formatMoney(payment.amount) }}
                                    </p>
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                        :class="
                                            statusBadge[payment.status] ||
                                            'badge-gray'
                                        "
                                        >{{ payment.status }}</span
                                    >
                                </div>
                                <p class="text-sm text-ink/60">
                                    {{
                                        paymentMethods[
                                            payment.payment_method
                                        ] || payment.payment_method
                                    }}
                                    ·
                                    {{
                                        tipoPagoOptions[
                                            payment.tipo_pago || ''
                                        ] ||
                                        payment.tipo_pago ||
                                        '—'
                                    }}
                                </p>
                                <p class="text-xs text-ink/50">
                                    {{ formatDate(payment.created_at) }} · Ref:
                                    {{ payment.reference || '-' }}
                                </p>
                                <div class="mt-2 flex gap-2">
                                    <Button
                                        v-if="payment.status === 'pendiente'"
                                        size="sm"
                                        class="btn-gradient-ghost glow-green"
                                        @click="confirmPayment(payment)"
                                    >
                                        <CheckCircle class="h-4 w-4" />
                                        Confirmar
                                    </Button>
                                    <Button
                                        v-if="payment.status === 'pendiente'"
                                        size="sm"
                                        class="btn-gradient-ghost text-red-600"
                                        @click="rejectPayment(payment)"
                                    >
                                        <XCircle class="h-4 w-4" /> Rechazar
                                    </Button>
                                    <Button
                                        size="sm"
                                        class="btn-gradient-ghost text-red-600"
                                        @click="destroyPayment(payment)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 space-y-3 border-t border-ink/10 pt-6">
                            <div
                                class="flex justify-between text-sm text-ink/75"
                            >
                                <span>Total del pedido:</span>
                                <span class="font-semibold text-ink">{{
                                    formatMoney(order.total)
                                }}</span>
                            </div>
                            <div
                                class="flex justify-between text-sm text-ink/75"
                            >
                                <span>Total pagado:</span>
                                <span class="font-semibold text-green-600">{{
                                    formatMoney(totalPaid)
                                }}</span>
                            </div>
                            <div
                                class="flex justify-between text-base font-bold"
                            >
                                <span class="text-ink/80"
                                    >Saldo pendiente:</span
                                >
                                <span
                                    :class="
                                        balance > 0
                                            ? 'text-orange-600'
                                            : 'text-green-600'
                                    "
                                    >{{ formatMoney(balance) }}</span
                                >
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div>
                <div class="glass-card rounded-2xl p-6">
                    <h3 class="mb-6 text-lg font-bold text-ink">
                        Registrar Pago
                    </h3>

                    <form class="space-y-4" @submit.prevent="submit">
                        <div class="space-y-2">
                            <Label for="amount">Monto * (Bs)</Label>
                            <Input
                                id="amount"
                                v-model="form.amount"
                                type="number"
                                step="0.01"
                                min="0.01"
                                class="glass"
                            />
                            <p
                                v-if="form.errors.amount"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.amount }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="payment_method">Método de Pago</Label>
                            <Select v-model="form.payment_method">
                                <SelectTrigger class="glass"
                                    ><SelectValue
                                /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="(label, key) in paymentMethods"
                                        :key="key"
                                        :value="key"
                                        >{{ label }}</SelectItem
                                    >
                                </SelectContent>
                            </Select>
                            <p
                                v-if="form.errors.payment_method"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.payment_method }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="tipo_pago">Tipo de Pago</Label>
                            <Select v-model="form.tipo_pago">
                                <SelectTrigger class="glass"
                                    ><SelectValue
                                /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="(label, key) in tipoPagoOptions"
                                        :key="key"
                                        :value="key"
                                        >{{ label }}</SelectItem
                                    >
                                </SelectContent>
                            </Select>
                            <p
                                v-if="form.errors.tipo_pago"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.tipo_pago }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="reference">Referencia</Label>
                            <Input
                                id="reference"
                                v-model="form.reference"
                                type="text"
                                placeholder="N° de operación"
                                class="glass"
                            />
                            <p
                                v-if="form.errors.reference"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.reference }}
                            </p>
                        </div>

                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="btn-gradient-purple btn-shimmer w-full"
                        >
                            <Loader2
                                v-if="form.processing"
                                class="mr-2 h-4 w-4 animate-spin"
                            />
                            Registrar Pago
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
