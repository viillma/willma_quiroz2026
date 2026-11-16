<script setup lang="ts">
import { ArrowRight } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { useReveal, useRevealGroup } from '@/composables/useScrollReveal';
import { scrollToSection } from '@/composables/useSmoothScroll';

const sectionRef = ref<HTMLElement | null>(null);

const services = [
    {
        number: '01',
        title: 'Branding',
        description: 'Construimos identidades visuales que dejan huella.',
        accent: 'bg-blush',
    },
    {
        number: '02',
        title: 'Diseño Gráfico',
        description: 'Piezas visuales que comunican, conectan y destacan.',
        accent: 'bg-lilac',
    },
    {
        number: '03',
        title: 'Diseño Web',
        description: 'Experiencias digitales modernas, rápidas y funcionales.',
        accent: 'bg-mint',
    },
    {
        number: '04',
        title: 'Marketing Digital',
        description: 'Estrategias visuales que generan resultados.',
        accent: 'bg-peach',
    },
];

function goTo(hash: string) {
    scrollToSection(hash);
}

onMounted(() => {
    useReveal('.services-head', { y: 40 });

    const cards = sectionRef.value?.querySelectorAll('.service-card');

    if (cards?.length) {
        useRevealGroup(sectionRef.value, cards, { y: 50, stagger: 0.12 });
    }
});
</script>

<template>
    <section
        id="servicios"
        ref="sectionRef"
        class="relative scroll-mt-24 overflow-hidden bg-cream py-24 lg:py-36"
    >
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div
                class="services-head mb-16 flex flex-col items-start justify-between gap-6 lg:mb-24 lg:flex-row lg:items-end"
            >
                <div class="max-w-2xl">
                    <span
                        class="text-xs font-bold tracking-[0.25em] text-pix uppercase"
                        >Nuestros servicios</span
                    >
                    <h2
                        class="mt-5 font-display text-4xl leading-[1.05] font-extrabold tracking-tight text-ink sm:text-5xl lg:text-6xl"
                    >
                        Todo lo que tu marca
                        <em class="text-gradient-soft not-italic">necesita.</em>
                    </h2>
                </div>
                <p
                    class="max-w-md text-base leading-relaxed text-ink/55 lg:text-right"
                >
                    Del logo a la experiencia completa: diseñamos cada punto de
                    contacto para que tu negocio conecte y venda.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <article
                    v-for="service in services"
                    :key="service.number"
                    class="service-card group relative overflow-hidden rounded-[28px] border border-ink/5 bg-white p-8 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-ink/8 lg:p-10"
                >
                    <div
                        class="absolute top-0 right-0 h-28 w-28 translate-x-10 -translate-y-10 rounded-full opacity-70 blur-2xl transition-opacity duration-500 group-hover:opacity-100"
                        :class="service.accent"
                    ></div>

                    <div class="relative">
                        <span
                            class="font-display text-7xl leading-none font-extrabold text-transparent lg:text-8xl"
                            style="
                                -webkit-text-stroke: 2px rgba(244, 63, 122, 0.4);
                            "
                        >
                            {{ service.number }}
                        </span>
                        <h3
                            class="mt-6 font-display text-2xl font-extrabold tracking-tight text-ink lg:text-3xl"
                        >
                            {{ service.title }}
                        </h3>
                        <p
                            class="mt-3 max-w-sm text-base leading-relaxed text-ink/55"
                        >
                            {{ service.description }}
                        </p>

                        <a
                            href="#contacto"
                            class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-pix transition-colors duration-300 hover:text-pix-2"
                            @click.prevent="goTo('#contacto')"
                        >
                            Saber más
                            <ArrowRight
                                class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
                            />
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>
