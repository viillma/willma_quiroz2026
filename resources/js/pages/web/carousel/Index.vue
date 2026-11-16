<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    ImagePlus,
    Images,
    Pencil,
    Plus,
    Trash2,
    X,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import adminWeb from '@/routes/admin-web';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage();

type Slide = {
    id: number;
    titulo: string;
    descripcion: string | null;
    boton_texto: string;
    boton_enlace: string | null;
    imagen: string | null;
    imagen_url: string | null;
    duracion: number;
    orden: number;
    activo: boolean;
};

type MediaFile = {
    path: string;
    name: string;
    url: string;
};

const slides = computed(() => (page.props.slides || []) as Slide[]);
const images = computed(() => (page.props.images || []) as MediaFile[]);

const isOpen = ref(false);
const editing = ref<Slide | null>(null);
const preview = ref<string | null>(null);
const galleryOpen = ref(false);

const form = useForm({
    titulo: '',
    descripcion: '',
    boton_texto: 'Solicitar servicio',
    boton_enlace: '#contacto',
    duracion: 5,
    imagen: null as File | null,
    imagen_url: '' as string,
    activo: true,
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    preview.value = null;
    isOpen.value = true;
}

function openEdit(slide: Slide) {
    editing.value = slide;
    form.clearErrors();
    form.titulo = slide.titulo;
    form.descripcion = slide.descripcion ?? '';
    form.boton_texto = slide.boton_texto;
    form.boton_enlace = slide.boton_enlace ?? '';
    form.duracion = slide.duracion;
    form.imagen = null;
    form.imagen_url = slide.imagen_url ?? '';
    form.activo = slide.activo;
    preview.value = slide.imagen_url;
    isOpen.value = true;
}

function onFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.imagen = file;
        form.imagen_url = '';
        preview.value = URL.createObjectURL(file);
    }
}

function selectFromGallery(img: MediaFile) {
    form.imagen = null;
    form.imagen_url = img.url;
    preview.value = img.url;
    galleryOpen.value = false;
}

function clearImage() {
    form.imagen = null;
    form.imagen_url = '';
    preview.value = null;
}

function save() {
    if (editing.value) {
        form.put(
            adminWeb.carousel.update.url({ carouselSlide: editing.value.id }),
            {
                preserveScroll: true,
                onSuccess: () => {
                    isOpen.value = false;
                },
            },
        );

        return;
    }

    form.post(adminWeb.carousel.store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            isOpen.value = false;
        },
    });
}

function remove(slide: Slide) {
    if (window.confirm(`¿Eliminar el slide "${slide.titulo}"?`)) {
        router.delete(
            adminWeb.carousel.destroy.url({ carouselSlide: slide.id }),
            {
                preserveScroll: true,
            },
        );
    }
}

function toggle(slide: Slide) {
    router.post(
        adminWeb.carousel.toggle.url({ carouselSlide: slide.id }),
        {},
        { preserveScroll: true },
    );
}

function move(slide: Slide, direction: -1 | 1) {
    const idx = slides.value.findIndex((s) => s.id === slide.id);
    const target = idx + direction;

    if (target < 0 || target >= slides.value.length) {
        return;
    }

    const next = slides.value.map((s) => s.id);
    [next[idx], next[target]] = [next[target], next[idx]];

    router.post(
        adminWeb.carousel.reorder.url(),
        { order: next },
        { preserveScroll: true },
    );
}
</script>

