<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Pencil,
    Plus,
    FileText,
    Package,
    Wallet,
    Trash2,
    Mail,
    Phone,
    MapPin,
} from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { usePermissions } from '@/composables/usePermissions';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { formatMoney, formatDate } from '@/lib/money';
import clientes from '@/routes/clientes';
import cotizacionesRoutes from '@/routes/cotizaciones';
import pedidos from '@/routes/pedidos';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage<{
    client: {
        id: number;
        code: string | null;
        name: string;
        tipo_cliente: string | null;
        ci_nit: string | null;
        phone: string | null;
        whatsapp: string | null;
        email: string | null;
        direccion: string | null;
        company: string | null;
        notes: string | null;
        activo: boolean;
        orders_count: number;
        cotizaciones_count: number;
        created_at: string | null;
    };
    orders: Array<{
        id: number;
        code: string;
        status: string;
        status_label: string;
        total: number;
        balance: number;
        payment_state: string;
        delivery_date: string | null;
        services_names: string;
        created_at: string;
    }>;
    cotizaciones: Array<{
        id: number;
        numero: string;
        estado: string;
        estado_label: string;
        total: number;
        fecha: string | null;
        fecha_validez: string | null;
        services_names: string;
    }>;
    pagos: Array<{
        id: number;
        order_code: string | null;
        amount: number;
        payment_method: string;
        tipo_pago: string | null;
        status: string;
        confirmed_at: string | null;
        created_at: string;
    }>;
    summary: {
        total_comprado: number;
        total_pagado: number;
        saldo_pendiente: number;
    };
}>();
const { can } = usePermissions();

const client = computed(() => page.props.client);
const orders = computed(() => page.props.orders ?? []);
const cotizaciones = computed(() => page.props.cotizaciones ?? []);
const pagos = computed(() => page.props.pagos ?? []);
const summary = computed(
    () =>
        page.props.summary ?? {
            total_comprado: 0,
            total_pagado: 0,
            saldo_pendiente: 0,
        },
);

const statusBadge: Record<string, string> = {
    nuevo: 'badge-cyan',
    confirmado: 'badge-blue',
    en_proceso: 'badge-purple',
    en_revision: 'badge-yellow',
    terminado: 'badge-cyan',
    entregado: 'badge-green',
    cancelado: 'badge-red',
};
const payStateBadge: Record<string, string> = {
    pendiente: 'badge-orange',
    parcial: 'badge-yellow',
    pagado: 'badge-green',
};

function remove() {
    if (window.confirm(`¿Eliminar al cliente "${client.value.name}"?`)) {
        router.delete(clientes.destroy.url({ cliente: client.value.id }));
    }
}

const paymentMethods: Record<string, string> = {
    efectivo: 'Efectivo',
    qr: 'QR',
    transferencia: 'Transferencia',
    tarjeta: 'Tarjeta',
    otro: 'Otro',
};

const contactRows = computed(() => [
    { icon: Phone, label: 'Teléfono', value: client.value.phone || '—' },
    {
        icon: Phone,
        label: 'WhatsApp',
        value: client.value.whatsapp ? `+${client.value.whatsapp}` : '—',
    },
    { icon: Mail, label: 'Correo', value: client.value.email || '—' },
    { icon: MapPin, label: 'Dirección', value: client.value.direccion || '—' },
]);
</script>

