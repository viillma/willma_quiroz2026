<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Lock, Mail, ShieldCheck } from 'lucide-vue-next';
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
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg shadow-purple-500/25">
                <ShieldCheck class="h-6 w-6 text-white" />
            </div>
            <h1 class="text-2xl font-bold text-white">Restablecer Contraseña</h1>
            <p class="mt-2 text-sm text-white/60">Elige una contraseña segura para tu cuenta</p>
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
                            autocomplete="email"
                            v-model="inputEmail"
                            readonly
                            class="pl-10 h-11 rounded-xl bg-white/5 border-white/10 text-white/60"
                        />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <!-- New Password -->
                <div class="space-y-2">
                    <Label for="password" class="text-sm font-medium text-white/80">
                        Nueva contraseña
                    </Label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                            <Lock class="h-4 w-4 text-white/40" />
                        </div>
                        <PasswordInput
                            id="password"
                            name="password"
                            autocomplete="new-password"
                            autofocus
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
                            name="password_confirmation"
                            autocomplete="new-password"
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