<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Plus, Eye, Search, CalendarDays } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { usePermissions } from '@/composables/usePermissions';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { formatMoney, formatDate } from '@/lib/money';
import pedidos from '@/routes/pedidos';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage<{
    orders: Array<{
        id: number;
        code: string;
        client: { id: number; name: string } | null;
        designer: { id: number; name: string } | null;
        status: string;
        status_label: string;
        total: number;
        descuento: number;
        adelanto: number;
        balance: number;
        total_pagado: number;
        payment_state: string;
        is_paid: boolean;
        is_overdue: boolean;
        services_count: number;
        delivery_date: string | null;
        created_at: string;
    }>;
    filters: {
        search: string;
        estado: string;
        cliente: string;
        filtro: string;
        fecha: string;
    };
    clients: Array<{ id: number; name: string }>;
    statuses: Record<string, string>;
}>();
const { can } = usePermissions();

const orders_data = computed(() => page.props.orders ?? []);
const statuses = computed(() => page.props.statuses ?? {});
const clientOptions = computed(() => page.props.clients ?? []);

const search = ref(page.props.filters?.search ?? '');
const estado = ref(page.props.filters?.estado ?? '');
const cliente = ref(page.props.filters?.cliente ?? '');
const filtro = ref(page.props.filters?.filtro ?? '');
const fecha = ref(page.props.filters?.fecha ?? '');

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

let searchTimer: ReturnType<typeof setTimeout> | null = null;
function applyFilters(preserveSearch = false) {
    if (searchTimer) {
        clearTimeout(searchTimer);
    }

    searchTimer = setTimeout(
        () => {
            router.get(
                pedidos.index().url,
                {
                    search: search.value || undefined,
                    estado: estado.value || undefined,
                    cliente: cliente.value || undefined,
                    filtro: filtro.value || undefined,
                    fecha: fecha.value || undefined,
                },
                { preserveState: true, preserveScroll: true, replace: true },
            );
        },
        preserveSearch ? 350 : 0,
    );
}

watch(search, () => applyFilters(true));
watch(estado, () => applyFilters());
watch(cliente, () => applyFilters());
watch(filtro, () => applyFilters());
watch(fecha, () => applyFilters());
</script>