<template>
    <Head :title="'Cliente ' + client.name" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-500/20 to-pink-500/20 text-lg font-bold text-purple-600"
                >
                    {{ client.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                    <Heading
                        :title="client.name"
                        :description="client.code || 'Cliente'"
                    />
                    <span
                        v-if="!client.activo"
                        class="badge-orange mt-1 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs"
                        >Inactivo</span
                    >
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <Button
                    v-if="can('pedidos.crear')"
                    size="sm"
                    class="btn-gradient-blue btn-shimmer"
                    as-child
                >
                    <Link :href="pedidos.create().url">
                        <Plus class="mr-1 h-4 w-4" /> Nuevo Pedido
                    </Link>
                </Button>
                <Button
                    v-if="can('cotizaciones.gestionar')"
                    size="sm"
                    class="btn-gradient-purple btn-shimmer"
                    as-child
                >
                    <Link :href="cotizacionesRoutes.create().url">
                        <FileText class="mr-1 h-4 w-4" /> Nueva Cotización
                    </Link>
                </Button>
                <Button
                    v-if="can('clientes.gestionar')"
                    size="sm"
                    class="btn-gradient-ghost"
                    as-child
                >
                    <Link :href="clientes.edit({ cliente: client.id }).url">
                        <Pencil class="mr-1 h-4 w-4" /> Editar
                    </Link>
                </Button>
                <Button size="sm" class="btn-gradient-ghost" as-child>
                    <Link :href="clientes.index().url">Volver</Link>
                </Button>
            </div>
        </div>

        <!-- Summary -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div
                class="kpi-green kpi-card glow-green rounded-2xl p-5 shadow-lg"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Total comprado
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ formatMoney(summary.total_comprado) }}
                </p>
            </div>
            <div class="kpi-blue kpi-card glow-blue rounded-2xl p-5 shadow-lg">
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Total pagado
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ formatMoney(summary.total_pagado) }}
                </p>
            </div>
            <div
                class="kpi-orange kpi-card glow-orange rounded-2xl p-5 shadow-lg"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Saldo pendiente
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ formatMoney(summary.saldo_pendiente) }}
                </p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Info -->
            <div class="space-y-6">
                <div class="glass-card rounded-2xl p-6">
                    <h3 class="mb-4 text-lg font-bold text-ink">
                        Información personal
                    </h3>
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-xs font-medium text-ink/50">
                                    Tipo
                                </p>
                                <p class="font-semibold text-ink capitalize">
                                    {{ client.tipo_cliente || '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-ink/50">
                                    CI / NIT
                                </p>
                                <p class="font-semibold text-ink">
                                    {{ client.ci_nit || '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-ink/50">
                                    Empresa
                                </p>
                                <p class="font-semibold text-ink">
                                    {{ client.company || '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-ink/50">
                                    Registrado
                                </p>
                                <p class="font-semibold text-ink">
                                    {{ formatDate(client.created_at) }}
                                </p>
                            </div>
                        </div>
                        <div
                            v-for="row in contactRows"
                            :key="row.label"
                            class="flex items-center gap-3 rounded-xl border border-ink/5 bg-white p-3"
                        >
                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-ink/5 text-ink/60"
                            >
                                <component :is="row.icon" class="h-4 w-4" />
                            </div>
                            <div>
                                <p class="text-xs text-ink/50">
                                    {{ row.label }}
                                </p>
                                <p class="text-sm font-semibold text-ink">
                                    {{ row.value }}
                                </p>
                            </div>
                        </div>
                        <div
                            v-if="client.notes"
                            class="rounded-xl bg-ink/5 p-3"
                        >
                            <p class="text-xs font-medium text-ink/50">Notas</p>
                            <p class="text-sm text-ink/70">
                                {{ client.notes }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 border-t border-ink/10 pt-4">
                        <Button
                            v-if="can('clientes.gestionar')"
                            size="sm"
                            variant="ghost"
                            class="btn-gradient-ghost text-red-600 hover:text-red-700"
                            @click="remove"
                        >
                            <Trash2 class="mr-1.5 h-4 w-4" /> Eliminar cliente
                        </Button>
                    </div>
                </div>

                <!-- Pagos -->
                <div class="glass-card rounded-2xl p-6">
                    <h3
                        class="mb-4 flex items-center gap-2 text-lg font-bold text-ink"
                    >
                        <Wallet class="h-5 w-5 text-green-600" /> Pagos
                    </h3>
                    <div
                        v-if="pagos.length === 0"
                        class="py-6 text-center text-sm text-ink/50"
                    >
                        Sin pagos registrados.
                    </div>
                    <div v-else class="space-y-2.5">
                        <div
                            v-for="p in pagos"
                            :key="p.id"
                            class="flex items-center justify-between rounded-xl border border-ink/5 bg-white p-3 text-sm"
                        >
                            <div>
                                <p class="font-semibold text-ink">
                                    {{ formatMoney(p.amount) }}
                                </p>
                                <p class="text-xs text-ink/50">
                                    {{ p.order_code || '' }} ·
                                    {{
                                        paymentMethods[p.payment_method] ||
                                        p.payment_method
                                    }}
                                </p>
                            </div>
                            <span
                                :class="
                                    p.status === 'confirmado'
                                        ? 'badge-green'
                                        : p.status === 'rechazado'
                                          ? 'badge-red'
                                          : 'badge-orange'
                                "
                                class="rounded-full px-2.5 py-0.5 text-xs"
                            >
                                {{ p.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6 lg:col-span-2">
                <!-- Pedidos -->
                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3
                            class="flex items-center gap-2 text-lg font-bold text-ink"
                        >
                            <Package class="h-5 w-5 text-pix" /> Pedidos
                            <span
                                class="rounded-full bg-ink/5 px-2.5 py-0.5 text-xs font-bold text-ink/60"
                                >{{ orders.length }}</span
                            >
                        </h3>
                    </div>
                    <div
                        v-if="orders.length === 0"
                        class="py-6 text-center text-sm text-ink/50"
                    >
                        Este cliente aún no tiene pedidos.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="table-glow w-full">
                            <thead>
                                <tr
                                    class="border-b border-ink/10 text-sm text-ink/60"
                                >
                                    <th class="pb-2 text-left">Código</th>
                                    <th class="pb-2 text-left">Servicios</th>
                                    <th class="pb-2 text-right">Total</th>
                                    <th class="pb-2 text-right">Saldo</th>
                                    <th class="pb-2 text-left">Pago</th>
                                    <th class="pb-2 text-left">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="o in orders"
                                    :key="o.id"
                                    class="border-b border-ink/5 text-sm text-ink/80 transition-colors hover:bg-pink-50/70"
                                >
                                    <td class="py-2.5">
                                        <Link
                                            :href="
                                                pedidos.show({ pedido: o.id })
                                                    .url
                                            "
                                            class="font-bold text-ink hover:text-pix"
                                            >{{ o.code }}</Link
                                        >
                                        <p class="text-xs text-ink/50">
                                            {{ formatDate(o.created_at) }}
                                        </p>
                                    </td>
                                    <td
                                        class="max-w-[200px] truncate py-2.5 text-ink/60"
                                    >
                                        {{ o.services_names || '—' }}
                                    </td>
                                    <td
                                        class="py-2.5 text-right font-semibold text-ink"
                                    >
                                        {{ formatMoney(o.total) }}
                                    </td>
                                    <td
                                        class="py-2.5 text-right font-semibold"
                                        :class="
                                            o.balance > 0
                                                ? 'text-red-600'
                                                : 'text-green-600'
                                        "
                                    >
                                        {{ formatMoney(o.balance) }}
                                    </td>
                                    <td class="py-2.5">
                                        <span
                                            :class="
                                                payStateBadge[
                                                    o.payment_state
                                                ] || 'badge-gray'
                                            "
                                            class="rounded-full px-2.5 py-0.5 text-xs"
                                            >{{ o.payment_state }}</span
                                        >
                                    </td>
                                    <td class="py-2.5">
                                        <span
                                            :class="
                                                statusBadge[o.status] ||
                                                'badge-gray'
                                            "
                                            class="rounded-full px-2.5 py-0.5 text-xs"
                                            >{{ o.status_label }}</span
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Cotizaciones -->
                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3
                            class="flex items-center gap-2 text-lg font-bold text-ink"
                        >
                            <FileText class="h-5 w-5 text-purple-600" />
                            Cotizaciones
                            <span
                                class="rounded-full bg-ink/5 px-2.5 py-0.5 text-xs font-bold text-ink/60"
                                >{{ cotizaciones.length }}</span
                            >
                        </h3>
                    </div>
                    <div
                        v-if="cotizaciones.length === 0"
                        class="py-6 text-center text-sm text-ink/50"
                    >
                        Este cliente aún no tiene cotizaciones.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="table-glow w-full">
                            <thead>
                                <tr
                                    class="border-b border-ink/10 text-sm text-ink/60"
                                >
                                    <th class="pb-2 text-left">Número</th>
                                    <th class="pb-2 text-left">Servicios</th>
                                    <th class="pb-2 text-left">Fecha</th>
                                    <th class="pb-2 text-right">Total</th>
                                    <th class="pb-2 text-left">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="c in cotizaciones"
                                    :key="c.id"
                                    class="border-b border-ink/5 text-sm text-ink/80 transition-colors hover:bg-pink-50/70"
                                >
                                    <td class="py-2.5">
                                        <Link
                                            :href="
                                                cotizacionesRoutes.show({
                                                    cotizacion: c.id,
                                                }).url
                                            "
                                            class="font-bold text-ink hover:text-pix"
                                            >{{ c.numero }}</Link
                                        >
                                    </td>
                                    <td
                                        class="max-w-[200px] truncate py-2.5 text-ink/60"
                                    >
                                        {{ c.services_names || '—' }}
                                    </td>
                                    <td class="py-2.5 text-ink/60">
                                        {{ formatDate(c.fecha) }}
                                    </td>
                                    <td
                                        class="py-2.5 text-right font-semibold text-ink"
                                    >
                                        {{ formatMoney(c.total) }}
                                    </td>
                                    <td class="py-2.5">
                                        <span
                                            :class="
                                                c.estado === 'aceptada'
                                                    ? 'badge-green'
                                                    : c.estado === 'rechazada'
                                                      ? 'badge-red'
                                                      : c.estado === 'vencida'
                                                        ? 'badge-gray'
                                                        : 'badge-yellow'
                                            "
                                            class="rounded-full px-2.5 py-0.5 text-xs"
                                            >{{ c.estado_label }}</span
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
