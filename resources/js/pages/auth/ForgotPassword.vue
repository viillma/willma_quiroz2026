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
        <div
            v-if="status"
            class="animate-slide-up rounded-xl bg-mint/50 p-4 text-center text-sm font-semibold text-[#17805a] ring-1 ring-mint"
        >
            {{ status }}
        </div>

        <!-- Title -->
        <div class="animate-slide-up text-center">
            <span
                class="text-[11px] font-bold tracking-[0.2em] text-pix uppercase"
                >Recupera tu acceso</span
            >
            <h1
                class="mt-3 font-display text-3xl font-extrabold tracking-tight text-ink"
            >
                Recuperar Contraseña
            </h1>
            <p class="mt-2 text-sm text-ink/55">
                Ingresa tu correo y te enviaremos un enlace
            </p>
        </div>

        <Form v-bind="email.form()" v-slot="{ errors, processing }">
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
                        autocomplete="off"
                        autofocus
                        placeholder="tu@correo.com"
                        class="h-11 rounded-xl border-ink/10 bg-warm pl-10 text-ink transition-all duration-200 placeholder:text-ink/35 focus:border-pix/40 focus:bg-white focus:ring-pix/20"
                    />
                </div>
                <InputError :message="errors.email" />
            </div>

            <div class="mt-6">
                <Button
                    class="group h-12 w-full rounded-xl bg-pix text-base font-bold text-white shadow-lg shadow-pix/25 transition-all duration-300 hover:-translate-y-0.5 hover:bg-pix-2 hover:shadow-xl hover:shadow-pix/30"
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
            <TextLink
                :href="login()"
                class="inline-flex items-center gap-1.5 text-sm font-semibold text-pix transition-colors hover:text-pix-2"
            >
                <ArrowLeft class="h-4 w-4" />
                Volver al inicio de sesión
            </TextLink>
        </div>
    </div>
</template>
