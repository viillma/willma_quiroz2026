<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Search, Eye, Power } from 'lucide-vue-next';
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
import { formatMoney } from '@/lib/money';
import servicios from '@/routes/servicios';

defineOptions({
    layout: AppSidebarLayout,
});

type Servicio = {
    id: number;
    codigo: string | null;
    nombre: string;
    categoria: string | null;
    descripcion: string | null;
    precio: number;
    costo_estimado: number | null;
    unidad: string | null;
    tiempo_estimado: number | null;
    imagen: string | null;
    estado: string;
    created_at: string | null;
};

const page = usePage<{
    servicios: Servicio[];
    categorias: string[];
    filters: {
        search: string;
        categoria: string;
        estado: string;
        orden: string;
    };
}>();
const { can } = usePermissions();

const servicios_data = computed<Servicio[]>(() => page.props.servicios ?? []);
const categorias = computed(() => page.props.categorias ?? []);

const search = ref(page.props.filters?.search ?? '');
const categoria = ref(page.props.filters?.categoria ?? '');
const estado = ref(page.props.filters?.estado ?? '');
const orden = ref(page.props.filters?.orden ?? 'recientes');

let searchTimer: ReturnType<typeof setTimeout> | null = null;
function applyFilters(preserveSearch = false) {
    if (searchTimer) {
        clearTimeout(searchTimer);
    }

    searchTimer = setTimeout(
        () => {
            router.get(
                servicios.index().url,
                {
                    search: search.value || undefined,
                    categoria: categoria.value || undefined,
                    estado: estado.value || undefined,
                    orden: orden.value,
                },
                { preserveState: true, preserveScroll: true, replace: true },
            );
        },
        preserveSearch ? 350 : 0,
    );
}

function toggle(servicio: Servicio) {
    router.post(
        servicios.toggle.url({ servicio: servicio.id }),
        {},
        { preserveScroll: true },
    );
}

function remove(servicio: Servicio) {
    if (window.confirm(`¿Eliminar el servicio "${servicio.nombre}"?`)) {
        router.delete(servicios.destroy.url({ servicio: servicio.id }), {
            preserveScroll: true,
        });
    }
}

watch(search, () => applyFilters(true));
watch(categoria, () => applyFilters());
watch(estado, () => applyFilters());
watch(orden, () => applyFilters());
</script>

