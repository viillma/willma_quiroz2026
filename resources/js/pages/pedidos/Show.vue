<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Pencil,
    Palette,
    DollarSign,
    Trash2,
    Check,
    ChevronRight,
    History,
} from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { usePermissions } from '@/composables/usePermissions';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { formatMoney, formatDate } from '@/lib/money';
import clientes from '@/routes/clientes';
import cotizaciones from '@/routes/cotizaciones';
import disenos from '@/routes/disenos';
import pedidos from '@/routes/pedidos';
import pagosRoutes from '@/routes/pedidos/pagos';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage<{
    order: {
        id: number;
        code: string;
        client: {
            id: number;
            name: string;
            phone: string;
            email: string;
            company: string;
        } | null;
        designer: { id: number; name: string } | null;
        status: string;
        status_label: string;
        notes: string | null;
        total: number;
        descuento: number;
        adelanto: number;
        forma_pago: string;
        responsable: string | null;
        subtotal: number;
        total_pagado: number;
        balance: number;
        payment_state: string;
        is_paid: boolean;
        is_overdue: boolean;
        delivery_date: string | null;
        created_at: string;
        servicios: Array<{
            id: number;
            nombre: string;
            codigo: string | null;
            quantity: number;
            unit_price: number;
            subtotal: number;
        }>;
        designFiles: Array<{
            id: number;
            version: number;
            file_type: string;
            comments: string | null;
            is_approved: boolean;
            uploaded_by_name: string | null;
            created_at: string;
        }>;
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
        cotizacion: { id: number; numero: string; estado: string } | null;
        histories: Array<{
            id: number;
            from_status: string | null;
            from_status_label: string | null;
            to_status: string;
            to_status_label: string;
            note: string | null;
            user_name: string;
            created_at: string;
        }>;
    };
    statuses: Record<string, string>;
}>();

const { can } = usePermissions();

const order = computed(() => page.props.order);
const histories = computed(() => page.props.order.histories ?? []);
const statusFlow = [
    'nuevo',
    'confirmado',
    'en_proceso',
    'en_revision',
    'terminado',
    'entregado',
];
const currentStep = computed(() => statusFlow.indexOf(order.value.status));

const statusBadge: Record<string, string> = {
    nuevo: 'badge-cyan',
    confirmado: 'badge-blue',
    en_proceso: 'badge-purple',
    en_revision: 'badge-yellow',
    terminado: 'badge-cyan',
    entregado: 'badge-green',
    cancelado: 'badge-red',
};

const nextStatus = computed(() => statusFlow[currentStep.value + 1] ?? null);

const paymentMethods: Record<string, string> = {
    efectivo: 'Efectivo',
    qr: 'QR',
    transferencia: 'Transferencia',
    tarjeta: 'Tarjeta',
    otro: 'Otro',
};

function advanceStatus() {
    if (!nextStatus.value || !can('pedidos.gestionar')) {
        return;
    }

    if (!window.confirm(`¿Avanzar el pedido a "${nextStatus.value}"?`)) {
        return;
    }

    const o = order.value;
    const payload = {
        client_id: o.client?.id ?? '',
        designer_id: o.designer?.id ?? undefined,
        status: nextStatus.value,
        delivery_date: o.delivery_date ?? undefined,
        notes: o.notes ?? '',
        descuento: o.descuento,
        adelanto: o.adelanto,
        forma_pago: o.forma_pago,
        responsable: o.responsable ?? '',
        servicios: o.servicios.map((s) => ({
            servicio_id: s.id,
            quantity: s.quantity,
            unit_price: s.unit_price,
        })),
    };
    router.post(
        pedidos.update.url({ pedido: o.id }),
        {
            _method: 'PUT',
            ...payload,
        },
        { preserveScroll: true },
    );
}

