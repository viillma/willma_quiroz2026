<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, ShieldCheck, Sparkles } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import roles from '@/routes/roles';
import type { Team } from '@/types';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage();

const teamSlug = ref<string>('');

watch(
    () => page.props.currentTeam,
    (team) => {
        teamSlug.value = (team as Team | null)?.slug ?? '';
    },
    { immediate: true },
);
</script>

<template>
    <Head title="Nuevo Rol" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 max-w-2xl space-y-6 relative">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="btn-gradient-ghost">
                    <Link :href="teamSlug ? roles.index(teamSlug).url : '#'">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-white">Nuevo Rol</h1>
                    <p class="mt-0.5 text-sm text-white/50">Agrega un nuevo rol al sistema</p>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 md:p-8 animate-slide-up">
            <div class="flex items-center gap-3 mb-7 pb-5 border-b border-white/10">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500/30 to-blue-500/30 shadow-lg shadow-purple-500/10">
                    <ShieldCheck class="h-5 w-5 text-purple-300" />
                </div>
                <div>
                    <h2 class="text-base font-semibold text-white">Información del rol</h2>
                    <p class="text-xs text-white/50">Completa los campos para crear un nuevo rol</p>
                </div>
            </div>

            <Form
                v-if="teamSlug"
                v-bind="roles.store.form(teamSlug)"
                class="space-y-5"
                #default="{ errors, processing }"
            >
                <div class="space-y-2">
                    <Label for="nombre" class="text-sm font-medium text-white/80">Nombre del rol</Label>
                    <div class="relative">
                        <ShieldCheck class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-white/40" />
                        <Input
                            id="nombre"
                            name="nombre"
                            type="text"
                            required
                            placeholder="Ej: Editor"
                            class="pl-10 bg-white/5 border-white/10 text-white placeholder:text-white/40 focus:border-purple-500/50 focus:ring-purple-500/20"
                        />
                    </div>
                    <InputError :message="errors.nombre" />
                </div>

                <div class="space-y-2">
                    <Label for="descripcion" class="text-sm font-medium text-white/80">Descripción</Label>
                    <div class="relative">
                        <Sparkles class="absolute left-3 top-3 h-4 w-4 text-white/40" />
                        <textarea
                            id="descripcion"
                            name="descripcion"
                            rows="4"
                            placeholder="Describe las responsabilidades del rol..."
                            class="flex w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 pl-10 text-sm text-white placeholder:text-white/40 focus:border-purple-500/50 focus:ring-purple-500/20 focus:outline-none transition-colors resize-none"
                        ></textarea>
                    </div>
                    <InputError :message="errors.descripcion" />
                </div>

                <div class="flex items-center gap-3 pt-3 border-t border-white/10">
                    <Button type="submit" :disabled="processing" class="btn-gradient-purple h-11 px-6 text-sm font-semibold rounded-xl shadow-lg shadow-purple-500/20 hover:shadow-purple-500/30">
                        <ShieldCheck class="mr-2 h-4 w-4" />
                        Crear Rol
                    </Button>
                    <Button type="button" variant="outline" as-child class="btn-gradient-ghost h-11 px-6 rounded-xl">
                        <Link :href="teamSlug ? roles.index(teamSlug).url : '#'">Cancelar</Link>
                    </Button>
                </div>
            </Form>

            <div v-else class="flex items-center justify-center py-12">
                <p class="text-sm text-white/40">Cargando...</p>
            </div>
        </div>
    </div>
</template>
