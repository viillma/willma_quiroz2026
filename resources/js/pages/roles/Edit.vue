<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, ShieldCheck, Sparkles } from 'lucide-vue-next';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import roles from '@/routes/roles';

defineOptions({
    layout: AppSidebarLayout,
});

defineProps<{
    role: {
        id: number;
        nombre: string;
        descripcion: string | null;
    };
}>();
</script>

<template>
    <Head :title="`Editar: ${role.nombre}`" />

    <div class="animate-fade-in relative max-w-2xl space-y-6 p-4 md:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button
                    variant="outline"
                    size="icon"
                    as-child
                    class="btn-gradient-ghost"
                >
                    <Link :href="roles.index().url">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div>
                    <h1
                        class="text-xl font-bold text-ink capitalize md:text-2xl"
                    >
                        Editar: {{ role.nombre }}
                    </h1>
                    <p class="mt-0.5 text-sm text-ink/60">
                        Modifica los datos del rol
                    </p>
                </div>
            </div>
        </div>

        <div class="glass-card animate-slide-up rounded-2xl p-6 md:p-8">
            <div
                class="mb-7 flex items-center gap-3 border-b border-ink/10 pb-5"
            >
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500/30 to-blue-500/30 shadow-lg shadow-purple-500/10"
                >
                    <ShieldCheck class="h-5 w-5 text-purple-600" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-ink">
                        Información del rol
                    </h2>
                    <p class="text-xs text-ink/60">
                        Actualiza los campos que deseas modificar
                    </p>
                </div>
            </div>

            <Form
                v-bind="roles.update.form({ role: role.id })"
                class="space-y-5"
                #default="{ errors, processing }"
            >
                <div class="space-y-2">
                    <Label for="nombre" class="text-sm font-medium text-ink/75"
                        >Nombre del rol</Label
                    >
                    <div class="relative">
                        <ShieldCheck
                            class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink/50"
                        />
                        <Input
                            id="nombre"
                            name="nombre"
                            type="text"
                            required
                            :default-value="role.nombre"
                            placeholder="Ej: Editor"
                            class="border-ink/10 bg-ink/5 pl-10 text-ink placeholder:text-ink/50 focus:border-purple-500/50 focus:ring-purple-500/20"
                        />
                    </div>
                    <InputError :message="errors.nombre" />
                </div>

                <div class="space-y-2">
                    <Label
                        for="descripcion"
                        class="text-sm font-medium text-ink/75"
                        >Descripción</Label
                    >
                    <div class="relative">
                        <Sparkles
                            class="absolute top-3 left-3 h-4 w-4 text-ink/50"
                        />
                        <textarea
                            id="descripcion"
                            name="descripcion"
                            rows="4"
                            :default-value="role.descripcion"
                            placeholder="Describe las responsabilidades del rol..."
                            class="flex w-full resize-none rounded-xl border border-ink/10 bg-ink/5 px-3 py-2.5 pl-10 text-sm text-ink transition-colors placeholder:text-ink/50 focus:border-purple-500/50 focus:ring-purple-500/20 focus:outline-none"
                        ></textarea>
                    </div>
                    <InputError :message="errors.descripcion" />
                </div>

                <div
                    class="flex items-center gap-3 border-t border-ink/10 pt-3"
                >
                    <Button
                        type="submit"
                        :disabled="processing"
                        class="btn-gradient-purple h-11 rounded-xl px-6 text-sm font-semibold shadow-lg shadow-purple-500/20 hover:shadow-purple-500/30"
                    >
                        <ShieldCheck class="mr-2 h-4 w-4" />
                        Actualizar Rol
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        as-child
                        class="btn-gradient-ghost h-11 rounded-xl px-6"
                    >
                        <Link :href="roles.index().url">Cancelar</Link>
                    </Button>
                </div>
            </Form>
        </div>
    </div>
</template>