function remove() {
    if (window.confirm(`¿Eliminar el pedido "${order.value.code}"?`)) {
        router.delete(pedidos.destroy.url({ pedido: order.value.id }));
    }
}
</script>

<template>
    <Head :title="'Pedido ' + order.code" />

    <div class="animate-fade-in space-y-6 p-4 md:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <Heading
                    :title="'Pedido ' + order.code"
                    :description="
                        'Registrado el ' + formatDate(order.created_at)
                    "
                />
                <div class="mt-1 flex items-center gap-2">
                    <span
                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                        :class="statusBadge[order.status] || 'badge-gray'"
                    >
                        {{ order.status_label }}
                    </span>
                    <span
                        v-if="order.is_overdue"
                        class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-600"
                        >Vencido</span
                    >
                </div>
            </div>
            <div class="flex gap-2">
                <Button
                    v-if="can('pedidos.gestionar')"
                    size="sm"
                    class="btn-gradient-ghost"
                    as-child
                >
                    <Link :href="pedidos.edit({ pedido: order.id }).url">
                        <Pencil class="mr-1 h-4 w-4" /> Editar
                    </Link>
                </Button>
                <Button size="sm" class="btn-gradient-ghost" as-child>
                    <Link :href="pedidos.index().url">Volver</Link>
                </Button>
            </div>
        </div>

        <!-- Status stepper -->
        <div
            v-if="order.status !== 'cancelado'"
            class="glass-card rounded-2xl p-5"
        >
            <div
                class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between"
            >
                <div class="flex flex-1 flex-wrap items-center gap-1">
                    <template v-for="(step, i) in statusFlow" :key="step">
                        <div class="flex items-center gap-1">
                            <div
                                class="flex h-8 items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold"
                                :class="
                                    i <= currentStep
                                        ? 'bg-gradient-to-r from-pix to-pix-2 text-white shadow-md shadow-pix/25'
                                        : 'bg-ink/5 text-ink/50'
                                "
                            >
                                <Check
                                    v-if="i < currentStep"
                                    class="h-3.5 w-3.5"
                                />
                                <span
                                    v-else-if="i === currentStep"
                                    class="h-1.5 w-1.5 rounded-full bg-white"
                                ></span>
                                <span>{{
                                    page.props.statuses?.[step] ?? step
                                }}</span>
                            </div>
                            <ChevronRight
                                v-if="i < statusFlow.length - 1"
                                class="h-3.5 w-3.5 text-ink/30"
                            />
                        </div>
                    </template>
                </div>
                <Button
                    v-if="nextStatus && can('pedidos.gestionar')"
                    size="sm"
                    class="btn-gradient-blue btn-shimmer"
                    @click="advanceStatus"
                >
                    Avanzar a
                    {{ page.props.statuses?.[nextStatus] || nextStatus }}
                </Button>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="glass-card rounded-2xl p-6">
                    <h3 class="mb-4 text-lg font-bold text-ink">
                        Información del Pedido
                    </h3>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <p class="text-sm text-ink/60">Cliente</p>
                            <p class="font-semibold text-ink">
                                {{ order.client?.name || '-' }}
                            </p>
                            <p
                                v-if="order.client?.company"
                                class="text-sm text-ink/60"
                            >
                                {{ order.client.company }}
                            </p>
                            <Link
                                v-if="order.client"
                                :href="
                                    clientes.show({ cliente: order.client.id })
                                        .url
                                "
                                class="text-xs text-pix hover:underline"
                                >Ver cliente</Link
                            >
                        </div>
                        <div>
                            <p class="text-sm text-ink/60">Diseñador</p>
                            <p class="font-semibold text-ink">
                                {{ order.designer?.name || 'Sin asignar' }}
                            </p>
                            <p class="text-sm text-ink/60">
                                Responsable: {{ order.responsable || '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-ink/60">Fecha de Entrega</p>
                            <p class="font-semibold text-ink">
                                {{
                                    order.delivery_date
                                        ? formatDate(order.delivery_date)
                                        : 'No definida'
                                }}
                            </p>
                            <p class="text-sm text-ink/60">
                                Forma de pago:
                                {{
                                    paymentMethods[order.forma_pago] ||
                                    order.forma_pago
                                }}
                            </p>
                        </div>
                    </div>
                    <div
                        v-if="order.cotizacion"
                        class="mt-4 rounded-xl bg-purple-50 p-3 text-sm"
                    >
                        <p class="text-ink/70">
                            Creado desde la cotización
                            <Link
                                :href="
                                    cotizaciones.show({
                                        cotizacion: order.cotizacion.id,
                                    }).url
                                "
                                class="font-bold text-purple-600 hover:underline"
                                >{{ order.cotizacion.numero }}</Link
                            >
                        </p>
                    </div>
                    <div v-if="order.notes" class="mt-4">
                        <p class="text-sm text-ink/60">Notas</p>
                        <p class="text-sm text-ink/80">{{ order.notes }}</p>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-ink">Servicios</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table-glow w-full">
                            <thead>
                                <tr
                                    class="border-b border-ink/10 text-sm text-ink/60"
                                >
                                    <th class="pb-2 text-left">Servicio</th>
                                    <th class="pb-2 text-center">Cant.</th>
                                    <th class="pb-2 text-right">
                                        Precio Unit.
                                    </th>
                                    <th class="pb-2 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="servicio in order.servicios"
                                    :key="servicio.id"
                                    class="border-b border-ink/5 text-sm text-ink/80 transition-colors hover:bg-pink-50/70"
                                >
                                    <td class="py-2">
                                        <p class="font-semibold text-ink">
                                            {{ servicio.nombre }}
                                        </p>
                                        <p
                                            v-if="servicio.codigo"
                                            class="text-xs text-ink/50"
                                        >
                                            {{ servicio.codigo }}
                                        </p>
                                    </td>
                                    <td class="py-2 text-center">
                                        {{ servicio.quantity }}
                                    </td>
                                    <td class="py-2 text-right">
                                        {{ formatMoney(servicio.unit_price) }}
                                    </td>
                                    <td
                                        class="py-2 text-right font-semibold text-ink"
                                    >
                                        {{ formatMoney(servicio.subtotal) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="text-sm text-ink/70">
                                    <td colspan="3" class="pt-3 text-right">
                                        Subtotal:
                                    </td>
                                    <td
                                        class="pt-3 text-right font-semibold text-ink"
                                    >
                                        {{ formatMoney(order.subtotal) }}
                                    </td>
                                </tr>
                                <tr
                                    v-if="order.descuento > 0"
                                    class="text-sm text-ink/70"
                                >
                                    <td colspan="3" class="pt-1 text-right">
                                        Descuento:
                                    </td>
                                    <td
                                        class="pt-1 text-right font-semibold text-red-600"
                                    >
                                        -{{ formatMoney(order.descuento) }}
                                    </td>
                                </tr>
                                <tr class="text-lg font-bold text-ink">
                                    <td colspan="3" class="pt-2 text-right">
                                        Total:
                                    </td>
                                    <td class="pt-2 text-right">
                                        {{ formatMoney(order.total) }}
                                    </td>
                                </tr>
                                <tr
                                    v-if="order.adelanto > 0"
                                    class="text-sm text-ink/70"
                                >
                                    <td colspan="3" class="pt-1 text-right">
                                        Adelanto registrado:
                                    </td>
                                    <td
                                        class="pt-1 text-right font-semibold text-ink"
                                    >
                                        {{ formatMoney(order.adelanto) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-ink">Diseños</h3>
                        <Button size="sm" class="btn-gradient-ghost" as-child>
                            <Link
                                :href="disenos.index.url({ order: order.id })"
                            >
                                <Palette class="mr-1 h-4 w-4" /> Ver Diseños
                            </Link>
                        </Button>
                    </div>
                    <p
                        v-if="order.designFiles.length === 0"
                        class="text-sm text-ink/60"
                    >
                        Sin diseños subidos aún
                    </p>
                    <div v-else class="space-y-2">
                        <div
                            v-for="file in order.designFiles"
                            :key="file.id"
                            class="glass flex items-center justify-between rounded-lg p-2 text-sm text-ink/80"
                        >
                            <span
                                >V{{ file.version }} -
                                {{ file.file_type?.toUpperCase() }}</span
                            >
                            <span
                                v-if="file.is_approved"
                                class="badge-green inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                >Aprobado</span
                            >
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-ink">Pagos</h3>
                        <Button size="sm" class="btn-gradient-ghost" as-child>
                            <Link
                                :href="
                                    pagosRoutes.index.url({ pedido: order.id })
                                "
                            >
                                <DollarSign class="mr-1 h-4 w-4" /> Ver Pagos
                            </Link>
                        </Button>
                    </div>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-ink/60">Total:</span>
                            <span class="font-semibold text-ink">{{
                                formatMoney(order.total)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ink/60">Pagado:</span>
                            <span class="font-semibold text-green-600">{{
                                formatMoney(order.total_pagado)
                            }}</span>
                        </div>
                        <div
                            class="flex justify-between border-t border-ink/10 pt-1"
                        >
                            <span class="font-semibold text-ink">Saldo:</span>
                            <span
                                :class="
                                    order.balance > 0
                                        ? 'font-semibold text-red-600'
                                        : 'font-semibold text-green-600'
                                "
                            >
                                {{ formatMoney(order.balance) }}
                            </span>
                        </div>
                        <span
                            class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                            :class="
                                order.payment_state === 'pagado'
                                    ? 'badge-green'
                                    : order.payment_state === 'parcial'
                                      ? 'badge-yellow'
                                      : 'badge-orange'
                            "
                        >
                            {{ order.payment_state }}
                        </span>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-ink">Historial</h3>
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-blush/60 text-pix"
                        >
                            <History class="h-4 w-4" />
                        </div>
                    </div>
                    <p
                        v-if="histories.length === 0"
                        class="text-sm text-ink/60"
                    >
                        Sin movimientos registrados.
                    </p>
                    <ol
                        v-else
                        class="relative space-y-4 border-l border-ink/10 pl-5"
                    >
                        <li v-for="h in histories" :key="h.id" class="relative">
                            <span
                                class="absolute top-1 -left-[1.6rem] flex h-3 w-3 items-center justify-center rounded-full ring-4 ring-white"
                                :class="
                                    h.from_status === h.to_status
                                        ? 'bg-lilac'
                                        : 'bg-pix'
                                "
                            ></span>
                            <p class="text-sm font-semibold text-ink">
                                <template
                                    v-if="
                                        h.from_status &&
                                        h.from_status !== h.to_status
                                    "
                                >
                                    {{ h.from_status_label }}
                                    <span class="text-ink/40">→</span>
                                    {{ h.to_status_label }}
                                </template>
                                <template v-else>
                                    {{ h.to_status_label }}
                                </template>
                            </p>
                            <p v-if="h.note" class="text-xs text-ink/60">
                                {{ h.note }}
                            </p>
                            <p class="mt-0.5 text-[11px] text-ink/40">
                                {{ h.user_name }} ·
                                {{ formatDate(h.created_at) }}
                            </p>
                        </li>
                    </ol>
                </div>

                <div
                    v-if="can('pedidos.gestionar')"
                    class="glass-card rounded-2xl p-6"
                >
                    <Button
                        size="sm"
                        variant="ghost"
                        class="btn-gradient-ghost w-full text-red-600 hover:text-red-700"
                        @click="remove"
                    >
                        <Trash2 class="mr-1.5 h-4 w-4" /> Eliminar pedido
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
