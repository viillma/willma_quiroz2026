<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Loader2, ImagePlus, X } from 'lucide-vue-next';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import servicios from '@/routes/servicios';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage<{
    servicio: {
        id: number;
        codigo: string | null;
        nombre: string;
        categoria: string | null;
        descripcion: string | null;
        precio: number;
        costo_estimado: number | null;
        unidad: string | null;
        tiempo_estimado: number | null;
        imagen: string | null;
        estado: string;
    };
    categorias: string[];
}>();

const servicio = page.props.servicio;
const categorias = page.props.categorias ?? [];

const form = useForm({
    nombre: servicio.nombre,
    codigo: servicio.codigo ?? '',
    categoria: servicio.categoria ?? '',
    descripcion: servicio.descripcion ?? '',
    precio: String(servicio.precio ?? ''),
    costo_estimado:
        servicio.costo_estimado !== null ? String(servicio.costo_estimado) : '',
    unidad: servicio.unidad ?? '',
    tiempo_estimado: servicio.tiempo_estimado
        ? String(servicio.tiempo_estimado)
        : '',
    imagen: null as File | null,
    estado: servicio.estado,
});

const preview = ref<string | null>(servicio.imagen);
const fileInput = ref<HTMLInputElement | null>(null);
const imageError = ref<string | null>(null);
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

function onFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file) {
        return;
    }

    imageError.value = null;

    if (!ALLOWED_TYPES.includes(file.type)) {
        imageError.value = 'Formato no válido. Usa JPG, PNG o WEBP.';
        target.value = '';

        return;
    }

    if (file.size > 5 * 1024 * 1024) {
        imageError.value = 'La imagen es muy pesada (máx. 5 MB).';
        target.value = '';

        return;
    }

    form.imagen = file;
    preview.value = URL.createObjectURL(file);
}

function submit() {
    form.transform((data) => ({ ...data, _method: 'PUT' })).post(
        servicios.update.url({ servicio: servicio.id }),
        {
            preserveScroll: true,
        },
    );
}
</script>

