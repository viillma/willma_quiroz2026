<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Mail, Lock, Eye, EyeOff, ArrowRight } from 'lucide-vue-next';

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
        <div v-if="status" class="animate-slide-up rounded-xl bg-green-500/10 p-4 text-center text-sm font-medium text-green-400 ring-1 ring-green-500/20">
            {{ status }}
        </div>

        <!-- Title inside card -->
        <div class="animate-slide-up text-center">
            <h1 class="text-2xl font-bold text-white">Iniciar Sesión</h1>
            <p class="mt-2 text-sm text-white/60">Ingresa tus credenciales para continuar</p>
        </div>

        <Form
            v-bind="loginStore.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
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
                        name="email"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="tu@correo.com"
                        :tabindex="1"
                        class="pl-10 h-11 rounded-xl bg-white/5 border-white/10 text-white placeholder:text-white/40 transition-all duration-200 focus:bg-white/10 focus:border-white/20 focus:ring-2 focus:ring-white/10"
                    />
                </div>
                <InputError :message="errors.email" />
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <Label for="password" class="text-sm font-medium text-white/80">
                        Contraseña
                    </Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-xs font-medium text-blue-400 hover:text-purple-400 transition-colors"
                        :tabindex="5"
                    >
                        ¿Olvidaste tu contraseña?
                    </TextLink>
                </div>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                        <Lock class="h-4 w-4 text-white/40" />
                    </div>
                    <PasswordInput
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Tu contraseña"
                        :tabindex="2"
                        class="pl-10 h-11 rounded-xl bg-white/5 border-white/10 text-white placeholder:text-white/40 transition-all duration-200 focus:bg-white/10 focus:border-white/20 focus:ring-2 focus:ring-white/10"
                    />
                </div>
                <InputError :message="errors.password" />
            </div>

            <!-- Remember me -->
            <div class="flex items-center">
                <Label
                    for="remember"
                    class="flex cursor-pointer items-center gap-3 text-sm text-white/70"
                >
                    <Checkbox
                        id="remember"
                        name="remember"
                        :tabindex="3"
                        class="data-[state=checked]:bg-blue-500 data-[state=checked]:border-blue-500 border-white/20"
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
                class="btn-gradient-blue btn-shimmer w-full h-12 text-base font-semibold"
            >
                <span class="flex items-center justify-center gap-2">
                    <Spinner v-if="processing" class="h-5 w-5 text-white" />
                    <template v-else>
                        Iniciar Sesión
                        <ArrowRight class="h-4 w-4" />
                    </template>
                </span>
            </Button>
        </Form>
    </div>
</template>