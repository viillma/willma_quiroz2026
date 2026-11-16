<script setup lang="ts">
import { Form, Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Shield,
    Plus,
    Pencil,
    Trash2,
    Users,
    ShieldCheck,
    Sparkles,
} from 'lucide-vue-next';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import roles from '@/routes/roles';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage();

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
        router.delete(roles.destroy.url({ role: role.id }));
    }
}
</script>

<template>
    <Head title="Roles" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <!-- Hero Header -->
        <div
            class="animate-slide-up relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-700 via-blue-700 to-pink-700 p-1"
        >
            <div
                class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-purple-400/20 via-transparent to-transparent"
            ></div>
            <div
                class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,_var(--tw-gradient-stops))] from-pink-400/20 via-transparent to-transparent"
            ></div>
            <div
                class="absolute -top-20 -right-20 h-40 w-40 rounded-full bg-purple-400/20 blur-3xl"
            ></div>
            <div
                class="absolute -bottom-10 -left-10 h-32 w-32 rounded-full bg-blue-400/20 blur-3xl"
            ></div>

            <div
                class="relative flex flex-col justify-between gap-6 rounded-[calc(1.5rem-4px)] bg-[#0B1120]/80 px-6 py-6 backdrop-blur-sm md:flex-row md:items-center md:px-8 md:py-7"
            >
                <div class="flex items-start gap-5">
                    <div
                        class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-500/30 to-blue-500/30 shadow-lg ring-1 shadow-purple-500/20 ring-white/20"
                    >
                        <Shield class="h-8 w-8 text-purple-300" />
                    </div>
                    <div>
                        <h1
                            class="text-2xl font-bold tracking-tight text-white md:text-3xl"
                        >
                            Roles
                        </h1>
                        <p
                            class="mt-1.5 max-w-xl text-sm text-purple-200/80 md:text-base"
                        >
                            Define los roles del sistema y asigna permisos a
                            cada perfil de usuario.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center gap-3 rounded-2xl bg-white/5 px-5 py-3 shadow-lg ring-1 ring-white/10"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500/30 to-pink-500/30"
                        >
                            <ShieldCheck class="h-5 w-5 text-purple-300" />
                        </div>
                        <div>
                            <p
                                class="text-xs font-semibold tracking-wider text-purple-300/70 uppercase"
                            >
                                Total
                            </p>
                            <p class="text-xl font-bold text-white">
                                {{ roles_data.length }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Role Card + Table -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Create Role Form -->
            <div
                class="glass-card animate-slide-up order-2 rounded-2xl p-6 lg:order-1 lg:p-8"
                style="animation-delay: 0.1s"
            >
                <div class="mb-6 flex items-center gap-3">
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/30 to-purple-500/30"
                    >
                        <Plus class="h-5 w-5 text-blue-600" />
                    </div>
                    <h2 class="text-lg font-bold text-ink">Crear nuevo rol</h2>
                </div>

                <Form
                    v-bind="roles.store.form()"
                    class="space-y-5"
                    #default="{ errors, processing }"
                >
                    <div class="space-y-2">
                        <Label
                            for="nombre"
                            class="text-sm font-medium text-ink/75"
                            >Nombre del rol</Label
                        >
                        <div class="relative">
                            <ShieldCheck
                                class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink/50"
                            />
                            <Input
                                id="nombre"
                                name="nombre"
                                type="text"
                                required
                                placeholder="Ej: Editor"
                                class="border-ink/10 bg-ink/5 pl-10 text-ink placeholder:text-ink/50 focus:border-purple-500/50 focus:ring-purple-500/20"
                            />
                        </div>
                        <InputError :message="errors.nombre" />
                    </div>

                    <div class="space-y-2">
                        <Label
                            for="descripcion"
                            class="text-sm font-medium text-ink/75"
                            >Descripción</Label
                        >
                        <div class="relative">
                            <Sparkles
                                class="absolute top-3 left-3 h-4 w-4 text-ink/50"
                            />
                            <textarea
                                id="descripcion"
                                name="descripcion"
                                rows="3"
                                placeholder="Describe el rol..."
                                class="flex w-full rounded-xl border border-ink/10 bg-ink/5 px-3 py-2.5 pl-10 text-sm text-ink transition-colors placeholder:text-ink/50 focus:border-purple-500/50 focus:ring-purple-500/20 focus:outline-none"
                            ></textarea>
                        </div>
                        <InputError :message="errors.descripcion" />
                    </div>

                    <Button
                        type="submit"
                        :disabled="processing"
                        class="btn-gradient-purple h-11 w-full rounded-xl text-sm font-semibold shadow-lg shadow-purple-500/20 hover:shadow-purple-500/30"
                    >
                        <Plus class="mr-2 h-4 w-4" />
                        Crear rol
                    </Button>
                </Form>
            </div>

            <!-- Table -->
            <div
                class="glass-card animate-slide-up order-1 overflow-hidden rounded-2xl p-6 lg:order-2 lg:col-span-2"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-purple-500/30 to-pink-500/30"
                        >
                            <Shield class="h-4 w-4 text-purple-600" />
                        </div>
                        <h2 class="text-base font-bold text-ink">
                            Roles del sistema
                        </h2>
                    </div>
                </div>

                <div v-if="roles_data.length > 0" class="overflow-x-auto">
                    <table class="table-glow w-full">
                        <thead>
                            <tr class="border-b border-ink/10">
                                <th
                                    class="w-12 px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/60 uppercase"
                                >
                                    #
                                </th>
                                <th
                                    class="px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/60 uppercase"
                                >
                                    Nombre
                                </th>
                                <th
                                    class="px-5 py-4 text-left text-xs font-bold tracking-wider text-ink/60 uppercase"
                                >
                                    Descripción
                                </th>
                                <th
                                    class="px-5 py-4 text-center text-xs font-bold tracking-wider text-ink/60 uppercase"
                                >
                                    Usuarios
                                </th>
                                <th
                                    class="px-5 py-4 text-right text-xs font-bold tracking-wider text-ink/60 uppercase"
                                >
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ink/5">
                            <tr
                                v-for="(role, index) in roles_data"
                                :key="role.id"
                                class="group transition-all duration-200 hover:bg-pink-50/70"
                            >
                                <td
                                    class="px-5 py-4 font-mono text-sm text-ink/50"
                                >
                                    {{ String(index + 1).padStart(2, '0') }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-lg transition-all duration-300 group-hover:scale-110"
                                            :class="{
                                                'bg-gradient-to-br from-purple-500/30 to-blue-500/30':
                                                    role.nombre ===
                                                    'administrador',
                                                'bg-gradient-to-br from-cyan-500/30 to-blue-500/30':
                                                    role.nombre === 'disenador',
                                                'bg-gradient-to-br from-green-500/30 to-teal-500/30':
                                                    role.nombre === 'asistente',
                                                'bg-gradient-to-br from-orange-500/30 to-pink-500/30':
                                                    role.nombre ===
                                                    'recepcionista',
                                                'bg-gradient-to-br from-indigo-500/30 to-purple-500/30':
                                                    ![
                                                        'administrador',
                                                        'disenador',
                                                        'asistente',
                                                        'recepcionista',
                                                    ].includes(role.nombre),
                                            }"
                                        >
                                            <ShieldCheck
                                                class="h-4 w-4"
                                                :class="{
                                                    'text-purple-600':
                                                        role.nombre ===
                                                        'administrador',
                                                    'text-cyan-600':
                                                        role.nombre ===
                                                        'disenador',
                                                    'text-green-600':
                                                        role.nombre ===
                                                        'asistente',
                                                    'text-orange-600':
                                                        role.nombre ===
                                                        'recepcionista',
                                                    'text-indigo-600': ![
                                                        'administrador',
                                                        'disenador',
                                                        'asistente',
                                                        'recepcionista',
                                                    ].includes(role.nombre),
                                                }"
                                            />
                                        </div>
                                        <span
                                            class="text-sm font-semibold text-ink capitalize"
                                            >{{ role.nombre }}</span
                                        >
                                    </div>
                                </td>
                                <td
                                    class="max-w-xs truncate px-5 py-4 text-sm text-ink/70"
                                >
                                    {{ role.descripcion || '—' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium transition-all duration-200"
                                        :class="{
                                            'badge-purple':
                                                role.users_count > 5,
                                            'badge-blue':
                                                role.users_count >= 1 &&
                                                role.users_count <= 5,
                                            'badge-gray':
                                                role.users_count === 0,
                                        }"
                                    >
                                        <Users class="h-3 w-3" />
                                        {{ role.users_count }}
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
                                                :href="roles.edit(role.id).url"
                                            >
                                                <Pencil class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button
                                            size="sm"
                                            class="btn-gradient-ghost text-red-600 hover:border-red-500/30 hover:text-red-700"
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
                    v-if="roles_data.length === 0"
                    class="flex flex-col items-center justify-center py-16 text-center"
                >
                    <div
                        class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-500/20 to-blue-500/20 ring-1 ring-ink/10"
                    >
                        <Shield class="h-7 w-7 text-purple-600" />
                    </div>
                    <p class="text-base font-medium text-ink/70">
                        No hay roles registrados
                    </p>
                    <p class="mt-1 text-sm text-ink/50">
                        Crea el primer rol para comenzar
                    </p>
                </div>
            </div>
        </div>

        <!-- Mobile Cards -->
        <div
            v-if="roles_data.length > 0"
            class="animate-slide-up space-y-3 lg:hidden"
        >
            <div
                v-for="(role, index) in roles_data"
                :key="role.id"
                class="glass-card rounded-2xl p-4 transition-all duration-200 hover:border-purple-500/30"
                style="animation-delay: 0.1s"
            >
                <div class="mb-3 flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg"
                            :class="{
                                'bg-gradient-to-br from-purple-500/30 to-blue-500/30':
                                    role.nombre === 'administrador',
                                'bg-gradient-to-br from-cyan-500/30 to-blue-500/30':
                                    role.nombre === 'disenador',
                                'bg-gradient-to-br from-green-500/30 to-teal-500/30':
                                    role.nombre === 'asistente',
                                'bg-gradient-to-br from-orange-500/30 to-pink-500/30':
                                    role.nombre === 'recepcionista',
                                'bg-gradient-to-br from-indigo-500/30 to-purple-500/30':
                                    ![
                                        'administrador',
                                        'disenador',
                                        'asistente',
                                        'recepcionista',
                                    ].includes(role.nombre),
                            }"
                        >
                            <ShieldCheck class="h-4 w-4 text-ink/75" />
                        </div>
                        <div>
                            <h3 class="font-bold text-ink capitalize">
                                {{ role.nombre }}
                            </h3>
                            <p class="text-xs text-ink/60">
                                {{ index + 1 }}. rol del sistema
                            </p>
                        </div>
                    </div>
                    <span
                        class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium"
                        :class="{
                            'badge-purple': role.users_count > 5,
                            'badge-blue':
                                role.users_count >= 1 && role.users_count <= 5,
                            'badge-gray': role.users_count === 0,
                        }"
                    >
                        <Users class="h-3 w-3" />
                        {{ role.users_count }}
                    </span>
                </div>

                <p class="mb-4 text-sm text-ink/70">
                    {{ role.descripcion || 'Sin descripción' }}
                </p>

                <div class="flex justify-end gap-1 border-t border-ink/10 pt-3">
                    <Button size="sm" class="btn-gradient-ghost" as-child>
                        <Link :href="roles.edit(role.id).url">
                            <Pencil class="mr-1.5 h-4 w-4" />
                            Editar
                        </Link>
                    </Button>
                    <Button
                        size="sm"
                        class="btn-gradient-ghost text-red-600 hover:text-red-700"
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
