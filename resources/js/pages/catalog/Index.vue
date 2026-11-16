<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogTitle } from '@/components/ui/dialog';
import type { CatalogItem } from '@/data/catalog';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage<{
    disenos: CatalogItem[];
    categorias: string[];
}>();

const catalogItems = computed(
    () => (page.props.disenos || []) as CatalogItem[],
);
const categorias = computed(() => {
    const cats = (page.props.categorias || []) as string[];

    return ['Todos', ...cats];
});

const selectedCategory = ref<string>('Todos');
const searchQuery = ref<string>('');
const sortBy = ref<string>('orden');
const selectedItem = ref<CatalogItem | null>(null);

const filteredItems = computed(() => {
    let items = catalogItems.value.filter((item) => {
        const matchesCategory =
            selectedCategory.value === 'Todos' ||
            item.categoria === selectedCategory.value;
        const matchesSearch =
            item.nombre
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()) ||
            (item.descripcion || '')
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase());

        return matchesCategory && matchesSearch;
    });

    items = [...items].sort((a, b) => {
        if (sortBy.value === 'precio') {
            return a.precio - b.precio;
        }

        if (sortBy.value === 'precio_desc') {
            return b.precio - a.precio;
        }

        return (a.orden ?? 0) - (b.orden ?? 0);
    });

    return items;
});

function selectCategory(cat: string) {
    selectedCategory.value = cat;
}

function openModal(item: CatalogItem) {
    selectedItem.value = item;
}

function closeModal() {
    selectedItem.value = null;
}

function solicitarWhatsApp(item: CatalogItem) {
    const mensaje = encodeURIComponent(
        `Hola, me interesa este diseño: ${item.nombre}`,
    );
    window.open(`https://wa.me/51999999999?text=${mensaje}`, '_blank');
}

function whatsappUrl(item: CatalogItem): string {
    const mensaje = encodeURIComponent(
        `Hola, me interesa este diseño: ${item.nombre}`,
    );

    return `https://wa.me/51999999999?text=${mensaje}`;
}
</script>

