<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Menu, X } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { scrollToSection } from '@/composables/useSmoothScroll';
import { login } from '@/routes';

const props = defineProps<{ configuracion?: Record<string, string | null> }>();

const logoUrl = computed(() => props.configuracion?.logo_url || null);
const nombreEmpresa = computed(
    () => props.configuracion?.nombre_empresa || 'CREAPIX',
);

const navItems = [
    { label: 'Inicio', href: '#carrusel' },
    { label: 'Servicios', href: '#servicios' },
    { label: 'Portafolio', href: '#portafolio' },
    { label: 'Precios', href: '#precios' },
    { label: 'Nosotros', href: '#nosotros' },
    { label: 'Contacto', href: '#contacto' },
];

const isScrolled = ref(false);
const isMenuOpen = ref(false);
const activeSection = ref('inicio');

let observer: IntersectionObserver | null = null;

function onScroll() {
    isScrolled.value = window.scrollY > 24;
}

function goTo(href: string) {
    isMenuOpen.value = false;
    scrollToSection(href);
}

function setupScrollSpy() {
    const spy = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    activeSection.value = entry.target.id;
                }
            });
        },
        { rootMargin: '-35% 0px -55% 0px' },
    );

    observer = spy;

    navItems.forEach((item) => {
        const section = document.querySelector(item.href);

        if (section) {
            spy.observe(section);
        }
    });
}

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    setupScrollSpy();
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', onScroll);

    if (observer) {
        observer.disconnect();
    }
});
</script>

<template>
    <header
        class="fixed inset-x-0 top-0 z-50 transition-all duration-500"
        :class="
            isScrolled || isMenuOpen
                ? 'bg-cream/85 shadow-[0_12px_40px_-16px_rgba(23,26,43,0.14)] backdrop-blur-xl'
                : 'bg-black/10 backdrop-blur-sm'
        "
    >
        <nav
            class="mx-auto flex h-20 max-w-7xl items-center justify-between px-6 lg:px-8"
        >
            <Link
                href="/"
                class="group flex items-center gap-3"
                @click="isMenuOpen = false"
            >
                <img
                    v-if="logoUrl"
                    :src="logoUrl"
                    :alt="nombreEmpresa"
                    class="h-10 w-auto max-w-[160px] object-contain transition-transform duration-500 group-hover:scale-105 group-hover:-rotate-6"
                />
                <span
                    v-else
                    class="font-display text-xl font-extrabold tracking-tight drop-shadow-[0_1px_2px_rgba(0,0,0,0.35)] transition-colors duration-300"
                    :class="isScrolled ? 'text-ink' : 'text-white'"
                >
                    {{ nombreEmpresa }}<span class="text-pix">.</span>
                </span>
            </Link>

            <div class="hidden items-center gap-8 lg:flex">
                <a
                    v-for="item in navItems"
                    :key="item.label"
                    :href="item.href"
                    class="group relative py-2 text-sm font-semibold drop-shadow-[0_1px_2px_rgba(0,0,0,0.35)] transition-colors duration-300"
                    :class="
                        isScrolled
                            ? activeSection === item.href.slice(1)
                                ? 'text-ink'
                                : 'text-ink/60 hover:text-pix'
                            : activeSection === item.href.slice(1)
                              ? 'text-white'
                              : 'text-white/90 hover:text-pix'
                    "
                    @click.prevent="goTo(item.href)"
                >
                    {{ item.label }}
                    <span
                        class="absolute -bottom-0.5 left-0 h-[2px] rounded-full bg-pix transition-all duration-300"
                        :class="
                            activeSection === item.href.slice(1)
                                ? 'w-full'
                                : 'w-0 group-hover:w-full'
                        "
                    ></span>
                </a>
            </div>

            <div class="hidden items-center gap-3 lg:flex">
                <Link
                    :href="login()"
                    class="rounded-full px-4 py-2 text-sm font-semibold drop-shadow-[0_1px_2px_rgba(0,0,0,0.35)] transition-colors duration-300"
                    :class="
                        isScrolled
                            ? 'text-ink/60 hover:text-ink'
                            : 'text-white/90 hover:text-pix'
                    "
                >
                    Iniciar sesión
                </Link>
                <Link
                    :href="login()"
                    class="group/btn inline-flex items-center gap-2 rounded-full bg-pix px-6 py-3 text-sm font-bold text-white shadow-lg shadow-pix/25 transition-all duration-300 hover:-translate-y-0.5 hover:bg-pix-2 hover:shadow-xl hover:shadow-pix/30"
                >
                    Comenzar proyecto
                    <ArrowRight
                        class="h-4 w-4 transition-transform duration-300 group-hover/btn:translate-x-1"
                    />
                </Link>
            </div>

            <button
                class="flex h-11 w-11 items-center justify-center rounded-xl border border-ink/10 bg-white/70 text-ink backdrop-blur lg:hidden"
                aria-label="Abrir menú"
                @click="isMenuOpen = !isMenuOpen"
            >
                <Menu v-if="!isMenuOpen" class="h-5 w-5" />
                <X v-else class="h-5 w-5" />
            </button>
        </nav>

        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div
                v-if="isMenuOpen"
                class="border-t border-ink/5 bg-cream/95 backdrop-blur-xl lg:hidden"
            >
                <div class="space-y-1 px-6 py-6">
                    <a
                        v-for="item in navItems"
                        :key="item.label"
                        :href="item.href"
                        class="block rounded-xl px-4 py-3 text-base font-semibold text-ink/70 transition-colors hover:bg-blush/40 hover:text-ink"
                        @click.prevent="goTo(item.href)"
                    >
                        {{ item.label }}
                    </a>
                    <div class="flex flex-col gap-3 pt-5">
                        <Link
                            :href="login()"
                            class="rounded-full border border-ink/10 py-3 text-center text-sm font-semibold text-ink"
                        >
                            Iniciar sesión
                        </Link>
                        <Link
                            :href="login()"
                            class="inline-flex items-center justify-center gap-2 rounded-full bg-pix py-3 text-sm font-bold text-white shadow-lg shadow-pix/25"
                        >
                            Comenzar proyecto
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </Transition>
    </header>
</template>
