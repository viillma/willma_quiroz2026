<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import servicios from '@/routes/servicios';
import { dashboard } from '@/routes';
import type { Team } from '@/types';

const page = usePage<{ currentTeam?: Team | null }>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam
                    ? dashboard(props.currentTeam.slug)
                    : '/',
            },
            {
                title: 'Servicios',
                href: props.currentTeam
                    ? servicios.index(props.currentTeam.slug)
                    : '#',
            },
            {
                title: 'Nuevo Servicio',
                href: '#',
            },
        ],
    }),
});

const currentTeam = page.props.currentTeam;
</script>

<template>
    <Head title="Nuevo Servicio" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 max-w-2xl space-y-6 relative">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 ring-1 ring-white/10">
                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3"/></svg>
            </div>
            <Heading
                title="Nuevo Servicio"
                description="Agrega un nuevo servicio al sistema"
            />
        </div>

        <div class="glass-card rounded-2xl p-6 md:p-8">
            <Form
                    v-if="currentTeam"
                    v-bind="servicios.store.form(currentTeam.slug)"
                    class="space-y-6"
                    #default="{ errors, processing }"
                >
            <div class="space-y-2">
                <Label for="nombre">Nombre *</Label>
                <Input
                    id="nombre"
                    name="nombre"
                    type="text"
                    required
                    placeholder="Nombre del servicio"
                    class="bg-white/5 border-white/10 text-white placeholder:text-white/40 focus:border-purple-500/50"
                />
                <InputError :message="errors.nombre" />
            </div>

            <div class="space-y-2">
                <Label for="descripcion">Descripcion</Label>
                <Textarea
                    id="descripcion"
                    name="descripcion"
                    rows="3"
                    placeholder="Descripcion del servicio"
                    class="bg-white/5 border-white/10 text-white placeholder:text-white/40 focus:border-purple-500/50"
                />
                <InputError :message="errors.descripcion" />
            </div>

            <div class="space-y-2">
                <Label for="precio">Precio * (S/)</Label>
                <Input
                    id="precio"
                    name="precio"
                    type="number"
                    step="0.01"
                    min="0"
                    required
                    placeholder="0.00"
                    class="bg-white/5 border-white/10 text-white placeholder:text-white/40 focus:border-purple-500/50"
                />
                <InputError :message="errors.precio" />
            </div>

            <div class="space-y-2">
                <Label for="tiempo_estimado">Tiempo Estimado (días)</Label>
                <Input
                    id="tiempo_estimado"
                    name="tiempo_estimado"
                    type="number"
                    min="1"
                    placeholder="5"
                    class="bg-white/5 border-white/10 text-white placeholder:text-white/40 focus:border-purple-500/50"
                />
                <InputError :message="errors.tiempo_estimado" />
            </div>

            <div class="space-y-2">
                <Label for="imagen">Imagen Referencial</Label>
                <Input
                    id="imagen"
                    name="imagen"
                    type="file"
                    accept="image/png,image/jpg,image/jpeg,image/svg,image/webp"
                    class="bg-white/5 border-white/10 text-white file:text-white file:bg-white/10 file:border-0 file:rounded-lg file:px-3 file:py-1.5 file:text-sm file:font-medium focus:border-purple-500/50"
                />
                <InputError :message="errors.imagen" />
            </div>

            <div class="space-y-2">
                <Label for="estado">Estado</Label>
                <Select name="estado" default-value="activo" required>
                    <SelectTrigger class="bg-white/5 border-white/10 text-white">
                        <SelectValue placeholder="Seleccionar estado" />
                    </SelectTrigger>
                    <SelectContent class="bg-gray-900 border-white/10 text-white">
                        <SelectItem value="activo">Activo</SelectItem>
                        <SelectItem value="inactivo">Inactivo</SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.estado" />
            </div>

            <div class="flex gap-4">
                <Button type="submit" :disabled="processing" class="btn-gradient-blue btn-shimmer">Guardar</Button>
                <Button type="button" variant="outline" as-child class="btn-gradient-ghost">
                    <a :href="servicios.index(currentTeam.slug).url"
                        >Cancelar</a
                    >
                </Button>
            </div>
                </Form>
        </div>
    </div>
</template>
