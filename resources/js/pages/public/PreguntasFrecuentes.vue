<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ChevronDown, HelpCircle, MessageCircle } from 'lucide-vue-next';
import { ref } from 'vue';
import BrandLogo from '@/components/brand/BrandLogo.vue';

type Faq = {
    id: number;
    pregunta: string;
    respuesta: string;
};

type Site = {
    nombre: string;
    logo: string | null;
    whatsapp: string;
    email: string;
    telefono: string;
    direccion: string;
    ciudad: string;
    nombre_pais: string;
};

const props = defineProps<{
    site: Site;
    faqs: Faq[];
}>();

const abierta = ref<number | null>(props.faqs[0]?.id ?? null);

function toggle(id: number) {
    abierta.value = abierta.value === id ? null : id;
}
</script>

<template>
    <Head title="Preguntas Frecuentes" />

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
                class="pointer-events-none absolute top-16 -left-24 h-72 w-72 rounded-full bg-peach/50 blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute top-32 -right-24 h-72 w-72 rounded-full bg-blush/50 blur-3xl"
            ></div>

            <div class="relative mx-auto max-w-7xl px-5 md:px-8">
                <p
                    class="text-xs font-bold tracking-[0.18em] text-pix uppercase"
                >
                    Preguntas Frecuentes
                </p>
                <h1
                    class="mt-3 max-w-2xl font-display text-4xl leading-tight font-extrabold tracking-tight md:text-5xl"
                >
                    Resolvemos tus dudas
                </h1>
                <p class="mt-4 max-w-xl text-ink/60">
                    Encuentra respuestas a las preguntas más habituales sobre
                    nuestros servicios, tiempos y formas de trabajo.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-3xl px-5 py-14 md:px-8">
            <p
                v-if="faqs.length === 0"
                class="rounded-3xl border border-ink/5 bg-white p-8 text-center text-ink/55"
            >
                Aún no hay preguntas frecuentes publicadas.
            </p>

            <div v-else class="space-y-4">
                <div
                    v-for="faq in faqs"
                    :key="faq.id"
                    class="overflow-hidden rounded-3xl border border-ink/5 bg-white shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)] transition-colors"
                    :class="abierta === faq.id ? 'border-pix/20' : ''"
                >
                    <button
                        type="button"
                        class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left"
                        @click="toggle(faq.id)"
                    >
                        <span class="flex items-center gap-3 font-semibold">
                            <HelpCircle class="h-5 w-5 shrink-0 text-pix" />
                            {{ faq.pregunta }}
                        </span>
                        <ChevronDown
                            class="h-5 w-5 shrink-0 text-ink/40 transition-transform duration-300"
                            :class="
                                abierta === faq.id ? 'rotate-180 text-pix' : ''
                            "
                        />
                    </button>
                    <div
                        v-if="abierta === faq.id"
                        class="border-t border-ink/5 px-6 py-5"
                    >
                        <p class="text-sm leading-relaxed text-ink/65">
                            {{ faq.respuesta }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-3xl px-5 pb-20 md:px-8">
            <div
                class="flex flex-col items-center justify-between gap-6 rounded-[2.5rem] bg-gradient-to-br from-lavender via-blush/40 to-peach/40 p-10 text-center md:flex-row md:text-left"
            >
                <div>
                    <h2
                        class="font-display text-2xl font-extrabold tracking-tight"
                    >
                        ¿Tienes otra duda?
                    </h2>
                    <p class="mt-2 text-sm text-ink/60">
                        Escríbenos y te respondemos a la brevedad.
                    </p>
                </div>
                <a
                    v-if="site.whatsapp"
                    :href="`https://wa.me/${site.whatsapp}`"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex shrink-0 items-center gap-2 rounded-full bg-pix px-6 py-3 text-sm font-bold text-white shadow-lg shadow-pix/25 transition-transform hover:-translate-y-0.5"
                >
                    <MessageCircle class="h-4 w-4" />
                    Contactar por WhatsApp
                </a>
            </div>
        </section>
    </div>
</template>
