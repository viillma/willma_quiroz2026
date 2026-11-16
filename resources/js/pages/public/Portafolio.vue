<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ImageIcon, MessageCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import BrandLogo from '@/components/brand/BrandLogo.vue';

type PortfolioItem = {
    id: number;
    titulo: string;
    categoria: string;
    descripcion: string;
    imagen: string | null;
    fecha: string | null;
    images: Array<{ id: number; url: string }>;
    servicio: string | null;
};

type Site = {
    nombre: string;
    logo: string | null;
    whatsapp: string;
    currency_symbol: string;
};

const props = defineProps<{
    site: Site;
    portafolios: PortfolioItem[];
    categorias: string[];
}>();

const categorias = computed(() => ['Todos', ...props.categorias]);
const active = ref('Todos');

const filtered = computed(() =>
    props.portafolios.filter(
        (p) => active.value === 'Todos' || p.categoria === active.value,
    ),
);

function waLink(titulo: string) {
    return `https://wa.me/${props.site.whatsapp}?text=${encodeURIComponent(`Hola, me interesa un proyecto similar a "${titulo}".`)}`;
}
</script>

<template>
    <Head title="Portafolio" />

    <div class="min-h-screen bg-cream font-sans text-ink antialiased">
        <header
            class="fixed inset-x-0 top-0 z-50 border-b border-ink/5 bg-cream/80 backdrop-blur-md"
        >
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4"
            >
                <Link href="/" class="flex items-center gap-3">
                    <BrandLogo
                        :logo="site.logo"
                        :name="site.nombre"
                        mode="compact"
                    />
                </Link>
                <nav class="hidden items-center gap-7 md:flex">
                    <Link
                        href="/"
                        class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix"
                        >Inicio</Link
                    >
                    <Link
                        href="/servicios"
                        class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix"
                        >Servicios</Link
                    >
                    <Link
                        href="/catalogo"
                        class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix"
                        >Catálogo</Link
                    >
                    <Link
                        href="/contacto"
                        class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix"
                        >Contacto</Link
                    >
                </nav>
            </div>
        </header>

        <section class="relative overflow-hidden pt-32">
            <div
                class="pointer-events-none absolute top-16 -left-24 h-72 w-72 rounded-full bg-mint/50 blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute top-32 -right-24 h-72 w-72 rounded-full bg-lilac/50 blur-3xl"
            ></div>

            <div class="relative mx-auto max-w-7xl px-5 md:px-8">
                <p
                    class="text-xs font-bold tracking-[0.18em] text-pix uppercase"
                >
                    Portafolio
                </p>
                <h1
                    class="mt-3 max-w-2xl font-display text-4xl leading-tight font-extrabold tracking-tight md:text-5xl"
                >
                    Nuestro trabajo habla por nosotros
                </h1>
                <p class="mt-4 max-w-xl text-ink/60">
                    Proyectos reales, resultados reales. Explora una muestra de
                    lo que hemos creado para nuestros clientes.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-14 md:px-8">
            <div
                v-if="categorias.length > 1"
                class="mb-10 flex flex-wrap gap-3"
            >
                <button
                    v-for="cat in categorias"
                    :key="cat"
                    @click="active = cat"
                    class="rounded-full px-5 py-2.5 text-sm font-bold transition-colors"
                    :class="
                        active === cat
                            ? 'bg-pix text-white shadow-lg shadow-pix/25'
                            : 'border border-ink/10 bg-white text-ink/60 hover:border-pix hover:text-pix'
                    "
                >
                    {{ cat }}
                </button>
            </div>

            <div
                v-if="filtered.length === 0"
                class="py-16 text-center text-ink/50"
            >
                No hay proyectos disponibles en esta categoría.
            </div>

            <div class="columns-1 gap-6 sm:columns-2 lg:columns-3 [&>*]:mb-6">
                <div
                    v-for="(p, i) in filtered"
                    :key="p.id"
                    class="group relative break-inside-avoid overflow-hidden rounded-3xl border border-ink/5 bg-white shadow-[0_14px_40px_-18px_rgba(23,26,43,0.16)]"
                >
                    <div
                        class="relative overflow-hidden"
                        :class="i % 2 === 0 ? 'aspect-[4/3]' : 'aspect-[3/4]'"
                    >
                        <img
                            v-if="p.imagen"
                            :src="p.imagen"
                            :alt="p.titulo"
                            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center bg-lavender/60 text-ink/20"
                        >
                            <ImageIcon class="h-12 w-12" />
                        </div>
                        <div
                            class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent p-5 pt-20"
                        >
                            <p
                                class="text-[11px] font-bold tracking-[0.14em] text-white/70 uppercase"
                            >
                                {{ p.categoria }}
                                <span v-if="p.servicio">
                                    · {{ p.servicio }}</span
                                >
                            </p>
                            <h2
                                class="mt-1 font-display text-lg font-extrabold text-white"
                            >
                                {{ p.titulo }}
                            </h2>
                            <p
                                v-if="p.descripcion"
                                class="mt-1 line-clamp-2 text-xs leading-relaxed text-white/70"
                            >
                                {{ p.descripcion }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between bg-white px-5 py-4"
                    >
                        <span class="text-xs font-semibold text-ink/40">{{
                            p.fecha
                        }}</span>
                        <a
                            :href="waLink(p.titulo)"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-pix text-white shadow-lg shadow-pix/20 transition-transform group-hover:scale-110"
                            title="Consultar por WhatsApp"
                        >
                            <MessageCircle class="h-4 w-4" />
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
