<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Loader2, UploadCloud, ImagePlus, X, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
import portafolio from '@/routes/portafolio';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage<{
    portafolio: {
        id: number;
        titulo: string;
        client: { id: number; name: string } | null;
        servicio: { id: number; nombre: string } | null;
        categoria: string | null;
        descripcion: string | null;
        imagen_principal: string | null;
        fecha: string | null;
        publicar: boolean;
        images: Array<{ id: number; url: string }>;
    };
    categorias: string[];
    clients: Array<{ id: number; name: string }>;
    servicios: Array<{ id: number; nombre: string }>;
}>();

const work = page.props.portafolio;

const form = useForm({
    titulo: work.titulo,
    client_id: work.client ? String(work.client.id) : '',
    servicio_id: work.servicio ? String(work.servicio.id) : '',
    categoria: work.categoria ?? '',
    descripcion: work.descripcion ?? '',
    fecha: work.fecha ?? '',
    publicar: work.publicar,
    imagen_principal: null as File | null,
    imagenes_adicionales: [] as File[],
});

const imagenPrincipal = ref<File | null>(null);
const imagenesAdicionales = ref<File[]>([]);

const nuevaPreview = computed(() =>
    imagenPrincipal.value ? URL.createObjectURL(imagenPrincipal.value) : null,
);
const adicionalPreviews = computed(() =>
    imagenesAdicionales.value.map((f) => URL.createObjectURL(f)),
);

function onMainFile(e: Event) {
    const target = e.target as HTMLInputElement;
    imagenPrincipal.value = target.files?.[0] ?? null;
}

function onAdditionalFiles(e: Event) {
    const target = e.target as HTMLInputElement;

    if (target.files) {
        imagenesAdicionales.value = [
            ...imagenesAdicionales.value,
            ...Array.from(target.files),
        ];
    }
}

function removeAdditional(index: number) {
    imagenesAdicionales.value = imagenesAdicionales.value.filter(
        (_, i) => i !== index,
    );
}

function deleteImage(image: { id: number }) {
    if (window.confirm('¿Eliminar esta imagen?')) {
        router.delete(
            portafolio.imagen.destroy.url({
                portafolio: work.id,
                image: image.id,
            }),
        );
    }
}

function submit() {
    const payload = {
        titulo: form.titulo,
        client_id: form.client_id || undefined,
        servicio_id: form.servicio_id || undefined,
        categoria: form.categoria || undefined,
        descripcion: form.descripcion,
        fecha: form.fecha || undefined,
        publicar: form.publicar,
        imagen_principal: imagenPrincipal.value ?? undefined,
        imagenes_adicionales: imagenesAdicionales.value.length
            ? imagenesAdicionales.value
            : undefined,
    };
    form.transform(() => payload).post(
        portafolio.update.url({ portafolio: work.id }),
        {
            preserveScroll: true,
            forceFormData: true,
        },
    );
}
</script>

