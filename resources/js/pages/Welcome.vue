<script setup lang="ts">
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { onBeforeUnmount, onMounted } from 'vue';
import {
    destroySmoothScroll,
    initSmoothScroll,
} from '@/composables/useSmoothScroll';
import About from './landing/About.vue';
import CTA from './landing/CTA.vue';
import FeaturedWork from './landing/FeaturedWork.vue';
import Footer from './landing/Footer.vue';
import Gallery from './landing/Gallery.vue';
import HeroCarousel from './landing/HeroCarousel.vue';
import Navbar from './landing/Navbar.vue';
import Pricing from './landing/Pricing.vue';
import Process from './landing/Process.vue';
import Services from './landing/Services.vue';
import Stats from './landing/Stats.vue';

defineProps({
    canRegister: Boolean,
    carousel: Array,
    landing: Object,
    configuracion: Object,
});

let tickerFn: ((time: number) => void) | null = null;

function setupSmoothScroll() {
    const lenis = initSmoothScroll();

    lenis.on('scroll', ScrollTrigger.update);

    tickerFn = (time) => lenis.raf(time * 1000);
    gsap.ticker.add(tickerFn);
    gsap.ticker.lagSmoothing(0);
}

onMounted(() => {
    gsap.registerPlugin(ScrollTrigger);
    setupSmoothScroll();
    ScrollTrigger.refresh();
});

onBeforeUnmount(() => {
    if (tickerFn) {
        gsap.ticker.remove(tickerFn);
    }

    destroySmoothScroll();
});
</script>

<template>
    <div
        class="min-h-screen overflow-x-hidden bg-cream font-sans text-ink antialiased"
    >
        <Navbar :configuracion="(configuracion as any) || {}" />
        <main>
            <HeroCarousel :slides="(carousel as any[]) || []" />
            <FeaturedWork />
            <Services />
            <Process />
            <Gallery />
            <About :landing="(landing as any) || {}" />
            <Stats :landing="(landing as any) || {}" />
            <Pricing />
            <CTA :landing="(landing as any) || {}" />
            <Footer
                :landing="(landing as any) || {}"
                :configuracion="(configuracion as any) || {}"
            />
        </main>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap');

::selection {
    background: rgba(244, 63, 122, 0.22);
    color: #171a2b;
}
</style>
