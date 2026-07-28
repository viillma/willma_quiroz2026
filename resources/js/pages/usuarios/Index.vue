<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Edit, Plus, Shield, Trash2, ToggleLeft, ToggleRight } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { usePermissions } from '@/composables/usePermissions';
import usuarios from '@/routes/usuarios';
import type { Team } from '@/types';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage();
const { can } = usePermissions();

const teamSlug = ref<string>('');

watch(
    () => page.props.currentTeam,
    (team) => {
        teamSlug.value = (team as Team | null)?.slug ?? '';
    },
    { immediate: true },
);

const users = computed(() =>
    (page.props.users as Array<{
        id: number;
        name: string;
        email: string;
        email_verified_at: string | null;
        activo: boolean;
        roles: string[];
        created_at: string;
    }>) ?? [],
);

const roles = computed(() =>
    (page.props.roles as Array<{
        id: number;
        nombre: string;
        descripcion: string;
    }>) ?? [],
);

function toggleActive(user: { id: number; name: string; activo: boolean }) {
    if (!teamSlug.value) return;

    const action = user.activo ? 'desactivar' : 'activar';
    if (!confirm(`¿Estás seguro de ${action} a "${user.name}"?`)) return;

    router.post(usuarios.toggleActive({ current_team: teamSlug.value, user: user.id }).url);
}

function deleteUser(user: { id: number; name: string; roles: string[] }) {
    if (!teamSlug.value) return;

    if (!confirm(`¿Estás seguro de eliminar a "${user.name}"? Esta acción no se puede deshacer.`)) return;

    router.delete(usuarios.destroy({ current_team: teamSlug.value, user: user.id }).url);
}
</script>

