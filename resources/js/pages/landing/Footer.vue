<script setup lang="ts">
import { ArrowUpRight, Facebook, Instagram } from 'lucide-vue-next';
import { computed } from 'vue';
import { scrollToSection } from '@/composables/useSmoothScroll';

const props = defineProps<{
    landing?: Record<string, string>;
    configuracion?: {
        nombre_empresa?: string | null;
        logo_url?: string | null;
    };
}>();

const L = (key: string, fallback: string) => props.landing?.[key] || fallback;

const logoUrl = computed(() => props.configuracion?.logo_url || null);
const nombreEmpresa = computed(
    () => props.configuracion?.nombre_empresa || 'CREAPIX',
);

const serviceLinks = [
    { label: 'Branding', href: '#servicios' },
    { label: 'Diseño Gráfico', href: '#servicios' },
    { label: 'Diseño Web', href: '#servicios' },
    { label: 'Marketing Digital', href: '#servicios' },
];

const studioLinks = [
    { label: 'Trabajos destacados', href: '#portafolio' },
    { label: 'Proceso creativo', href: '#proceso' },
    { label: 'Nosotros', href: '#nosotros' },
    { label: 'Precios', href: '#precios' },
    { label: 'Contacto', href: '#contacto' },
];

const socials = [
    {
        label: 'Instagram',
        icon: Instagram,
        href: L('contacto_instagram', 'https://instagram.com/creapix'),
    },
    {
        label: 'Facebook',
        icon: Facebook,
        href: L('contacto_facebook', 'https://facebook.com/creapix'),
    },
];

function whatsappHref(num: string | number) {
    return `https://wa.me/${String(num).replace(/\D/g, '')}`;
}

function goTo(hash: string) {
    scrollToSection(hash);
}
</script>

<template>
    <footer class="relative overflow-hidden bg-ink text-cream">
        <div
            class="pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full bg-pix/15 blur-3xl"
        ></div>
        <div
            class="pointer-events-none absolute -right-24 -bottom-24 h-72 w-72 rounded-full bg-lilac/15 blur-3xl"
        ></div>

        <div class="relative mx-auto max-w-7xl px-6 py-20 lg:px-8">
            <div class="grid gap-14 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr]">
                <div>
                    <div class="flex items-center gap-3">
                        <img
                            v-if="logoUrl"
                            :src="logoUrl"
                            :alt="nombreEmpresa"
                            class="h-10 w-auto max-w-[160px] object-contain"
                        />
                        <span
                            v-else
                            class="font-display text-xl font-extrabold tracking-tight text-cream"
                        >
                            {{ nombreEmpresa }}<span class="text-pix">.</span>
                        </span>
                    </div>
                    <p
                        class="mt-6 max-w-sm text-sm leading-relaxed text-cream/55"
                    >
                        CREAMOS · CAPTURAMOS · TRANSFORMAMOS
                    </p>
                    <div class="mt-7 flex gap-3">
                        <a
                            v-for="social in socials"
                            :key="social.label"
                            :href="social.href"
                            target="_blank"
                            rel="noopener"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-cream/10 text-cream/60 transition-all duration-300 hover:-translate-y-0.5 hover:border-pix/50 hover:bg-pix/15 hover:text-pix-2"
                            :aria-label="social.label"
                        >
                            <component :is="social.icon" class="h-4 w-4" />
                        </a>
                        <a
                            :href="
                                L(
                                    'contacto_tiktok',
                                    'https://tiktok.com/@creapix',
                                )
                            "
                            target="_blank"
                            rel="noopener"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-cream/10 text-cream/60 transition-all duration-300 hover:-translate-y-0.5 hover:border-pix/50 hover:bg-pix/15 hover:text-pix-2"
                            aria-label="TikTok"
                        >
                            <svg
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.3 0 .58.05.85.13V9.4a6.33 6.33 0 0 0-5.4 10.47 6.33 6.33 0 0 0 10.89-4.55v-6.9a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.11z"
                                />
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h4
                        class="text-sm font-bold tracking-widest text-cream/40 uppercase"
                    >
                        Servicios
                    </h4>
                    <ul class="mt-6 space-y-3 text-sm">
                        <li v-for="link in serviceLinks" :key="link.label">
                            <a
                                :href="link.href"
                                class="text-cream/70 transition-colors duration-300 hover:text-pix-2"
                                @click.prevent="goTo(link.href)"
                                >{{ link.label }}</a
                            >
                        </li>
                    </ul>
                </div>

                <div>
                    <h4
                        class="text-sm font-bold tracking-widest text-cream/40 uppercase"
                    >
                        Estudio
                    </h4>
                    <ul class="mt-6 space-y-3 text-sm">
                        <li v-for="link in studioLinks" :key="link.label">
                            <a
                                :href="link.href"
                                class="text-cream/70 transition-colors duration-300 hover:text-pix-2"
                                @click.prevent="goTo(link.href)"
                                >{{ link.label }}</a
                            >
                        </li>
                    </ul>
                </div>

                <div>
                    <h4
                        class="text-sm font-bold tracking-widest text-cream/40 uppercase"
                    >
                        Contacto
                    </h4>
                    <ul class="mt-6 space-y-3 text-sm text-cream/70">
                        <li>
                            <a
                                :href="`mailto:${L('contacto_correo', 'hola@creapix.com')}`"
                                class="transition-colors hover:text-pix-2"
                                >{{
                                    L('contacto_correo', 'hola@creapix.com')
                                }}</a
                            >
                        </li>
                        <li>
                            <a
                                :href="
                                    whatsappHref(
                                        L('contacto_whatsapp', '6404467'),
                                    )
                                "
                                target="_blank"
                                rel="noopener"
                                class="transition-colors hover:text-pix-2"
                                >WhatsApp:
                                {{ L('contacto_whatsapp', '6404467') }}</a
                            >
                        </li>
                        <li class="pt-3 leading-relaxed text-cream/50">
                            CREAMOS · CAPTURAMOS · TRANSFORMAMOS
                        </li>
                    </ul>
                </div>
            </div>

            <div
                class="mt-16 flex flex-col items-center justify-between gap-4 border-t border-cream/10 pt-8 md:flex-row"
            >
                <p class="text-sm text-cream/45">
                    &copy; {{ new Date().getFullYear() }} {{ nombreEmpresa }}.
                    Todos los derechos reservados.
                </p>
                <div class="flex items-center gap-6 text-sm text-cream/45">
                    <a href="#" class="transition-colors hover:text-cream"
                        >Política de privacidad</a
                    >
                    <a href="#" class="transition-colors hover:text-cream"
                        >Términos de servicio</a
                    >
                    <span class="inline-flex items-center gap-1 text-cream/35">
                        Hecho con <span class="text-pix">♥</span>
                        <ArrowUpRight class="h-3.5 w-3.5" />
                    </span>
                </div>
            </div>
        </div>
    </footer>
</template>