<template>
    <Head title="Administrador de Carrusel" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-pix/20 to-purple-500/20 ring-1 ring-white/10"
                >
                    <svg
                        class="h-5 w-5 text-pix"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <rect x="3" y="3" width="7" height="7" rx="1.5" />
                        <rect x="14" y="3" width="7" height="7" rx="1.5" />
                        <rect x="14" y="14" width="7" height="7" rx="1.5" />
                        <rect x="3" y="14" width="7" height="7" rx="1.5" />
                    </svg>
                </div>
                <Heading
                    title="Administrador de Carrusel"
                    description="Agrega, edita, reordena y activa los slides de tu página"
                    variant="small"
                />
            </div>
            <Button class="btn-gradient-blue btn-shimmer" @click="openCreate">
                <Plus class="mr-2 h-4 w-4" />
                Nuevo Slide
            </Button>
        </div>

        <div
            v-if="slides.length > 0"
            class="grid grid-cols-1 gap-4 sm:grid-cols-3"
        >
            <div class="glass-card animate-slide-up rounded-2xl p-5 shadow-lg">
                <p
                    class="text-xs font-medium tracking-wider text-white/70 uppercase"
                >
                    Total Slides
                </p>
                <p class="mt-1 text-2xl font-bold text-white">
                    {{ slides.length }}
                </p>
            </div>
            <div
                class="glass-card animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.08s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-white/70 uppercase"
                >
                    Activos
                </p>
                <p class="mt-1 text-2xl font-bold text-white">
                    {{ slides.filter((s) => s.activo).length }}
                </p>
            </div>
            <div
                class="glass-card animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.16s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-white/70 uppercase"
                >
                    Duración promedio
                </p>
                <p class="mt-1 text-2xl font-bold text-white">
                    {{
                        Math.round(
                            slides.reduce((a, s) => a + s.duracion, 0) /
                                slides.length,
                        )
                    }}s
                </p>
            </div>
        </div>

        <div
            v-if="slides.length > 0"
            class="glass-card overflow-hidden rounded-2xl"
        >
            <div class="overflow-x-auto">
                <table class="table-glow w-full">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th
                                class="px-5 py-4 text-left text-xs font-semibold tracking-wider text-white/50 uppercase"
                            >
                                #
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-semibold tracking-wider text-white/50 uppercase"
                            >
                                Imagen
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-semibold tracking-wider text-white/50 uppercase"
                            >
                                Título
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-semibold tracking-wider text-white/50 uppercase"
                            >
                                Descripción
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-semibold tracking-wider text-white/50 uppercase"
                            >
                                Duración
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-semibold tracking-wider text-white/50 uppercase"
                            >
                                Estado
                            </th>
                            <th
                                class="px-5 py-4 text-left text-xs font-semibold tracking-wider text-white/50 uppercase"
                            >
                                Orden
                            </th>
                            <th
                                class="px-5 py-4 text-right text-xs font-semibold tracking-wider text-white/50 uppercase"
                            >
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr
                            v-for="(slide, i) in slides"
                            :key="slide.id"
                            class="transition-colors hover:bg-white/5"
                        >
                            <td
                                class="px-5 py-4 text-sm font-medium text-white/50"
                            >
                                {{ i + 1 }}
                            </td>
                            <td class="px-5 py-4">
                                <img
                                    :src="slide.imagen_url || ''"
                                    :alt="slide.titulo"
                                    class="h-12 w-20 rounded-lg object-cover ring-1 ring-white/10"
                                />
                            </td>
                            <td
                                class="px-5 py-4 text-sm font-medium text-white"
                            >
                                {{ slide.titulo }}
                            </td>
                            <td
                                class="max-w-[220px] px-5 py-4 text-sm text-white/60"
                            >
                                {{ slide.descripcion || '-' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-white/60">
                                {{ slide.duracion }}s
                            </td>
                            <td class="px-5 py-4">
                                <button
                                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium transition-all"
                                    :class="
                                        slide.activo
                                            ? 'bg-emerald-500/15 text-emerald-300'
                                            : 'bg-white/10 text-white/50'
                                    "
                                    @click="toggle(slide)"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full"
                                        :class="
                                            slide.activo
                                                ? 'bg-emerald-400'
                                                : 'bg-white/30'
                                        "
                                    ></span>
                                    {{ slide.activo ? 'Activo' : 'Inactivo' }}
                                </button>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-col gap-0.5">
                                    <button
                                        class="text-white/40 transition-colors hover:text-white disabled:opacity-20"
                                        :disabled="i === 0"
                                        @click="move(slide, -1)"
                                    >
                                        <ArrowLeft class="h-4 w-4 rotate-90" />
                                    </button>
                                    <button
                                        class="text-white/40 transition-colors hover:text-white disabled:opacity-20"
                                        :disabled="i === slides.length - 1"
                                        @click="move(slide, 1)"
                                    >
                                        <ArrowRight class="h-4 w-4 rotate-90" />
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <Button
                                        size="sm"
                                        class="btn-gradient-ghost"
                                        @click="openEdit(slide)"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        size="sm"
                                        class="btn-gradient-ghost text-red-400 hover:text-red-300"
                                        @click="remove(slide)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div
            v-if="slides.length === 0"
            class="glass-card flex flex-col items-center justify-center rounded-2xl px-6 py-16 text-center"
        >
            <div
                class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-white/10"
            >
                <ImagePlus class="h-7 w-7 text-white/40" />
            </div>
            <p class="text-base font-medium text-white/60">
                No hay slides todavía
            </p>
            <Button
                class="btn-gradient-blue btn-shimmer mt-4"
                @click="openCreate"
            >
                <Plus class="mr-2 h-4 w-4" />
                Crear primer slide
            </Button>
        </div>

        <Dialog v-model:open="isOpen">
            <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{
                        editing ? 'Editar Slide' : 'Nuevo Slide'
                    }}</DialogTitle>
                    <DialogDescription>
                        Personaliza la imagen, el título, la descripción y el
                        botón del slide.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-5 py-2">
                    <div class="space-y-2">
                        <Label>Imagen principal</Label>
                        <div
                            class="relative flex aspect-video w-full cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-white/10 bg-white/5 transition-colors hover:border-pix/40"
                        >
                            <img
                                v-if="preview"
                                :src="preview"
                                class="h-full w-full object-cover"
                                alt="Vista previa"
                            />
                            <div
                                v-else
                                class="flex flex-col items-center gap-2 text-white/40"
                            >
                                <ImagePlus class="h-7 w-7" />
                                <span class="text-xs"
                                    >Subir imagen o elegir de la galería</span
                                >
                            </div>
                            <input
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                class="hidden"
                                @change="onFileChange"
                            />
                            <button
                                v-if="preview"
                                type="button"
                                class="absolute top-2 right-2 flex h-7 w-7 items-center justify-center rounded-full bg-black/60 text-white/90 transition-colors hover:bg-red-500/80"
                                @click="clearImage"
                            >
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <label
                                for="carousel-upload"
                                class="inline-flex h-9 flex-1 cursor-pointer items-center justify-center gap-2 rounded-lg border border-white/15 text-sm font-medium text-white/85 transition-colors hover:bg-white/10 hover:text-white"
                            >
                                <ImagePlus class="h-4 w-4" />
                                Subir archivo
                            </label>
                            <input
                                id="carousel-upload"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                class="hidden"
                                @change="onFileChange"
                            />
                            <Button
                                type="button"
                                variant="outline"
                                class="h-9 flex-1 border-white/15 text-white hover:bg-white/10"
                                @click="galleryOpen = true"
                            >
                                <Images class="mr-2 h-4 w-4" />
                                Elegir de galería
                            </Button>
                        </div>
                        <InputError :message="form.errors.imagen" />
                    </div>

                    <div class="space-y-2">
                        <Label for="titulo">Título *</Label>
                        <Input
                            id="titulo"
                            v-model="form.titulo"
                            type="text"
                            placeholder="DISEÑO DE LOGOTIPOS"
                            class="border-white/10 bg-white/5 text-white placeholder:text-white/40 focus:border-pix/50"
                        />
                        <InputError :message="form.errors.titulo" />
                    </div>

                    <div class="space-y-2">
                        <Label for="descripcion">Descripción</Label>
                        <Textarea
                            id="descripcion"
                            v-model="form.descripcion"
                            rows="3"
                            placeholder="Breve descripción del servicio"
                            class="border-white/10 bg-white/5 text-white placeholder:text-white/40 focus:border-pix/50"
                        />
                        <InputError :message="form.errors.descripcion" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="boton_texto">Texto del botón</Label>
                            <Input
                                id="boton_texto"
                                v-model="form.boton_texto"
                                type="text"
                                placeholder="Solicitar servicio"
                                class="border-white/10 bg-white/5 text-white placeholder:text-white/40 focus:border-pix/50"
                            />
                            <InputError :message="form.errors.boton_texto" />
                        </div>
                        <div class="space-y-2">
                            <Label for="boton_enlace">Enlace del botón</Label>
                            <Input
                                id="boton_enlace"
                                v-model="form.boton_enlace"
                                type="text"
                                placeholder="#contacto"
                                class="border-white/10 bg-white/5 text-white placeholder:text-white/40 focus:border-pix/50"
                            />
                            <InputError :message="form.errors.boton_enlace" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="duracion">Duración (segundos) *</Label>
                        <Input
                            id="duracion"
                            v-model.number="form.duracion"
                            type="number"
                            min="2"
                            max="120"
                            class="border-white/10 bg-white/5 text-white focus:border-pix/50"
                        />
                        <InputError :message="form.errors.duracion" />
                    </div>

                    <label class="flex cursor-pointer items-center gap-3">
                        <div
                            class="relative h-6 w-11 rounded-full transition-colors"
                            :class="
                                form.activo ? 'bg-emerald-500' : 'bg-white/15'
                            "
                            @click="form.activo = !form.activo"
                        >
                            <span
                                class="absolute top-0.5 h-5 w-5 rounded-full bg-white transition-all"
                                :class="
                                    form.activo ? 'left-[22px]' : 'left-0.5'
                                "
                            ></span>
                        </div>
                        <span class="text-sm font-medium text-white/80"
                            >Slide activo</span
                        >
                    </label>
                </div>

                <DialogFooter>
                    <Button variant="ghost" @click="isOpen = false"
                        >Cancelar</Button
                    >
                    <Button
                        class="btn-gradient-blue"
                        :disabled="form.processing"
                        @click="save"
                    >
                        {{ editing ? 'Guardar cambios' : 'Crear slide' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <Dialog
            :open="galleryOpen"
            @update:open="
                (v: boolean) => {
                    galleryOpen = v;
                }
            "
        >
            <DialogContent class="max-h-[85vh] overflow-y-auto sm:max-w-2xl">
                <DialogHeader>
                    <DialogTitle>Elegir de la galería</DialogTitle>
                    <DialogDescription
                        >Selecciona una imagen que hayas subido a tu biblioteca
                        de medios.</DialogDescription
                    >
                </DialogHeader>
                <div
                    v-if="images.length > 0"
                    class="grid grid-cols-2 gap-3 sm:grid-cols-3"
                >
                    <button
                        v-for="img in images"
                        :key="img.path"
                        type="button"
                        class="group overflow-hidden rounded-xl border border-white/10 transition-all hover:border-pix/60"
                        :class="
                            form.imagen_url === img.url ? 'ring-2 ring-pix' : ''
                        "
                        @click="selectFromGallery(img)"
                    >
                        <img
                            :src="img.url"
                            :alt="img.name"
                            class="aspect-square w-full object-cover transition-transform duration-300 group-hover:scale-105"
                        />
                    </button>
                </div>
                <div v-else class="py-10 text-center text-sm text-white/50">
                    Tu galería está vacía. Sube imágenes desde Medios primero.
                </div>
                <DialogFooter>
                    <Button variant="ghost" @click="galleryOpen = false"
                        >Cancelar</Button
                    >
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
