<script setup lang="ts">
import { defineProps, defineEmits, computed, ref, onUnmounted, nextTick } from 'vue';
import { ArrowRight, Play } from 'lucide-vue-next';

type CarouselSlide = {
    id: number;
    titulo: string;
    descripcion: string | null;
    boton_texto: string;
    boton_enlace: string | null;
    imagen: string | null;
    imagen_url: string | null;
    duracion: number;
};

const props = defineProps<{ slides: CarouselSlide[] }>();
const emit = defineEmits(['next', 'prev']);

const carouselRef = ref<HTMLDivElement>();
let slideWidth = 0;
let totalSlides = 0;
let currentSlide = 0;
let timeoutId: ReturnType<typeof setTimeout> | null = null;

const wrapperStyle = computed(() => {
    if (!carouselRef.value) return '';
    return {
        transform: `translateX(-${currentSlide * slideWidth}px)`,
        transition: currentSlide === 0 || currentSlide === totalSlides - 1 ? 'transform 0.5s ease' : 'transform 0.3s ease',
    };
});

const goToSlide = (slideIndex: number) => {
    const maxSlide = totalSlides - 1;
    currentSlide = Math.max(0, Math.min(slideIndex, maxSlide));
    if (carouselRef.value) {
        carouselRef.value.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
    }
};

const nextSlide = () => {
    goToSlide(currentSlide + 1);
};

const prevSlide = () => {
    goToSlide(currentSlide - 1);
};

const startAutoSlide = () => {
    if (props.slides.length <= 1) return;
    timeoutId = setTimeout(() => {
        nextSlide();
        startAutoSlide();
    }, props.slides[0]?.duracion || 3000);
};

const stopAutoSlide = () => {
    if (timeoutId) {
        clearTimeout(timeoutId);
        timeoutId = null;
    }
};

onUnmounted(() => {
    stopAutoSlide();
});

nextTick(() => {
    if (carouselRef.value) {
        slideWidth = carouselRef.value.clientWidth;
        totalSlides = props.slides.length;
        if (totalSlides > 1) {
            startAutoSlide();
        }
    }
    // Update width on resize
    window.addEventListener('resize', () => {
        nextTick(() => {
            slideWidth = carouselRef.value.clientWidth;
        });
    });
});
</script>

<template>
    <div
        ref="carouselRef"
        class="relative group rounded-2xl overflow-hidden mb-8 shadow-card-elevated border-2 border-white"
    >
        <div
            class="relative w-full"
            :style="wrapperStyle"
        >
            <slot v-for="(slide, index) in slides" :key="slide.id">
                <div
                    class="relative flex-shrink-0 w-full"
                    :class="{
                        'opacity-100 transition-opacity duration-300': index === currentSlide,
                        'opacity-0': index !== currentSlide,
                    }"
                >
                    <img
                        v-if="slide.imagen"
                        :src="slide.imagen"
                        :alt="slide.titulo"
                        class="w-full h-64 object-cover rounded-t-lg transition-opacity duration-300"
                    />
                    <div
                        v-else
                        class="relative h-64 bg-gradient-to-b from-pix/50 via-lilac/40 to-transparent rounded-t-lg"
                    >
                        <ArrowRight class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-pix text-2xl opacity-50" />
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 right-0 p-6 pt-8"
                    style="background: rgba(0,0,0,0.6);; background: rgba(0,0,0,0.6);"
                >
                    <div class="max-w-2xl mx-auto">
                        <h3 class="text-white font-display text-2xl font-bold tracking-tight mb-2">
                            {{ slide.titulo }}
                        </h3>
                        <p class="text-white text-pix text-sm leading-relaxed mb-4 line-clamp-2">
                            {{ slide.descripcion }}
                        </p>
                        <a
                            href="{{ slide.boton_enlace }}"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center gap-2 rounded-full bg-pix px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-pix/25 transition-transform hover:-translate-y-0.5"
                        >
                            {{ slide.boton_texto }}
                            <ArrowRight class="h-4 w-4 transition-transform group-hover:translate-x-1" />
                        </a>
                    </div>
                </div>
            </slot>
        </div>

        <!-- Navigation buttons -->
        <div
            v-if="slides.length > 1"
            class="absolute inset-y-0 flex items-center gap-2 px-2 pointer-events-none md:pointer-events-z-index"
        >
            <button
                v-if="currentSlide > 0"
                @click="prevSlide"
                class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-white/70 px-3 py-1.5 text-sm font-medium text-ink transition-colors hover:bg-white"
            >
                <span class="sr-only">Anterior</span>
            </button>
            <button
                v-if="currentSlide < slides.length - 1"
                @click="nextSlide"
                class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-white/70 px-3 py-1.5 text-sm font-medium text-ink transition-colors hover:bg-white"
            >
                <span class="sr-only">Siguiente</span>
            </button>
        </div>

        <!-- Pagination dots -->
        <div
            v-if="slides.length > 1"
            class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2"
        >
            <button
                v-for="(slide, index) in slides"
                :key="slide.id"
                @click="goToSlide(index)"
                class="w-2 h-2 rounded-full bg-white/50 transition-colors hover:bg-white opacity-80 data-[state=active]:bg-white data-[state=focus]:bg-white"
            />
        </div>
    </div>
</template>

<style scoped>
/* Carousel container animations */
.carousel-slide-enter-active {
    transition: opacity 0.5s ease;
}
.carousel-slide-leave-active {
    transition: opacity 0.5s ease;
}
.carousel-slide-enter-from {
    opacity: 0;
}
.carousel-slide-leave-to {
    opacity: 0;
}

/* Nav button hover */
button[data-vue-hash] {
    transition: all 0.2s ease;
}
</style>