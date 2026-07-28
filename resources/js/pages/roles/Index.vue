<script setup lang="ts">
import { Form, Head, Link, router, usePage } from '@inertiajs/vue3';
import { Shield, Plus, Pencil, Trash2, Users, ShieldCheck, Sparkles } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import roles from '@/routes/roles';
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

const roles_data = computed(
    () =>
        (page.props.roles || []) as Array<{
            id: number;
            nombre: string;
            descripcion: string | null;
            users_count: number;
        }>,
);

function deleteRole(role: { id: number; nombre: string }) {
    if (confirm(`¿Estás seguro de eliminar el rol "${role.nombre}"?`)) {
        router.delete(roles.destroy.url({ current_team: teamSlug.value, role: role.id }));
    }
}
</script>

<template>
    <Head title="Roles" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 space-y-6 relative">
        <!-- Hero Header -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-700 via-blue-700 to-pink-700 p-1 animate-slide-up">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-purple-400/20 via-transparent to-transparent"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,_var(--tw-gradient-stops))] from-pink-400/20 via-transparent to-transparent"></div>
            <div class="absolute -top-20 -right-20 h-40 w-40 rounded-full bg-purple-400/20 blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 h-32 w-32 rounded-full bg-blue-400/20 blur-3xl"></div>

            <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6 rounded-[calc(1.5rem-4px)] bg-[#0B1120]/80 backdrop-blur-sm px-6 py-6 md:px-8 md:py-7">
                <div class="flex items-start gap-5">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-500/30 to-blue-500/30 ring-1 ring-white/20 shadow-lg shadow-purple-500/20">
                        <Shield class="h-8 w-8 text-purple-300" />
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Roles</h1>
                        <p class="mt-1.5 text-sm md:text-base text-purple-200/80 max-w-xl">
                            Define los roles del sistema y asigna permisos a cada perfil de usuario.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 rounded-2xl bg-white/5 px-5 py-3 ring-1 ring-white/10 shadow-lg">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500/30 to-pink-500/30">
                            <ShieldCheck class="h-5 w-5 text-purple-300" />
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-purple-300/70">Total</p>
                            <p class="text-xl font-bold text-white">{{ roles_data.length }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Role Card + Table -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Create Role Form -->
            <div class="glass-card rounded-2xl p-6 lg:p-8 animate-slide-up order-2 lg:order-1" style="animation-delay: 0.1s">
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/30 to-purple-500/30">
                        <Plus class="h-5 w-5 text-blue-300" />
                    </div>
                    <h2 class="text-lg font-semibold text-white">Crear nuevo rol</h2>
                </div>

                <Form
                    v-if="teamSlug"
                    v-bind="roles.store.form(teamSlug)"
                    class="space-y-5"
                    #default="{ errors, processing }"
                >
                    <div class="space-y-2">
                        <Label for="nombre" class="text-sm font-medium text-white/80">Nombre del rol</Label>
                        <div class="relative">
                            <ShieldCheck class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-white/40" />
                            <Input
                                id="nombre"
                                name="nombre"
                                type="text"
                                required
                                placeholder="Ej: Editor"
                                class="pl-10 bg-white/5 border-white/10 text-white placeholder:text-white/40 focus:border-purple-500/50 focus:ring-purple-500/20"
                            />
                        </div>
                        <InputError :message="errors.nombre" />
                    </div>

                    <div class="space-y-2">
                        <Label for="descripcion" class="text-sm font-medium text-white/80">Descripción</Label>
                        <div class="relative">
                            <Sparkles class="absolute left-3 top-3 h-4 w-4 text-white/40" />
                            <textarea
                                id="descripcion"
                                name="descripcion"
                                rows="3"
                                placeholder="Describe el rol..."
                                class="flex w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 pl-10 text-sm text-white placeholder:text-white/40 focus:border-purple-500/50 focus:ring-purple-500/20 focus:outline-none transition-colors"
                            ></textarea>
                        </div>
                        <InputError :message="errors.descripcion" />
                    </div>

                    <Button type="submit" :disabled="processing" class="btn-gradient-purple w-full h-11 text-sm font-semibold rounded-xl shadow-lg shadow-purple-500/20 hover:shadow-purple-500/30">
                        <Plus class="mr-2 h-4 w-4" />
                        Crear rol
                    </Button>
                </Form>

                <div v-else class="flex items-center justify-center py-8">
                    <p class="text-sm text-white/40">Cargando...</p>
                </div>
            </div>

            <!-- Table -->
            <div class="lg:col-span-2 glass-card rounded-2xl p-6 overflow-hidden animate-slide-up order-1 lg:order-2">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-purple-500/30 to-pink-500/30">
                            <Shield class="h-4 w-4 text-purple-300" />
                        </div>
                        <h2 class="text-base font-semibold text-white">Roles del sistema</h2>
                    </div>
                </div>

                <div v-if="teamSlug && roles_data.length > 0" class="overflow-x-auto">
                    <table class="table-glow w-full">
                        <thead>
                            <tr class="border-b border-white/10">
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50 w-12">#</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">Nombre</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/50">Descripción</th>
                                <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-white/50">Usuarios</th>
                                <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-white/50">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr
                                v-for="(role, index) in roles_data"
                                :key="role.id"
                                class="transition-all duration-200 hover:bg-white/[0.03] group"
                            >
                                <td class="px-5 py-4 text-sm font-mono text-white/40">
                                    {{ String(index + 1).padStart(2, '0') }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-lg transition-all duration-300 group-hover:scale-110"
                                            :class="{
                                                'bg-gradient-to-br from-purple-500/30 to-blue-500/30': role.nombre === 'administrador',
                                                'bg-gradient-to-br from-cyan-500/30 to-blue-500/30': role.nombre === 'disenador',
                                                'bg-gradient-to-br from-green-500/30 to-teal-500/30': role.nombre === 'asistente',
                                                'bg-gradient-to-br from-orange-500/30 to-pink-500/30': role.nombre === 'recepcionista',
                                                'bg-gradient-to-br from-indigo-500/30 to-purple-500/30': !['administrador', 'disenador', 'asistente', 'recepcionista'].includes(role.nombre),
                                            }"
                                        >
                                            <ShieldCheck
                                                class="h-4 w-4"
                                                :class="{
                                                    'text-purple-300': role.nombre === 'administrador',
                                                    'text-cyan-300': role.nombre === 'disenador',
                                                    'text-green-300': role.nombre === 'asistente',
                                                    'text-orange-300': role.nombre === 'recepcionista',
                                                    'text-indigo-300': !['administrador', 'disenador', 'asistente', 'recepcionista'].includes(role.nombre),
                                                }"
                                            />
                                        </div>
                                        <span class="text-sm font-medium text-white capitalize">{{ role.nombre }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-white/60 max-w-xs truncate">
                                    {{ role.descripcion || '—' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium transition-all duration-200"
                                        :class="{
                                            'badge-purple': role.users_count > 5,
                                            'badge-blue': role.users_count >= 1 && role.users_count <= 5,
                                            'badge-gray': role.users_count === 0,
                                        }"
                                    >
                                        <Users class="h-3 w-3" />
                                        {{ role.users_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-1">
                                        <Button size="sm" class="btn-gradient-ghost" as-child>
                                            <Link
                                                :href="
                                                    roles.edit({
                                                        current_team: teamSlug,
                                                        role: role.id,
                                                    }).url
                                                "
                                            >
                                                <Pencil class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button
                                            size="sm"
                                            class="btn-gradient-ghost text-red-400 hover:text-red-300 hover:border-red-500/30"
                                            @click="deleteRole(role)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="teamSlug && roles_data.length === 0"
                    class="flex flex-col items-center justify-center py-16 text-center"
                >
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-500/20 to-blue-500/20 ring-1 ring-white/10">
                        <Shield class="h-7 w-7 text-purple-300/50" />
                    </div>
                    <p class="text-base font-medium text-white/60">No hay roles registrados</p>
                    <p class="mt-1 text-sm text-white/40">Crea el primer rol para comenzar</p>
                </div>

                <div
                    v-if="!teamSlug"
                    class="flex flex-col items-center justify-center py-16 text-center"
                >
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/5 ring-1 ring-white/10">
                        <svg class="h-7 w-7 text-white/40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <p class="text-base font-medium text-white/60">Cargando...</p>
                </div>
            </div>
        </div>

        <!-- Mobile Cards -->
        <div v-if="teamSlug && roles_data.length > 0" class="space-y-3 lg:hidden animate-slide-up">
            <div
                v-for="(role, index) in roles_data"
                :key="role.id"
                class="glass-card rounded-2xl p-4 transition-all duration-200 hover:border-purple-500/30"
                style="animation-delay: 0.1s"
            >
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg"
                            :class="{
                                'bg-gradient-to-br from-purple-500/30 to-blue-500/30': role.nombre === 'administrador',
                                'bg-gradient-to-br from-cyan-500/30 to-blue-500/30': role.nombre === 'disenador',
                                'bg-gradient-to-br from-green-500/30 to-teal-500/30': role.nombre === 'asistente',
                                'bg-gradient-to-br from-orange-500/30 to-pink-500/30': role.nombre === 'recepcionista',
                                'bg-gradient-to-br from-indigo-500/30 to-purple-500/30': !['administrador', 'disenador', 'asistente', 'recepcionista'].includes(role.nombre),
                            }"
                        >
                            <ShieldCheck class="h-4 w-4 text-white/80" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-white capitalize">{{ role.nombre }}</h3>
                            <p class="text-xs text-white/50">{{ index + 1 }}. rol del sistema</p>
                        </div>
                    </div>
                    <span
                        class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium"
                        :class="{
                            'badge-purple': role.users_count > 5,
                            'badge-blue': role.users_count >= 1 && role.users_count <= 5,
                            'badge-gray': role.users_count === 0,
                        }"
                    >
                        <Users class="h-3 w-3" />
                        {{ role.users_count }}
                    </span>
                </div>

                <p class="text-sm text-white/60 mb-4">{{ role.descripcion || 'Sin descripción' }}</p>

                <div class="flex justify-end gap-1 border-t border-white/10 pt-3">
                    <Button size="sm" class="btn-gradient-ghost" as-child>
                        <Link
                            :href="
                                roles.edit({
                                    current_team: teamSlug,
                                    role: role.id,
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
                        @click="deleteRole(role)"
                    >
                        <Trash2 class="mr-1.5 h-4 w-4" />
                        Eliminar
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
