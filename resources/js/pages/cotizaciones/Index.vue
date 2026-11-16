<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Plus, Search, Eye, Copy } from 'lucide-vue-next';
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
import cotizaciones from '@/routes/cotizaciones';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage<{
    cotizaciones: Array<{
        id: number;
        numero: string;
        client: { id: number; name: string } | null;
        fecha: string | null;
        fecha_validez: string | null;
        total: number;
        descuento: number;
        estado: string;
        estado_label: string;
        is_expired: boolean;
        created_at: string;
    }>;
    clients: Array<{ id: number; name: string }>;
    statuses: Record<string, string>;
    filters: { search: string; estado: string; cliente: string };
}>();

const { can } = usePermissions();

const items = computed(() => page.props.cotizaciones ?? []);
const statuses = computed(() => page.props.statuses ?? {});
const clientOptions = computed(() => page.props.clients ?? []);

const search = ref(page.props.filters?.search ?? '');
const estado = ref(page.props.filters?.estado ?? '');
const cliente = ref(page.props.filters?.cliente ?? '');

const statusBadge: Record<string, string> = {
    borrador: 'badge-gray',
    enviada: 'badge-blue',
    aceptada: 'badge-green',
    rechazada: 'badge-red',
    vencida: 'badge-orange',
};

const kpis = computed(() => ({
    total: items.value.length,
    borrador: items.value.filter((c) => c.estado === 'borrador').length,
    enviadas: items.value.filter((c) => c.estado === 'enviada').length,
    aceptadas: items.value.filter((c) => c.estado === 'aceptada').length,
    importe: items.value
        .filter((c) => ['enviada', 'aceptada'].includes(c.estado))
        .reduce((s, c) => s + c.total, 0),
}));

let searchTimer: ReturnType<typeof setTimeout> | null = null;
function applyFilters(preserveSearch = false) {
    if (searchTimer) {
        clearTimeout(searchTimer);
    }

    searchTimer = setTimeout(
        () => {
            router.get(
                cotizaciones.index().url,
                {
                    search: search.value || undefined,
                    estado: estado.value || undefined,
                    cliente: cliente.value || undefined,
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
</script>

<template>
    <Head title="Cotizaciones" />

    <div class="animate-fade-in space-y-6 p-4 md:p-6 lg:p-8">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading
                title="Cotizaciones"
                description="Cotizaciones para tus clientes"
            />
            <Button
                v-if="can('cotizaciones.gestionar')"
                as-child
                class="btn-gradient-blue"
            >
                <Link :href="cotizaciones.create().url">
                    <Plus class="mr-2 h-4 w-4" />
                    Nueva Cotización
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
                    Total
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">{{ kpis.total }}</p>
            </div>
            <div
                class="kpi-orange kpi-card glow-orange animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.1s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Borradores
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ kpis.borrador }}
                </p>
            </div>
            <div
                class="kpi-purple kpi-card glow-purple animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.2s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Enviadas
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ kpis.enviadas }}
                </p>
            </div>
            <div
                class="kpi-green kpi-card glow-green animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.3s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Aceptadas · {{ formatMoney(kpis.importe) }}
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ kpis.aceptadas }}
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
                        placeholder="Buscar cotización o cliente..."
                        class="glass pl-9"
                    />
                </div>
                <div class="flex flex-wrap gap-2">
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
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-5">
            <div class="hidden overflow-x-auto md:block">
                <table class="table-glow w-full">
                    <thead>
                        <tr class="border-b border-ink/10 text-sm text-ink/60">
                            <th class="pb-2 text-left">Cotización</th>
                            <th class="pb-2 text-left">Cliente</th>
                            <th class="pb-2 text-right">Total</th>
                            <th class="pb-2 text-center">Estado</th>
                            <th class="pb-2 text-right">Fecha</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="c in items"
                            :key="c.id"
                            class="border-b border-ink/5 text-sm text-ink/80 transition-colors hover:bg-pink-50/70"
                        >
                            <td class="py-2.5 font-semibold text-ink">
                                <Link
                                    :href="
                                        cotizaciones.show({ cotizacion: c.id })
                                            .url
                                    "
                                    class="hover:text-pix"
                                    >{{ c.numero }}</Link
                                >
                                <span
                                    v-if="c.is_expired"
                                    class="ml-2 rounded-full bg-orange-100 px-2 py-0.5 text-xs font-semibold text-orange-600"
                                    >Vencida</span
                                >
                            </td>
                            <td class="py-2.5">{{ c.client?.name || '—' }}</td>
                            <td
                                class="py-2.5 text-right font-semibold text-ink"
                            >
                                {{ formatMoney(c.total) }}
                            </td>
                            <td class="py-2.5 text-center">
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                    :class="
                                        statusBadge[c.estado] || 'badge-gray'
                                    "
                                >
                                    {{ c.estado_label }}
                                </span>
                            </td>
                            <td class="py-2.5 text-right text-ink/60">
                                {{
                                    c.fecha
                                        ? formatDate(c.fecha)
                                        : formatDate(c.created_at)
                                }}
                            </td>
                            <td class="py-2.5 text-right">
                                <div class="flex justify-end gap-1">
                                    <Button
                                        v-if="
                                            c.estado !== 'aceptada' &&
                                            can('cotizaciones.gestionar')
                                        "
                                        size="sm"
                                        variant="ghost"
                                        class="btn-gradient-ghost"
                                        title="Duplicar"
                                        @click="
                                            router.post(
                                                cotizaciones.duplicar.url({
                                                    cotizacion: c.id,
                                                }),
                                            )
                                        "
                                    >
                                        <Copy class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        size="sm"
                                        variant="ghost"
                                        class="btn-gradient-ghost"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                cotizaciones.show({
                                                    cotizacion: c.id,
                                                }).url
                                            "
                                        >
                                            <Eye class="mr-1 h-4 w-4" /> Ver
                                        </Link>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="items.length === 0">
                            <td colspan="6" class="py-10 text-center">
                                <p class="text-ink/50">
                                    No se encontraron cotizaciones
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="space-y-3 md:hidden">
                <div
                    v-for="c in items"
                    :key="c.id"
                    class="glass rounded-xl p-4"
                >
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-ink">{{ c.numero }}</p>
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                            :class="statusBadge[c.estado] || 'badge-gray'"
                            >{{ c.estado_label }}</span
                        >
                    </div>
                    <p class="text-sm text-ink/60">
                        {{ c.client?.name || '—' }}
                    </p>
                    <div class="mt-2 flex items-center justify-between">
                        <p class="text-lg font-bold text-ink">
                            {{ formatMoney(c.total) }}
                        </p>
                        <p class="text-xs text-ink/50">
                            {{ c.fecha ? formatDate(c.fecha) : '' }}
                        </p>
                    </div>
                    <div class="mt-2">
                        <Button
                            size="sm"
                            variant="ghost"
                            class="btn-gradient-ghost"
                            as-child
                        >
                            <Link
                                :href="
                                    cotizaciones.show({ cotizacion: c.id }).url
                                "
                            >
                                <Eye class="mr-1 h-4 w-4" /> Ver
                            </Link>
                        </Button>
                    </div>
                </div>
                <p
                    v-if="items.length === 0"
                    class="py-8 text-center text-ink/50"
                >
                    No se encontraron cotizaciones
                </p>
            </div>
        </div>
    </div>
</template>
