<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import clientes from '@/routes/clientes';
import type { Team } from '@/types';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage();

const teamSlug = ref<string>('');
const search = ref('');

watch(
    () => page.props.currentTeam,
    (team) => {
        teamSlug.value = (team as Team | null)?.slug ?? '';
    },
    { immediate: true },
);

const clients_data = computed(
    () =>
        (page.props.clients || []) as Array<{
            id: number;
            name: string;
            phone: string;
            email: string;
            company: string;
            notes: string;
            created_at: string;
        }>,
);

const filteredClients = computed(() => {
    if (!search.value) return clients_data.value;
    const q = search.value.toLowerCase();
    return clients_data.value.filter(
        (c) =>
            c.name.toLowerCase().includes(q) ||
            (c.phone && c.phone.toLowerCase().includes(q)) ||
            (c.email && c.email.toLowerCase().includes(q)),
    );
});
</script>

<template>
    <Head title="Clientes" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 space-y-6 relative">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500/20 to-pink-500/20 ring-1 ring-white/10">
                    <svg class="h-5 w-5 text-purple-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <Heading
                    title="Clientes"
                    description="Gestiona los clientes de la empresa"
                />
            </div>
            <Button v-if="teamSlug" as-child class="btn-gradient-blue btn-shimmer w-full sm:w-auto">
                <Link :href="clientes.create({ current_team: teamSlug }).url">
                    <Plus class="mr-2 h-4 w-4" />
                    Nuevo Cliente
                </Link>
            </Button>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="kpi-blue kpi-card rounded-2xl p-5 shadow-lg glow-blue animate-slide-up">
                <p class="text-xs font-medium uppercase tracking-wider text-white/70">Total clientes</p>
                <p class="mt-1 text-2xl font-bold text-white">{{ clients_data.length }}</p>
            </div>
            <div class="kpi-purple kpi-card rounded-2xl p-5 shadow-lg glow-purple animate-slide-up" style="animation-delay: 0.1s">
                <p class="text-xs font-medium uppercase tracking-wider text-white/70">Con correo</p>
                <p class="mt-1 text-2xl font-bold text-white">{{ clients_data.filter(c => c.email).length }}</p>
            </div>
            <div class="kpi-cyan kpi-card rounded-2xl p-5 shadow-lg glow-cyan animate-slide-up" style="animation-delay: 0.2s">
                <p class="text-xs font-medium uppercase tracking-wider text-white/70">Con empresa</p>
                <p class="mt-1 text-2xl font-bold text-white">{{ clients_data.filter(c => c.company).length }}</p>
            </div>
        </div>

        <div class="relative">
            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/40" />
            <Input
                v-model="search"
                placeholder="Buscar por nombre, teléfono o correo..."
                class="bg-white/5 border-white/10 text-white placeholder:text-white/40 focus:border-purple-500/50 max-w-sm pl-9"
            />
        </div>

        <div v-if="teamSlug && filteredClients.length > 0">
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table-glow w-full">
                        <thead>
                            <tr class="border-b border-white/10">
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">Nombre</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">Teléfono</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">Correo</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">Empresa</th>
                                <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-white/50">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr
                                v-for="client in filteredClients"
                                :key="client.id"
                                class="transition-colors hover:bg-white/5"
                            >
                                <td class="px-5 py-4 text-sm font-medium text-white">{{ client.name }}</td>
                                <td class="px-5 py-4 text-sm text-white/60">{{ client.phone || '—' }}</td>
                                <td class="px-5 py-4 text-sm text-white/60">{{ client.email || '—' }}</td>
                                <td class="px-5 py-4 text-sm text-white/60">{{ client.company || '—' }}</td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-1">
                                        <Button size="sm" as-child class="btn-gradient-ghost">
                                            <Link
                                                :href="
                                                    clientes.edit({
                                                        current_team: teamSlug,
                                                        cliente: client.id,
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
                                                    clientes.destroy.url({
                                                        current_team: teamSlug,
                                                        cliente: client.id,
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

            <div class="mt-4 grid gap-4 md:hidden">
                <div
                    v-for="client in filteredClients"
                    :key="client.id"
                    class="glass-card rounded-2xl p-4 animate-slide-up"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-sm font-semibold text-white">{{ client.name }}</span>
                        <div class="flex gap-1">
                            <Button size="sm" as-child class="btn-gradient-ghost">
                                <Link
                                    :href="
                                        clientes.edit({
                                            current_team: teamSlug,
                                            cliente: client.id,
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
                                        clientes.destroy.url({
                                            current_team: teamSlug,
                                            cliente: client.id,
                                        }),
                                    )
                                "
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                    <div class="space-y-1 text-sm text-white/60">
                        <div class="flex justify-between">
                            <span>Teléfono</span>
                            <span>{{ client.phone || '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Correo</span>
                            <span>{{ client.email || '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Empresa</span>
                            <span>{{ client.company || '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-else
            class="flex flex-col items-center justify-center glass-card rounded-2xl px-6 py-16 text-center"
        >
            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white/40 animate-float" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <p class="text-base font-medium text-white/60">
                {{ teamSlug ? 'No hay clientes registrados' : 'Cargando...' }}
            </p>
            <p v-if="teamSlug" class="mt-1 text-sm text-white/40">
                Comienza agregando un nuevo cliente
            </p>
            <Button v-if="teamSlug && clients_data.length === 0" class="btn-gradient-blue btn-shimmer mt-4" as-child>
                <Link :href="clientes.create({ current_team: teamSlug }).url">
                    <Plus class="mr-2 h-4 w-4" />
                    Crear primer cliente
                </Link>
            </Button>
        </div>
    </div>
</template>
