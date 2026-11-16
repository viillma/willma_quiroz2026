<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import {
    Plus,
    Pencil,
    Trash2,
    Search,
    ImagePlus,
    X,
    Loader2,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
} from '@/components/ui/dialog';
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
import { usePermissions } from '@/composables/usePermissions';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { formatMoney } from '@/lib/money';
import catalogo from '@/routes/catalogo';

defineOptions({
    layout: AppSidebarLayout,
});

type Diseno = {
    id: number;
    nombre: string;
    categoria: string;
    descripcion: string | null;
    precio: number;
    precio_anterior: number | null;
    estado: string;
    orden: number;
    imagen: string | null;
    created_at: string | null;
};

const page = usePage<{ disenos: Diseno[]; categorias: string[] }>();
const { can } = usePermissions();

const disenos = computed(() => (page.props.disenos || []) as Diseno[]);
const categorias = computed(() => (page.props.categorias || []) as string[]);

const searchQuery = ref('');
const categoryFilter = ref('Todas');
const sortBy = ref('orden');

const filteredDisenos = computed(() => {
    let items = disenos.value.filter((d) => {
        const matchesCat =
            categoryFilter.value === 'Todas' ||
            d.categoria === categoryFilter.value;
        const q = searchQuery.value.toLowerCase();
        const matchesSearch =
            !q ||
            d.nombre.toLowerCase().includes(q) ||
            (d.descripcion || '').toLowerCase().includes(q);

        return matchesCat && matchesSearch;
    });

    items = [...items].sort((a, b) => {
        if (sortBy.value === 'precio') {
            return a.precio - b.precio;
        }

        if (sortBy.value === 'precio_desc') {
            return b.precio - a.precio;
        }

        if (sortBy.value === 'nuevo') {
            return (b.created_at || '').localeCompare(a.created_at || '');
        }

        return a.orden - b.orden;
    });

    return items;
});

const dialogOpen = ref(false);
const editingId = ref<number | null>(null);
const preview = ref<string | null>(null);
const imageError = ref<string | null>(null);
const selectedFileName = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    nombre: '',
    categoria: 'Logos',
    descripcion: '',
    precio: '',
    precio_anterior: '',
    estado: 'activo',
    orden: 0,
    imagen: null as File | null,
});

function resetForm() {
    form.clearErrors();
    form.reset();
    form.categoria = 'Logos';
    form.estado = 'activo';
    form.orden = 0;
    preview.value = null;
    imageError.value = null;
    selectedFileName.value = null;
    editingId.value = null;
}

function openCreate() {
    resetForm();
    dialogOpen.value = true;
}

function openEdit(diseno: Diseno) {
    resetForm();
    editingId.value = diseno.id;
    form.nombre = diseno.nombre;
    form.categoria = diseno.categoria;
    form.descripcion = diseno.descripcion || '';
    form.precio = String(diseno.precio);
    form.precio_anterior =
        diseno.precio_anterior !== null ? String(diseno.precio_anterior) : '';
    form.estado = diseno.estado;
    form.orden = diseno.orden;
    preview.value = diseno.imagen;
    dialogOpen.value = true;
}

const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
const MAX_SIZE = 10 * 1024 * 1024;

function onFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file) {
        return;
    }

    imageError.value = null;

    if (!ALLOWED_TYPES.includes(file.type)) {
        imageError.value = 'Formato no válido. Usa una imagen JPG, PNG o WEBP.';
        target.value = '';
        selectedFileName.value = null;

        return;
    }

    if (file.size > MAX_SIZE) {
        imageError.value = 'La imagen es muy pesada (máx. 10 MB).';
        target.value = '';
        selectedFileName.value = null;

        return;
    }

    form.imagen = file;
    preview.value = URL.createObjectURL(file);
    selectedFileName.value = file.name;
}

function clearImage() {
    form.imagen = null;
    preview.value = null;
    imageError.value = null;
    selectedFileName.value = null;

    if (fileInput.value) {
        fileInput.value.value = '';
    }
}

function submit() {
    if (editingId.value !== null) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(catalogo.update.url({ catalogoDiseno: editingId.value }), {
            preserveScroll: true,
            onSuccess: () => {
                dialogOpen.value = false;
                resetForm();
            },
        });
    } else {
        form.post(catalogo.store.url(), {
            preserveScroll: true,
            onSuccess: () => {
                dialogOpen.value = false;
                resetForm();
            },
        });
    }
}

function remove(diseno: Diseno) {
    if (window.confirm(`¿Eliminar el diseño "${diseno.nombre}"?`)) {
        router.delete(catalogo.destroy.url({ catalogoDiseno: diseno.id }), {
            preserveScroll: true,
        });
    }
}

