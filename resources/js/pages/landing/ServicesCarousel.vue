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
    }

    if (progressTimer) {
        clearInterval(progressTimer);
    }

    timer = null;
    progressTimer = null;
}

function schedule() {
    clearTimers();

    if (props.slides.length < 2) {
        return;
    }

    const step = () => {
        if (paused.value) {
            progressTimer = setInterval(step, 250);

            return;
        }

        clearTimers();
        timer = setTimeout(() => {
            if (!paused.value) {
                next();
            }
        }, slideDuration.value);
        progressTimer = setInterval(step, 250);
    };

    progressTimer = setInterval(step, 250);
}

watch(current, schedule);

onMounted(() => {
    useReveal('.carousel-head', { y: 40 });
    schedule();
});

onBeforeUnmount(() => {
    clearTimers();
});
</script>

<template>
    <section
        id="carrusel"
        class="carousel-wrap relative scroll-mt-24 overflow-hidden bg-ink pt-32 pb-24 lg:pt-36 lg:pb-32"
    >
        <!-- Ambient purple-magenta glow -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="absolute top-10 -left-40 h-[520px] w-[520px] rounded-full bg-pix/25 blur-[140px]"
            ></div>
            <div
                class="absolute -right-40 bottom-0 h-[480px] w-[480px] rounded-full bg-lilac/25 blur-[140px]"
            ></div>
            <div
                class="absolute top-0 left-1/2 h-[380px] w-[380px] -translate-x-1/2 rounded-full bg-pix-2/15 blur-[120px]"
            ></div>
        </div>
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.05)_1px,transparent_1px)] [background-size:26px_26px] opacity-60"
        ></div>

        <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
            <div
                class="carousel-head mb-12 flex flex-col items-start justify-between gap-6 lg:mb-16 lg:flex-row lg:items-end"
            >
                <div class="max-w-2xl">
                    <span
                        class="inline-flex items-center gap-2 text-xs font-bold tracking-[0.3em] text-pix-2 uppercase"
                    >
                        <span class="h-px w-8 bg-pix-2"></span>
                        Portafolio de servicios
                    </span>
                    <h2
                        class="mt-5 font-display text-4xl leading-[1.05] font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl"
                    >
                        Explora lo que
                        <em class="text-gradient-soft not-italic">creamos.</em>
                    </h2>
                </div>
                <p
                    class="max-w-md text-base leading-relaxed text-white/55 lg:text-right"
                >
                    Soluciones creativas de principio a fin para posicionar tu
                    marca a nivel internacional.
                </p>
            </div>

            <div
                v-if="slides.length > 0"
                class="relative overflow-hidden rounded-[32px] border border-white/10 bg-black shadow-[0_40px_120px_-30px_rgba(244,63,122,0.35)]"
            >
                <div
                    class="relative aspect-[4/5] w-full sm:aspect-[16/10] lg:aspect-[21/9]"
                >
                    <Transition name="cinematic" mode="out-in">
                        <div
                            :key="activeSlide && activeSlide.id"
                            class="absolute inset-0"
                            @mouseenter="paused = true"
                            @mouseleave="paused = false"
                            @touchstart="paused = true"
                            @touchend="paused = false"
                        >
                            <img
                                :src="
                                    activeSlide &&
                                    (activeSlide.imagen_url || '')
                                "
                                :alt="activeSlide && activeSlide.titulo"
                                class="cinematic-img absolute inset-0 h-full w-full object-cover"
                                loading="eager"
                                :style="{
                                    animationDuration: slideDuration + 'ms',
                                }"
                            />

                            <!-- Cinematic overlays -->
                            <div
                                class="cinematic-vignette absolute inset-0"
                            ></div>
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/35 to-black/10"
                            ></div>
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-black/70 via-transparent to-purple-900/25"
                            ></div>

                            <!-- Top / bottom cinematic bar -->
                            <div
                                class="cine-bar absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-pix-2/60 to-transparent"
                            ></div>
                            <div
                                class="cine-bar absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"
                            ></div>

                            <div
                                class="absolute inset-x-0 bottom-0 p-6 sm:p-10 lg:p-14"
                            >
                                <Transition name="content" appear>
                                    <div>
                                        <span
                                            class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 backdrop-blur"
                                        >
                                            <span
                                                class="h-1.5 w-1.5 rounded-full bg-pix-2"
                                            ></span>
                                            <span
                                                class="font-display text-xs font-extrabold tracking-widest text-white"
                                                >CREAPIX</span
                                            >
                                        </span>
                                        <h3
                                            class="max-w-3xl font-display text-3xl leading-tight font-extrabold tracking-tight text-white drop-shadow-lg sm:text-4xl lg:text-[3.4rem] lg:leading-[1.05]"
                                        >
                                            {{
                                                activeSlide &&
                                                activeSlide.titulo
                                            }}
                                        </h3>
                                        <p
                                            class="mt-4 max-w-xl text-sm leading-relaxed text-white/80 sm:text-lg"
                                        >
                                            {{
                                                activeSlide &&
                                                activeSlide.descripcion
                                            }}
                                        </p>
                                        <a
                                            :href="
                                                (activeSlide &&
                                                    activeSlide.boton_enlace) ||
                                                '#contacto'
                                            "
                                            class="group mt-7 inline-flex items-center gap-2 rounded-full bg-pix px-7 py-3.5 text-sm font-bold text-white shadow-lg shadow-pix/40 transition-all duration-300 hover:-translate-y-0.5 hover:bg-pix-2"
                                            @click.prevent="
                                                goToHash(
                                                    activeSlide &&
                                                        activeSlide.boton_enlace,
                                                )
                                            "
                                        >
                                            {{
                                                (activeSlide &&
                                                    activeSlide.boton_texto) ||
                                                'Solicitar servicio'
                                            }}
                                            <ArrowRight
                                                class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
                                            />
                                        </a>
                                    </div>
                                </Transition>
                            </div>
                        </div>
                    </Transition>

                    <!-- Progress bar for auto-advance -->
                    <div
                        v-if="slides.length > 1"
                        class="absolute top-8 right-10 z-20 flex items-center gap-5 sm:right-12"
                    >
                        <span
                            class="font-display text-sm font-bold tracking-widest text-white/80 tabular-nums"
                        >
                            {{ String(current + 1).padStart(2, '0')
                            }}<span class="text-white/40">
                                /
                                {{
                                    String(slides.length).padStart(2, '0')
                                }}</span
                            >
                        </span>
                        <div
                            class="h-[3px] w-24 overflow-hidden rounded-full bg-white/20 sm:w-32"
                        >
                            <div
                                v-if="!paused"
                                :key="current"
                                class="progress-fill h-full rounded-full bg-gradient-to-r from-pix to-pix-2"
                                :style="{
                                    animationDuration: slideDuration + 'ms',
                                }"
                            ></div>
                        </div>
                    </div>
                </div>

                <button
                    class="absolute top-1/2 left-4 z-20 -translate-y-1/2 rounded-full border border-white/20 bg-black/30 p-3 text-white backdrop-blur transition-all duration-300 hover:scale-105 hover:bg-pix/80 sm:left-6"
                    aria-label="Anterior"
                    @click="prev"
                >
                    <ArrowLeft class="h-5 w-5" />
                </button>
                <button
                    class="absolute top-1/2 right-4 z-20 -translate-y-1/2 rounded-full border border-white/20 bg-black/30 p-3 text-white backdrop-blur transition-all duration-300 hover:scale-105 hover:bg-pix/80 sm:right-6"
                    aria-label="Siguiente"
                    @click="next"
                >
                    <ArrowRight class="h-5 w-5" />
                </button>

                <div
                    class="absolute bottom-5 left-1/2 z-20 flex -translate-x-1/2 items-center gap-2.5"
                >
                    <button
                        v-for="(slide, i) in slides"
                        :key="slide.id"
                        class="h-2 rounded-full transition-all duration-300"
                        :class="
                            i === current
                                ? 'w-9 bg-gradient-to-r from-pix to-pix-2 shadow-[0_0_12px_rgba(244,63,122,0.8)]'
                                : 'w-2 bg-white/35 hover:bg-white/70'
                        "
                        :aria-label="`Ir al slide ${i + 1}`"
                        @click="current = i"
                    ></button>
                </div>
            </div>

            <div
                v-else
                class="flex items-center justify-center rounded-[32px] border border-white/10 bg-black py-20 text-white/50"
            >
                No hay servicios configurados todavía.
            </div>
        </div>
    </section>
