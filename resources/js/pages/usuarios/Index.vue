<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Edit,
    Plus,
    Shield,
    Trash2,
    ToggleLeft,
    ToggleRight,
} from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { usePermissions } from '@/composables/usePermissions';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import usuarios from '@/routes/usuarios';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage();
const { can } = usePermissions();

const users = computed(
    () =>
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

const roles = computed(
    () =>
        (page.props.roles as Array<{
            id: number;
            nombre: string;
            descripcion: string;
        }>) ?? [],
);

function toggleActive(user: { id: number; name: string; activo: boolean }) {
    const action = user.activo ? 'desactivar' : 'activar';

    if (!confirm(`¿Estás seguro de ${action} a "${user.name}"?`)) {
        return;
    }

    router.post(usuarios.toggleActive({ user: user.id }).url);
}

function deleteUser(user: { id: number; name: string; roles: string[] }) {
    if (
        !confirm(
            `¿Estás seguro de eliminar a "${user.name}"? Esta acción no se puede deshacer.`,
        )
    ) {
        return;
    }

    router.delete(usuarios.destroy({ user: user.id }).url);
}
</script>

<template>
    <Head title="Usuarios" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <!-- Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading
                title="Usuarios"
                description="Gestiona los usuarios del sistema"
            />
            <Button
                v-if="can('usuarios.gestionar')"
                as-child
                class="btn-gradient-blue self-start sm:self-auto"
            >
                <Link :href="usuarios.create().url">
                    <Plus class="mr-2 h-4 w-4" />
                    Nuevo usuario
                </Link>
            </Button>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                class="kpi-blue kpi-card glow-blue animate-slide-up rounded-2xl p-5 shadow-lg"
            >
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-ink/75">
                        Total Usuarios
                    </p>
                    <Shield class="h-5 w-5 text-ink/70" />
                </div>
                <div class="text-3xl font-bold tracking-tight text-ink">
                    {{ users.length }}
                </div>
                <p class="mt-1 text-xs text-ink/70">
                    Usuarios registrados en el sistema
                </p>
            </div>
            <div
                class="kpi-purple kpi-card glow-purple animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.1s"
            >
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-ink/75">Activos</p>
                    <ToggleRight class="h-5 w-5 text-ink/70" />
                </div>
                <div class="text-3xl font-bold tracking-tight text-ink">
                    {{ users.filter((u) => u.activo).length }}
                </div>
                <p class="mt-1 text-xs text-ink/70">
                    Usuarios con acceso activo
                </p>
            </div>
            <div
                class="kpi-pink kpi-card glow-pink animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.2s"
            >
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-ink/75">Inactivos</p>
                    <ToggleLeft class="h-5 w-5 text-ink/70" />
                </div>
                <div class="text-3xl font-bold tracking-tight text-ink">
                    {{ users.filter((u) => !u.activo).length }}
                </div>
                <p class="mt-1 text-xs text-ink/70">Usuarios sin acceso</p>
            </div>
            <div
                class="kpi-cyan kpi-card glow-cyan animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.3s"
            >
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-ink/75">Roles</p>
                    <Shield class="h-5 w-5 text-ink/70" />
                </div>
                <div class="text-3xl font-bold tracking-tight text-ink">
                    {{ roles.length }}
                </div>
                <p class="mt-1 text-xs text-ink/70">Roles disponibles</p>
            </div>
        </div>

        <!-- Users Table Card -->
        <div class="glass-card animate-slide-up overflow-hidden rounded-2xl">
            <div
                class="flex items-center gap-2 border-b border-ink/10 bg-ink/5 px-6 py-4"
            >
                <Shield class="h-5 w-5 text-ink/70" />
                <h2 class="text-lg font-bold text-ink">Usuarios registrados</h2>
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
                            <span class="font-semibold text-ink">{{
                                user.name
                            }}</span>
                            <div class="flex items-center gap-1">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    :title="
                                        user.activo ? 'Desactivar' : 'Activar'
                                    "
                                    @click="toggleActive(user)"
                                    class="btn-gradient-ghost"
                                >
                                    <ToggleLeft
                                        v-if="user.activo"
                                        class="h-4 w-4 text-amber-500"
                                    />
                                    <ToggleRight
                                        v-else
                                        class="h-4 w-4 text-green-500"
                                    />
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    title="Editar"
                                    as-child
                                    class="btn-gradient-ghost"
                                >
                                    <Link
                                        :href="
                                            usuarios.edit({ user: user.id }).url
                                        "
                                    >
                                        <Edit class="h-4 w-4" />
                                    </Link>
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    title="Eliminar"
                                    @click="deleteUser(user)"
                                    class="btn-gradient-ghost"
                                >
                                    <Trash2 class="h-4 w-4 text-red-600" />
                                </Button>
                            </div>
                        </div>
                        <div class="text-sm text-ink/70">
                            {{ user.email }}
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="flex flex-wrap gap-1">
                                <Badge
                                    v-for="role in user.roles"
                                    :key="role"
                                    :class="
                                        role === 'administrador'
                                            ? 'badge-purple'
                                            : role === 'disenador'
                                              ? 'badge-cyan'
                                              : role === 'asistente'
                                                ? 'badge-green'
                                                : role === 'recepcionista'
                                                  ? 'badge-orange'
                                                  : role === 'superadmin'
                                                    ? 'badge-pink'
                                                    : role === 'admin'
                                                      ? 'badge-blue'
                                                      : role === 'supervisor'
                                                        ? 'badge-yellow'
                                                        : 'badge-gray'
                                    "
                                    class="transition-all duration-300 hover:scale-105"
                                >
                                    {{ role }}
                                </Badge>
                                <span
                                    v-if="!user.roles.length"
                                    class="text-sm text-ink/70"
                                >
                                    Sin rol
                                </span>
                            </div>
                            <Badge
                                :class="
                                    user.activo ? 'badge-green' : 'badge-orange'
                                "
                                class="transition-all duration-300 hover:scale-105"
                            >
                                {{ user.activo ? 'Activo' : 'Inactivo' }}
                            </Badge>
                            <Badge
                                :class="
                                    user.email_verified_at
                                        ? 'badge-blue'
                                        : 'badge-gray'
                                "
                                class="transition-all duration-300 hover:scale-105"
                            >
                                {{ user.email_verified_at ? 'Sí' : 'No' }}
                            </Badge>
                            <span class="text-xs text-ink/50">
                                {{
                                    new Date(
                                        user.created_at,
                                    ).toLocaleDateString()
                                }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Desktop: Table -->
                <div class="hidden overflow-x-auto md:block">
                    <Table class="table-glow w-full">
                        <TableHeader>
                            <TableRow class="bg-ink/5">
                                <TableHead class="py-3.5 font-bold text-ink/70"
                                    >Nombre</TableHead
                                >
                                <TableHead class="py-3.5 font-bold text-ink/70"
                                    >Email</TableHead
                                >
                                <TableHead class="py-3.5 font-bold text-ink/70"
                                    >Roles</TableHead
                                >
                                <TableHead class="py-3.5 font-bold text-ink/70"
                                    >Estado</TableHead
                                >
                                <TableHead class="py-3.5 font-bold text-ink/70"
                                    >Verificado</TableHead
                                >
                                <TableHead class="py-3.5 font-bold text-ink/70"
                                    >Registro</TableHead
                                >
                                <TableHead
                                    class="py-3.5 text-right font-bold text-ink/70"
                                    >Acciones</TableHead
                                >
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="user in users"
                                :key="user.id"
                                class="transition-all duration-200 hover:bg-pink-50/70"
                                :class="{
                                    'opacity-60': !user.activo,
                                }"
                            >
                                <TableCell
                                    class="py-3.5 font-semibold text-ink"
                                    >{{ user.name }}</TableCell
                                >
                                <TableCell class="py-3.5 text-ink/75">{{
                                    user.email
                                }}</TableCell>
                                <TableCell class="py-3.5">
                                    <div class="flex flex-wrap gap-1">
                                        <Badge
                                            v-for="role in user.roles"
                                            :key="role"
                                            :class="
                                                role === 'administrador'
                                                    ? 'badge-purple'
                                                    : role === 'disenador'
                                                      ? 'badge-cyan'
                                                      : role === 'asistente'
                                                        ? 'badge-green'
                                                        : role ===
                                                            'recepcionista'
                                                          ? 'badge-orange'
                                                          : role ===
                                                              'superadmin'
                                                            ? 'badge-pink'
                                                            : role === 'admin'
                                                              ? 'badge-blue'
                                                              : role ===
                                                                  'supervisor'
                                                                ? 'badge-yellow'
                                                                : 'badge-gray'
                                            "
                                            class="transition-all duration-300 hover:scale-105"
                                        >
                                            {{ role }}
                                        </Badge>
                                        <span
                                            v-if="!user.roles.length"
                                            class="text-sm text-ink/70"
                                        >
                                            Sin rol
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell class="py-3.5">
                                    <Badge
                                        :class="
                                            user.activo
                                                ? 'badge-green'
                                                : 'badge-orange'
                                        "
                                        class="transition-all duration-300 hover:scale-105"
                                    >
                                        {{
                                            user.activo ? 'Activo' : 'Inactivo'
                                        }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="py-3.5">
                                    <Badge
                                        :class="
                                            user.email_verified_at
                                                ? 'badge-blue'
                                                : 'badge-gray'
                                        "
                                        class="transition-all duration-300 hover:scale-105"
                                    >
                                        {{
                                            user.email_verified_at ? 'Sí' : 'No'
                                        }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="py-3.5 text-sm text-ink/70">
                                    {{
                                        new Date(
                                            user.created_at,
                                        ).toLocaleDateString()
                                    }}
                                </TableCell>
                                <TableCell class="py-3.5 text-right">
                                    <div
                                        class="flex items-center justify-end gap-1"
                                    >
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            :title="
                                                user.activo
                                                    ? 'Desactivar'
                                                    : 'Activar'
                                            "
                                            @click="toggleActive(user)"
                                            class="btn-gradient-ghost"
                                        >
                                            <ToggleLeft
                                                v-if="user.activo"
                                                class="h-4 w-4 text-amber-500"
                                            />
                                            <ToggleRight
                                                v-else
                                                class="h-4 w-4 text-green-500"
                                            />
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            title="Editar"
                                            as-child
                                            class="btn-gradient-ghost"
                                        >
                                            <Link
                                                :href="
                                                    usuarios.edit({
                                                        user: user.id,
                                                    }).url
                                                "
                                            >
                                                <Edit class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            title="Eliminar"
                                            @click="deleteUser(user)"
                                            class="btn-gradient-ghost"
                                        >
                                            <Trash2
                                                class="h-4 w-4 text-red-600"
                                            />
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
