<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Mail, Lock, ArrowRight } from 'lucide-vue-next';

import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';

import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

import { store as loginStore } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Iniciar Sesión',
        description: 'Accede a tu panel de diseño y gestión de servicios',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Iniciar Sesión" />

    <div class="animate-fade-in space-y-6">
        <!-- Status message -->
        <div
            v-if="status"
            class="animate-slide-up rounded-xl bg-mint/50 p-4 text-center text-sm font-semibold text-[#17805a] ring-1 ring-mint"
        >
            {{ status }}
        </div>

        <!-- Title inside card -->
        <div class="animate-slide-up text-center">
            <span
                class="text-[11px] font-bold tracking-[0.2em] text-pix uppercase"
                >Bienvenido de nuevo</span
            >
            <h1
                class="mt-3 font-display text-3xl font-extrabold tracking-tight text-ink"
            >
                Iniciar Sesión
            </h1>
            <p class="mt-2 text-sm text-ink/55">
                Ingresa tus credenciales para continuar
            </p>
        </div>

        <Form
            v-bind="loginStore.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <!-- Email -->
            <div class="space-y-2">
                <Label for="email" class="text-sm font-semibold text-ink/80">
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
                        name="email"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="tu@correo.com"
                        :tabindex="1"
                        class="h-11 rounded-xl border-ink/10 bg-warm pl-10 text-ink transition-all duration-200 placeholder:text-ink/35 focus:border-pix/40 focus:bg-white focus:ring-pix/20"
                    />
                </div>
                <InputError :message="errors.email" />
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <Label
                        for="password"
                        class="text-sm font-semibold text-ink/80"
                    >
                        Contraseña
                    </Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-xs font-semibold text-pix transition-colors hover:text-pix-2"
                        :tabindex="5"
                    >
                        ¿Olvidaste tu contraseña?
                    </TextLink>
                </div>
                <div class="relative">
                    <div
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5"
                    >
                        <Lock class="h-4 w-4 text-ink/35" />
                    </div>
                    <PasswordInput
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Tu contraseña"
                        :tabindex="2"
                        class="h-11 rounded-xl border-ink/10 bg-warm pl-10 text-ink transition-all duration-200 placeholder:text-ink/35 focus:border-pix/40 focus:bg-white focus:ring-pix/20"
                    />
                </div>
                <InputError :message="errors.password" />
            </div>

            <!-- Remember me -->
            <div class="flex items-center">
                <Label
                    for="remember"
                    class="flex cursor-pointer items-center gap-3 text-sm font-medium text-ink/65"
                >
                    <Checkbox
                        id="remember"
                        name="remember"
                        :tabindex="3"
                        class="border-ink/20 data-[state=checked]:border-pix data-[state=checked]:bg-pix"
                    />
                    <span>Recordar sesión</span>
                </Label>
            </div>

            <!-- Submit button -->
            <Button
                type="submit"
                :disabled="processing"
                :tabindex="4"
                data-test="login-button"
                class="group h-12 w-full rounded-xl bg-pix text-base font-bold text-white shadow-lg shadow-pix/25 transition-all duration-300 hover:-translate-y-0.5 hover:bg-pix-2 hover:shadow-xl hover:shadow-pix/30"
            >
                <span class="flex items-center justify-center gap-2">
                    <Spinner v-if="processing" class="h-5 w-5 text-white" />
                    <template v-else>
                        Iniciar Sesión
                        <ArrowRight
                            class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
                        />
                    </template>
                </span>
            </Button>
        </Form>

        <p class="mt-2 text-center text-sm text-ink/45">
            ¿No tienes cuenta?
            <a
                href="/register"
                class="font-bold text-pix transition-colors hover:text-pix-2"
            >
                Registrarse
            </a>
        </p>
    </div>
</template>
