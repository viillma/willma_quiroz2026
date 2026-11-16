<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, Check, Clock } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import BrandLogo from '@/components/brand/BrandLogo.vue';

type Servicio = {
    id: number;
    codigo: string;
    slug: string;
    nombre: string;
    categoria: string;
    descripcion: string;
    precio: number;
    unidad: string;
    tiempo_estimado: string;
    imagen: string | null;
    publicado: boolean;
    destacado: boolean;
    caracteristicas: string[];
    incluye: string[];
};

type Site = {
    nombre: string;
    logo: string | null;
    whatsapp: string;
    currency_symbol: string;
};

const props = defineProps<{
    site: Site;
    servicios: Servicio[];
    categorias: string[];
}>();

const active = ref<string>('Todos');

const filtered = computed(() =>
    props.servicios.filter(
        (s) => active.value === 'Todos' || s.categoria === active.value,
    ),
);
</script>

<template>
    <Head title="Servicios" />

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
                        href="/catalogo"
                        class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix"
                        >Catálogo</Link
                    >
                    <Link
                        href="/portafolio"
                        class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix"
                        >Portafolio</Link
                    >
                    <Link
                        href="/nosotros"
                        class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix"
                        >Nosotros</Link
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
                class="pointer-events-none absolute top-16 -left-24 h-72 w-72 rounded-full bg-blush/50 blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute top-32 -right-24 h-72 w-72 rounded-full bg-lilac/50 blur-3xl"
            ></div>
            <div class="relative mx-auto max-w-7xl px-5 md:px-8">
                <p
                    class="text-xs font-bold tracking-[0.18em] text-pix uppercase"
                >
                    Nuestros servicios
                </p>
                <h1
                    class="mt-3 max-w-2xl font-display text-4xl leading-tight font-extrabold tracking-tight md:text-5xl"
                >
                    Servicios creativos diseñados para impulsar tu marca
                </h1>
                <p class="mt-4 max-w-xl text-ink/60">
                    Desde diseño gráfico hasta fotografía profesional, tenemos
                    la solución perfecta para cada necesidad.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-14 md:px-8">
            <div class="mb-10 flex flex-wrap gap-3">
                <button
                    @click="active = 'Todos'"
                    class="rounded-full px-5 py-2.5 text-sm font-bold transition-colors"
                    :class="
                        active === 'Todos'
                            ? 'bg-pix text-white shadow-lg shadow-pix/25'
                            : 'border border-ink/10 bg-white text-ink/60 hover:border-pix hover:text-pix'
                    "
                >
                    Todos
                </button>
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
                No hay servicios disponibles en esta categoría.
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="s in filtered"
                    :key="s.id"
                    :href="`/servicios/${s.slug}`"
                    class="group relative flex flex-col overflow-hidden rounded-3xl border border-ink/5 bg-white p-7 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.16)] transition-all duration-500 hover:-translate-y-1.5 hover:shadow-[0_24px_50px_-20px_rgba(23,26,43,0.24)]"
                >
                    <div
                        v-if="s.imagen"
                        class="relative -mx-7 -mt-7 mb-6 h-44 overflow-hidden"
                    >
                        <img
                            :src="s.imagen"
                            :alt="s.nombre"
                            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                        />
                    </div>
                    <div class="flex items-center justify-between">
                        <span
                            class="text-xs font-bold tracking-[0.14em] text-ink/40 uppercase"
                            >{{ s.categoria }}</span
                        >
                        <span
                            class="rounded-full bg-blush/50 px-3 py-1 text-[11px] font-bold tracking-wide text-pix uppercase"
                        >
                            {{ s.codigo }}
                        </span>
                    </div>
                    <h2
                        class="mt-4 font-display text-xl font-extrabold tracking-tight"
                    >
                        {{ s.nombre }}
                    </h2>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-ink/55">
                        {{ s.descripcion }}
                    </p>

                    <div
                        v-if="s.caracteristicas?.length"
                        class="mt-5 space-y-2"
                    >
                        <p
                            v-for="c in s.caracteristicas.slice(0, 4)"
                            :key="c"
                            class="flex items-center gap-2 text-xs text-ink/55"
                        >
                            <Check class="h-3.5 w-3.5 shrink-0 text-pix" />
                            {{ c }}
                        </p>
                    </div>

                    <div
                        class="mt-6 flex items-center justify-between border-t border-ink/5 pt-5"
                    >
                        <div>
                            <span class="text-lg font-extrabold text-pix"
                                >{{ site.currency_symbol }} {{ s.precio }}</span
                            >
                            <span
                                v-if="s.unidad"
                                class="ml-1 text-xs text-ink/40"
                                >{{ s.unidad }}</span
                            >
                        </div>
                        <span
                            class="inline-flex items-center gap-1 text-xs font-semibold text-ink/40"
                        >
                            <Clock class="h-3.5 w-3.5" />
                            {{ s.tiempo_estimado }}
                        </span>
                    </div>
                </Link>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-16 md:px-8">
            <div
                class="relative overflow-hidden rounded-[2.5rem] bg-ink p-10 text-center md:p-14"
            >
                <div
                    class="pointer-events-none absolute -top-16 -right-16 h-64 w-64 rounded-full bg-pix/30 blur-3xl"
                ></div>
                <h2
                    class="relative font-display text-3xl font-extrabold text-white md:text-4xl"
                >
                    ¿Necesitas algo a medida?
                </h2>
                <p class="relative mx-auto mt-3 max-w-lg text-white/70">
                    Escríbenos y cotizamos tu proyecto sin compromiso.
                </p>
                <a
                    :href="`https://wa.me/${site.whatsapp}`"
                    target="_blank"
                    rel="noopener"
                    class="relative mt-7 inline-flex items-center gap-2 rounded-full bg-pix px-8 py-3.5 text-sm font-bold text-white shadow-xl shadow-pix/30 transition-transform hover:-translate-y-1"
                >
                    Contáctanos <ArrowRight class="h-4 w-4" />
                </a>
            </div>
        </section>
    </div>
</template>
