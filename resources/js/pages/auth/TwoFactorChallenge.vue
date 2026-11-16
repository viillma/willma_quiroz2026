<script setup lang="ts">
import { Form, Head, setLayoutProps } from '@inertiajs/vue3';
import { KeyRound, ShieldAlert } from 'lucide-vue-next';
import { computed, ref, watchEffect } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    InputOTP,
    InputOTPGroup,
    InputOTPSlot,
} from '@/components/ui/input-otp';
import { store } from '@/routes/two-factor/login';
import type { TwoFactorConfigContent } from '@/types';

const authConfigContent = computed<TwoFactorConfigContent>(() => {
    if (showRecoveryInput.value) {
        return {
            title: 'Código de Recuperación',
            description:
                'Ingresa uno de tus códigos de recuperación de emergencia.',
            buttonText: 'Usar código de autenticación',
        };
    }

    return {
        title: 'Código de Autenticación',
        description:
            'Ingresa el código de 6 dígitos de tu aplicación autenticadora.',
        buttonText: 'Usar código de recuperación',
    };
});

watchEffect(() => {
    setLayoutProps({
        title: authConfigContent.value.title,
        description: authConfigContent.value.description,
    });
});

const showRecoveryInput = ref<boolean>(false);

const toggleRecoveryMode = (clearErrors: () => void): void => {
    showRecoveryInput.value = !showRecoveryInput.value;
    clearErrors();
    code.value = '';
};

const code = ref<string>('');
</script>

<template>
    <Head :title="authConfigContent.title" />

    <div class="animate-fade-in space-y-6">
        <!-- Title -->
        <div class="animate-slide-up text-center">
            <div
                class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-lilac to-blush text-[#6d5bd0] shadow-lg shadow-lilac/50"
            >
                <KeyRound class="h-6 w-6" />
            </div>
            <span
                class="text-[11px] font-bold tracking-[0.2em] text-pix uppercase"
                >Verificación en dos pasos</span
            >
            <h1
                class="mt-3 font-display text-3xl font-extrabold tracking-tight text-ink"
            >
                {{ authConfigContent.title }}
            </h1>
            <p class="mt-2 text-sm text-ink/55">
                {{ authConfigContent.description }}
            </p>
        </div>

        <div class="space-y-6">
            <template v-if="!showRecoveryInput">
                <Form
                    v-bind="store.form()"
                    class="space-y-6"
                    reset-on-error
                    @error="code = ''"
                    #default="{ errors, processing, clearErrors }"
                >
                    <input type="hidden" name="code" :value="code" />
                    <div
                        class="flex flex-col items-center justify-center space-y-3 text-center"
                    >
                        <div class="flex w-full items-center justify-center">
                            <InputOTP
                                id="otp"
                                v-model="code"
                                :maxlength="6"
                                :disabled="processing"
                                autofocus
                                class="[&>*]:border-ink/10 [&>*]:bg-warm [&>*]:text-ink [&>*]:focus:border-pix/50"
                            >
                                <InputOTPGroup>
                                    <InputOTPSlot
                                        v-for="index in 6"
                                        :key="index"
                                        :index="index - 1"
                                    />
                                </InputOTPGroup>
                            </InputOTP>
                        </div>
                        <InputError :message="errors.code" />
                    </div>

                    <Button
                        type="submit"
                        class="h-12 w-full rounded-xl bg-pix font-bold text-white shadow-lg shadow-pix/25 transition-all duration-300 hover:-translate-y-0.5 hover:bg-pix-2 hover:shadow-xl hover:shadow-pix/30"
                        :disabled="processing || code.length < 6"
                    >
                        <span class="flex items-center justify-center gap-2">
                            Verificar
                        </span>
                    </Button>

                    <div class="text-center text-sm text-ink/55">
                        <span>o </span>
                        <button
                            type="button"
                            class="font-bold text-pix underline underline-offset-2 transition-colors hover:text-pix-2"
                            @click="() => toggleRecoveryMode(clearErrors)"
                        >
                            {{ authConfigContent.buttonText }}
                        </button>
                    </div>
                </Form>
            </template>

            <template v-else>
                <Form
                    v-bind="store.form()"
                    class="space-y-6"
                    reset-on-error
                    #default="{ errors, processing, clearErrors }"
                >
                    <div class="space-y-2">
                        <label
                            for="recovery_code"
                            class="text-sm font-semibold text-ink/80"
                        >
                            Código de recuperación
                        </label>
                        <div class="relative">
                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5"
                            >
                                <ShieldAlert class="h-4 w-4 text-ink/35" />
                            </div>
                            <Input
                                id="recovery_code"
                                name="recovery_code"
                                type="text"
                                placeholder="Ingresa tu código de recuperación"
                                :autofocus="showRecoveryInput"
                                required
                                class="h-11 rounded-xl border-ink/10 bg-warm pl-10 text-ink transition-all duration-200 placeholder:text-ink/35 focus:border-pix/40 focus:bg-white focus:ring-pix/20"
                            />
                        </div>
                        <InputError :message="errors.recovery_code" />
                    </div>

                    <Button
                        type="submit"
                        class="h-12 w-full rounded-xl bg-pix font-bold text-white shadow-lg shadow-pix/25 transition-all duration-300 hover:-translate-y-0.5 hover:bg-pix-2 hover:shadow-xl hover:shadow-pix/30"
                        :disabled="processing"
                    >
                        <span class="flex items-center justify-center gap-2">
                            Verificar
                        </span>
                    </Button>

                    <div class="text-center text-sm text-ink/55">
                        <span>o </span>
                        <button
                            type="button"
                            class="font-bold text-pix underline underline-offset-2 transition-colors hover:text-pix-2"
                            @click="() => toggleRecoveryMode(clearErrors)"
                        >
                            {{ authConfigContent.buttonText }}
                        </button>
                    </div>
                </Form>
            </template>
        </div>
    </div>
</template>