<template>
    <Head title="Servicios" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 ring-1 ring-ink/10"
                >
                    <svg
                        class="h-5 w-5 text-blue-600"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                </div>
                <Heading
                    title="Servicios"
                    description="Gestiona los servicios disponibles"
                />
            </div>
            <Button
                v-if="can('servicios.gestionar')"
                as-child
                class="btn-gradient-blue btn-shimmer"
            >
                <Link :href="servicios.create().url">
                    <Plus class="mr-2 h-4 w-4" />
                    Nuevo Servicio
                </Link>
            </Button>
        </div>

        <div
            v-if="servicios_data.length > 0"
            class="grid grid-cols-1 gap-4 sm:grid-cols-3"
        >
            <div
                class="kpi-blue kpi-card glow-blue animate-slide-up rounded-2xl p-5 shadow-lg"
            >
                <p
                    class="text-xs font-bold tracking-wider text-ink/70 uppercase"
                >
                    Total Servicios
                </p>
                <p class="mt-1 text-2xl font-extrabold text-white">
                    {{ servicios_data.length }}
                </p>
            </div>
            <div
                class="kpi-green kpi-card glow-green animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.1s"
            >
                <p
                    class="text-xs font-bold tracking-wider text-ink/70 uppercase"
                >
                    Activos
                </p>
                <p class="mt-1 text-2xl font-extrabold text-white">
                    {{
                        servicios_data.filter((s) => s.estado === 'activo')
                            .length
                    }}
                </p>
            </div>
            <div
                class="kpi-orange kpi-card glow-orange animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.2s"
            >
                <p
                    class="text-xs font-bold tracking-wider text-ink/70 uppercase"
                >
                    Inactivos
                </p>
                <p class="mt-1 text-2xl font-extrabold text-white">
                    {{
                        servicios_data.filter((s) => s.estado !== 'activo')
                            .length
                    }}
                </p>
            </div>
        </div>

        <div class="glass-card space-y-3 rounded-2xl p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center">
                <div class="relative flex-1">
                    <Search
                        class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink/50"
                    />
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Buscar servicio..."
                        class="glass pl-9"
                    />
                </div>
                <div class="flex flex-wrap gap-2">
                    <Select v-model="categoria">
                        <SelectTrigger class="glass w-44">
                            <SelectValue placeholder="Categoría" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value=""
                                >Todas las categorías</SelectItem
                            >
                            <SelectItem
                                v-for="cat in categorias"
                                :key="cat"
                                :value="cat"
                                >{{ cat }}</SelectItem
                            >
                        </SelectContent>
                    </Select>
                    <Select v-model="estado">
                        <SelectTrigger class="glass w-36">
                            <SelectValue placeholder="Estado" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Todos los estados</SelectItem>
                            <SelectItem value="activo">Activo</SelectItem>
                            <SelectItem value="inactivo">Inactivo</SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="orden">
                        <SelectTrigger class="glass w-32">
                            <SelectValue placeholder="Ordenar" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="recientes"
                                >Más recientes</SelectItem
                            >
                            <SelectItem value="antiguos"
                                >Más antiguos</SelectItem
                            >
                            <SelectItem value="az">A - Z</SelectItem>
                            <SelectItem value="precio_asc"
                                >Menor precio</SelectItem
                            >
                            <SelectItem value="precio_desc"
                                >Mayor precio</SelectItem
                            >
                        </SelectContent>
                    </Select>
                </div>
            </div>
        </div>

        <div
            v-if="servicios_data.length > 0"
            class="glass-card hidden overflow-hidden rounded-2xl p-6 md:block"
        >
            <div class="overflow-x-auto">
                <table class="table-glow w-full">
                    <thead>
                        <tr class="border-b border-ink/10">
                            <th
                                class="px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/75 uppercase"
                            >
                                Servicio
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/75 uppercase"
                            >
                                Categoría
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/75 uppercase"
                            >
                                Precio
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/75 uppercase"
                            >
                                Costo Est.
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/75 uppercase"
                            >
                                Tiempo Est.
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/75 uppercase"
                            >
                                Estado
                            </th>
                            <th
                                class="px-5 py-4 text-right text-xs font-bold tracking-wider text-ink/75 uppercase"
                            >
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/10">
                        <tr
                            v-for="servicio in servicios_data"
                            :key="servicio.id"
                            class="transition-colors hover:bg-pink-50/70"
                        >
                            <td class="px-5 py-4">
                                <Link
                                    :href="
                                        servicios.show({
                                            servicio: servicio.id,
                                        }).url
                                    "
                                    class="group flex items-center gap-3"
                                >
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-ink/5"
                                    >
                                        <img
                                            v-if="servicio.imagen"
                                            :src="servicio.imagen"
                                            :alt="servicio.nombre"
                                            class="h-full w-full object-cover"
                                        />
                                        <svg
                                            v-else
                                            class="h-5 w-5 text-ink/40"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-bold text-ink group-hover:text-pix"
                                        >
                                            {{ servicio.nombre }}
                                        </p>
                                        <p class="text-xs text-ink/50">
                                            {{
                                                servicio.codigo || 'Sin código'
                                            }}
                                        </p>
                                    </div>
                                </Link>
                            </td>
                            <td class="px-5 py-4 text-sm text-ink/70">
                                <span
                                    v-if="servicio.categoria"
                                    class="rounded-full bg-ink/5 px-3 py-1 text-xs font-semibold text-ink/70"
                                    >{{ servicio.categoria }}</span
                                >
                                <span v-else>-</span>
                            </td>
                            <td class="px-5 py-4 text-sm font-bold text-ink">
                                {{ formatMoney(servicio.precio) }}
                            </td>
                            <td class="px-5 py-4 text-sm text-ink/70">
                                {{
                                    servicio.costo_estimado !== null
                                        ? formatMoney(servicio.costo_estimado)
                                        : '-'
                                }}
                            </td>
                            <td class="px-5 py-4 text-sm text-ink/70">
                                {{
                                    servicio.tiempo_estimado
                                        ? servicio.tiempo_estimado + ' días'
                                        : '-'
                                }}
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    :class="
                                        servicio.estado === 'activo'
                                            ? 'badge-green'
                                            : 'badge-orange'
                                    "
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                >
                                    <span
                                        :class="
                                            servicio.estado === 'activo'
                                                ? 'bg-green-500'
                                                : 'bg-orange-500'
                                        "
                                        class="mr-1.5 inline-block h-1.5 w-1.5 rounded-full"
                                    ></span>
                                    {{ servicio.estado }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <Button
                                        size="sm"
                                        class="btn-gradient-ghost"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                servicios.show({
                                                    servicio: servicio.id,
                                                }).url
                                            "
                                        >
                                            <Eye class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        v-if="can('servicios.gestionar')"
                                        size="sm"
                                        class="btn-gradient-ghost"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                servicios.edit({
                                                    servicio: servicio.id,
                                                }).url
                                            "
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        v-if="can('servicios.gestionar')"
                                        size="sm"
                                        class="btn-gradient-ghost"
                                        @click="toggle(servicio)"
                                    >
                                        <Power
                                            class="h-4 w-4"
                                            :class="
                                                servicio.estado === 'activo'
                                                    ? 'text-orange-500'
                                                    : 'text-green-600'
                                            "
                                        />
                                    </Button>
                                    <Button
                                        v-if="can('servicios.gestionar')"
                                        size="sm"
                                        class="btn-gradient-ghost text-red-600 hover:text-red-700"
                                        @click="remove(servicio)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="servicios_data.length > 0" class="space-y-3 md:hidden">
            <div
                v-for="servicio in servicios_data"
                :key="servicio.id"
                class="glass-card animate-slide-up rounded-2xl p-4"
            >
                <div class="mb-3 flex items-start justify-between gap-2">
                    <Link :href="servicios.show({ servicio: servicio.id }).url">
                        <h3 class="font-bold text-ink">
                            {{ servicio.nombre }}
                        </h3>
                        <p class="mt-0.5 text-xs text-ink/50">
                            {{ servicio.codigo || '' }}
                        </p>
                    </Link>
                    <span
                        :class="
                            servicio.estado === 'activo'
                                ? 'badge-green'
                                : 'badge-orange'
                        "
                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                    >
                        {{ servicio.estado }}
                    </span>
                </div>
                <div class="mb-3 grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="text-xs font-medium text-ink/55"
                            >Precio</span
                        >
                        <p class="font-bold text-ink">
                            {{ formatMoney(servicio.precio) }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-ink/55"
                            >Categoría</span
                        >
                        <p class="font-bold text-ink">
                            {{ servicio.categoria || '-' }}
                        </p>
                    </div>
                </div>
                <div class="flex justify-end gap-1 border-t border-ink/10 pt-3">
                    <Button size="sm" class="btn-gradient-ghost" as-child>
                        <Link
                            :href="
                                servicios.edit({ servicio: servicio.id }).url
                            "
                        >
                            <Pencil class="mr-1.5 h-4 w-4" /> Editar
                        </Link>
                    </Button>
                    <Button
                        size="sm"
                        class="btn-gradient-ghost text-red-600 hover:text-red-700"
                        @click="remove(servicio)"
                    >
                        <Trash2 class="mr-1.5 h-4 w-4" /> Eliminar
                    </Button>
                </div>
            </div>
        </div>

        <div
            v-if="servicios_data.length === 0"
            class="glass-card flex flex-col items-center justify-center rounded-2xl px-6 py-16 text-center"
        >
            <div
                class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-ink/5"
            >
                <Search class="h-7 w-7 text-ink/45" />
            </div>
            <p class="text-base font-semibold text-ink/70">
                No se encontraron servicios
            </p>
            <p class="mt-1 text-sm text-ink/50">
                {{
                    search || categoria || estado
                        ? 'Prueba con otros filtros.'
                        : 'Registra tu primer servicio.'
                }}
            </p>
            <Button
                v-if="!search && !categoria && !estado"
                class="btn-gradient-blue btn-shimmer mt-4"
                as-child
            >
                <Link :href="servicios.create().url">
                    <Plus class="mr-2 h-4 w-4" /> Crear primer servicio
                </Link>
            </Button>
        </div>
    </div>
</template>
