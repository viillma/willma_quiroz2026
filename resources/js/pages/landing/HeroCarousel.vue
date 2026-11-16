<script setup lang="ts">
import { ArrowLeft, ArrowRight } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useReveal } from '@/composables/useScrollReveal';
import { scrollToSection } from '@/composables/useSmoothScroll';

type Slide = {
    id: number;
    titulo: string;
    descripcion: string | null;
    boton_texto: string;
    boton_enlace: string | null;
    imagen: string | null;
    imagen_url: string | null;
    duracion: number;
};

const props = defineProps<{ slides: Slide[] }>();

const current = ref(0);
const paused = ref(false);
let timer: ReturnType<typeof setTimeout> | null = null;
let progressTimer: ReturnType<typeof setInterval> | null = null;
let elapsed = 0;

const activeSlide = computed(
    () => props.slides[current.value] as Slide | undefined,
);

const slideDuration = computed(() => {
    const d =
        activeSlide.value?.duracion && activeSlide.value.duracion > 0
            ? activeSlide.value.duracion
            : 5;

    return Math.min(d, 20) * 1000;
});

const progressPct = computed(() => {
    if (slideDuration.value <= 0) {
        return 0;
    }

    return Math.max(
        0,
        Math.min(100, 100 - (elapsed / slideDuration.value) * 100),
    );
});

function goToHash(hash?: string | null) {
    if (hash && hash.startsWith('#')) {
        scrollToSection(hash);
    } else if (hash) {
        window.open(hash, '_blank', 'noopener');
    }
}

function next() {
    if (props.slides.length === 0) {
        return;
    }

    current.value = (current.value + 1) % props.slides.length;
}

function prev() {
    if (props.slides.length === 0) {
        return;
    }

    current.value =
        (current.value - 1 + props.slides.length) % props.slides.length;
}

function clearTimers() {
    if (timer) {
        clearTimeout(timer);
        timer = null;
    }

    if (progressTimer) {
        clearInterval(progressTimer);
        progressTimer = null;
    }

    elapsed = 0;
}

function schedule() {
    clearTimers();

    if (props.slides.length < 2 || paused.value) {
        return;
    }

    progressTimer = setInterval(() => {
        if (paused.value) {
            return;
        }

        elapsed += 100;
    }, 100);

    timer = setTimeout(() => {
        next();
    }, slideDuration.value);
}

watch(current, schedule);

watch(paused, (isPaused) => {
    if (isPaused) {
        clearTimers();
    } else {
        schedule();
    }
});

onMounted(() => {
    useReveal('.hero-carousel-content');
    schedule();
});

onBeforeUnmount(() => {
    clearTimers();
});
</script>

