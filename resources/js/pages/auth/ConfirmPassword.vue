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
            <div
                class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-peach to-blush text-[#cf6c0b] shadow-lg shadow-peach/40"
            >
                <ShieldAlert class="h-6 w-6" />
            </div>
            <span
                class="text-[11px] font-bold tracking-[0.2em] text-pix uppercase"
                >Zona segura</span
            >
            <h1
                class="mt-3 font-display text-3xl font-extrabold tracking-tight text-ink"
            >
                Confirmar Contraseña
            </h1>
            <p class="mt-2 text-sm text-ink/55">
                Esta es un área segura. Por favor confirma tu contraseña antes
                de continuar.
            </p>
        </div>

        <Form
            v-bind="store.form()"
            reset-on-success
            v-slot="{ errors, processing }"
        >
            <div class="space-y-2">
                <Label for="password" class="text-sm font-semibold text-ink/80">
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
                        name="password"
                        required
                        autocomplete="current-password"
                        autofocus
                        placeholder="Tu contraseña actual"
                        class="h-11 rounded-xl border-ink/10 bg-warm pl-10 text-ink transition-all duration-200 placeholder:text-ink/35 focus:border-pix/40 focus:bg-white focus:ring-pix/20"
                    />
                </div>
                <InputError :message="errors.password" />
            </div>

            <div class="mt-6">
                <Button
                    class="h-12 w-full rounded-xl bg-pix font-bold text-white shadow-lg shadow-pix/25 transition-all duration-300 hover:-translate-y-0.5 hover:bg-pix-2 hover:shadow-xl hover:shadow-pix/30"
                    :disabled="processing"
                    data-test="confirm-password-button"
                >
                    <span class="flex items-center justify-center gap-2">
                        <Spinner v-if="processing" class="h-5 w-5 text-white" />
                        <template v-else> Confirmar Contraseña </template>
                    </span>
                </Button>
            </div>
        </Form>
    </div>
</template>
