<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Check } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { useReveal, useRevealGroup } from '@/composables/useScrollReveal';
import { scrollToSection } from '@/composables/useSmoothScroll';
import { login } from '@/routes';

const sectionRef = ref<HTMLElement | null>(null);

function goTo(hash: string) {
    scrollToSection(hash);
}

const plans = [
    {
        name: 'Branding Esencial',
        price: '$190',
        description: 'Para arrancar con una identidad clara y memorable.',
        features: [
            'Diseño de logotipo',
            'Paleta de color',
            'Tipografías',
            '2 rondas de revisión',
        ],
        featured: false,
    },
    {
        name: 'Crecimiento',
        price: '$390',
        description: 'Identidad completa para marcas que quieren crecer.',
        features: [
            'Logotipo + variantes',
            'Manual de marca',
            'Plantillas para redes',
            'Tarjetas de presentación',
            '4 rondas de revisión',
        ],
        featured: true,
    },
    {
        name: 'Estudio Completo',
        price: '$750+',
        description: 'Branding, web y marketing para dominar el mercado.',
        features: [
            'Identidad completa',
            'Diseño web',
            'Estrategia de contenido',
            'Soporte prioritario',
        ],
        featured: false,
    },
];

onMounted(() => {
    useReveal('.pricing-head', { y: 40 });

    const cards = sectionRef.value?.querySelectorAll('.pricing-card');

    if (cards?.length) {
        useRevealGroup(sectionRef.value, cards, { y: 50, stagger: 0.12 });
    }
});
</script>

<template>
    <section
        id="precios"
        ref="sectionRef"
        class="relative scroll-mt-24 overflow-hidden bg-warm py-24 lg:py-36"
    >
        <div
            class="pointer-events-none absolute top-20 -right-32 h-[400px] w-[400px] rounded-full bg-lilac/50 blur-3xl"
        ></div>

        <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
            <div class="pricing-head mb-16 max-w-2xl lg:mb-20">
                <span
                    class="text-xs font-bold tracking-[0.25em] text-pix uppercase"
                    >Planes y precios</span
                >
                <h2
                    class="mt-5 font-display text-4xl leading-[1.05] font-extrabold tracking-tight text-ink sm:text-5xl lg:text-6xl"
                >
                    Elige el plan ideal para tu
                    <em class="text-gradient-pink not-italic">proyecto.</em>
                </h2>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <article
                    v-for="plan in plans"
                    :key="plan.name"
                    class="pricing-card relative flex flex-col rounded-[28px] p-8 transition-all duration-500 lg:p-9"
                    :class="
                        plan.featured
                            ? 'bg-ink text-cream shadow-2xl shadow-ink/25 lg:-translate-y-4'
                            : 'border border-ink/5 bg-white hover:-translate-y-2 hover:shadow-2xl hover:shadow-ink/10'
                    "
                >
                    <div
                        v-if="plan.featured"
                        class="absolute -top-3 left-8 rounded-full bg-pix px-4 py-1 text-[10px] font-bold tracking-widest text-white uppercase shadow-lg shadow-pix/30"
                    >
                        Más popular
                    </div>

                    <h3
                        class="font-display text-xl font-extrabold tracking-tight"
                        :class="plan.featured ? 'text-cream' : 'text-ink'"
                    >
                        {{ plan.name }}
                    </h3>
                    <p
                        class="mt-2 text-sm leading-relaxed"
                        :class="plan.featured ? 'text-cream/60' : 'text-ink/55'"
                    >
                        {{ plan.description }}
                    </p>

                    <div class="mt-6 flex items-baseline gap-1">
                        <span
                            class="font-display text-5xl font-extrabold tracking-tight"
                            :class="plan.featured ? 'text-cream' : 'text-ink'"
                            >{{ plan.price }}</span
                        >
                        <span
                            class="text-sm font-semibold"
                            :class="
                                plan.featured ? 'text-cream/50' : 'text-ink/40'
                            "
                            >/ proyecto</span
                        >
                    </div>

                    <ul class="mt-8 flex-1 space-y-3">
                        <li
                            v-for="feature in plan.features"
                            :key="feature"
                            class="flex items-start gap-3 text-sm font-medium"
                            :class="
                                plan.featured ? 'text-cream/80' : 'text-ink/65'
                            "
                        >
                            <span
                                class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full"
                                :class="
                                    plan.featured
                                        ? 'bg-pix/25 text-pix-2'
                                        : 'bg-mint text-ink/70'
                                "
                            >
                                <Check class="h-3 w-3" />
                            </span>
                            {{ feature }}
                        </li>
                    </ul>

                    <Link
                        :href="login()"
                        class="group/btn mt-9 inline-flex items-center justify-center gap-2 rounded-full py-3.5 text-sm font-bold transition-all duration-300"
                        :class="
                            plan.featured
                                ? 'bg-pix text-white shadow-lg shadow-pix/30 hover:-translate-y-0.5 hover:bg-pix-2'
                                : 'border-2 border-ink/10 text-ink hover:-translate-y-0.5 hover:border-pix/40 hover:bg-blush/30'
                        "
                    >
                        Comenzar
                        <ArrowRight
                            class="h-4 w-4 transition-transform duration-300 group-hover/btn:translate-x-1"
                        />
                    </Link>
                </article>
            </div>

            <p class="pricing-foot mt-10 text-center text-sm text-ink/45">
                ¿Necesitas algo a medida?
                <a
                    href="#contacto"
                    class="font-bold text-pix underline-offset-4 hover:underline"
                    @click.prevent="goTo('#contacto')"
                    >Hablemos de tu proyecto</a
                >
            </p>
        </div>
    </section>
</template>