<template>
    <Head :title="'Editar ' + servicio.nombre" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <Heading
            :title="'Editar ' + servicio.nombre"
            description="Modifica la información del servicio"
        />

        <div class="glass-card rounded-2xl p-6 md:p-8">
            <form class="space-y-6" @submit.prevent="submit">
                <div class="flex flex-col gap-6 md:flex-row">
                    <div class="flex flex-col items-center gap-3 md:w-64">
                        <div
                            class="relative flex aspect-[4/3] w-full items-center justify-center overflow-hidden rounded-xl border border-dashed border-ink/20 bg-ink/5"
                        >
                            <img
                                v-if="preview"
                                :src="preview"
                                alt="Vista previa"
                                class="h-full w-full object-cover"
                            />
                            <ImagePlus v-else class="h-10 w-10 text-ink/30" />
                            <span
                                v-if="preview && !form.imagen"
                                class="absolute bottom-2 left-1/2 -translate-x-1/2 rounded-full bg-black/50 px-3 py-1 text-[10px] text-white/80"
                            >
                                Imagen actual
                            </span>
                            <button
                                v-if="preview && form.imagen"
                                type="button"
                                class="absolute top-2 right-2 flex h-7 w-7 items-center justify-center rounded-full bg-black/50 text-white/80 hover:bg-red-500/80"
                                @click="
                                    form.imagen = null;
                                    preview = servicio.imagen;
                                "
                            >
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                        <input
                            ref="fileInput"
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            class="hidden"
                            @change="onFileChange"
                        />
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            class="btn-gradient-ghost w-full"
                            @click="fileInput?.click()"
                        >
                            <ImagePlus class="mr-1.5 h-4 w-4" />
                            {{
                                form.imagen ? 'Cambiar imagen' : 'Subir imagen'
                            }}
                        </Button>
                        <p
                            v-if="imageError"
                            class="w-full text-center text-xs text-red-500"
                        >
                            {{ imageError }}
                        </p>
                        <p
                            v-if="form.errors.imagen"
                            class="w-full text-center text-xs text-red-500"
                        >
                            {{ form.errors.imagen }}
                        </p>
                    </div>

                    <div class="flex-1 space-y-5">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="nombre"
                                    >Nombre del servicio *</Label
                                >
                                <Input
                                    id="nombre"
                                    v-model="form.nombre"
                                    type="text"
                                    placeholder="Ej: Diseño de logo"
                                    class="glass"
                                />
                                <p
                                    v-if="form.errors.nombre"
                                    class="text-xs text-red-500"
                                >
                                    {{ form.errors.nombre }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <Label for="codigo">Código</Label>
                                <Input
                                    id="codigo"
                                    v-model="form.codigo"
                                    type="text"
                                    placeholder="Ej: SRV-0001"
                                    class="glass"
                                />
                                <p
                                    v-if="form.errors.codigo"
                                    class="text-xs text-red-500"
                                >
                                    {{ form.errors.codigo }}
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="space-y-2">
                                <Label for="categoria">Categoría</Label>
                                <Select v-model="form.categoria">
                                    <SelectTrigger class="glass"
                                        ><SelectValue placeholder="Seleccionar"
                                    /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="cat in categorias"
                                            :key="cat"
                                            :value="cat"
                                            >{{ cat }}</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                                <p
                                    v-if="form.errors.categoria"
                                    class="text-xs text-red-500"
                                >
                                    {{ form.errors.categoria }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <Label for="precio">Precio * (Bs)</Label>
                                <Input
                                    id="precio"
                                    v-model="form.precio"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                    class="glass"
                                />
                                <p
                                    v-if="form.errors.precio"
                                    class="text-xs text-red-500"
                                >
                                    {{ form.errors.precio }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <Label for="costo_estimado"
                                    >Costo estimado (Bs)</Label
                                >
                                <Input
                                    id="costo_estimado"
                                    v-model="form.costo_estimado"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                    class="glass"
                                />
                                <p
                                    v-if="form.errors.costo_estimado"
                                    class="text-xs text-red-500"
                                >
                                    {{ form.errors.costo_estimado }}
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="space-y-2">
                                <Label for="unidad">Unidad</Label>
                                <Input
                                    id="unidad"
                                    v-model="form.unidad"
                                    type="text"
                                    placeholder="Ej: unidad, hora"
                                    class="glass"
                                />
                                <p
                                    v-if="form.errors.unidad"
                                    class="text-xs text-red-500"
                                >
                                    {{ form.errors.unidad }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <Label for="tiempo_estimado"
                                    >Tiempo estimado (días)</Label
                                >
                                <Input
                                    id="tiempo_estimado"
                                    v-model="form.tiempo_estimado"
                                    type="number"
                                    min="1"
                                    placeholder="5"
                                    class="glass"
                                />
                                <p
                                    v-if="form.errors.tiempo_estimado"
                                    class="text-xs text-red-500"
                                >
                                    {{ form.errors.tiempo_estimado }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <Label for="estado">Estado</Label>
                                <Select v-model="form.estado">
                                    <SelectTrigger class="glass"
                                        ><SelectValue
                                    /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="activo"
                                            >Activo</SelectItem
                                        >
                                        <SelectItem value="inactivo"
                                            >Inactivo</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="descripcion">Descripción</Label>
                            <Textarea
                                id="descripcion"
                                v-model="form.descripcion"
                                rows="3"
                                placeholder="Describe el servicio..."
                                class="glass"
                            />
                            <p
                                v-if="form.errors.descripcion"
                                class="text-xs text-red-500"
                            >
                                {{ form.errors.descripcion }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 border-t border-ink/10 pt-5">
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="btn-gradient-blue btn-shimmer"
                    >
                        <Loader2
                            v-if="form.processing"
                            class="mr-2 h-4 w-4 animate-spin"
                        />
                        Guardar Cambios
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        as-child
                        class="btn-gradient-ghost"
                    >
                        <a :href="servicios.index().url">Cancelar</a>
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
