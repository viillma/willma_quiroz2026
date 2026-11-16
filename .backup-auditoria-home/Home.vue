<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, CarFront, FilesIcon, Star } from 'lucide-vue-next';
import { login } from '@/routes';
import HeroCarousel from '../landing/HeroCarousel.vue';

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

type Portafolio = {
    id: number;
    titulo: string;
    categoria: string;
    descripcion: string;
    imagen: string | null;
    fecha: string | null;
    images: Array<{ id: number; url: string }>;
    servicio: string | null;
};

type Testimonio = {
    id: number;
    nombre: string;
    texto: string;
    calificacion: number;
    servicio: string | null;
    foto: string | null;
};

type CarouselSlide = {
    id: number;
    titulo: string;
    descripcion: string | null;
    boton_texto: string;
    boton_enlace: string | null;
    imagen: string | null;
    imagen_url: string | null;
    duracion: number;
};

type Site = {
    nombre: string;
    eslogan: string;
    logo: string | null;
    whatsapp: string;
    social: Record<string, string>;
    hero: {
        linea1: string;
        linea2: string;
        linea3: string;
        subtitulo: string;
        imagen: string;
        boton_primario: string;
        boton_secundario: string;
    };
    stats: {
        proyectos: string;
        clientes: string;
        anios: string;
    };
    nosotros: string;
    currency_symbol: string;
    servicios: Array<{ nombre: string; slug: string }>;
};

const props = defineProps<{
    site: Site;
    carousel: CarouselSlide[];
    servicios_destacados: Servicio[];
    portafolios: Portafolio[];
    testimonios: Testimonio[];
}>();

const whatsappUrl = `https://wa.me/${props.site.whatsapp}`;
</script>