<template>
    <Head :title="'Editar ' + work.titulo" />

    <div class="animate-fade-in max-w-4xl space-y-6 p-4 md:p-6 lg:p-8">
        <Heading
            :title="'Editar ' + work.titulo"
            description="Modifica el trabajo del portafolio"
        />

        <div class="glass-card rounded-2xl p-6 md:p-8">
            <form class="space-y-6" @submit.prevent="submit">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="titulo">Título *</Label>
                        <Input
                            id="titulo"
                            v-model="form.titulo"
                            type="text"
                            placeholder="Nombre del trabajo"
                            class="glass"
                        />
                        <p
                            v-if="form.errors.titulo"
                            class="text-xs text-red-500"
                        >
                            {{ form.errors.titulo }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label for="categoria">Categoría</Label>
                        <Select v-model="form.categoria">
                            <SelectTrigger class="glass"
                                ><SelectValue placeholder="Seleccionar"
                            /></SelectTrigger>
                            <SelectContent>
                                <SelectItem :value="''"
                                    >Sin categoría</SelectItem
                                >
                                <SelectItem
                                    v-for="c in page.props.categorias"
                                    :key="c"
                                    :value="c"
                                    >{{ c }}</SelectItem
                                >
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="client_id">Cliente</Label>
                        <Select v-model="form.client_id">
                            <SelectTrigger class="glass"
                                ><SelectValue placeholder="Seleccionar"
                            /></SelectTrigger>
                            <SelectContent>
                                <SelectItem :value="''">Sin cliente</SelectItem>
                                <SelectItem
                                    v-for="c in page.props.clients"
                                    :key="c.id"
                                    :value="String(c.id)"
                                    >{{ c.name }}</SelectItem
                                >
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="space-y-2">
                        <Label for="servicio_id">Servicio</Label>
                        <Select v-model="form.servicio_id">
                            <SelectTrigger class="glass"
                                ><SelectValue placeholder="Seleccionar"
                            /></SelectTrigger>
                            <SelectContent>
                                <SelectItem :value="''"
                                    >Sin servicio</SelectItem
                                >
                                <SelectItem
                                    v-for="s in page.props.servicios"
                                    :key="s.id"
                                    :value="String(s.id)"
                                    >{{ s.nombre }}</SelectItem
                                >
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="fecha">Fecha</Label>
                        <Input
                            id="fecha"
                            v-model="form.fecha"
                            type="date"
                            class="glass"
                        />
                    </div>
                    <div class="flex items-end pb-2">
                        <label
                            class="flex items-center gap-2 text-sm text-ink/80"
                        >
                            <Checkbox
                                v-model:checked="form.publicar"
                                class="rounded-md border-ink/20"
                            />
                            <span>Publicar en el portafolio</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="descripcion">Descripción</Label>
                    <Textarea
                        id="descripcion"
                        v-model="form.descripcion"
                        rows="3"
                        placeholder="Descripción del trabajo realizado"
                        class="glass"
                    />
                </div>

                <div class="space-y-2">
                    <Label>Imagen Principal</Label>
                    <div class="flex flex-wrap items-start gap-4">
                        <img
                            v-if="work.imagen_principal && !nuevaPreview"
                            :src="work.imagen_principal"
                            :alt="work.titulo"
                            class="h-40 w-40 rounded-xl object-cover shadow-lg"
                        />
                        <img
                            v-if="nuevaPreview"
                            :src="nuevaPreview"
                            alt="Nueva imagen"
                            class="h-40 w-40 rounded-xl object-cover shadow-lg"
                        />
                        <label
                            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-ink/15 bg-ink/5 p-6 text-center transition-colors hover:border-pix/50 hover:bg-pix/5"
                        >
                            <UploadCloud class="h-8 w-8 text-ink/40" />
                            <span class="text-sm text-ink/60"
                                >Reemplazar imagen</span
                            >
                            <input
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="onMainFile"
                            />
                        </label>
                    </div>
                    <div v-if="nuevaPreview" class="mt-2">
                        <Button
                            type="button"
                            size="sm"
                            variant="ghost"
                            class="text-red-600"
                            @click="imagenPrincipal = null"
                        >
                            <X class="mr-1 h-4 w-4" /> Quitar nueva imagen
                        </Button>
                    </div>
                    <p
                        v-if="form.errors.imagen_principal"
                        class="text-xs text-red-500"
                    >
                        {{ form.errors.imagen_principal }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label>Imágenes Actuales</Label>
                    <div
                        v-if="work.images.length === 0"
                        class="text-sm text-ink/50"
                    >
                        Sin imágenes adicionales.
                    </div>
                    <div v-else class="flex flex-wrap gap-3">
                        <div
                            v-for="image in work.images"
                            :key="image.id"
                            class="relative"
                        >
                            <img
                                :src="image.url"
                                alt=""
                                class="h-24 w-24 rounded-xl object-cover shadow"
                            />
                            <button
                                type="button"
                                class="absolute -top-2 -right-2 rounded-full bg-red-500 p-1 text-white shadow"
                                @click="deleteImage(image)"
                            >
                                <Trash2 class="h-3.5 w-3.5" />
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label>Agregar Imágenes</Label>
                    <label
                        class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-ink/15 bg-ink/5 p-6 text-center transition-colors hover:border-pix/50 hover:bg-pix/5"
                    >
                        <ImagePlus class="h-8 w-8 text-ink/40" />
                        <span class="text-sm text-ink/60"
                            >Haz clic para agregar más imágenes</span
                        >
                        <input
                            type="file"
                            accept="image/*"
                            multiple
                            class="hidden"
                            @change="onAdditionalFiles"
                        />
                    </label>
                    <div
                        v-if="imagenesAdicionales.length"
                        class="mt-2 flex flex-wrap gap-3"
                    >
                        <div
                            v-for="(url, i) in adicionalPreviews"
                            :key="i"
                            class="relative"
                        >
                            <img
                                :src="url"
                                alt=""
                                class="h-24 w-24 rounded-xl object-cover shadow"
                            />
                            <button
                                type="button"
                                class="absolute -top-2 -right-2 rounded-full bg-red-500 p-1 text-white shadow"
                                @click="removeAdditional(i)"
                            >
                                <X class="h-3.5 w-3.5" />
                            </button>
                        </div>
                    </div>
                    <p
                        v-if="form.errors.imagenes_adicionales"
                        class="text-xs text-red-500"
                    >
                        {{ form.errors.imagenes_adicionales }}
                    </p>
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
                        <a :href="portafolio.index().url">Cancelar</a>
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
