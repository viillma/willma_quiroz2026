<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Lock, Mail, ShieldCheck } from 'lucide-vue-next';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { update } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Restablecer Contraseña',
        description: 'Ingresa tu nueva contraseña',
    },
});

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <Head title="Restablecer Contraseña" />

    <div class="animate-fade-in space-y-6">
        <!-- Title -->
        <div class="animate-slide-up text-center">
            <div
                class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blush to-lilac text-pix shadow-lg shadow-pix/15"
            >
                <ShieldCheck class="h-6 w-6" />
            </div>
            <span
                class="text-[11px] font-bold tracking-[0.2em] text-pix uppercase"
                >Nueva contraseña</span
            >
            <h1
                class="mt-3 font-display text-3xl font-extrabold tracking-tight text-ink"
            >
                Restablecer Contraseña
            </h1>
            <p class="mt-2 text-sm text-ink/55">
                Elige una contraseña segura para tu cuenta
            </p>
        </div>

        <Form
            v-bind="update.form()"
            :transform="(data) => ({ ...data, token, email })"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-5">
                <!-- Email (readonly) -->
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
                            name="email"
                            autocomplete="email"
                            v-model="inputEmail"
                            readonly
                            class="h-11 rounded-xl border-ink/10 bg-warm pl-10 text-ink/60"
                        />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <!-- New Password -->
                <div class="space-y-2">
                    <Label
                        for="password"
                        class="text-sm font-semibold text-ink/80"
                    >
                        Nueva contraseña
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
                            autocomplete="new-password"
                            autofocus
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
                            name="password_confirmation"
                            autocomplete="new-password"
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
                    :disabled="processing"
                    data-test="reset-password-button"
                >
                    <span class="flex items-center justify-center gap-2">
                        <Spinner v-if="processing" class="h-5 w-5 text-white" />
                        <template v-else>
                            <ShieldCheck class="h-4 w-4" />
                            Restablecer contraseña
                        </template>
                    </span>
                </Button>
            </div>
        </Form>
    </div>
</template>
