<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import servicios from '@/routes/servicios';
import type { Team } from '@/types';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage();

const teamSlug = ref<string>('');

watch(
    () => page.props.currentTeam,
    (team) => {
        teamSlug.value = (team as Team | null)?.slug ?? '';
    },
    { immediate: true },
);

const servicios_data = computed(
    () =>
        (page.props.servicios || []) as Array<{
            id: number;
            nombre: string;
            descripcion: string;
            precio: string;
            tiempo_estimado: number | null;
            estado: string;
        }>,
);
</script>

<template>
    <Head title="Servicios" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 space-y-6 relative">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 ring-1 ring-white/10">
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <Heading
                    title="Servicios"
                    description="Gestiona los servicios disponibles"
                />
            </div>
            <Button v-if="teamSlug" as-child class="btn-gradient-blue btn-shimmer">
                <Link :href="servicios.create({ current_team: teamSlug }).url">
                    <Plus class="mr-2 h-4 w-4" />
                    Nuevo Servicio
                </Link>
            </Button>
        </div>

        <div
            v-if="teamSlug && servicios_data.length > 0"
            class="grid grid-cols-1 gap-4 sm:grid-cols-3"
        >
            <div class="kpi-blue kpi-card rounded-2xl p-5 shadow-lg glow-blue animate-slide-up">
                <p class="text-xs font-medium uppercase tracking-wider text-white/70">
                    Total Servicios
                </p>
                <p class="mt-1 text-2xl font-bold text-white">{{ servicios_data.length }}</p>
            </div>
            <div class="kpi-green kpi-card rounded-2xl p-5 shadow-lg glow-green animate-slide-up" style="animation-delay: 0.1s">
                <p class="text-xs font-medium uppercase tracking-wider text-white/70">
                    Activos
                </p>
                <p class="mt-1 text-2xl font-bold text-white">
                    {{ servicios_data.filter(s => s.estado === 'activo').length }}
                </p>
            </div>
            <div class="kpi-orange kpi-card rounded-2xl p-5 shadow-lg glow-orange animate-slide-up" style="animation-delay: 0.2s">
                <p class="text-xs font-medium uppercase tracking-wider text-white/70">
                    Inactivos
                </p>
                <p class="mt-1 text-2xl font-bold text-white">
                    {{ servicios_data.filter(s => s.estado !== 'activo').length }}
                </p>
            </div>
        </div>

        <div
            v-if="teamSlug && servicios_data.length > 0"
            class="glass-card rounded-2xl p-6 overflow-hidden"
        >
            <div class="overflow-x-auto">
                <table class="table-glow w-full">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">
                                Nombre
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">
                                Descripción
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">
                                Precio
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">
                                Tiempo Est.
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">
                                Estado
                            </th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-white/50">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr
                            v-for="servicio in servicios_data"
                            :key="servicio.id"
                            class="transition-colors hover:bg-white/5"
                        >
                            <td class="px-5 py-4 text-sm font-medium text-white">
                                {{ servicio.nombre }}
                            </td>
                            <td class="px-5 py-4 text-sm text-white/60">
                                {{ servicio.descripcion || '-' }}
                            </td>
                            <td class="px-5 py-4 text-sm font-medium text-white">
                                S/ {{ servicio.precio }}
                            </td>
                            <td class="px-5 py-4 text-sm text-white/60">
                                {{ servicio.tiempo_estimado ? servicio.tiempo_estimado + ' días' : '-' }}
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    :class="servicio.estado === 'activo' ? 'badge-green' : 'badge-orange'"
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium transition-all duration-200"
                                >
                                    <span
                                        :class="
                                            servicio.estado === 'activo'
                                                ? 'bg-green-400'
                                                : 'bg-orange-400'
                                        "
                                        class="mr-1.5 inline-block h-1.5 w-1.5 rounded-full"
                                    ></span>
                                    {{ servicio.estado }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <Button size="sm" class="btn-gradient-ghost" as-child>
                                        <Link
                                            :href="
                                                servicios.edit({
                                                    current_team: teamSlug,
                                                    servicio: servicio.id,
                                                }).url
                                            "
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        size="sm"
                                        class="btn-gradient-ghost text-red-400 hover:text-red-300"
                                        @click="
                                            router.delete(
                                                servicios.destroy.url({
                                                    current_team: teamSlug,
                                                    servicio: servicio.id,
                                                }),
                                            )
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
        </div>

        <div
            v-if="teamSlug && servicios_data.length > 0"
            class="space-y-3 md:hidden"
        >
            <div
                v-for="servicio in servicios_data"
                :key="servicio.id"
                class="glass-card rounded-2xl p-4 animate-slide-up"
            >
                <div class="mb-3 flex items-start justify-between">
                    <div>
                        <h3 class="font-semibold text-white">{{ servicio.nombre }}</h3>
                        <p class="mt-0.5 text-sm text-white/60">
                            {{ servicio.descripcion || '-' }}
                        </p>
                    </div>
                    <span
                        :class="servicio.estado === 'activo' ? 'badge-green' : 'badge-orange'"
                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium transition-all duration-200"
                    >
                        <span
                            :class="
                                servicio.estado === 'activo'
                                    ? 'bg-green-400'
                                    : 'bg-orange-400'
                            "
                            class="mr-1.5 inline-block h-1.5 w-1.5 rounded-full"
                        ></span>
                        {{ servicio.estado }}
                    </span>
                </div>
                <div class="mb-3 grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="text-xs text-white/50">Precio</span>
                        <p class="font-medium text-white">S/ {{ servicio.precio }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-white/50">Tiempo Est.</span>
                        <p class="font-medium text-white">
                            {{ servicio.tiempo_estimado ? servicio.tiempo_estimado + ' días' : '-' }}
                        </p>
                    </div>
                </div>
                <div class="flex justify-end gap-1 border-t border-white/10 pt-3">
                    <Button size="sm" class="btn-gradient-ghost" as-child>
                        <Link
                            :href="
                                servicios.edit({
                                    current_team: teamSlug,
                                    servicio: servicio.id,
                                }).url
                            "
                        >
                            <Pencil class="mr-1.5 h-4 w-4" />
                            Editar
                        </Link>
                    </Button>
                    <Button
                        size="sm"
                        class="btn-gradient-ghost text-red-400 hover:text-red-300"
                        @click="
                            router.delete(
                                servicios.destroy.url({
                                    current_team: teamSlug,
                                    servicio: servicio.id,
                                }),
                            )
                        "
                    >
                        <Trash2 class="mr-1.5 h-4 w-4" />
                        Eliminar
                    </Button>
                </div>
            </div>
        </div>

        <div
            v-if="!teamSlug"
            class="flex flex-col items-center justify-center glass-card rounded-2xl px-6 py-16 text-center"
        >
            <div
                class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-white/10"
            >
                <svg
                    class="h-7 w-7 text-white/40 animate-float"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                    />
                </svg>
            </div>
            <p class="text-base font-medium text-white/60">
                Cargando...
            </p>
        </div>

        <div
            v-if="teamSlug && servicios_data.length === 0"
            class="flex flex-col items-center justify-center glass-card rounded-2xl px-6 py-16 text-center"
        >
            <div
                class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-white/10"
            >
                <svg
                    class="h-7 w-7 text-white/40"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                    />
                </svg>
            </div>
            <p class="text-base font-medium text-white/60">
                No hay servicios registrados
            </p>
            <Button v-if="teamSlug" class="btn-gradient-blue btn-shimmer mt-4" as-child>
                <Link :href="servicios.create({ current_team: teamSlug }).url">
                    <Plus class="mr-2 h-4 w-4" />
                    Crear primer servicio
                </Link>
            </Button>
        </div>
    </div>
</template>
