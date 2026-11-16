<script setup lang="ts">
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { ArrowUpRight } from 'lucide-vue-next';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { useReveal } from '@/composables/useScrollReveal';

gsap.registerPlugin(ScrollTrigger);

const sectionRef = ref<HTMLElement | null>(null);

let mm: ReturnType<typeof gsap.matchMedia> | null = null;

const projects = [
    {
        number: '01',
        title: 'Identidad Vitale',
        category: 'Branding',
        image: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=1600&q=80',
    },
    {
        number: '02',
        title: 'Aurora Packaging',
        category: 'Packaging',
        image: 'https://images.unsplash.com/photo-1503602642458-232111445657?auto=format&fit=crop&w=1600&q=80',
    },
    {
        number: '03',
        title: 'Lumen Editorial',
        category: 'Diseño editorial',
        image: 'https://images.unsplash.com/photo-1586717791821-3f44a563fa4c?auto=format&fit=crop&w=1600&q=80',
    },
    {
        number: '04',
        title: 'Nova Digital',
        category: 'Diseño web',
        image: 'https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?auto=format&fit=crop&w=1600&q=80',
    },
];

function setupExpansions() {
    const frames = gsap.utils.toArray<HTMLElement>(
        '.featured-frame',
        sectionRef.value,
    );

    if (!frames?.length) {
        return;
    }

    frames.forEach((frame) => {
        const initialWidth = frame.dataset.width ?? '72%';

        gsap.fromTo(
            frame,
            { scale: 0.92, width: initialWidth, borderRadius: 28 },
            {
                scale: 1,
                width: '100%',
                borderRadius: 12,
                ease: 'none',
                scrollTrigger: {
                    trigger: frame,
                    start: 'top 92%',
                    end: 'center center',
                    scrub: true,
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
        setupExpansions();
    });
}

onMounted(() => {
    useReveal('.work-head', { y: 40 });

    const items = sectionRef.value?.querySelectorAll('.work-item');

    if (items?.length) {
        useReveal(items, {
            y: 60,
            stagger: 0.08,
            start: 'top 90%',
            duration: 1.2,
        });
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
        id="portafolio"
        ref="sectionRef"
        class="relative scroll-mt-24 overflow-hidden bg-warm py-24 lg:py-36"
    >
        <div
            class="pointer-events-none absolute top-1/3 -left-32 h-[420px] w-[420px] rounded-full bg-lavender/60 blur-3xl"
        ></div>
        <div
            class="pointer-events-none absolute -right-32 bottom-0 h-[380px] w-[380px] rounded-full bg-blush/50 blur-3xl"
        ></div>

        <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
            <div class="work-head mb-16 max-w-3xl lg:mb-24">
                <span
                    class="text-xs font-bold tracking-[0.25em] text-pix uppercase"
                    >Trabajos destacados</span
                >
                <h2
                    class="mt-5 font-display text-4xl leading-[1.05] font-extrabold tracking-tight text-ink sm:text-5xl lg:text-6xl"
                >
                    Ideas que se convirtieron en
                    <em class="text-gradient-pink not-italic">marcas.</em>
                </h2>
            </div>

            <div class="space-y-20 lg:space-y-28">
                <article
                    v-for="(project, idx) in projects"
                    :key="project.title"
                    class="work-item group"
                >
                    <div
                        class="flex"
                        :class="idx % 2 === 1 ? 'justify-end' : 'justify-start'"
                    >
                        <div
                            class="featured-frame relative overflow-hidden shadow-2xl shadow-ink/10"
                            :data-width="idx % 2 === 1 ? '78%' : '72%'"
                            :style="{ width: idx % 2 === 1 ? '78%' : '72%' }"
                        >
                            <img
                                :src="project.image"
                                :alt="project.title"
                                loading="lazy"
                                decoding="async"
                                class="h-[52vh] w-full object-cover transition-transform duration-[1.2s] ease-out group-hover:scale-[1.04]"
                            />
                            <div
                                class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-ink/75 via-ink/10 to-transparent p-8 opacity-0 transition-opacity duration-500 group-hover:opacity-100"
                            >
                                <span
                                    class="w-fit rounded-full bg-cream/90 px-3 py-1 text-[10px] font-bold tracking-widest text-pix uppercase"
                                    >{{ project.category }}</span
                                >
                                <h3
                                    class="mt-3 font-display text-2xl font-extrabold text-cream lg:text-3xl"
                                >
                                    {{ project.title }}
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between gap-4">
                        <span
                            class="font-display text-sm font-bold tracking-widest text-pix"
                            >{{ project.number }}</span
                        >
                        <h3
                            class="font-display text-xl font-extrabold text-ink lg:text-2xl"
                        >
                            {{ project.title }}
                        </h3>
                        <span
                            class="inline-flex items-center gap-1 text-xs font-bold tracking-widest text-ink/45 uppercase transition-colors duration-300 group-hover:text-pix"
                        >
                            {{ project.category }}
                            <ArrowUpRight
                                class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                            />
                        </span>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>
