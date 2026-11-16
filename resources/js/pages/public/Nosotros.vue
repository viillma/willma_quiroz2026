<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, Mail, MapPin, Phone } from 'lucide-vue-next';
import BrandLogo from '@/components/brand/BrandLogo.vue';

type Site = {
    nombre: string;
    logo: string | null;
    eslogan: string;
    whatsapp: string;
    whatsapp_local: string;
    email: string;
    telefono: string;
    direccion: string;
    ciudad: string;
    nombre_pais: string;
    social: Record<string, string>;
    nosotros: string;
    currency_symbol: string;
    stats: { proyectos: string; clientes: string; anios: string };
};

defineProps<{
    site: Site;
}>();
</script>

<template>
    <Head title="Nosotros" />

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
                class="pointer-events-none absolute top-32 -right-24 h-72 w-72 rounded-full bg-blush/50 blur-3xl"
            ></div>

            <div class="relative mx-auto max-w-7xl px-5 md:px-8">
                <p
                    class="text-xs font-bold tracking-[0.18em] text-pix uppercase"
                >
                    Nosotros
                </p>
                <h1
                    class="mt-3 max-w-2xl font-display text-4xl leading-tight font-extrabold tracking-tight md:text-5xl"
                >
                    {{ site.nombre }} — {{ site.eslogan }}
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-ink/65">
                    {{ site.nosotros }}
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-14 md:px-8">
            <div class="grid gap-6 sm:grid-cols-3">
                <div
                    class="rounded-3xl border border-ink/5 bg-white p-8 text-center shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
                >
                    <p class="font-display text-4xl font-extrabold text-pix">
                        {{ site.stats.proyectos }}
                    </p>
                    <p class="mt-2 text-sm font-semibold text-ink/50">
                        Proyectos realizados
                    </p>
                </div>
                <div
                    class="rounded-3xl border border-ink/5 bg-white p-8 text-center shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
                >
                    <p class="font-display text-4xl font-extrabold text-pix">
                        {{ site.stats.clientes }}
                    </p>
                    <p class="mt-2 text-sm font-semibold text-ink/50">
                        Clientes satisfechos
                    </p>
                </div>
                <div
                    class="rounded-3xl border border-ink/5 bg-white p-8 text-center shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
                >
                    <p class="font-display text-4xl font-extrabold text-pix">
                        {{ site.stats.anios }}
                    </p>
                    <p class="mt-2 text-sm font-semibold text-ink/50">
                        Años de experiencia
                    </p>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 pb-20 md:px-8">
            <div
                class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-lavender via-blush/40 to-peach/40 p-10 md:p-14"
            >
                <div
                    class="pointer-events-none absolute -top-20 -right-20 h-64 w-64 rounded-full bg-lilac/50 blur-3xl"
                ></div>
                <div class="relative grid gap-10 md:grid-cols-2">
                    <div>
                        <h2
                            class="font-display text-2xl font-extrabold tracking-tight"
                        >
                            Nuestros pilares
                        </h2>
                        <div class="mt-6 space-y-4">
                            <div class="flex items-center gap-4">
                                <span
                                    class="h-3 w-3 rounded-full bg-pix"
                                ></span>
                                <p class="font-semibold">Estrategia</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span
                                    class="h-3 w-3 rounded-full bg-lilac"
                                ></span>
                                <p class="font-semibold">Diseño</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span
                                    class="h-3 w-3 rounded-full bg-mint"
                                ></span>
                                <p class="font-semibold">Tecnología</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span
                                    class="h-3 w-3 rounded-full bg-peach"
                                ></span>
                                <p class="font-semibold">Creatividad</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h2
                            class="font-display text-2xl font-extrabold tracking-tight"
                        >
                            Datos de contacto
                        </h2>
                        <div class="mt-6 space-y-4 text-sm text-ink/70">
                            <p
                                v-if="site.direccion"
                                class="flex items-center gap-3"
                            >
                                <MapPin class="h-5 w-5 shrink-0 text-pix" />
                                {{ site.direccion
                                }}{{ site.ciudad ? `, ${site.ciudad}` : ''
                                }}{{
                                    site.nombre_pais
                                        ? ` · ${site.nombre_pais}`
                                        : ''
                                }}
                            </p>
                            <p
                                v-if="site.telefono"
                                class="flex items-center gap-3"
                            >
                                <Phone class="h-5 w-5 shrink-0 text-pix" />
                                {{ site.telefono }}
                            </p>
                            <p
                                v-if="site.email"
                                class="flex items-center gap-3"
                            >
                                <Mail class="h-5 w-5 shrink-0 text-pix" />
                                {{ site.email }}
                            </p>
                            <a
                                v-if="site.whatsapp"
                                :href="`https://wa.me/${site.whatsapp}`"
                                target="_blank"
                                rel="noopener"
                                class="mt-6 inline-flex items-center gap-2 rounded-full bg-pix px-6 py-3 text-sm font-bold text-white shadow-lg shadow-pix/25 transition-transform hover:-translate-y-0.5"
                            >
                                Escríbenos por WhatsApp
                                <ArrowRight class="h-4 w-4" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
