<script setup lang="ts">
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { useReveal } from '@/composables/useScrollReveal';

gsap.registerPlugin(ScrollTrigger);

const props = defineProps<{ landing?: Record<string, string> }>();

const L = (key: string, fallback: string) => props.landing?.[key] || fallback;

const sectionRef = ref<HTMLElement | null>(null);

let mm: ReturnType<typeof gsap.matchMedia> | null = null;

const pillars = [
    { label: 'Estrategia', dot: 'bg-pix' },
    { label: 'Diseño', dot: 'bg-lilac' },
    { label: 'Tecnología', dot: 'bg-mint' },
    { label: 'Creatividad', dot: 'bg-peach' },
];

function setupImageParallax() {
    const wrap = sectionRef.value?.querySelector('.about-image-wrap');
    const image = sectionRef.value?.querySelector('.about-image');

    if (!wrap || !image) {
        return;
    }

    gsap.fromTo(
        image,
        { yPercent: -12 },
        {
            yPercent: 12,
            ease: 'none',
            scrollTrigger: {
                trigger: wrap,
                start: 'top bottom',
                end: 'bottom top',
                scrub: true,
            },
        },
    );
}

function init() {
    if (typeof window === 'undefined') {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    mm = gsap.matchMedia();

    mm.add('(min-width: 768px)', () => {
        setupImageParallax();
    });
}

onMounted(() => {
    useReveal('.about-head', { y: 40 });

    const copy = sectionRef.value?.querySelectorAll('.about-copy');

    if (copy?.length) {
        useReveal(copy, { y: 34, stagger: 0.12 });
    }

    init();
});

onBeforeUnmount(() => {
    if (mm) {
        mm.revert();
    }
});
</script>

<template>
    <section
        id="nosotros"
        ref="sectionRef"
        class="relative scroll-mt-24 overflow-hidden bg-warm py-24 lg:py-36"
    >
        <div
            class="pointer-events-none absolute top-24 -left-28 h-[380px] w-[380px] rounded-full bg-blush/60 blur-3xl"
        ></div>

        <div
            class="relative mx-auto grid max-w-7xl items-center gap-16 px-6 lg:grid-cols-2 lg:gap-20 lg:px-8"
        >
            <!-- Copy -->
            <div class="order-2 lg:order-1">
                <div class="about-head">
                    <span
                        class="text-xs font-bold tracking-[0.25em] text-pix uppercase"
                        >Sobre nosotros</span
                    >
                    <h2
                        class="mt-5 font-display text-4xl leading-[1.05] font-extrabold tracking-tight text-ink sm:text-5xl lg:text-6xl"
                    >
                        Creatividad con
                        <em class="text-gradient-pink not-italic"
                            >propósito.</em
                        >
                    </h2>
                </div>

                <p
                    class="about-copy mt-7 max-w-xl text-lg leading-relaxed text-ink/60"
                >
                    {{
                        L(
                            'nosotros_texto',
                            'Somos un estudio creativo especializado en transformar ideas en piezas visuales que conectan con las personas.',
                        )
                    }}
                </p>

                <div class="about-copy mt-9 flex flex-wrap gap-3">
                    <span
                        v-for="pillar in pillars"
                        :key="pillar.label"
                        class="inline-flex items-center gap-2 rounded-full border border-ink/8 bg-white px-4 py-2 text-sm font-bold text-ink/75"
                    >
                        <span
                            class="h-2 w-2 rounded-full"
                            :class="pillar.dot"
                        ></span>
                        {{ pillar.label }}
                    </span>
                </div>
            </div>

            <!-- Image -->
            <div class="order-1 lg:order-2">
                <div
                    class="about-image-wrap relative overflow-hidden rounded-[32px] shadow-2xl shadow-ink/15"
                >
                    <img
                        src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=1200&q=80"
                        alt="Estudio creativo CREAPIX"
                        loading="lazy"
                        decoding="async"
                        class="about-image h-[440px] w-full scale-[1.18] object-cover lg:h-[560px]"
                    />
                    <div
                        class="pointer-events-none absolute inset-0 bg-gradient-to-t from-ink/30 via-transparent to-transparent"
                    ></div>

                    <div
                        class="absolute bottom-6 left-6 rounded-2xl bg-cream/95 px-6 py-5 shadow-xl backdrop-blur"
                    >
                        <span
                            class="font-display text-4xl font-extrabold text-pix"
                            >5+</span
                        >
                        <span class="ml-2 text-sm font-semibold text-ink/60"
                            >años creando marcas</span
                        >
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