<template>
    <section
        id="carrusel"
        class="hero-carousel relative h-screen max-h-[100vh] min-h-[600px] w-full overflow-hidden bg-ink"
    >
        <Transition name="hero-slide" mode="out-in">
            <div
                v-if="activeSlide"
                :key="activeSlide.id"
                class="absolute inset-0"
                @mouseenter="paused = true"
                @mouseleave="paused = false"
                @touchstart="paused = true"
                @touchend="paused = false"
            >
                <img
                    :src="activeSlide.imagen_url || ''"
                    :alt="activeSlide.titulo"
                    class="hero-img absolute inset-0 h-full w-full object-cover"
                    loading="eager"
                    :style="{ animationDuration: slideDuration + 'ms' }"
                />

                <!-- Overlays for readability -->
                <div class="hero-vignette absolute inset-0"></div>
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-black/20"
                ></div>
                <div
                    class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/20 to-transparent"
                ></div>

                <div
                    class="absolute inset-0 flex items-center px-6 sm:px-10 lg:px-20"
                >
                    <div class="hero-carousel-content max-w-2xl lg:max-w-3xl">
                        <slot v-if="$slots.hero" name="hero" />

                        <template v-else>
                            <span
                                class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-1.5 backdrop-blur-md"
                            >
                                <span
                                    class="h-2 w-2 rounded-full bg-pix-2"
                                ></span>
                                <span
                                    class="font-display text-xs font-extrabold tracking-widest text-white uppercase"
                                    >Nuestro estudio</span
                                >
                            </span>

                            <h1
                                class="font-display text-4xl leading-[1.05] font-extrabold tracking-tight text-white drop-shadow-lg sm:text-5xl lg:text-7xl"
                            >
                                {{ activeSlide.titulo }}
                            </h1>

                            <p
                                v-if="activeSlide.descripcion"
                                class="mt-6 max-w-xl text-base leading-relaxed text-white/85 sm:text-lg lg:text-xl"
                            >
                                {{ activeSlide.descripcion }}
                            </p>

                            <a
                                :href="activeSlide.boton_enlace || '#servicios'"
                                class="group mt-9 inline-flex items-center gap-2 rounded-full bg-pix px-8 py-4 text-sm font-bold text-white shadow-lg shadow-pix/40 transition-all duration-300 hover:-translate-y-0.5 hover:bg-pix-2"
                                @click.prevent="
                                    goToHash(activeSlide.boton_enlace)
                                "
                            >
                                {{
                                    activeSlide.boton_texto ||
                                    'Explorar servicios'
                                }}
                                <ArrowRight
                                    class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
                                />
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Slide counter / progress -->
        <div
            v-if="slides.length > 1"
            class="absolute right-6 bottom-6 z-20 flex items-center gap-5 sm:right-10 sm:bottom-8"
        >
            <span
                class="font-display text-sm font-bold tracking-widest text-white/80 tabular-nums"
            >
                {{ String(current + 1).padStart(2, '0')
                }}<span class="text-white/40">
                    / {{ String(slides.length).padStart(2, '0') }}</span
                >
            </span>
            <div
                class="h-[3px] w-24 overflow-hidden rounded-full bg-white/25 sm:w-32"
            >
                <div
                    v-if="!paused"
                    class="h-full rounded-full bg-gradient-to-r from-pix to-pix-2 transition-none"
                    :style="{ width: progressPct + '%' }"
                ></div>
            </div>
        </div>

        <!-- Arrows -->
        <button
            v-if="slides.length > 1"
            class="absolute top-1/2 left-4 z-20 -translate-y-1/2 rounded-full border border-white/25 bg-black/30 p-3.5 text-white backdrop-blur transition-all duration-300 hover:scale-105 hover:bg-pix/80 sm:left-8"
            aria-label="Anterior"
            @click="prev"
        >
            <ArrowLeft class="h-5 w-5" />
        </button>
        <button
            v-if="slides.length > 1"
            class="absolute top-1/2 right-4 z-20 -translate-y-1/2 rounded-full border border-white/25 bg-black/30 p-3.5 text-white backdrop-blur transition-all duration-300 hover:scale-105 hover:bg-pix/80 sm:right-8"
            aria-label="Siguiente"
            @click="next"
        >
            <ArrowRight class="h-5 w-5" />
        </button>

        <!-- Indicators -->
        <div
            v-if="slides.length > 1"
            class="absolute bottom-7 left-1/2 z-20 flex -translate-x-1/2 items-center gap-2.5 sm:bottom-8"
        >
            <button
                v-for="(slide, i) in slides"
                :key="slide.id"
                class="h-2 rounded-full transition-all duration-300"
                :class="
                    i === current
                        ? 'w-9 bg-gradient-to-r from-pix to-pix-2 shadow-[0_0_12px_rgba(244,63,122,0.8)]'
                        : 'w-2 bg-white/40 hover:bg-white/70'
                "
                :aria-label="`Ir al slide ${i + 1}`"
                @click="current = i"
            ></button>
        </div>

        <!-- Empty state -->
        <div
            v-if="slides.length === 0"
            class="flex h-full w-full items-center justify-center px-6 text-center text-white/50"
        >
            No hay imágenes configuradas para el carrusel todavía.
        </div>
    </section>
</template>

<style scoped>
.hero-img {
    animation: herokenburns 6s ease-out forwards;
    will-change: transform;
}

@keyframes herokenburns {
    0% {
        transform: scale(1);
    }
    100% {
        transform: scale(1.15);
    }
}

@media (prefers-reduced-motion: reduce) {
    .hero-img {
        animation: none;
    }
    .hero-progress {
        animation: none;
        width: 100%;
    }
}

.hero-vignette {
    background: radial-gradient(
        ellipse at center,
        transparent 45%,
        rgba(0, 0, 0, 0.6) 100%
    );
}

.hero-slide-enter-active,
.hero-slide-leave-active {
    transition:
        opacity 1s ease,
        transform 1s ease;
}

.hero-slide-enter-from {
    opacity: 0;
    transform: scale(1.08);
}

.hero-slide-leave-to {
    opacity: 0;
    transform: scale(0.98);
}

.hero-progress {
    animation: heroProgressbar linear forwards;
}

@keyframes heroProgressbar {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}
</style>
