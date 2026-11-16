<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useReveal } from '@/composables/useScrollReveal';

const props = defineProps<{ landing?: Record<string, string> }>();

const L = (key: string, fallback: string) => props.landing?.[key] || fallback;

const sectionRef = ref<HTMLElement | null>(null);

const stats = [
    {
        value: () => L('stats_proyectos', '150+'),
        label: 'Proyectos realizados',
    },
    { value: () => L('stats_clientes', '100+'), label: 'Clientes satisfechos' },
    { value: () => L('stats_anios', '8'), label: 'Años de experiencia' },
    { value: () => '∞', label: 'Creatividad sin límites' },
];

onMounted(() => {
    useReveal('.stats-head', { y: 40 });

    const blocks = sectionRef.value?.querySelectorAll('.stat-block');

    if (blocks?.length) {
        useReveal(blocks, { y: 40, stagger: 0.1 });
    }
});
</script>

<template>
    <section
        id="numeros"
        ref="sectionRef"
        class="relative scroll-mt-24 overflow-hidden bg-cream py-20 lg:py-28"
    >
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="stats-head mb-14 max-w-2xl">
                <span
                    class="text-xs font-bold tracking-[0.25em] text-pix uppercase"
                    >Nuestros números</span
                >
                <h2
                    class="mt-5 font-display text-3xl leading-tight font-extrabold tracking-tight text-ink sm:text-4xl lg:text-5xl"
                >
                    Resultados que hablan
                    <em class="text-gradient-soft not-italic">por nosotros.</em>
                </h2>
            </div>

            <div class="grid grid-cols-2 gap-y-12 lg:grid-cols-4">
                <div
                    v-for="(stat, idx) in stats"
                    :key="stat.label"
                    class="stat-block relative px-2 lg:px-10 lg:text-center"
                    :class="{ 'lg:border-l lg:border-ink/8': idx > 0 }"
                >
                    <span
                        class="font-display text-5xl font-extrabold tracking-tight text-ink lg:text-6xl"
                    >
                        {{ stat.value() }}
                    </span>
                    <span
                        class="mt-3 block text-sm font-bold tracking-widest text-ink/45 uppercase"
                        >{{ stat.label }}</span
                    >
                </div>
            </div>
        </div>
    </section>
</template>
