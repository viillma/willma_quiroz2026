<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { User, Mail, Lock, UserPlus } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login, register } from '@/routes';

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
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg shadow-purple-500/25">
                <UserPlus class="h-6 w-6 text-white" />
            </div>
            <h1 class="text-2xl font-bold text-white">Crear Cuenta</h1>
            <p class="mt-2 text-sm text-white/60">Ingresa tus datos para registrarte</p>
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
                    <Label for="name" class="text-sm font-medium text-white/80">
                        Nombre completo
                    </Label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                            <User class="h-4 w-4 text-white/40" />
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
                            class="pl-10 h-11 rounded-xl bg-white/5 border-white/10 text-white placeholder:text-white/40 transition-all duration-200 focus:bg-white/10 focus:border-white/20 focus:ring-2 focus:ring-white/10"
                        />
                    </div>
                    <InputError :message="errors.name" />
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <Label for="email" class="text-sm font-medium text-white/80">
                        Correo electrónico
                    </Label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                            <Mail class="h-4 w-4 text-white/40" />
                        </div>
                        <Input
                            id="email"
                            type="email"
                            required
                            :tabindex="2"
                            autocomplete="email"
                            name="email"
                            placeholder="tu@correo.com"
                            class="pl-10 h-11 rounded-xl bg-white/5 border-white/10 text-white placeholder:text-white/40 transition-all duration-200 focus:bg-white/10 focus:border-white/20 focus:ring-2 focus:ring-white/10"
                        />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <Label for="password" class="text-sm font-medium text-white/80">
                        Contraseña
                    </Label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                            <Lock class="h-4 w-4 text-white/40" />
                        </div>
                        <PasswordInput
                            id="password"
                            required
                            :tabindex="3"
                            autocomplete="new-password"
                            name="password"
                            placeholder="••••••••"
                            class="pl-10 h-11 rounded-xl bg-white/5 border-white/10 text-white placeholder:text-white/40 transition-all duration-200 focus:bg-white/10 focus:border-white/20 focus:ring-2 focus:ring-white/10"
                        />
                    </div>
                    <InputError :message="errors.password" />
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <Label for="password_confirmation" class="text-sm font-medium text-white/80">
                        Confirmar contraseña
                    </Label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                            <Lock class="h-4 w-4 text-white/40" />
                        </div>
                        <PasswordInput
                            id="password_confirmation"
                            required
                            :tabindex="4"
                            autocomplete="new-password"
                            name="password_confirmation"
                            placeholder="••••••••"
                            class="pl-10 h-11 rounded-xl bg-white/5 border-white/10 text-white placeholder:text-white/40 transition-all duration-200 focus:bg-white/10 focus:border-white/20 focus:ring-2 focus:ring-white/10"
                        />
                    </div>
                    <InputError :message="errors.password_confirmation" />
                </div>

                <!-- Submit -->
                <Button
                    type="submit"
                    class="btn-gradient-blue btn-shimmer w-full h-12 text-base font-semibold"
                    tabindex="5"
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

            <div class="mt-4 text-center text-sm text-white/60">
                ¿Ya tienes una cuenta?
                <TextLink
                    :href="login()"
                    class="font-medium text-blue-400 hover:text-purple-400 transition-colors"
                    :tabindex="6"
                >Iniciar sesión</TextLink>
            </div>
        </Form>
    </div>
</template>