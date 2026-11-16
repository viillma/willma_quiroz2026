<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Search, Eye } from 'lucide-vue-next';
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
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { formatMoney, formatDate } from '@/lib/money';
import pagos from '@/routes/pagos';
import pedidos from '@/routes/pedidos';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage<{
    payments: Array<{
        id: number;
        amount: number;
        payment_method: string;
        tipo_pago: string | null;
        status: string;
        reference: string | null;
        order: { id: number; code: string; client_name: string | null } | null;
        created_at: string;
        confirmed_at: string | null;
    }>;
    clients: Array<{ id: number; name: string }>;
    filters: {
        search: string;
        estado: string;
        metodo: string;
        cliente: string;
    };
}>();

const payments = computed(() => page.props.payments ?? []);
const clientOptions = computed(() => page.props.clients ?? []);

const search = ref(page.props.filters?.search ?? '');
const estado = ref(page.props.filters?.estado ?? '');
const metodo = ref(page.props.filters?.metodo ?? '');
const cliente = ref(page.props.filters?.cliente ?? '');

const totals = computed(() => {
    const total = payments.value.reduce((sum, p) => sum + p.amount, 0);
    const confirmado = payments.value.reduce(
        (s, p) => s + (p.status === 'confirmado' ? p.amount : 0),
        0,
    );
    const pendiente = payments.value.reduce(
        (s, p) => s + (p.status === 'pendiente' ? p.amount : 0),
        0,
    );

    return { total, confirmado, pendiente, count: payments.value.length };
});

const statusBadge: Record<string, string> = {
    pendiente: 'badge-orange',
    confirmado: 'badge-green',
    rechazado: 'badge-red',
};

const methodLabel: Record<string, string> = {
    efectivo: 'Efectivo',
    qr: 'QR',
    transferencia: 'Transferencia',
    tarjeta: 'Tarjeta',
    otro: 'Otro',
};

const tipoLabel: Record<string, string> = {
    adelanto: 'Adelanto',
    pago_parcial: 'Pago parcial',
    saldo_final: 'Saldo final',
    pago_total: 'Pago total',
};

let searchTimer: ReturnType<typeof setTimeout> | null = null;
function applyFilters(preserveSearch = false) {
    if (searchTimer) {
        clearTimeout(searchTimer);
    }

    searchTimer = setTimeout(
        () => {
            router.get(
                pagos.index.url({
                    query: {
                        search: search.value || undefined,
                        estado: estado.value || undefined,
                        metodo: metodo.value || undefined,
                        cliente: cliente.value || undefined,
                    },
                }),
                {},
                { preserveState: true, preserveScroll: true, replace: true },
            );
        },
        preserveSearch ? 350 : 0,
    );
}

watch(search, () => applyFilters(true));
watch(estado, () => applyFilters());
watch(metodo, () => applyFilters());
watch(cliente, () => applyFilters());
</script>

