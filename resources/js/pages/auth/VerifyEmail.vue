<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { MailCheck, LogOut, Send } from 'lucide-vue-next';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        title: 'Verificar Correo',
        description: 'Verifica tu dirección de correo electrónico',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Verificación de Correo" />

    <div class="animate-fade-in space-y-6">
        <!-- Title -->
        <div class="animate-slide-up text-center">
            <div
                class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-peach to-blush text-[#cf6c0b] shadow-lg shadow-peach/40"
            >
                <MailCheck class="h-6 w-6" />
            </div>
            <span
                class="text-[11px] font-bold tracking-[0.2em] text-pix uppercase"
                >Último paso</span
            >
            <h1
                class="mt-3 font-display text-3xl font-extrabold tracking-tight text-ink"
            >
                Verifica tu Correo
            </h1>
            <p class="mt-2 text-sm text-ink/55">
                Gracias por registrarte. Te hemos enviado un enlace de
                verificación.
            </p>
        </div>

        <!-- Status -->
        <div
            v-if="status === 'verification-link-sent'"
            class="mb-6 rounded-xl bg-lavender/60 p-4 text-center text-sm font-semibold text-[#6d5bd0] ring-1 ring-lilac"
        >
            Se ha enviado un nuevo enlace de verificación a tu correo
            electrónico.
        </div>

        <Form
            v-bind="send.form()"
            class="space-y-4 text-center"
            v-slot="{ processing }"
        >
            <Button
                :disabled="processing"
                variant="secondary"
                class="h-12 w-full rounded-xl bg-pix font-bold text-white shadow-lg shadow-pix/25 transition-all duration-300 hover:-translate-y-0.5 hover:bg-pix-2 hover:shadow-xl hover:shadow-pix/30"
            >
                <Spinner v-if="processing" class="h-4 w-4 text-white" />
                <template v-else>
                    <Send class="h-4 w-4" />
                    Reenviar verificación
                </template>
            </Button>

            <TextLink
                :href="logout()"
                as="button"
                class="mx-auto mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-ink/50 transition-colors hover:text-pix"
            >
                <LogOut class="h-4 w-4" />
                Cerrar sesión
            </TextLink>
        </Form>
    </div>
</template>