<template>
    <Head :title="site.nombre" />

    <div class="min-h-screen bg-cream font-sans text-ink antialiased">
        <!-- Navbar -->
        <header class="fixed inset-x-0 top-0 z-50 border-b border-ink/5 bg-cream/80 backdrop-blur-md">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4">
                <Link href="/" class="flex items-center gap-3">
                    <img v-if="site.logo" :src="site.logo" :alt="site.nombre" class="h-10 w-auto" />
                    <span v-else class="font-display text-xl font-extrabold tracking-tight">{{ site.nombre }}</span>
                </Link>
                <nav class="hidden items-center gap-7 md:flex">
                    <Link href="/servicios" class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix">Servicios</Link>
                    <Link href="/catalogo" class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix">Catálogo</Link>
                    <Link href="/portafolio" class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix">Portafolio</Link>
                    <Link href="/nosotros" class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix">Nosotros</Link>
                    <Link href="/contacto" class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix">Contacto</Link>
                </nav>
                <Link
                    :href="login().url"
                    class="hidden rounded-full bg-pix px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-pix/25 transition-transform hover:-translate-y-0.5 sm:inline-flex"
                >
                    Registrarse
                </Link>
            </div>
        </header>

        <!-- Hero -->
        <section id="carrusel">
            <HeroCarousel :slides="carousel" />
        </section>

        <!-- Services -->
        <section id="servicios" class="mx-auto max-w-7xl px-5 py-20 md:px-8" v-if="servicios_destacados.length">
            <div class="mb-10 flex items-end justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-pix">Servicios</p>
                    <h2 class="mt-2 font-display text-3xl font-extrabold tracking-tight md:text-4xl">Lo que hacemos</h2>
                </div>
                <Link href="/servicios" class="hidden items-center gap-1 text-sm font-bold text-pix sm:inline-flex">
                    Ver todos <ArrowRight class="h-4 w-4" />
                </Link>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="(s, i) in servicios_destacados"
                    :key="s.id"
                    :href="`/servicios/${s.slug}`"
                    class="group relative overflow-hidden rounded-3xl border border-ink/5 bg-white p-7 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.16)] transition-all duration-500 hover:-translate-y-1.5 hover:shadow-[0_24px_50px_-20px_rgba(23,26,43,0.24)]"
                    :class="['bg-blush/30', 'bg-lilac/25', 'bg-mint/30', 'bg-peach/30'][i % 4]"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-ink/40">{{ s.categoria }}</span>
                        <span class="relative flex h-10 w-10 items-center justify-center rounded-2xl bg-white/80 text-pix shadow-sm transition-transform duration-500 group-hover:scale-110">
                            <ArrowRight class="h-4 w-4" />
                        </span>
                    </div>
                    <h3 class="mt-5 font-display text-xl font-extrabold tracking-tight text-ink">{{ s.nombre }}</h3>
                    <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-ink/55">{{ s.descripcion }}</p>
                    <div class="mt-6 flex items-center justify-between border-t border-ink/5 pt-5">
                        <span class="text-lg font-extrabold text-pix">{{ site.currency_symbol }} {{ s.precio }}</span>
                        <span class="text-xs font-semibold text-ink/40">{{ s.tiempo_estimado }}</span>
                    </div>
                </Link>
            </div>
        </section>

        <!-- Portfolio preview -->
        <section class="mx-auto max-w-7xl px-5 py-20 md:px-8" v-if="portafolios.length">
            <div class="mb-10 flex items-end justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-pix">Portafolio</p>
                    <h2 class="mt-2 font-display text-3xl font-extrabold tracking-tight md:text-4xl">Trabajos recientes</h2>
                </div>
                <Link href="/portafolio" class="hidden items-center gap-1 text-sm font-bold text-pix sm:inline-flex">
                    Ver todo <ArrowRight class="h-4 w-4" />
                </Link>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="p in portafolios"
                    :key="p.id"
                    href="/portafolio"
                    class="group relative overflow-hidden rounded-3xl border border-ink/5 bg-white shadow-[0_14px_40px_-18px_rgba(23,26,43,0.16)]"
                >
                    <div class="relative aspect-[4/3] overflow-hidden bg-lavender/50">
                        <img
                            v-if="p.imagen"
                            :src="p.imagen"
                            :alt="p.titulo"
                            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center text-3xl text-ink/20">
                            <FilesIcon class="h-12 w-12" />
                        </div>
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-5 pt-16">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-white/70">{{ p.categoria }}</p>
                            <h3 class="mt-1 font-display text-lg font-extrabold text-white">{{ p.titulo }}</h3>
                        </div>
                    </div>
                </Link>
            </div>
        </section>

        <!-- About blurb -->
        <section class="mx-auto max-w-7xl px-5 py-20 md:px-8">
            <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-lavender via-blush/40 to-mint/30 p-10 md:p-16">
                <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-lilac/50 blur-3xl"></div>
                <div class="relative max-w-3xl">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-pix">Nosotros</p>
                    <h2 class="mt-3 font-display text-3xl font-extrabold tracking-tight md:text-4xl">{{ site.nombre }}</h2>
                    <p class="mt-5 text-lg leading-relaxed text-ink/65">{{ site.nosotros }}</p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <Link
                            href="/nosotros"
                            class="inline-flex items-center gap-2 rounded-full bg-ink px-6 py-3 text-sm font-bold text-white transition-transform hover:-translate-y-0.5"
                        >
                            Conócenos <ArrowRight class="h-4 w-4" />
                        </Link>
                        <Link
                            href="/contacto"
                            class="inline-flex items-center gap-2 rounded-full border border-ink/15 px-6 py-3 text-sm font-bold text-ink transition-colors hover:border-pix hover:text-pix"
                        >
                            Hablemos
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="mx-auto max-w-7xl px-5 py-20 md:px-8" v-if="testimonios.length">
            <div class="mb-10">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-pix">Testimonios</p>
                <h2 class="mt-2 font-display text-3xl font-extrabold tracking-tight md:text-4xl">Lo que dicen nuestros clientes</h2>
            </div>
            <div class="grid gap-6 md:grid-cols-3">
                <div v-for="t in testimonios" :key="t.id" class="rounded-3xl border border-ink/5 bg-white p-7 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]">
                    <div class="flex gap-1 text-pix">
                        <Star v-for="s in t.calificacion" :key="s" class="h-4 w-4 fill-current" />
                    </div>
                    <p class="mt-4 text-sm leading-relaxed text-ink/70">“{{ t.texto }}”</p>
                    <div class="mt-6 flex items-center gap-3 border-t border-ink/5 pt-5">
                        <div v-if="t.foto" class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-lilac/40">
                            <img :src="t.foto" :alt="t.nombre" class="h-full w-full object-cover" />
                        </div>
                        <div v-else class="flex h-10 w-10 items-center justify-center rounded-full bg-lilac/40 font-bold text-ink/60">
                            {{ t.nombre.charAt(0) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-ink">{{ t.nombre }}</p>
                            <p v-if="t.servicio" class="text-xs text-ink/45">{{ t.servicio }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section id="contacto" class="mx-auto max-w-7xl px-5 py-20 md:px-8">
            <div class="relative overflow-hidden rounded-[2.5rem] bg-ink p-10 text-center md:p-16">
                <div class="pointer-events-none absolute -left-20 -top-20 h-72 w-72 rounded-full bg-pix/30 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-20 -right-20 h-72 w-72 rounded-full bg-lilac/30 blur-3xl"></div>
                <div class="relative">
                    <h2 class="font-display text-3xl font-extrabold tracking-tight text-white md:text-4xl">
                        ¿Listo para crear algo increíble?
                    </h2>
                    <p class="mx-auto mt-4 max-w-xl text-ink/70 text-white/70">
                        Cuéntanos tu idea y hagámosla realidad. Respondemos en menos de 24 horas.
                    </p>
                    <a
                        :href="whatsappUrl"
                        target="_blank"
                        rel="noopener"
                        class="mt-8 inline-flex items-center gap-2 rounded-full bg-pix px-8 py-4 text-sm font-bold text-white shadow-xl shadow-pix/30 transition-transform hover:-translate-y-1"
                    >
                        <CarFront class="h-4 w-4" />
                        Escribir por WhatsApp
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-ink/5 bg-warm">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-6 px-5 py-12 md:flex-row md:px-8">
                <div class="flex items-center gap-3">
                    <img v-if="site.logo" :src="site.logo" :alt="site.nombre" class="h-9 w-auto" />
                    <span v-else class="font-display text-lg font-extrabold">{{ site.nombre }}</span>
                </div>
                <div class="flex items-center gap-7 text-sm font-semibold text-ink/55">
                    <Link href="/servicios" class="hover:text-pix">Servicios</Link>
                    <Link href="/catalogo" class="hover:text-pix">Catálogo</Link>
                    <Link href="/portafolio" class="hover:text-pix">Portafolio</Link>
                    <Link href="/nosotros" class="hover:text-pix">Nosotros</Link>
                </div>
                <p class="text-xs text-ink/40">© {{ new Date().getFullYear() }} {{ site.nombre }}. Todos los derechos reservados.</p>
            </div>
        </footer>
    </div>
</template>