<template>
    <Head title="Pagos" />

    <div class="animate-fade-in space-y-6 p-4 md:p-6 lg:p-8">
        <Heading
            title="Pagos"
            description="Historial de pagos de todos los pedidos"
        />

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div
                class="kpi-blue kpi-card glow-blue animate-slide-up rounded-2xl p-5 shadow-lg"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Registros
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ totals.count }}
                </p>
            </div>
            <div
                class="kpi-purple kpi-card glow-purple animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.1s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Monto Total
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ formatMoney(totals.total) }}
                </p>
            </div>
            <div
                class="kpi-green kpi-card glow-green animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.2s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Confirmado
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ formatMoney(totals.confirmado) }}
                </p>
            </div>
            <div
                class="kpi-orange kpi-card glow-orange animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.3s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Pendiente
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ formatMoney(totals.pendiente) }}
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
                        placeholder="Buscar referencia, pedido o cliente..."
                        class="glass pl-9"
                    />
                </div>
                <div class="flex flex-wrap gap-2">
                    <Select v-model="estado">
                        <SelectTrigger class="glass w-40">
                            <SelectValue placeholder="Estado" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Todos</SelectItem>
                            <SelectItem value="pendiente">Pendiente</SelectItem>
                            <SelectItem value="confirmado"
                                >Confirmado</SelectItem
                            >
                            <SelectItem value="rechazado">Rechazado</SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="metodo">
                        <SelectTrigger class="glass w-40">
                            <SelectValue placeholder="Método" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Todos</SelectItem>
                            <SelectItem value="efectivo">Efectivo</SelectItem>
                            <SelectItem value="qr">QR</SelectItem>
                            <SelectItem value="transferencia"
                                >Transferencia</SelectItem
                            >
                            <SelectItem value="tarjeta">Tarjeta</SelectItem>
                            <SelectItem value="otro">Otro</SelectItem>
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
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-5">
            <div class="hidden overflow-x-auto md:block">
                <table class="table-glow w-full">
                    <thead>
                        <tr class="border-b border-ink/10 text-sm text-ink/60">
                            <th class="pb-2 text-left">Pedido</th>
                            <th class="pb-2 text-left">Cliente</th>
                            <th class="pb-2 text-center">Método</th>
                            <th class="pb-2 text-center">Tipo</th>
                            <th class="pb-2 text-right">Monto</th>
                            <th class="pb-2 text-center">Estado</th>
                            <th class="pb-2 text-right">Fecha</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="p in payments"
                            :key="p.id"
                            class="border-b border-ink/5 text-sm text-ink/80 transition-colors hover:bg-pink-50/70"
                        >
                            <td class="py-2.5 font-semibold text-ink">
                                <Link
                                    v-if="p.order"
                                    :href="
                                        pedidos.show({ pedido: p.order.id }).url
                                    "
                                    class="hover:text-pix"
                                    >{{ p.order.code || '—' }}</Link
                                >
                                <span v-else>—</span>
                            </td>
                            <td class="py-2.5">
                                {{ p.order?.client_name || '—' }}
                            </td>
                            <td class="py-2.5 text-center">
                                {{
                                    methodLabel[p.payment_method] ||
                                    p.payment_method
                                }}
                            </td>
                            <td class="py-2.5 text-center">
                                {{
                                    tipoLabel[p.tipo_pago || ''] ||
                                    p.tipo_pago ||
                                    '—'
                                }}
                            </td>
                            <td
                                class="py-2.5 text-right font-semibold text-ink"
                            >
                                {{ formatMoney(p.amount) }}
                            </td>
                            <td class="py-2.5 text-center">
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                    :class="
                                        statusBadge[p.status] || 'badge-gray'
                                    "
                                >
                                    {{ p.status }}
                                </span>
                            </td>
                            <td class="py-2.5 text-right text-ink/60">
                                {{ formatDate(p.confirmed_at || p.created_at) }}
                            </td>
                            <td class="py-2.5 text-right">
                                <Button
                                    v-if="p.order"
                                    size="sm"
                                    variant="ghost"
                                    class="btn-gradient-ghost"
                                    as-child
                                >
                                    <Link
                                        :href="
                                            pedidos.show({ pedido: p.order.id })
                                                .url
                                        "
                                    >
                                        <Eye class="mr-1 h-4 w-4" /> Ver
                                    </Link>
                                </Button>
                            </td>
                        </tr>
                        <tr v-if="payments.length === 0">
                            <td colspan="8" class="py-10 text-center">
                                <p class="text-ink/50">
                                    No se encontraron pagos
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="space-y-3 md:hidden">
                <div
                    v-for="p in payments"
                    :key="p.id"
                    class="glass rounded-xl p-4"
                >
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-ink">
                            {{ p.order?.code || 'Sin pedido' }}
                        </p>
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                            :class="statusBadge[p.status] || 'badge-gray'"
                            >{{ p.status }}</span
                        >
                    </div>
                    <p class="text-sm text-ink/60">
                        {{ p.order?.client_name || '—' }}
                    </p>
                    <div class="mt-2 flex items-center justify-between">
                        <p class="text-lg font-bold text-ink">
                            {{ formatMoney(p.amount) }}
                        </p>
                        <p class="text-xs text-ink/50">
                            {{
                                methodLabel[p.payment_method] ||
                                p.payment_method
                            }}
                            · {{ formatDate(p.confirmed_at || p.created_at) }}
                        </p>
                    </div>
                    <div v-if="p.order" class="mt-2">
                        <Button
                            size="sm"
                            variant="ghost"
                            class="btn-gradient-ghost"
                            as-child
                        >
                            <Link
                                :href="pedidos.show({ pedido: p.order.id }).url"
                            >
                                <Eye class="mr-1 h-4 w-4" /> Ver pedido
                            </Link>
                        </Button>
                    </div>
                </div>
                <p
                    v-if="payments.length === 0"
                    class="py-8 text-center text-ink/50"
                >
                    No se encontraron pagos
                </p>
            </div>
        </div>
    </div>
</template>
