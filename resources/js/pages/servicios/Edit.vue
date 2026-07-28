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

const page = usePage<{
    currentTeam?: Team | null;
    servicio: {
        id: number;
        nombre: string;
        descripcion: string;
        precio: string;
        tiempo_estimado: number | null;
        imagen: string | null;
        estado: string;
    };
}>();

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
                title: 'Editar Servicio',
                href: '#',
            },
        ],
    }),
});

const currentTeam = page.props.currentTeam;
</script>

<template>
    <Head title="Editar Servicio" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 max-w-2xl space-y-6 relative">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 ring-1 ring-white/10">
                <svg class="h-5 w-5 text-purple-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <Heading
                title="Editar Servicio"
                description="Modifica los datos del servicio"
            />
        </div>

        <div class="glass-card rounded-2xl p-6 md:p-8">
            <Form
                    v-if="currentTeam"
                    v-bind="
                        servicios.update.form({
                            current_team: currentTeam.slug,
                            servicio: page.props.servicio.id,
                        })
                    "
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
                    :default-value="page.props.servicio.nombre"
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
                    :default-value="page.props.servicio.descripcion"
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
                    :default-value="page.props.servicio.precio"
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
                    :default-value="page.props.servicio.tiempo_estimado ?? ''"
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
                <p v-if="page.props.servicio.imagen" class="text-xs text-white/50">
                    Deja en blanco para mantener la imagen actual
                </p>
            </div>

            <div class="space-y-2">
                <Label for="estado">Estado</Label>
                <Select
                    name="estado"
                    :default-value="page.props.servicio.estado"
                    required
                >
                    <SelectTrigger class="bg-white/5 border-white/10 text-white">
                        <SelectValue
                            :placeholder="page.props.servicio.estado"
                        />
                    </SelectTrigger>
                    <SelectContent class="bg-gray-900 border-white/10 text-white">
                        <SelectItem value="activo">Activo</SelectItem>
                        <SelectItem value="inactivo">Inactivo</SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.estado" />
            </div>

            <div class="flex gap-4">
                <Button type="submit" :disabled="processing" class="btn-gradient-blue btn-shimmer">Actualizar</Button>
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
