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
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 shadow-lg shadow-orange-500/25">
                <MailCheck class="h-6 w-6 text-white" />
            </div>
            <h1 class="text-2xl font-bold text-white">Verifica tu Correo</h1>
            <p class="mt-2 text-sm text-white/60">
                Gracias por registrarte. Te hemos enviado un enlace de verificación.
            </p>
        </div>

        <!-- Status -->
        <div
            v-if="status === 'verification-link-sent'"
            class="mb-6 rounded-xl bg-blue-500/10 p-4 text-center text-sm font-medium text-blue-400 ring-1 ring-blue-500/20"
        >
            Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
        </div>

        <Form
            v-bind="send.form()"
            class="space-y-4 text-center"
            v-slot="{ processing }"
        >
            <Button
                :disabled="processing"
                variant="secondary"
                class="btn-gradient-blue btn-shimmer w-full h-12 text-base font-semibold"
            >
                <Spinner v-if="processing" class="h-4 w-4" />
                <template v-else>
                    <Send class="h-4 w-4" />
                    Reenviar verificación
                </template>
            </Button>

            <TextLink :href="logout()" as="button" class="mx-auto mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-white/60 hover:text-white/80 transition-colors">
                <LogOut class="h-4 w-4" />
                Cerrar sesión
            </TextLink>
        </Form>
    </div>
</template>