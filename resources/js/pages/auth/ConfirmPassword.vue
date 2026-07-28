<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Lock, ShieldAlert } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/password/confirm';

defineOptions({
    layout: {
        title: 'Confirmar Contraseña',
        description: 'Por seguridad, confirma tu contraseña para continuar',
    },
});
</script>

<template>
    <Head title="Confirmar Contraseña" />

    <div class="animate-fade-in space-y-6">
        <!-- Title -->
        <div class="animate-slide-up text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-pink-500 shadow-lg shadow-pink-500/25">
                <ShieldAlert class="h-6 w-6 text-white" />
            </div>
            <h1 class="text-2xl font-bold text-white">Confirmar Contraseña</h1>
            <p class="mt-2 text-sm text-white/60">
                Esta es un área segura. Por favor confirma tu contraseña antes de continuar.
            </p>
        </div>

        <Form
            v-bind="store.form()"
            reset-on-success
            v-slot="{ errors, processing }"
        >
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
                        name="password"
                        required
                        autocomplete="current-password"
                        autofocus
                        placeholder="Tu contraseña actual"
                        class="pl-10 h-11 rounded-xl bg-white/5 border-white/10 text-white placeholder:text-white/40 transition-all duration-200 focus:bg-white/10 focus:border-white/20 focus:ring-2 focus:ring-white/10"
                    />
                </div>
                <InputError :message="errors.password" />
            </div>

            <div class="mt-6">
                <Button
                    class="btn-gradient-blue btn-shimmer w-full h-12 text-base font-semibold"
                    :disabled="processing"
                    data-test="confirm-password-button"
                >
                    <span class="flex items-center justify-center gap-2">
                        <Spinner v-if="processing" class="h-5 w-5 text-white" />
                        <template v-else>
                            Confirmar Contraseña
                        </template>
                    </span>
                </Button>
            </div>
        </Form>
    </div>
</template>