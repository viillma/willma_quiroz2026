<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useReveal, useRevealGroup } from '@/composables/useScrollReveal';

const sectionRef = ref<HTMLElement | null>(null);

const galleryItems = [
    {
        image: 'https://images.unsplash.com/photo-1522542550221-31fd19575a2d?auto=format&fit=crop&w=900&q=80',
        caption: 'Sistema de marca',
        tall: true,
    },
    {
        image: 'https://images.unsplash.com/photo-1558655146-9f40138edfeb?auto=format&fit=crop&w=900&q=80',
        caption: 'Diseño digital',
        tall: false,
    },
    {
        image: 'https://images.unsplash.com/photo-1572044162444-ad60f128bdea?auto=format&fit=crop&w=900&q=80',
        caption: 'Ilustración',
        tall: false,
    },
    {
        image: 'https://images.unsplash.com/photo-1611348586804-61bf6c080437?auto=format&fit=crop&w=900&q=80',
        caption: 'Arte abstracto',
        tall: true,
    },
    {
        image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=900&q=80',
        caption: 'Fotografía de producto',
        tall: false,
    },
    {
        image: 'https://images.unsplash.com/photo-1542744094-3a31f272c490?auto=format&fit=crop&w=900&q=80',
        caption: 'Presentación de marca',
        tall: false,
    },
    {
        image: 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=900&q=80',
        caption: 'Colaboración creativa',
        tall: true,
    },
];

onMounted(() => {
    useReveal('.gallery-head', { y: 40 });

    const items = sectionRef.value?.querySelectorAll('.gallery-item');

    if (items?.length) {
        useRevealGroup(sectionRef.value, items, {
            y: 50,
            scale: 0.94,
            stagger: 0.08,
        });
    }
});
</script>

<template>
    <section
        id="galeria"
        ref="sectionRef"
        class="relative scroll-mt-24 overflow-hidden bg-cream py-24 lg:py-36"
    >
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="gallery-head mb-16 max-w-3xl lg:mb-20">
                <span
                    class="text-xs font-bold tracking-[0.25em] text-pix uppercase"
                    >Galería</span
                >
                <h2
                    class="mt-5 font-display text-4xl leading-[1.05] font-extrabold tracking-tight text-ink sm:text-5xl lg:text-6xl"
                >
                    Piezas que hablan por
                    <em class="text-gradient-soft not-italic">sí solas.</em>
                </h2>
            </div>

            <div class="columns-1 gap-6 sm:columns-2 lg:columns-3">
                <figure
                    v-for="(item, idx) in galleryItems"
                    :key="idx"
                    class="gallery-item group mb-6 break-inside-avoid"
                >
                    <div
                        class="relative overflow-hidden rounded-3xl shadow-lg shadow-ink/5 transition-shadow duration-500 group-hover:shadow-2xl group-hover:shadow-ink/15"
                    >
                        <img
                            :src="item.image"
                            :alt="item.caption"
                            loading="lazy"
                            decoding="async"
                            :class="item.tall ? 'aspect-[3/4]' : 'aspect-[4/3]'"
                            class="w-full object-cover transition-transform duration-[1.1s] ease-out group-hover:scale-[1.05]"
                        />
                        <div
                            class="absolute inset-0 flex items-end bg-gradient-to-t from-ink/55 via-transparent to-transparent p-5 opacity-0 transition-opacity duration-500 group-hover:opacity-100"
                        >
                            <span
                                class="text-xs font-bold tracking-widest text-cream uppercase"
                                >{{ item.caption }}</span
                            >
                        </div>
                    </div>
                </figure>
            </div>
        </div>
    </section>
</template>