const canCreate = computed(() => can('catalogo.crear'));
const canEdit = computed(() => can('catalogo.editar'));
const canDelete = computed(() => can('catalogo.eliminar'));

function estadoBadge(estado: string) {
    return estado === 'activo' ? 'badge-green' : 'badge-orange';
}
</script>

<template>
    <Head title="Catálogo · Administrar" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <div
            class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500/20 to-pink-500/20 ring-1 ring-ink/10"
                >
                    <svg
                        class="h-5 w-5 text-purple-600"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0021.75 19.5V4.5A1.5 1.5 0 0020.25 3H3.75A1.5 1.5 0 002.25 4.5v15A1.5 1.5 0 003.75 21z"
                        />
                    </svg>
                </div>
                <Heading
                    title="Catálogo de Diseños"
                    description="Administra los productos y diseños que se muestran en tu catálogo"
                />
            </div>
            <Button
                v-if="canCreate"
                class="btn-gradient-purple btn-shimmer"
                @click="openCreate"
            >
                <Plus class="mr-2 h-4 w-4" />
                Agregar nuevo diseño
            </Button>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div
                class="kpi-blue kpi-card glow-blue animate-slide-up rounded-2xl p-5 shadow-lg"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Total diseños
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ disenos.length }}
                </p>
            </div>
            <div
                class="kpi-green kpi-card glow-green animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.1s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Activos
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ disenos.filter((d) => d.estado === 'activo').length }}
                </p>
            </div>
            <div
                class="kpi-orange kpi-card glow-orange animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.2s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Inactivos
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ disenos.filter((d) => d.estado !== 'activo').length }}
                </p>
            </div>
        </div>

        <div class="glass-card space-y-3 rounded-2xl p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center">
                <div class="relative flex-1">
                    <Search
                        class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink/50"
                    />
                    <Input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Buscar por nombre o descripción..."
                        class="glass pl-9"
                    />
                </div>
                <div class="flex gap-2">
                    <Select v-model="categoryFilter">
                        <SelectTrigger class="glass w-44">
                            <SelectValue placeholder="Categoría" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="Todas"
                                >Todas las categorías</SelectItem
                            >
                            <SelectItem
                                v-for="cat in categorias"
                                :key="cat"
                                :value="cat"
                                >{{ cat }}</SelectItem
                            >
                        </SelectContent>
                    </Select>
                    <Select v-model="sortBy">
                        <SelectTrigger class="glass w-48">
                            <SelectValue placeholder="Ordenar" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="orden"
                                >Orden de aparición</SelectItem
                            >
                            <SelectItem value="nuevo">Más recientes</SelectItem>
                            <SelectItem value="precio"
                                >Precio: menor a mayor</SelectItem
                            >
                            <SelectItem value="precio_desc"
                                >Precio: mayor a menor</SelectItem
                            >
                        </SelectContent>
                    </Select>
                </div>
            </div>
        </div>

        <p class="text-sm text-ink/60">
            Mostrando
            <span class="font-semibold text-ink/80">{{
                filteredDisenos.length
            }}</span>
            de
            <span class="font-semibold text-ink/80">{{ disenos.length }}</span>
            diseños
        </p>

        <div
            v-if="filteredDisenos.length > 0"
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3"
        >
            <article
                v-for="(diseno, index) in filteredDisenos"
                :key="diseno.id"
                class="glass-card group animate-scale-in"
                :style="{ animationDelay: `${index * 0.05}s` }"
            >
                <div
                    class="relative aspect-[3/2] overflow-hidden rounded-t-2xl"
                >
                    <img
                        :src="diseno.imagen ?? undefined"
                        :alt="diseno.nombre"
                        loading="lazy"
                        class="h-full w-full object-cover transition-all duration-700 group-hover:scale-110"
                    />
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-[#0a0f24]/80 via-transparent to-transparent"
                    />
                    <span
                        class="absolute top-3 left-3 rounded-full bg-black/40 px-3 py-1 text-[10px] font-medium text-white/90 ring-1 ring-white/10 backdrop-blur-md"
                    >
                        {{ diseno.categoria }}
                    </span>
                    <span
                        class="absolute top-3 right-3 inline-flex items-center rounded-full px-3 py-1 text-[10px] font-medium backdrop-blur-md"
                        :class="estadoBadge(diseno.estado)"
                    >
                        {{ diseno.estado }}
                    </span>
                </div>

                <div class="space-y-2.5 p-4">
                    <h3 class="text-base font-bold text-ink">
                        {{ diseno.nombre }}
                    </h3>
                    <p class="line-clamp-2 text-sm leading-relaxed text-ink/60">
                        {{ diseno.descripcion || 'Sin descripción' }}
                    </p>
                    <div class="flex items-center gap-2 pt-0.5">
                        <span class="text-lg font-bold text-ink">{{
                            formatMoney(diseno.precio)
                        }}</span>
                        <span
                            v-if="diseno.precio_anterior"
                            class="text-sm text-ink/50 line-through"
                        >
                            {{ formatMoney(diseno.precio_anterior) }}
                        </span>
                    </div>
                    <div
                        v-if="canEdit || canDelete"
                        class="flex gap-2 border-t border-ink/10 pt-3"
                    >
                        <Button
                            v-if="canEdit"
                            size="sm"
                            class="btn-gradient-ghost flex-1"
                            @click="openEdit(diseno)"
                        >
                            <Pencil class="mr-1.5 h-4 w-4" />
                            Editar
                        </Button>
                        <Button
                            v-if="canDelete"
                            size="sm"
                            class="btn-gradient-ghost flex-1 text-red-600 hover:text-red-700"
                            @click="remove(diseno)"
                        >
                            <Trash2 class="mr-1.5 h-4 w-4" />
                            Eliminar
                        </Button>
                    </div>
                </div>
            </article>
        </div>

        <div
            v-else
            class="glass-card flex flex-col items-center justify-center rounded-2xl px-6 py-16 text-center"
        >
            <div
                class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-ink/10"
            >
                <ImagePlus class="h-7 w-7 text-ink/50" />
            </div>
            <p class="text-base font-semibold text-ink/70">
                {{
                    disenos.length === 0
                        ? 'No hay diseños en el catálogo'
                        : 'No se encontraron diseños'
                }}
            </p>
            <p class="mt-1 text-sm text-ink/50">
                {{
                    disenos.length === 0
                        ? 'Usa el botón "Agregar nuevo diseño" para comenzar.'
                        : 'Prueba con otros filtros o búsqueda.'
                }}
            </p>
        </div>

        <Dialog
            :open="dialogOpen"
            @update:open="
                (val: boolean) => {
                    if (!val) {
                        dialogOpen = false;
                        resetForm();
                    }
                }
            "
        >
            <DialogContent
                class="max-w-2xl border-white/10 bg-[#0a0f24]/95 backdrop-blur-xl"
            >
                <DialogHeader>
                    <DialogTitle class="text-lg font-semibold text-white">
                        {{
                            editingId !== null
                                ? 'Editar diseño'
                                : 'Agregar nuevo diseño'
                        }}
                    </DialogTitle>
                    <DialogDescription class="text-sm text-white/50">
                        {{
                            editingId !== null
                                ? 'Modifica la información del diseño.'
                                : 'Completa los datos; podrás subir una imagen en JPG, PNG o WEBP.'
                        }}
                    </DialogDescription>
                </DialogHeader>

                <form class="space-y-5" @submit.prevent="submit">
                    <div class="flex flex-col gap-5 md:flex-row">
                        <div class="flex flex-col items-center gap-3 md:w-64">
                            <div
                                class="relative flex aspect-[4/3] w-full items-center justify-center overflow-hidden rounded-xl border border-dashed border-white/25 bg-white/5"
                            >
                                <img
                                    v-if="preview"
                                    :src="preview"
                                    :alt="form.nombre || 'Vista previa'"
                                    class="h-full w-full object-cover"
                                />
                                <label
                                    v-else
                                    for="catalogo-imagen"
                                    class="flex h-full w-full cursor-pointer flex-col items-center justify-center gap-2 p-6 text-center text-white/40 transition-colors hover:text-purple-300"
                                >
                                    <ImagePlus class="h-8 w-8" />
                                    <span class="text-xs">Subir imagen</span>
                                </label>
                                <span
                                    v-if="preview && !selectedFileName"
                                    class="absolute bottom-2 left-1/2 -translate-x-1/2 rounded-full bg-black/50 px-3 py-1 text-[10px] text-white/80 ring-1 ring-white/10 backdrop-blur-md"
                                >
                                    Imagen actual
                                </span>
                                <button
                                    v-if="preview"
                                    type="button"
                                    class="absolute top-2 right-2 flex h-7 w-7 items-center justify-center rounded-full bg-black/50 text-white/80 transition-colors hover:bg-red-500/80 hover:text-white"
                                    @click="clearImage"
                                >
                                    <X class="h-4 w-4" />
                                </button>
                            </div>

                            <input
                                id="catalogo-imagen"
                                ref="fileInput"
                                type="file"
                                accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp"
                                class="hidden"
                                @change="onFileChange"
                            />

                            <p
                                v-if="selectedFileName"
                                class="w-full max-w-full truncate text-center text-xs text-white/70"
                                :title="selectedFileName"
                            >
                                <span class="font-semibold text-white/85">{{
                                    selectedFileName
                                }}</span>
                            </p>

                            <div class="flex w-full gap-2">
                                <label
                                    for="catalogo-imagen"
                                    class="inline-flex h-8 flex-1 cursor-pointer items-center justify-center gap-2 rounded-md border border-white/15 text-sm font-medium text-white/85 transition-colors hover:bg-white/10 hover:text-white"
                                >
                                    <ImagePlus class="h-4 w-4" />
                                    {{
                                        preview !== null
                                            ? 'Cambiar imagen'
                                            : 'Seleccionar imagen'
                                    }}
                                </label>
                                <button
                                    v-if="preview"
                                    type="button"
                                    class="inline-flex h-8 cursor-pointer items-center justify-center gap-2 rounded-md border border-white/15 px-3 text-sm font-medium text-red-400 transition-colors hover:bg-red-500/10 hover:text-red-300"
                                    @click="clearImage"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>

                            <p
                                v-if="imageError"
                                class="w-full text-xs text-red-400"
                            >
                                {{ imageError }}
                            </p>
                            <p
                                v-if="form.errors.imagen"
                                class="w-full text-xs text-red-400"
                            >
                                {{ form.errors.imagen }}
                            </p>
                            <p
                                class="w-full text-center text-[10px] leading-tight text-white/40"
                            >
                                JPG, JPEG, PNG o WEBP · Máx. 10 MB
                            </p>
                        </div>

                        <div class="flex-1 space-y-4">
                            <div class="space-y-2">
                                <Label for="nombre" class="text-white/80"
                                    >Nombre del diseño *</Label
                                >
                                <Input
                                    id="nombre"
                                    v-model="form.nombre"
                                    type="text"
                                    placeholder="Ej: Logotipo Premium"
                                    class="glass"
                                />
                                <p
                                    v-if="form.errors.nombre"
                                    class="text-xs text-red-400"
                                >
                                    {{ form.errors.nombre }}
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <Label class="text-white/80"
                                        >Categoría *</Label
                                    >
                                    <Select v-model="form.categoria">
                                        <SelectTrigger class="glass">
                                            <SelectValue
                                                placeholder="Categoría"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="cat in categorias"
                                                :key="cat"
                                                :value="cat"
                                                >{{ cat }}</SelectItem
                                            >
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div class="space-y-2">
                                    <Label class="text-white/80"
                                        >Estado *</Label
                                    >
                                    <Select v-model="form.estado">
                                        <SelectTrigger class="glass">
                                            <SelectValue />
                                        </SelectTrigger>
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
                                <Label for="descripcion" class="text-white/80"
                                    >Descripción</Label
                                >
                                <Textarea
                                    id="descripcion"
                                    v-model="form.descripcion"
                                    rows="3"
                                    placeholder="Breve descripción del diseño..."
                                    class="glass"
                                />
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <Label for="precio" class="text-white/80"
                                        >Precio (Bs) *</Label
                                    >
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
                                        class="text-xs text-red-400"
                                    >
                                        {{ form.errors.precio }}
                                    </p>
                                </div>
                                <div class="space-y-2">
                                    <Label
                                        for="precio_anterior"
                                        class="text-white/80"
                                        >Precio anterior (Bs)</Label
                                    >
                                    <Input
                                        id="precio_anterior"
                                        v-model="form.precio_anterior"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        placeholder="Opcional"
                                        class="glass"
                                    />
                                    <p
                                        v-if="form.errors.precio_anterior"
                                        class="text-xs text-red-400"
                                    >
                                        {{ form.errors.precio_anterior }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="orden" class="text-white/80"
                                    >Orden de aparición</Label
                                >
                                <Input
                                    id="orden"
                                    v-model.number="form.orden"
                                    type="number"
                                    min="0"
                                    placeholder="0"
                                    class="glass"
                                />
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex justify-end gap-2 border-t border-white/10 pt-4"
                    >
                        <Button
                            type="button"
                            variant="ghost"
                            class="text-white/60"
                            @click="
                                dialogOpen = false;
                                resetForm();
                            "
                        >
                            Cancelar
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="btn-gradient-purple"
                        >
                            <Loader2
                                v-if="form.processing"
                                class="mr-2 h-4 w-4 animate-spin"
                            />
                            {{
                                editingId !== null
                                    ? 'Guardar cambios'
                                    : 'Agregar diseño'
                            }}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