<template>
    <Head title="Catálogo de Diseños" />

    <div class="animate-fade-in space-y-8 p-4 md:p-6 lg:p-8">
        <!-- Hero Section -->
        <div
            class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-600/20 via-blue-600/15 to-pink-600/20 p-8 md:p-12"
        >
            <div
                class="absolute -top-20 -right-20 h-72 w-72 animate-pulse rounded-full bg-purple-500/20 blur-3xl"
                style="animation-duration: 6s"
            />
            <div
                class="absolute -bottom-20 -left-20 h-72 w-72 animate-pulse rounded-full bg-pink-500/15 blur-3xl"
                style="animation-duration: 8s; animation-delay: 2s"
            />
            <div
                class="absolute top-1/2 left-1/3 h-48 w-48 animate-pulse rounded-full bg-blue-500/10 blur-3xl"
                style="animation-duration: 7s; animation-delay: 1s"
            />

            <div class="relative z-10">
                <div class="mb-4 flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500/30 to-pink-500/30"
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
                                d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 0 2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128m0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42"
                            />
                        </svg>
                    </div>
                    <span
                        class="rounded-full bg-purple-500/10 px-3 py-1 text-xs font-medium text-purple-600 ring-1 ring-purple-500/20"
                    >
                        Portafolio
                    </span>
                </div>

                <h1
                    class="text-3xl font-bold tracking-tight text-ink md:text-4xl lg:text-5xl"
                >
                    Catálogo de
                    <span
                        class="bg-gradient-to-r from-purple-600 via-blue-600 to-pink-600 bg-clip-text text-transparent"
                    >
                        Diseños CREAPIX
                    </span>
                </h1>
                <p class="mt-3 max-w-2xl text-base text-ink/60 md:text-lg">
                    Explora nuestra galería de trabajos profesionales. Cada
                    pieza es diseñada con pasión y atención al detalle para
                    ofrecer resultados excepcionales.
                </p>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="space-y-4">
            <!-- Search Bar -->
            <div class="relative">
                <svg
                    class="pointer-events-none absolute top-1/2 left-4 h-4 w-4 -translate-y-1/2 text-ink/40"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Buscar diseños..."
                    class="h-12 w-full rounded-2xl border border-ink/10 bg-ink/[0.03] pr-4 pl-11 text-sm text-ink backdrop-blur-sm transition-all duration-300 placeholder:text-ink/30 focus:border-purple-500/40 focus:ring-2 focus:ring-purple-500/15 focus:outline-none"
                />
            </div>

            <!-- Category Filters -->
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="cat in categorias"
                    :key="cat"
                    @click="selectCategory(cat)"
                    class="rounded-full px-4 py-2 text-xs font-medium transition-all duration-300"
                    :class="
                        selectedCategory === cat
                            ? 'scale-105 bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg shadow-purple-500/25'
                            : 'bg-ink/[0.03] text-ink/60 ring-1 ring-ink/5 hover:bg-blush/40 hover:text-ink/80'
                    "
                >
                    {{ cat }}
                </button>
            </div>
        </div>

        <!-- Results Count -->
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-ink/50">
                Mostrando
                <span class="font-medium text-ink/80">{{
                    filteredItems.length
                }}</span>
                de
                <span class="font-medium text-ink/80">{{
                    catalogItems.length
                }}</span>
                diseños
            </p>
            <div class="flex items-center gap-2">
                <label class="text-xs text-ink/50">Ordenar:</label>
                <select
                    v-model="sortBy"
                    class="h-9 rounded-xl border border-ink/10 bg-ink/[0.03] px-3 text-xs text-ink/70 focus:border-purple-500/40 focus:ring-2 focus:ring-purple-500/15 focus:outline-none"
                >
                    <option value="orden">Orden de aparición</option>
                    <option value="precio">Precio: menor a mayor</option>
                    <option value="precio_desc">Precio: mayor a menor</option>
                </select>
            </div>
        </div>

        <!-- Card Grid -->
        <div
            v-if="filteredItems.length > 0"
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3"
        >
            <article
                v-for="(item, index) in filteredItems"
                :key="item.id"
                class="group/card catalog-card animate-scale-in"
                :style="{ animationDelay: `${index * 0.05}s` }"
            >
                <!-- Image Container -->
                <div class="relative aspect-[3/2] overflow-hidden rounded-2xl">
                    <img
                        :src="item.imagen ?? undefined"
                        :alt="item.nombre"
                        loading="lazy"
                        class="h-full w-full object-cover transition-all duration-700 group-hover/card:scale-110"
                    />

                    <div
                        class="absolute inset-0 bg-gradient-to-t from-[#050916]/80 via-transparent to-transparent opacity-0 transition-opacity duration-500 group-hover/card:opacity-100"
                    />

                    <!-- Category Badge -->
                    <span
                        class="absolute top-3 left-3 rounded-full bg-black/40 px-3 py-1 text-[10px] font-medium text-white/90 ring-1 ring-white/10 backdrop-blur-md"
                    >
                        {{ item.categoria }}
                    </span>

                    <!-- Price Badge on Image -->
                    <span
                        class="absolute right-3 bottom-3 translate-y-2 rounded-xl bg-gradient-to-r from-purple-600/90 to-pink-600/90 px-3 py-1.5 text-sm font-bold text-white opacity-0 shadow-lg shadow-purple-500/30 backdrop-blur-md transition-all duration-500 group-hover/card:translate-y-0 group-hover/card:opacity-100"
                    >
                        S/ {{ item.precio.toFixed(2) }}
                    </span>
                </div>

                <!-- Card Body -->
                <div class="mt-4 space-y-2.5">
                    <h3
                        class="text-base font-bold text-ink transition-colors duration-300 group-hover/card:bg-gradient-to-r group-hover/card:from-purple-600 group-hover/card:to-pink-600 group-hover/card:bg-clip-text group-hover/card:text-transparent"
                    >
                        {{ item.nombre }}
                    </h3>

                    <p class="line-clamp-2 text-sm leading-relaxed text-ink/50">
                        {{
                            item.descripcion ||
                            'Diseño sin descripción disponible'
                        }}
                    </p>

                    <div class="flex items-center gap-2 pt-0.5">
                        <span class="text-lg font-bold text-ink"
                            >S/ {{ item.precio.toFixed(2) }}</span
                        >
                        <span
                            v-if="item.precio_anterior"
                            class="text-sm text-ink/40 line-through"
                        >
                            S/ {{ Number(item.precio_anterior).toFixed(2) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 pt-0.5">
                        <Button
                            variant="outline"
                            size="sm"
                            class="h-9 flex-1 rounded-xl border-ink/10 text-xs font-medium text-ink/70 transition-all duration-300 hover:border-pix/30 hover:bg-purple-500/10 hover:text-purple-300"
                            @click="openModal(item)"
                        >
                            <svg
                                class="mr-1.5 h-3.5 w-3.5"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"
                                />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            Ver Diseño
                        </Button>
                        <Button
                            size="sm"
                            class="h-9 flex-1 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-xs font-semibold text-white shadow-lg shadow-purple-500/20 transition-all duration-300 hover:from-purple-500 hover:to-pink-500 hover:shadow-purple-500/40"
                            @click="solicitarWhatsApp(item)"
                        >
                            <svg
                                class="mr-1.5 h-3.5 w-3.5"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"
                                />
                            </svg>
                            Solicitar
                        </Button>
                    </div>
                </div>
            </article>
        </div>

        <!-- Empty State -->
        <div
            v-else
            class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-ink/10 bg-ink/[0.02] px-6 py-16 text-center"
        >
            <svg
                class="mb-4 h-12 w-12 text-ink/30"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1"
            >
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
            <p class="text-sm font-medium text-ink/40">
                No se encontraron diseños
            </p>
            <p class="mt-1 text-xs text-ink/30">
                Intenta con otros términos de búsqueda o categoría
            </p>
        </div>

        <!-- Preview Modal -->
        <Dialog
            :open="selectedItem !== null"
            @update:open="
                (val: boolean) => {
                    if (!val) closeModal();
                }
            "
        >
            <DialogContent
                class="max-w-4xl border-white/10 bg-[#0a0f24]/95 p-0 backdrop-blur-xl"
                show-close-button
            >
                <div
                    v-if="selectedItem"
                    class="grid overflow-hidden md:grid-cols-5"
                >
                    <!-- Image Section -->
                    <div
                        class="relative aspect-[4/3] md:col-span-3 md:aspect-auto"
                    >
                        <img
                            :src="selectedItem.imagen ?? undefined"
                            :alt="selectedItem.nombre"
                            class="h-full w-full object-cover"
                        />
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-transparent to-[#0a0f24]/40"
                        />
                        <span
                            class="absolute top-4 left-4 rounded-full bg-black/50 px-3 py-1 text-[11px] font-medium text-white/90 ring-1 ring-white/10 backdrop-blur-md"
                        >
                            {{ selectedItem.categoria }}
                        </span>
                    </div>

                    <!-- Details Section -->
                    <div
                        class="flex flex-col justify-center space-y-5 p-6 md:col-span-2 md:p-8"
                    >
                        <div>
                            <DialogTitle
                                class="text-xl font-bold text-white md:text-2xl"
                            >
                                {{ selectedItem.nombre }}
                            </DialogTitle>
                        </div>

                        <p class="text-sm leading-relaxed text-white/60">
                            {{
                                selectedItem.descripcion ||
                                'Diseño sin descripción disponible'
                            }}
                        </p>

                        <div
                            class="flex items-baseline gap-2 rounded-2xl bg-gradient-to-r from-purple-500/10 to-pink-500/10 px-5 py-4"
                        >
                            <span class="text-sm text-white/40">Precio:</span>
                            <span
                                class="bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-3xl font-bold text-transparent"
                            >
                                S/ {{ selectedItem.precio.toFixed(2) }}
                            </span>
                        </div>

                        <a
                            :href="whatsappUrl(selectedItem)"
                            target="_blank"
                            class="flex h-12 items-center justify-center gap-2.5 rounded-2xl bg-gradient-to-r from-green-600 to-emerald-600 text-sm font-semibold text-white shadow-lg shadow-green-500/25 transition-all duration-300 hover:from-green-500 hover:to-emerald-500 hover:shadow-green-500/40"
                        >
                            <svg
                                class="h-5 w-5"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"
                                />
                            </svg>
                            Solicitar por WhatsApp
                        </a>

                        <p class="text-center text-[11px] text-white/30">
                            Al hacer clic se abrirá WhatsApp con el mensaje
                            listo
                        </p>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>

<style scoped>
.catalog-card {
    animation-fill-mode: both;
}
</style>
