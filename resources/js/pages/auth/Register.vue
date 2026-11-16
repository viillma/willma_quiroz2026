<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import {
    User,
    Mail,
    Lock,
    Phone,
    ShieldCheck,
    UserPlus,
} from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';

defineOptions({
    layout: {
        title: 'Crear Cuenta',
        description: 'Regístrate para acceder al panel de diseño',
    },
});
</script>

<template>
    <Head title="Registrarse" />

    <div class="animate-fade-in space-y-6">
        <!-- Title -->
        <div class="animate-slide-up text-center">
            <div
                class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blush to-lilac text-pix shadow-lg shadow-pix/15"
            >
                <UserPlus class="h-6 w-6" />
            </div>
            <span
                class="text-[11px] font-bold tracking-[0.2em] text-pix uppercase"
                >Únete al estudio</span
            >
            <h1
                class="mt-3 font-display text-3xl font-extrabold tracking-tight text-ink"
            >
                Crear Cuenta
            </h1>
            <p class="mt-2 text-sm text-ink/55">
                Ingresa tus datos para registrarte
            </p>
        </div>

        <Form
            action="/register"
            method="post"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-5">
                <!-- Name -->
                <div class="space-y-2">
                    <Label for="name" class="text-sm font-semibold text-ink/80">
                        Nombre completo
                    </Label>
                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5"
                        >
                            <User class="h-4 w-4 text-ink/35" />
                        </div>
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="name"
                            name="name"
                            placeholder="Tu nombre"
                            class="h-11 rounded-xl border-ink/10 bg-warm pl-10 text-ink transition-all duration-200 placeholder:text-ink/35 focus:border-pix/40 focus:bg-white focus:ring-pix/20"
                        />
                    </div>
                    <InputError :message="errors.name" />
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <Label
                        for="email"
                        class="text-sm font-semibold text-ink/80"
                    >
                        Correo electrónico
                    </Label>
                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5"
                        >
                            <Mail class="h-4 w-4 text-ink/35" />
                        </div>
                        <Input
                            id="email"
                            type="email"
                            required
                            :tabindex="2"
                            autocomplete="email"
                            name="email"
                            placeholder="tu@correo.com"
                            class="h-11 rounded-xl border-ink/10 bg-warm pl-10 text-ink transition-all duration-200 placeholder:text-ink/35 focus:border-pix/40 focus:bg-white focus:ring-pix/20"
                        />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <!-- Phone -->
                <div class="space-y-2">
                    <Label
                        for="phone"
                        class="text-sm font-semibold text-ink/80"
                    >
                        Teléfono
                    </Label>
                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5"
                        >
                            <Phone class="h-4 w-4 text-ink/35" />
                        </div>
                        <Input
                            id="phone"
                            type="tel"
                            name="phone"
                            :tabindex="3"
                            autocomplete="tel"
                            placeholder="+591 6XXXXXXXX"
                            class="h-11 rounded-xl border-ink/10 bg-warm pl-10 text-ink transition-all duration-200 placeholder:text-ink/35 focus:border-pix/40 focus:bg-white focus:ring-pix/20"
                        />
                    </div>
                    <InputError :message="errors.phone" />
                </div>

                <!-- Tipo de usuario -->
                <div class="space-y-2">
                    <Label
                        for="tipo_usuario"
                        class="text-sm font-semibold text-ink/80"
                    >
                        Tipo de usuario
                    </Label>
                    <div class="grid gap-2.5">
                        <label
                            v-for="tipo in [
                                {
                                    value: 'recepcionista',
                                    label: 'Recepcionista',
                                    desc: 'Clientes, pedidos y cotizaciones',
                                },
                                {
                                    value: 'empleado',
                                    label: 'Empleado / Diseñador',
                                    desc: 'Pedidos asignados, diseños y archivos',
                                },
                                {
                                    value: 'administrador',
                                    label: 'Administrador',
                                    desc: 'Acceso total al sistema',
                                },
                            ]"
                            :key="tipo.value"
                            class="relative flex cursor-pointer items-center gap-3 rounded-xl border border-ink/10 bg-warm px-4 py-3 transition-all duration-200 has-[:checked]:border-pix/50 has-[:checked]:bg-blush/30"
                        >
                            <input
                                type="radio"
                                name="tipo_usuario"
                                :value="tipo.value"
                                :tabindex="4"
                                class="peer h-4 w-4 shrink-0 accent-pix"
                                :checked="tipo.value === 'recepcionista'"
                            />
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/80 text-pix shadow-sm"
                            >
                                <ShieldCheck class="h-4 w-4" />
                            </span>
                            <span>
                                <span
                                    class="block text-sm font-bold text-ink"
                                    >{{ tipo.label }}</span
                                >
                                <span class="block text-xs text-ink/50">{{
                                    tipo.desc
                                }}</span>
                            </span>
                        </label>
                    </div>
                    <InputError :message="errors.tipo_usuario" />
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <Label
                        for="password"
                        class="text-sm font-semibold text-ink/80"
                    >
                        Contraseña
                    </Label>
                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5"
                        >
                            <Lock class="h-4 w-4 text-ink/35" />
                        </div>
                        <PasswordInput
                            id="password"
                            required
                            :tabindex="5"
                            autocomplete="new-password"
                            name="password"
                            placeholder="••••••••"
                            class="h-11 rounded-xl border-ink/10 bg-warm pl-10 text-ink transition-all duration-200 placeholder:text-ink/35 focus:border-pix/40 focus:bg-white focus:ring-pix/20"
                        />
                    </div>
                    <InputError :message="errors.password" />
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <Label
                        for="password_confirmation"
                        class="text-sm font-semibold text-ink/80"
                    >
                        Confirmar contraseña
                    </Label>
                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5"
                        >
                            <Lock class="h-4 w-4 text-ink/35" />
                        </div>
                        <PasswordInput
                            id="password_confirmation"
                            required
                            :tabindex="6"
                            autocomplete="new-password"
                            name="password_confirmation"
                            placeholder="••••••••"
                            class="h-11 rounded-xl border-ink/10 bg-warm pl-10 text-ink transition-all duration-200 placeholder:text-ink/35 focus:border-pix/40 focus:bg-white focus:ring-pix/20"
                        />
                    </div>
                    <InputError :message="errors.password_confirmation" />
                </div>

                <!-- Submit -->
                <Button
                    type="submit"
                    class="h-12 w-full rounded-xl bg-pix text-base font-bold text-white shadow-lg shadow-pix/25 transition-all duration-300 hover:-translate-y-0.5 hover:bg-pix-2 hover:shadow-xl hover:shadow-pix/30"
                    tabindex="7"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <span class="flex items-center justify-center gap-2">
                        <Spinner v-if="processing" class="h-5 w-5 text-white" />
                        <template v-else>
                            <UserPlus class="h-4 w-4" />
                            Crear cuenta
                        </template>
                    </span>
                </Button>
            </div>

            <div class="mt-4 text-center text-sm text-ink/55">
                ¿Ya tienes una cuenta?
                <TextLink
                    :href="login()"
                    class="font-bold text-pix transition-colors hover:text-pix-2"
                    :tabindex="8"
                    >Iniciar sesión</TextLink
                >
            </div>
        </Form>
    </div>
</template>
