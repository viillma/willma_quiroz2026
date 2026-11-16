<script setup lang="ts">
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { useReveal } from '@/composables/useScrollReveal';

gsap.registerPlugin(ScrollTrigger);

const sectionRef = ref<HTMLElement | null>(null);

let mm: ReturnType<typeof gsap.matchMedia> | null = null;

const steps = [
    {
        number: '01',
        title: 'Descubrimos',
        description:
            'Entendemos tu marca, tu audiencia y tus objetivos para definir una estrategia clara.',
    },
    {
        number: '02',
        title: 'Diseñamos',
        description:
            'Exploramos, bocetamos y diseñamos la solución visual desde el primer trazo.',
    },
    {
        number: '03',
        title: 'Refinamos',
        description:
            'Iteramos cada detalle —tipografía, color y forma— hasta lograr la pieza perfecta.',
    },
    {
        number: '04',
        title: 'Lanzamos',
        description:
            'Entregamos todo listo para publicar, escalar y crecer con tu nueva identidad.',
    },
];

function setupLine() {
    const line = sectionRef.value?.querySelector('.process-line-fill');

    if (!line) {
        return;
    }

    gsap.fromTo(
        line,
        { scaleY: 0 },
        {
            scaleY: 1,
            ease: 'none',
            scrollTrigger: {
                trigger: sectionRef.value,
                start: 'top 75%',
                end: 'bottom 70%',
                scrub: true,
            },
        },
    );
}

function setupSteps() {
    const stepsNodes = sectionRef.value?.querySelectorAll('.process-step');

    if (!stepsNodes?.length) {
        return;
    }

    stepsNodes.forEach((step) => {
        gsap.fromTo(
            step,
            { opacity: 0, y: 50 },
            {
                opacity: 1,
                y: 0,
                duration: 0.9,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: step,
                    start: 'top 78%',
                    toggleActions: 'play none none none',
                },
            },
        );
    });
}

function init() {
    if (typeof window === 'undefined') {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    mm = gsap.matchMedia();

    mm.add('(min-width: 0px)', () => {
        setupLine();
        setupSteps();
    });
}

onMounted(() => {
    useReveal('.process-head', { y: 40 });
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
        id="proceso"
        ref="sectionRef"
        class="relative scroll-mt-24 overflow-hidden bg-warm py-24 lg:py-36"
    >
        <div
            class="pointer-events-none absolute top-16 -right-24 h-[360px] w-[360px] rounded-full bg-lilac/50 blur-3xl"
        ></div>
        <div
            class="pointer-events-none absolute bottom-16 -left-24 h-[340px] w-[340px] rounded-full bg-mint/60 blur-3xl"
        ></div>

        <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
            <div class="process-head mb-16 max-w-2xl lg:mb-24">
                <span
                    class="text-xs font-bold tracking-[0.25em] text-pix uppercase"
                    >Cómo trabajamos</span
                >
                <h2
                    class="mt-5 font-display text-4xl leading-[1.05] font-extrabold tracking-tight text-ink sm:text-5xl lg:text-6xl"
                >
                    Del concepto a la
                    <em class="text-gradient-pink not-italic">realidad.</em>
                </h2>
            </div>

            <div class="relative mx-auto max-w-3xl">
                <div
                    class="absolute top-4 bottom-4 left-5 w-[2px] rounded-full bg-ink/8"
                ></div>
                <div
                    class="process-line-fill absolute top-4 bottom-4 left-5 w-[2px] origin-top rounded-full bg-gradient-to-b from-pix via-pix-2 to-lilac"
                ></div>

                <div class="space-y-14 lg:space-y-20">
                    <div
                        v-for="step in steps"
                        :key="step.number"
                        class="process-step relative pl-16"
                    >
                        <div
                            class="absolute top-0 left-0 flex h-11 w-11 items-center justify-center rounded-full border-2 border-pix/30 bg-cream font-display text-sm font-extrabold text-pix shadow-lg shadow-pix/10"
                        >
                            {{ step.number }}
                        </div>
                        <h3
                            class="pt-1 font-display text-2xl font-extrabold tracking-tight text-ink lg:text-3xl"
                        >
                            {{ step.title }}
                        </h3>
                        <p
                            class="mt-3 max-w-xl text-base leading-relaxed text-ink/55"
                        >
                            {{ step.description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