<template>
    <Head title="Usuarios" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 space-y-6 relative">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Heading
                title="Usuarios"
                description="Gestiona los usuarios del sistema"
            />
            <Button
                v-if="teamSlug && can('usuarios.gestionar')"
                as-child
                class="btn-gradient-blue self-start sm:self-auto"
            >
                <Link :href="usuarios.create(teamSlug).url">
                    <Plus class="mr-2 h-4 w-4" />
                    Nuevo usuario
                </Link>
            </Button>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="kpi-blue rounded-2xl p-5 kpi-card shadow-lg glow-blue animate-slide-up">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-white/80">Total Usuarios</p>
                    <Shield class="h-5 w-5 text-white/70" />
                </div>
                <div class="text-3xl font-bold tracking-tight text-white">{{ users.length }}</div>
                <p class="mt-1 text-xs text-white/60">Usuarios registrados en el sistema</p>
            </div>
            <div class="kpi-purple rounded-2xl p-5 kpi-card shadow-lg glow-purple animate-slide-up" style="animation-delay: 0.1s">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-white/80">Activos</p>
                    <ToggleRight class="h-5 w-5 text-white/70" />
                </div>
                <div class="text-3xl font-bold tracking-tight text-white">
                    {{ users.filter(u => u.activo).length }}
                </div>
                <p class="mt-1 text-xs text-white/60">Usuarios con acceso activo</p>
            </div>
            <div class="kpi-pink rounded-2xl p-5 kpi-card shadow-lg glow-pink animate-slide-up" style="animation-delay: 0.2s">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-white/80">Inactivos</p>
                    <ToggleLeft class="h-5 w-5 text-white/70" />
                </div>
                <div class="text-3xl font-bold tracking-tight text-white">
                    {{ users.filter(u => !u.activo).length }}
                </div>
                <p class="mt-1 text-xs text-white/60">Usuarios sin acceso</p>
            </div>
            <div class="kpi-cyan rounded-2xl p-5 kpi-card shadow-lg glow-cyan animate-slide-up" style="animation-delay: 0.3s">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-white/80">Roles</p>
                    <Shield class="h-5 w-5 text-white/70" />
                </div>
                <div class="text-3xl font-bold tracking-tight text-white">
                    {{ roles.length }}
                </div>
                <p class="mt-1 text-xs text-white/60">Roles disponibles</p>
            </div>
        </div>

        <!-- Users Table Card -->
        <div class="glass-card rounded-2xl overflow-hidden animate-slide-up">
            <div class="flex items-center gap-2 border-b border-white/10 bg-white/5 px-6 py-4">
                <Shield class="h-5 w-5 text-white/70" />
                <h2 class="text-lg font-semibold text-white">Usuarios registrados</h2>
            </div>
            <div>
                <!-- Mobile: Stacked Cards -->
                <div class="space-y-3 p-4 md:hidden">
                    <div
                        v-for="user in users"
                        :key="user.id"
                        class="glass rounded-2xl p-4 transition-all duration-200"
                        :class="{ 'opacity-60': !user.activo }"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-white">{{ user.name }}</span>
                            <div class="flex items-center gap-1">
                                <Button
                                    v-if="teamSlug"
                                    variant="ghost"
                                    size="icon"
                                    :title="user.activo ? 'Desactivar' : 'Activar'"
                                    @click="toggleActive(user)"
                                    class="btn-gradient-ghost"
                                >
                                    <ToggleLeft v-if="user.activo" class="h-4 w-4 text-amber-400" />
                                    <ToggleRight v-else class="h-4 w-4 text-green-400" />
                                </Button>
                                <Button
                                    v-if="teamSlug"
                                    variant="ghost"
                                    size="icon"
                                    title="Editar"
                                    as-child
                                    class="btn-gradient-ghost"
                                >
                                    <Link :href="usuarios.edit({ current_team: teamSlug, user: user.id }).url">
                                        <Edit class="h-4 w-4" />
                                    </Link>
                                </Button>
                                <Button
                                    v-if="teamSlug"
                                    variant="ghost"
                                    size="icon"
                                    title="Eliminar"
                                    @click="deleteUser(user)"
                                    class="btn-gradient-ghost"
                                >
                                    <Trash2 class="h-4 w-4 text-red-400" />
                                </Button>
                            </div>
                        </div>
                        <div class="text-sm text-white/60">
                            {{ user.email }}
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="flex flex-wrap gap-1">
                                <Badge
                                    v-for="(role, roleIdx) in user.roles"
                                    :key="role"
                                    :class="role === 'administrador' ? 'badge-purple' : role === 'disenador' ? 'badge-cyan' : role === 'asistente' ? 'badge-green' : role === 'recepcionista' ? 'badge-orange' : role === 'superadmin' ? 'badge-pink' : role === 'admin' ? 'badge-blue' : role === 'supervisor' ? 'badge-yellow' : 'badge-gray'"
                                    class="transition-all duration-300 hover:scale-105"
                                >
                                    {{ role }}
                                </Badge>
                                <span
                                    v-if="!user.roles.length"
                                    class="text-sm text-white/60"
                                >
                                    Sin rol
                                </span>
                            </div>
                            <Badge
                                :class="user.activo ? 'badge-green' : 'badge-orange'"
                                class="transition-all duration-300 hover:scale-105"
                            >
                                {{ user.activo ? 'Activo' : 'Inactivo' }}
                            </Badge>
                            <Badge
                                :class="user.email_verified_at ? 'badge-blue' : 'badge-gray'"
                                class="transition-all duration-300 hover:scale-105"
                            >
                                {{ user.email_verified_at ? 'Sí' : 'No' }}
                            </Badge>
                            <span class="text-xs text-white/40">
                                {{ new Date(user.created_at).toLocaleDateString() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Desktop: Table -->
                <div class="hidden overflow-x-auto md:block">
                    <Table class="table-glow w-full">
                        <TableHeader>
                            <TableRow class="bg-white/5">
                                <TableHead class="py-3.5 font-semibold text-white/70">Nombre</TableHead>
                                <TableHead class="py-3.5 font-semibold text-white/70">Email</TableHead>
                                <TableHead class="py-3.5 font-semibold text-white/70">Roles</TableHead>
                                <TableHead class="py-3.5 font-semibold text-white/70">Estado</TableHead>
                                <TableHead class="py-3.5 font-semibold text-white/70">Verificado</TableHead>
                                <TableHead class="py-3.5 font-semibold text-white/70">Registro</TableHead>
                                <TableHead class="py-3.5 text-right font-semibold text-white/70">Acciones</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="user in users"
                                :key="user.id"
                                class="transition-all duration-200 hover:bg-white/5"
                                :class="{
                                    'opacity-60': !user.activo,
                                }"
                            >
                                <TableCell class="py-3.5 font-medium text-white">{{ user.name }}</TableCell>
                                <TableCell class="py-3.5 text-white/80">{{ user.email }}</TableCell>
                                <TableCell class="py-3.5">
                                    <div class="flex flex-wrap gap-1">
                                        <Badge
                                            v-for="(role, roleIdx) in user.roles"
                                            :key="role"
                                            :class="role === 'administrador' ? 'badge-purple' : role === 'disenador' ? 'badge-cyan' : role === 'asistente' ? 'badge-green' : role === 'recepcionista' ? 'badge-orange' : role === 'superadmin' ? 'badge-pink' : role === 'admin' ? 'badge-blue' : role === 'supervisor' ? 'badge-yellow' : 'badge-gray'"
                                            class="transition-all duration-300 hover:scale-105"
                                        >
                                            {{ role }}
                                        </Badge>
                                        <span
                                            v-if="!user.roles.length"
                                            class="text-sm text-white/60"
                                        >
                                            Sin rol
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell class="py-3.5">
                                    <Badge
                                        :class="user.activo ? 'badge-green' : 'badge-orange'"
                                        class="transition-all duration-300 hover:scale-105"
                                    >
                                        {{ user.activo ? 'Activo' : 'Inactivo' }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="py-3.5">
                                    <Badge
                                        :class="user.email_verified_at ? 'badge-blue' : 'badge-gray'"
                                        class="transition-all duration-300 hover:scale-105"
                                    >
                                        {{ user.email_verified_at ? 'Sí' : 'No' }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="py-3.5 text-sm text-white/60">
                                    {{ new Date(user.created_at).toLocaleDateString() }}
                                </TableCell>
                                <TableCell class="py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button
                                            v-if="teamSlug"
                                            variant="ghost"
                                            size="icon"
                                            :title="user.activo ? 'Desactivar' : 'Activar'"
                                            @click="toggleActive(user)"
                                            class="btn-gradient-ghost"
                                        >
                                            <ToggleLeft v-if="user.activo" class="h-4 w-4 text-amber-400" />
                                            <ToggleRight v-else class="h-4 w-4 text-green-400" />
                                        </Button>
                                        <Button
                                            v-if="teamSlug"
                                            variant="ghost"
                                            size="icon"
                                            title="Editar"
                                            as-child
                                            class="btn-gradient-ghost"
                                        >
                                            <Link :href="usuarios.edit({ current_team: teamSlug, user: user.id }).url">
                                                <Edit class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button
                                            v-if="teamSlug"
                                            variant="ghost"
                                            size="icon"
                                            title="Eliminar"
                                            @click="deleteUser(user)"
                                            class="btn-gradient-ghost"
                                        >
                                            <Trash2 class="h-4 w-4 text-red-400" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </div>
    </div>
</template>