<template>
    <Head title="Pedidos" />

    <div class="animate-fade-in space-y-6 p-4 md:p-6 lg:p-8">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading
                title="Pedidos"
                description="Registro de pedidos de diseño"
            />
            <Button
                v-if="can('pedidos.crear')"
                as-child
                class="btn-gradient-blue"
            >
                <Link :href="pedidos.create().url">
                    <Plus class="mr-2 h-4 w-4" />
                    Nuevo Pedido
                </Link>
            </Button>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div
                class="kpi-blue kpi-card glow-blue animate-slide-up rounded-2xl p-5 shadow-lg"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Total Pedidos
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ orders_data.length }}
                </p>
            </div>
            <div
                class="kpi-orange kpi-card glow-orange animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.1s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Pendientes de pago
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{
                        orders_data.filter(
                            (o) => o.payment_state === 'pendiente',
                        ).length
                    }}
                </p>
            </div>
            <div
                class="kpi-purple kpi-card glow-purple animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.2s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    En proceso
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{
                        orders_data.filter((o) =>
                            [
                                'nuevo',
                                'confirmado',
                                'en_proceso',
                                'en_revision',
                            ].includes(o.status),
                        ).length
                    }}
                </p>
            </div>
            <div
                class="kpi-green kpi-card glow-green animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.3s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Entregados
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{
                        orders_data.filter((o) => o.status === 'entregado')
                            .length
                    }}
                </p>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <div class="relative flex-1">
                    <Search
                        class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink/50"
                    />
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Buscar pedido, cliente o servicio..."
                        class="glass pl-9"
                    />
                </div>
                <div class="flex flex-wrap gap-2">
                    <Select v-model="filtro">
                        <SelectTrigger class="glass w-40">
                            <SelectValue placeholder="Filtro" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Todos</SelectItem>
                            <SelectItem value="pendientes"
                                >Pendientes</SelectItem
                            >
                            <SelectItem value="terminados"
                                >Terminados</SelectItem
                            >
                            <SelectItem value="vencidos">Vencidos</SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="estado">
                        <SelectTrigger class="glass w-40">
                            <SelectValue placeholder="Estado" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Todos los estados</SelectItem>
                            <SelectItem
                                v-for="(label, key) in statuses"
                                :key="key"
                                :value="key"
                                >{{ label }}</SelectItem
                            >
                        </SelectContent>
                    </Select>
                    <Select v-model="cliente">
                        <SelectTrigger class="glass w-44">
                            <SelectValue placeholder="Cliente" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Todos los clientes</SelectItem>
                            <SelectItem
                                v-for="c in clientOptions"
                                :key="c.id"
                                :value="String(c.id)"
                                >{{ c.name }}</SelectItem
                            >
                        </SelectContent>
                    </Select>
                    <div class="relative">
                        <CalendarDays
                            class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink/50"
                        />
                        <Input
                            v-model="fecha"
                            type="date"
                            class="glass w-44 pl-9"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-card hidden overflow-hidden rounded-2xl p-6 md:block">
            <div class="overflow-x-auto">
                <table class="table-glow w-full">
                    <thead>
                        <tr class="border-b border-ink/10">
                            <th
                                class="px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/55 uppercase"
                            >
                                Código
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/60 uppercase"
                            >
                                Cliente
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/60 uppercase"
                            >
                                Servicios
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/60 uppercase"
                            >
                                Total
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/60 uppercase"
                            >
                                Saldo
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/55 uppercase"
                            >
                                Estado
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/55 uppercase"
                            >
                                Entrega
                            </th>
                            <th
                                class="px-5 py-4 text-right text-xs font-bold tracking-wider text-ink/55 uppercase"
                            >
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/5">
                        <template v-if="orders_data.length > 0">
                            <tr
                                v-for="order in orders_data"
                                :key="order.id"
                                class="transition-colors hover:bg-blush/30"
                            >
                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold text-ink">
                                        {{ order.code }}
                                    </p>
                                    <p class="text-xs text-ink/50">
                                        {{ formatDate(order.created_at) }}
                                    </p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-sm text-ink/80">
                                        {{ order.client?.name || '-' }}
                                    </p>
                                </td>
                                <td class="px-5 py-4 text-sm text-ink/60">
                                    {{ order.services_count }} servicio(s)
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold text-ink">
                                        {{ formatMoney(order.total) }}
                                    </p>
                                    <p
                                        v-if="order.descuento > 0"
                                        class="text-xs text-ink/50"
                                    >
                                        desc. {{ formatMoney(order.descuento) }}
                                    </p>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="rounded-full px-2.5 py-0.5 text-xs"
                                        :class="
                                            order.balance > 0
                                                ? 'bg-red-50 font-semibold text-red-600'
                                                : 'bg-green-50 font-semibold text-green-700'
                                        "
                                    >
                                        {{ formatMoney(order.balance) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1.5">
                                        <span
                                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                            :class="
                                                statusBadge[order.status] ||
                                                'badge-gray'
                                            "
                                        >
                                            {{ order.status_label }}
                                        </span>
                                        <span
                                            v-if="order.is_overdue"
                                            title="Pedido vencido"
                                            class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-600"
                                        >
                                            Vencido
                                        </span>
                                    </div>
                                    <span
                                        class="mt-1 inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-semibold"
                                        :class="
                                            payStateBadge[
                                                order.payment_state
                                            ] || 'badge-gray'
                                        "
                                    >
                                        {{ order.payment_state }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm text-ink/70">
                                    {{
                                        order.delivery_date
                                            ? formatDate(order.delivery_date)
                                            : '—'
                                    }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            size="sm"
                                            as-child
                                            class="btn-gradient-ghost"
                                        >
                                            <Link
                                                :href="
                                                    pedidos.show({
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
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <Search class="h-12 w-12 text-ink/30" />
                                    <p class="text-lg font-medium text-ink/60">
                                        {{
                                            orders_data.length === 0 &&
                                            (search ||
                                                estado ||
                                                cliente ||
                                                filtro ||
                                                fecha)
                                                ? 'No se encontraron pedidos'
                                                : 'No hay pedidos registrados'
                                        }}
                                    </p>
                                    <p
                                        v-if="
                                            orders_data.length === 0 &&
                                            !search &&
                                            !estado &&
                                            !cliente &&
                                            !filtro &&
                                            !fecha
                                        "
                                        class="text-sm text-ink/40"
                                    >
                                        Crea tu primer pedido para comenzar
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4 md:hidden">
            <template v-if="orders_data.length > 0">
                <div
                    v-for="order in orders_data"
                    :key="order.id"
                    class="glass-card animate-slide-up rounded-2xl p-4"
                >
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-sm font-semibold text-ink">{{
                            order.code
                        }}</span>
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                            :class="statusBadge[order.status] || 'badge-gray'"
                        >
                            {{ order.status_label }}
                        </span>
                    </div>
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-ink/50">Cliente:</span>
                            <span class="text-ink/80">{{
                                order.client?.name || '-'
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ink/50">Total:</span>
                            <span class="font-semibold text-ink">{{
                                formatMoney(order.total)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ink/50">Saldo:</span>
                            <span
                                :class="
                                    order.balance > 0
                                        ? 'font-semibold text-red-600'
                                        : 'font-semibold text-green-600'
                                "
                                >{{ formatMoney(order.balance) }}</span
                            >
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ink/50">Entrega:</span>
                            <span class="text-ink/80">{{
                                order.delivery_date
                                    ? formatDate(order.delivery_date)
                                    : '—'
                            }}</span>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-end">
                        <Button size="sm" as-child class="btn-gradient-ghost">
                            <Link
                                :href="pedidos.show({ pedido: order.id }).url"
                            >
                                <Eye class="mr-1 h-4 w-4" /> Ver detalle
                            </Link>
                        </Button>
                    </div>
                </div>
            </template>
            <div
                v-else
                class="flex flex-col items-center gap-3 py-16 text-center"
            >
                <Search class="h-12 w-12 text-ink/30" />
                <p class="text-lg font-medium text-ink/60">
                    {{ 'No se encontraron pedidos' }}
                </p>
            </div>
        </div>
    </div>
</template>
