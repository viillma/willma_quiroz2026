<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';

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

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage();

const roles = computed(
    () =>
        (page.props.roles as Array<{
            id: number;
            nombre: string;
            descripcion: string;
        }>) ?? [],
);
</script>

<template>
    <Head title="Nuevo usuario" />

    <div class="animate-fade-in relative max-w-2xl space-y-6 p-4 md:p-6 lg:p-8">
        <Heading
            title="Nuevo usuario"
            description="Crea un nuevo usuario en el sistema"
        />

        <div class="glass-card animate-scale-in rounded-2xl p-6 md:p-8">
            <div
                class="mb-7 flex items-center gap-3 border-b border-ink/10 pb-5"
            >
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/30 to-purple-500/30 shadow-lg shadow-blue-500/10"
                >
                    <svg
                        class="h-5 w-5 text-blue-600"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"
                        />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-ink">
                        Datos del usuario
                    </h2>
                    <p class="text-xs text-ink/60">
                        Completa los campos para registrar un nuevo usuario
                    </p>
                </div>
            </div>

            <Form
                v-bind="usuarios.store.form()"
                method="post"
                class="flex flex-col gap-6"
            >
                <div class="grid gap-2">
                    <Label class="text-sm font-medium text-ink/75" for="name"
                        >Nombre</Label
                    >
                    <Input
                        id="name"
                        name="name"
                        type="text"
                        required
                        autofocus
                        placeholder="Nombre completo"
                        class="border-ink/10 bg-ink/5 text-ink placeholder:text-ink/50 focus:border-blue-500/50 focus:ring-blue-500/20"
                    />
                    <InputError :message="$page.props.errors?.name" />
                </div>

                <div class="grid gap-2">
                    <Label class="text-sm font-medium text-ink/75" for="email"
                        >Email</Label
                    >
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        required
                        placeholder="email@ejemplo.com"
                        class="border-ink/10 bg-ink/5 text-ink placeholder:text-ink/50 focus:border-blue-500/50 focus:ring-blue-500/20"
                    />
                    <InputError :message="$page.props.errors?.email" />
                </div>

                <div class="grid gap-2">
                    <Label
                        class="text-sm font-medium text-ink/75"
                        for="password"
                        >Contraseña</Label
                    >
                    <PasswordInput
                        id="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Contraseña"
                    />
                    <InputError :message="$page.props.errors?.password" />
                </div>

                <div class="grid gap-2">
                    <Label
                        class="text-sm font-medium text-ink/75"
                        for="password_confirmation"
                        >Confirmar contraseña</Label
                    >
                    <PasswordInput
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirmar contraseña"
                    />
                </div>

                <div class="grid gap-2">
                    <Label class="text-sm font-medium text-ink/75" for="role"
                        >Rol</Label
                    >
                    <Select name="role">
                        <SelectTrigger class="border-ink/10 bg-ink/5 text-ink">
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

                <div
                    class="flex items-center gap-3 border-t border-ink/10 pt-3"
                >
                    <Button
                        type="submit"
                        class="btn-gradient-blue h-11 rounded-xl px-6 text-sm font-semibold shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30"
                    >
                        Crear usuario
                    </Button>
                </div>
            </Form>
        </div>
    </div>
</template>
