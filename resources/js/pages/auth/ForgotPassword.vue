<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Mail, ArrowLeft, Send } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Recuperar Contraseña',
        description: 'Te enviaremos un enlace para restablecer tu contraseña',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Recuperar Contraseña" />

    <div class="animate-fade-in space-y-6">
        <!-- Status -->
        <div v-if="status" class="animate-slide-up rounded-xl bg-green-500/10 p-4 text-center text-sm font-medium text-green-400 ring-1 ring-green-500/20">
            {{ status }}
        </div>

        <!-- Title -->
        <div class="animate-slide-up text-center">
            <h1 class="text-2xl font-bold text-white">Recuperar Contraseña</h1>
            <p class="mt-2 text-sm text-white/60">Ingresa tu correo y te enviaremos un enlace</p>
        </div>

        <Form v-bind="email.form()" v-slot="{ errors, processing }">
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
                        autocomplete="off"
                        autofocus
                        placeholder="tu@correo.com"
                        class="pl-10 h-11 rounded-xl bg-white/5 border-white/10 text-white placeholder:text-white/40 transition-all duration-200 focus:bg-white/10 focus:border-white/20 focus:ring-2 focus:ring-white/10"
                    />
                </div>
                <InputError :message="errors.email" />
            </div>

            <div class="mt-6">
                <Button
                    class="btn-gradient-blue btn-shimmer w-full h-12 text-base font-semibold"
                    :disabled="processing"
                    data-test="email-password-reset-link-button"
                >
                    <span class="flex items-center justify-center gap-2">
                        <Spinner v-if="processing" class="h-5 w-5 text-white" />
                        <template v-else>
                            <Send class="h-4 w-4" />
                            Enviar enlace
                        </template>
                    </span>
                </Button>
            </div>
        </Form>

        <div class="mt-8 text-center">
            <TextLink :href="login()" class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-400 hover:text-purple-400 transition-colors">
                <ArrowLeft class="h-4 w-4" />
                Volver al inicio de sesión
            </TextLink>
        </div>
    </div>
</template>