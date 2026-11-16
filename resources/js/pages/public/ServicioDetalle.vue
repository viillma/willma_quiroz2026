<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, Check, Clock } from 'lucide-vue-next';
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

defineProps<{
    site: Site;
    servicio: Servicio;
    relacionados: Servicio[];
}>();
</script>

<template>
    <Head :title="servicio.nombre" />

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
                class="pointer-events-none absolute top-16 -left-24 h-72 w-72 rounded-full bg-blush/50 blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute top-32 -right-24 h-72 w-72 rounded-full bg-lilac/50 blur-3xl"
            ></div>

            <div
                class="relative mx-auto grid max-w-7xl items-start gap-12 px-5 pb-16 md:px-8 lg:grid-cols-2"
            >
                <div>
                    <Link
                        href="/servicios"
                        class="inline-flex items-center gap-2 text-sm font-bold text-ink/50 transition-colors hover:text-pix"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Volver a servicios
                    </Link>

                    <div class="mt-6 flex items-center gap-3">
                        <span
                            class="text-xs font-bold tracking-[0.14em] text-ink/40 uppercase"
                            >{{ servicio.categoria }}</span
                        >
                        <span
                            class="rounded-full bg-blush/50 px-3 py-1 text-[11px] font-bold tracking-wide text-pix uppercase"
                        >
                            {{ servicio.codigo }}
                        </span>
                    </div>

                    <h1
                        class="mt-4 font-display text-4xl leading-tight font-extrabold tracking-tight md:text-5xl"
                    >
                        {{ servicio.nombre }}
                    </h1>
                    <p class="mt-5 text-lg leading-relaxed text-ink/60">
                        {{ servicio.descripcion }}
                    </p>

                    <div
                        class="mt-8 flex flex-wrap items-center gap-5 rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.16)]"
                    >
                        <div>
                            <span class="text-3xl font-extrabold text-pix"
                                >{{ site.currency_symbol }}
                                {{ servicio.precio }}</span
                            >
                            <span
                                v-if="servicio.unidad"
                                class="ml-1 text-sm text-ink/40"
                                >{{ servicio.unidad }}</span
                            >
                        </div>
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-warm px-4 py-2 text-xs font-semibold text-ink/60"
                        >
                            <Clock class="h-3.5 w-3.5 text-pix" />
                            {{ servicio.tiempo_estimado }}
                        </span>
                        <a
                            :href="`https://wa.me/${site.whatsapp}`"
                            target="_blank"
                            rel="noopener"
                            class="ml-auto inline-flex items-center gap-2 rounded-full bg-pix px-6 py-3 text-sm font-bold text-white shadow-lg shadow-pix/25 transition-transform hover:-translate-y-0.5"
                        >
                            Cotizar ahora <ArrowRight class="h-4 w-4" />
                        </a>
                    </div>

                    <div v-if="servicio.caracteristicas?.length" class="mt-8">
                        <h2 class="font-display text-xl font-extrabold">
                            Características
                        </h2>
                        <ul class="mt-4 space-y-3">
                            <li
                                v-for="c in servicio.caracteristicas"
                                :key="c"
                                class="flex items-start gap-3 text-ink/70"
                            >
                                <span
                                    class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-mint/60 text-[#17805a]"
                                >
                                    <Check class="h-3.5 w-3.5" />
                                </span>
                                {{ c }}
                            </li>
                        </ul>
                    </div>

                    <div v-if="servicio.incluye?.length" class="mt-8">
                        <h2 class="font-display text-xl font-extrabold">
                            ¿Qué incluye?
                        </h2>
                        <ul class="mt-4 space-y-3">
                            <li
                                v-for="i in servicio.incluye"
                                :key="i"
                                class="flex items-start gap-3 text-ink/70"
                            >
                                <span
                                    class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-lilac/50 text-[#7b5ee0]"
                                >
                                    <Check class="h-3.5 w-3.5" />
                                </span>
                                {{ i }}
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="relative lg:sticky lg:top-28">
                    <div
                        class="relative overflow-hidden rounded-[2.5rem] border border-ink/5 bg-gradient-to-br from-lavender to-blush/40 shadow-[0_30px_80px_-30px_rgba(23,26,43,0.45)]"
                    >
                        <img
                            v-if="servicio.imagen"
                            :src="servicio.imagen"
                            :alt="servicio.nombre"
                            class="aspect-[4/3] w-full object-cover"
                        />
                        <div
                            v-else
                            class="flex aspect-[4/3] w-full flex-col items-center justify-center bg-white/60 text-center"
                        >
                            <p
                                class="font-display text-3xl font-extrabold text-ink/15"
                            >
                                {{ servicio.codigo }}
                            </p>
                            <p class="mt-2 text-sm font-semibold text-ink/30">
                                {{ servicio.nombre }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section
            v-if="relacionados.length"
            class="mx-auto max-w-7xl px-5 pb-20 md:px-8"
        >
            <h2 class="font-display text-2xl font-extrabold tracking-tight">
                Servicios relacionados
            </h2>
            <div class="mt-6 grid gap-6 md:grid-cols-3">
                <Link
                    v-for="s in relacionados"
                    :key="s.id"
                    :href="`/servicios/${s.slug}`"
                    class="group rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)] transition-all duration-500 hover:-translate-y-1 hover:shadow-[0_24px_50px_-20px_rgba(23,26,43,0.22)]"
                >
                    <p
                        class="text-xs font-bold tracking-[0.14em] text-ink/40 uppercase"
                    >
                        {{ s.categoria }}
                    </p>
                    <h3 class="mt-2 font-display text-lg font-extrabold">
                        {{ s.nombre }}
                    </h3>
                    <p class="mt-2 line-clamp-2 text-sm text-ink/55">
                        {{ s.descripcion }}
                    </p>
                    <p class="mt-4 text-lg font-extrabold text-pix">
                        {{ site.currency_symbol }} {{ s.precio }}
                    </p>
                </Link>
            </div>
        </section>
    </div>
</template>
