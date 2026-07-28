<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import usuarios from '@/routes/usuarios';
import type { Team } from '@/types';

defineOptions({
    layout: AppSidebarLayout,
});

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        activo: boolean;
        roles: string[];
    };
    roles: Array<{
        id: number;
        nombre: string;
        descripcion: string;
    }>;
}>();

const page = usePage();

const teamSlug = ref<string>('');

watch(
    () => page.props.currentTeam,
    (team) => {
        teamSlug.value = (team as Team | null)?.slug ?? '';
    },
    { immediate: true },
);
</script>

<template>
    <Head :title="`Editar: ${user.name}`" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 max-w-2xl space-y-6 relative">
        <div class="flex items-center gap-2">
            <Button variant="outline" size="icon" as-child class="btn-gradient-ghost">
                <Link :href="teamSlug ? usuarios.index(teamSlug).url : '#'">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
            </Button>
            <Heading
                :title="`Editar: ${user.name}`"
                description="Modifica los datos del usuario"
            />
        </div>

        <div class="glass-card rounded-2xl p-6 md:p-8 animate-scale-in">
            <div class="flex items-center gap-3 mb-7 pb-5 border-b border-white/10">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/30 to-purple-500/30 shadow-lg shadow-blue-500/10">
                    <svg class="h-5 w-5 text-blue-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-white">Datos del usuario</h2>
                    <p class="text-xs text-white/50">Actualiza los campos que deseas modificar</p>
                </div>
            </div>

            <Form
                v-if="teamSlug"
                v-bind="usuarios.update(teamSlug, user.id).form()"
                method="put"
                :defaults="{
                    name: user.name,
                    email: user.email,
                    role: user.roles[0] ?? '',
                    activo: user.activo ? '1' : '0',
                    password: '',
                    password_confirmation: '',
                }"
                class="flex flex-col gap-6"
            >
                <div class="grid gap-2">
                    <Label class="text-sm font-medium text-white/80" for="name">Nombre</Label>
                    <Input
                        id="name"
                        name="name"
                        type="text"
                        required
                        autofocus
                        :default-value="user.name"
                        class="bg-white/5 border-white/10 text-white placeholder:text-white/40 focus:border-blue-500/50 focus:ring-blue-500/20"
                    />
                    <InputError :message="$page.props.errors?.name" />
                </div>

                <div class="grid gap-2">
                    <Label class="text-sm font-medium text-white/80" for="email">Email</Label>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        required
                        :default-value="user.email"
                        class="bg-white/5 border-white/10 text-white placeholder:text-white/40 focus:border-blue-500/50 focus:ring-blue-500/20"
                    />
                    <InputError :message="$page.props.errors?.email" />
                </div>

                <div class="grid gap-2">
                    <Label class="text-sm font-medium text-white/80" for="password">
                        Nueva contraseña
                        <span class="text-xs text-white/40">(dejar vacío para mantener la actual)</span>
                    </Label>
                    <PasswordInput
                        id="password"
                        name="password"
                        autocomplete="new-password"
                        placeholder="Nueva contraseña"
                    />
                    <InputError :message="$page.props.errors?.password" />
                </div>

                <div class="grid gap-2">
                    <Label class="text-sm font-medium text-white/80" for="password_confirmation">Confirmar nueva contraseña</Label>
                    <PasswordInput
                        id="password_confirmation"
                        name="password_confirmation"
                        autocomplete="new-password"
                        placeholder="Confirmar contraseña"
                    />
                </div>

                <div class="grid gap-2">
                    <Label class="text-sm font-medium text-white/80" for="role">Rol</Label>
                    <Select name="role" :default-value="user.roles[0] ?? ''">
                        <SelectTrigger class="bg-white/5 border-white/10 text-white">
                            <SelectValue placeholder="Seleccionar rol" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="role in roles"
                                :key="role.id"
                                :value="role.nombre"
                            >
                                {{ role.nombre }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="$page.props.errors?.role" />
                </div>

                <div class="grid gap-2">
                    <Label class="flex items-center gap-3 text-sm font-medium text-white/80">
                        <input
                            type="checkbox"
                            name="activo"
                            value="1"
                            class="h-4 w-4 rounded border-white/20 bg-white/10 text-indigo-500 focus:ring-indigo-500"
                            :checked="user.activo"
                        />
                        <span>Usuario activo</span>
                    </Label>
                    <p class="text-sm text-white/40">
                        Los usuarios inactivos no podrán iniciar sesión.
                    </p>
                    <InputError :message="$page.props.errors?.activo" />
                </div>

                <div class="flex items-center gap-3 pt-3 border-t border-white/10">
                    <Button type="submit" class="btn-gradient-blue h-11 px-6 text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30">
                        Guardar cambios
                    </Button>
                    <Button variant="outline" as-child class="btn-gradient-ghost h-11 px-6 rounded-xl">
                        <Link :href="teamSlug ? usuarios.index(teamSlug).url : '#'">Cancelar</Link>
                    </Button>
                </div>
            </Form>
        </div>
    </div>
</template>
