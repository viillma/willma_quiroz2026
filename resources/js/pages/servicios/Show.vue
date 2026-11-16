<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Pencil, Power, Trash2, Package } from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { usePermissions } from '@/composables/usePermissions';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { formatMoney } from '@/lib/money';
import servicios from '@/routes/servicios';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage<{
    servicio: {
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
    total_pedidos: number;
}>();
const { can } = usePermissions();

const servicio = computed(() => page.props.servicio);
const totalPedidos = computed(() => page.props.total_pedidos ?? 0);

function toggle() {
    router.post(
        servicios.toggle.url({ servicio: servicio.value.id }),
        {},
        { preserveScroll: true },
    );
}

function remove() {
    if (window.confirm(`¿Eliminar el servicio "${servicio.value.nombre}"?`)) {
        router.delete(servicios.destroy.url({ servicio: servicio.value.id }));
    }
}

const detailRows = computed(() => [
    { label: 'Código', value: servicio.value.codigo || '—' },
    { label: 'Categoría', value: servicio.value.categoria || '—' },
    {
        label: 'Costo estimado',
        value:
            servicio.value.costo_estimado !== null
                ? formatMoney(servicio.value.costo_estimado)
                : '—',
    },
    { label: 'Unidad', value: servicio.value.unidad || '—' },
    {
        label: 'Tiempo estimado',
        value: servicio.value.tiempo_estimado
            ? servicio.value.tiempo_estimado + ' días'
            : '—',
    },
    {
        label: 'Precio',
        value: formatMoney(servicio.value.precio),
        highlight: true,
    },
]);
</script>

<template>
    <Head :title="'Servicio ' + servicio.nombre" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 ring-1 ring-ink/10"
                >
                    <Package class="h-5 w-5 text-blue-600" />
                </div>
                <Heading
                    :title="servicio.nombre"
                    :description="servicio.codigo || 'Servicio'"
                />
            </div>
            <div class="flex gap-2">
                <Button size="sm" class="btn-gradient-ghost" as-child>
                    <Link :href="servicios.index().url">Volver</Link>
                </Button>
                <Button
                    v-if="can('servicios.gestionar')"
                    size="sm"
                    class="btn-gradient-ghost"
                    as-child
                >
                    <Link :href="servicios.edit({ servicio: servicio.id }).url">
                        <Pencil class="mr-1 h-4 w-4" /> Editar
                    </Link>
                </Button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="kpi-blue kpi-card glow-blue rounded-2xl p-5 shadow-lg">
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Precio
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ formatMoney(servicio.precio) }}
                </p>
            </div>
            <div
                class="kpi-green kpi-card glow-green rounded-2xl p-5 shadow-lg"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Pedidos asociados
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ totalPedidos }}
                </p>
            </div>
            <div
                class="kpi-card rounded-2xl p-5 shadow-lg"
                :class="
                    servicio.estado === 'activo'
                        ? 'kpi-purple glow-green'
                        : 'kpi-orange glow-orange'
                "
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Estado
                </p>
                <p class="mt-1 text-2xl font-bold text-ink capitalize">
                    {{ servicio.estado }}
                </p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="glass-card rounded-2xl p-6 lg:col-span-2">
                <h3 class="mb-4 text-lg font-bold text-ink">
                    Información del servicio
                </h3>

                <div
                    v-if="servicio.descripcion"
                    class="mb-6 rounded-xl bg-ink/5 p-4"
                >
                    <p class="text-sm leading-relaxed text-ink/75">
                        {{ servicio.descripcion }}
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div
                        v-for="row in detailRows"
                        :key="row.label"
                        class="rounded-xl border border-ink/5 bg-white p-4"
                    >
                        <p class="text-xs font-medium text-ink/50">
                            {{ row.label }}
                        </p>
                        <p
                            class="mt-1 font-semibold"
                            :class="
                                row.highlight ? 'text-lg text-pix' : 'text-ink'
                            "
                        >
                            {{ row.value }}
                        </p>
                    </div>
                </div>

                <div
                    class="mt-6 flex flex-wrap gap-2 border-t border-ink/10 pt-5"
                >
                    <Button
                        v-if="can('servicios.gestionar')"
                        size="sm"
                        class="btn-gradient-ghost"
                        @click="toggle"
                    >
                        <Power
                            class="mr-1.5 h-4 w-4"
                            :class="
                                servicio.estado === 'activo'
                                    ? 'text-orange-500'
                                    : 'text-green-600'
                            "
                        />
                        {{
                            servicio.estado === 'activo'
                                ? 'Desactivar'
                                : 'Activar'
                        }}
                    </Button>
                    <Button
                        v-if="can('servicios.gestionar')"
                        size="sm"
                        class="btn-gradient-ghost text-red-600 hover:text-red-700"
                        @click="remove"
                    >
                        <Trash2 class="mr-1.5 h-4 w-4" /> Eliminar
                    </Button>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6">
                <h3 class="mb-4 text-lg font-bold text-ink">
                    Imagen referencial
                </h3>
                <div
                    class="flex aspect-[4/3] items-center justify-center overflow-hidden rounded-xl bg-ink/5"
                >
                    <img
                        v-if="servicio.imagen"
                        :src="servicio.imagen"
                        :alt="servicio.nombre"
                        class="h-full w-full object-cover"
                    />
                    <Package v-else class="h-12 w-12 text-ink/30" />
                </div>
                <p class="mt-3 text-center text-xs text-ink/50">
                    Registrado
                    {{
                        servicio.created_at
                            ? new Date(servicio.created_at).toLocaleDateString(
                                  'es-BO',
                              )
                            : '—'
                    }}
                </p>
            </div>
        </div>
    </div>
</template>
