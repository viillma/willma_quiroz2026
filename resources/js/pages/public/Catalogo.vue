<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ImageIcon, MessageCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import BrandLogo from '@/components/brand/BrandLogo.vue';

type CatalogoItem = {
    id: number;
    nombre: string;
    categoria: string;
    descripcion: string;
    precio: number;
    precio_anterior: number | null;
    estado: string;
    publicado: boolean;
    destacado: boolean;
    imagen: string | null;
};

type Site = {
    nombre: string;
    logo: string | null;
    whatsapp: string;
    currency_symbol: string;
};

const props = defineProps<{
    site: Site;
    disenos: CatalogoItem[];
    categorias: string[];
}>();

const categorias = computed(() => ['Todos', ...props.categorias]);
const active = ref('Todos');
const search = ref('');

const filtered = computed(() => {
    return props.disenos.filter((d) => {
        const byCat = active.value === 'Todos' || d.categoria === active.value;
        const q = search.value.trim().toLowerCase();
        const bySearch =
            !q ||
            d.nombre.toLowerCase().includes(q) ||
            d.descripcion.toLowerCase().includes(q);

        return byCat && bySearch;
    });
});

function waLink(nombre: string) {
    return `https://wa.me/${props.site.whatsapp}?text=${encodeURIComponent(`Hola, me interesa el diseño "${nombre}". ¿Me dan más información?`)}`;
}
</script>

<template>
    <Head title="Catálogo" />

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
                        href="/portafolio"
                        class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix"
                        >Portafolio</Link
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
                class="pointer-events-none absolute top-16 -left-24 h-72 w-72 rounded-full bg-lilac/50 blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute top-32 -right-24 h-72 w-72 rounded-full bg-peach/50 blur-3xl"
            ></div>

            <div class="relative mx-auto max-w-7xl px-5 md:px-8">
                <p
                    class="text-xs font-bold tracking-[0.18em] text-pix uppercase"
                >
                    Catálogo
                </p>
                <h1
                    class="mt-3 max-w-2xl font-display text-4xl leading-tight font-extrabold tracking-tight md:text-5xl"
                >
                    Diseños listos para usar
                </h1>
                <p class="mt-4 max-w-xl text-ink/60">
                    Explora nuestra galería de diseños disponibles. Elige el que
                    más te guste y lo personalizamos para ti.
                </p>

                <div
                    class="mt-8 flex flex-col gap-4 md:flex-row md:items-center"
                >
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Buscar diseño..."
                        class="w-full rounded-full border border-ink/10 bg-white px-6 py-3.5 text-sm transition-colors outline-none placeholder:text-ink/30 focus:border-pix md:max-w-sm"
                    />
                </div>

                <div
                    v-if="categorias.length > 1"
                    class="mt-5 flex flex-wrap gap-3"
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
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-14 md:px-8">
            <div
                v-if="filtered.length === 0"
                class="py-16 text-center text-ink/50"
            >
                No se encontraron diseños con los filtros actuales.
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="d in filtered"
                    :key="d.id"
                    class="group relative flex flex-col overflow-hidden rounded-3xl border border-ink/5 bg-white shadow-[0_14px_40px_-18px_rgba(23,26,43,0.16)] transition-all duration-500 hover:-translate-y-1.5 hover:shadow-[0_24px_50px_-20px_rgba(23,26,43,0.24)]"
                >
                    <div
                        class="relative aspect-[4/3] overflow-hidden bg-lavender/60"
                    >
                        <img
                            v-if="d.imagen"
                            :src="d.imagen"
                            :alt="d.nombre"
                            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center text-ink/20"
                        >
                            <ImageIcon class="h-12 w-12" />
                        </div>
                        <span
                            class="absolute top-4 left-4 rounded-full bg-white/90 px-3 py-1 text-[11px] font-bold tracking-wide text-ink/60 uppercase backdrop-blur"
                        >
                            {{ d.categoria }}
                        </span>
                    </div>

                    <div class="flex flex-1 flex-col p-5">
                        <h2
                            class="font-display text-lg font-extrabold tracking-tight"
                        >
                            {{ d.nombre }}
                        </h2>
                        <p class="mt-2 line-clamp-2 flex-1 text-sm text-ink/55">
                            {{ d.descripcion }}
                        </p>

                        <div
                            class="mt-5 flex items-end justify-between border-t border-ink/5 pt-4"
                        >
                            <div>
                                <span class="text-xl font-extrabold text-pix">
                                    {{ site.currency_symbol }} {{ d.precio }}
                                </span>
                                <span
                                    v-if="
                                        d.precio_anterior &&
                                        d.precio_anterior > d.precio
                                    "
                                    class="ml-2 text-sm text-ink/35 line-through"
                                >
                                    {{ site.currency_symbol }}
                                    {{ d.precio_anterior }}
                                </span>
                            </div>
                            <a
                                :href="waLink(d.nombre)"
                                target="_blank"
                                rel="noopener"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-pix text-white shadow-lg shadow-pix/25 transition-transform group-hover:scale-110"
                                title="Consultar por WhatsApp"
                            >
                                <MessageCircle class="h-4 w-4" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