</template>

<style scoped>
/* Slow Ken-Burns zoom on the active image (duration matches the autoplay via inline style) */
.cinematic-img {
    animation: kenburns 5s ease-out forwards;
    will-change: transform;
}

@keyframes kenburns {
    0% {
        transform: scale(1.02) translateY(0);
    }
    100% {
        transform: scale(1.14) translateY(-6px);
    }
}

@media (prefers-reduced-motion: reduce) {
    .cinematic-img {
        animation: none;
    }
}

/* Film-style vignette */
.cinematic-vignette {
    background: radial-gradient(
        ellipse at center,
        transparent 40%,
        rgba(0, 0, 0, 0.65) 100%
    );
}

.cinematic-enter-active,
.cinematic-leave-active {
    transition:
        opacity 0.9s ease,
        filter 0.9s ease,
        transform 0.9s ease;
}

.cinematic-enter-from {
    opacity: 0;
    filter: blur(14px) saturate(0.8);
    transform: scale(1.06);
}

.cinematic-leave-to {
    opacity: 0;
    filter: blur(10px) saturate(1.2);
    transform: scale(0.96);
}

/* Content stagger, fades up after the image settles */
.content-enter-active {
    transition:
        opacity 0.7s ease 0.15s,
        transform 0.7s ease 0.15s;
}

.content-enter-from {
    opacity: 0;
    transform: translateY(24px);
}

/* Progress bar fill matches the autoplay duration (driven by inline animationDuration) */
.progress-fill {
    animation: progress linear forwards;
    animation-duration: 5s;
}

@keyframes progress {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}

@media (prefers-reduced-motion: reduce) {
    .progress-fill {
        animation: none;
        width: 100%;
    }
}
</style>
