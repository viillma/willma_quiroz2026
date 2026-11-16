<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Copy, ImagePlus, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import media from '@/routes/media';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage();

type MediaFile = {
    path: string;
    name: string;
    url: string;
    size: number;
    modified: number;
};

const images = computed(() => (page.props.images || []) as MediaFile[]);

const form = useForm({
    imagen: null as File | null,
});

const preview = ref<string | null>(null);
const selected = ref<MediaFile | null>(null);

function onFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.imagen = file;
        preview.value = URL.createObjectURL(file);
    }
}

function upload() {
    form.post(media.store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            preview.value = null;
            const input = document.getElementById(
                'media-upload',
            ) as HTMLInputElement;

            if (input) {
                input.value = '';
            }
        },
    });
}

function formatSize(bytes: number) {
    if (bytes < 1024) {
        return `${bytes} B`;
    }

    if (bytes < 1024 * 1024) {
        return `${(bytes / 1024).toFixed(1)} KB`;
    }

    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

function formatDate(ts: number) {
    return new Date(ts * 1000).toLocaleDateString();
}

function remove(file: MediaFile) {
    if (window.confirm(`¿Eliminar "${file.name}"?`)) {
        router.delete(media.destroy.url({ medium: file.name }), {
            data: { path: file.path },
            preserveScroll: true,
        });
    }
}

function copyUrl(file: MediaFile) {
    navigator.clipboard?.writeText(file.url);
    toast.success('URL copiada');
}
</script>

<template>
    <Head title="Biblioteca Multimedia" />

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
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="9" cy="9" r="2" />
                        <path d="m21 15-3.1-3.1a2 2 0 0 0-2.8 0L6 21" />
                    </svg>
                </div>
                <Heading
                    title="Biblioteca Multimedia"
                    description="Sube y administra las imágenes de tu página"
                    variant="small"
                />
            </div>
        </div>

        <div class="glass-card rounded-2xl p-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <label
                    class="flex aspect-video w-full max-w-[220px] cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-white/10 bg-white/5 transition-colors hover:border-pix/40"
                >
                    <img
                        v-if="preview"
                        :src="preview"
                        class="h-full w-full object-cover"
                        alt="Vista previa"
                    />
                    <div
                        v-else
                        class="flex flex-col items-center gap-2 p-4 text-center text-white/40"
                    >
                        <ImagePlus class="h-6 w-6" />
                        <span class="text-xs">Elegir imagen</span>
                    </div>
                    <input
                        id="media-upload"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        class="hidden"
                        @change="onFileChange"
                    />
                </label>
                <div class="flex-1">
                    <p
                        class="mb-1 text-xs tracking-wider text-white/40 uppercase"
                    >
                        Nuevo archivo
                    </p>
                    <p class="text-sm text-white/60">
                        Formatos admitidos: JPG, PNG, WEBP.
                    </p>
                    <InputError :message="form.errors.imagen" />
                    <Button
                        class="btn-gradient-blue mt-3"
                        :disabled="!form.imagen || form.processing"
                        @click="upload"
                    >
                        <ImagePlus class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'Subiendo...' : 'Subir imagen' }}
                    </Button>
                </div>
            </div>
        </div>

        <div
            v-if="images.length > 0"
            class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4"
        >
            <div
                v-for="img in images"
                :key="img.path"
                class="group relative overflow-hidden rounded-2xl bg-white/5 ring-1 ring-white/10"
                @click="selected = img"
            >
                <img
                    :src="img.url"
                    :alt="img.name"
                    class="aspect-square w-full object-cover transition-transform duration-500 group-hover:scale-105"
                />
                <div class="flex flex-col gap-2 p-3">
                    <p
                        class="truncate text-xs font-medium text-white/70"
                        :title="img.name"
                    >
                        {{ img.name }}
                    </p>
                    <p class="text-[11px] text-white/40">
                        {{ formatSize(img.size) }} ·
                        {{ formatDate(img.modified) }}
                    </p>
                </div>
            </div>
        </div>

        <div
            v-if="images.length === 0"
            class="glass-card flex flex-col items-center justify-center rounded-2xl px-6 py-16 text-center"
        >
            <div
                class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-white/10"
            >
                <ImagePlus class="h-7 w-7 text-white/40" />
            </div>
            <p class="text-base font-medium text-white/60">
                Tu biblioteca está vacía
            </p>
        </div>

        <Dialog
            :open="!!selected"
            @update:open="
                (v: boolean) => {
                    if (!v) selected = null;
                }
            "
        >
            <DialogContent v-if="selected" class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{ selected.name }}</DialogTitle>
                    <DialogDescription
                        >Vista previa y opciones del archivo.</DialogDescription
                    >
                </DialogHeader>
                <img
                    :src="selected.url"
                    :alt="selected.name"
                    class="max-h-[50vh] w-full rounded-xl object-contain"
                />
                <div class="flex items-center justify-between gap-3">
                    <Button variant="outline" @click="copyUrl(selected)">
                        <Copy class="mr-2 h-4 w-4" />
                        Copiar URL
                    </Button>
                    <Button variant="destructive" @click="remove(selected)">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Eliminar
                    </Button>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>
