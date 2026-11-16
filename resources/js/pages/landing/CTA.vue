<script setup lang="ts">
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { ArrowRight } from 'lucide-vue-next';
import { onBeforeUnmount, onMounted, ref } from 'vue';

gsap.registerPlugin(ScrollTrigger);

const props = defineProps<{ landing?: Record<string, string> }>();

const L = (key: string, fallback: string) => props.landing?.[key] || fallback;

function whatsappHref(num: string | number) {
    return `https://wa.me/${String(num).replace(/\D/g, '')}`;
}

const sectionRef = ref<HTMLElement | null>(null);

let mm: ReturnType<typeof gsap.matchMedia> | null = null;

function setupParallax() {
    const section = sectionRef.value;

    if (!section) {
        return;
    }

    gsap.utils
        .toArray<HTMLElement>('[data-cta-speed]', section)
        .forEach((el) => {
            const speed = parseFloat(el.dataset.ctaSpeed ?? '0');

            gsap.to(el, {
                yPercent: speed,
                ease: 'none',
                scrollTrigger: {
                    trigger: section,
                    start: 'top bottom',
                    end: 'bottom top',
                    scrub: true,
                },
            });
        });
}

function init() {
    if (typeof window === 'undefined') {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const media = gsap.matchMedia();
    mm = media;

    media.add('(min-width: 768px)', () => {
        setupParallax();
    });
}

onMounted(() => {
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
        id="contacto"
        ref="sectionRef"
        class="relative scroll-mt-24 overflow-hidden bg-cream py-24 lg:py-32"
    >
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div
                class="relative overflow-hidden rounded-[36px] px-8 py-16 text-center shadow-2xl shadow-ink/10 sm:px-16 lg:py-24"
                style="
                    background: linear-gradient(
                        135deg,
                        #fff7fa,
                        #f5eeff 45%,
                        #fff3ea 100%
                    );
                "
            >
                <!-- Decorative shapes -->
                <div class="pointer-events-none absolute inset-0">
                    <div
                        class="absolute -top-20 -left-16 h-72 w-72 rounded-full bg-pix/15 blur-3xl"
                        data-cta-speed="18"
                    ></div>
                    <div
                        class="absolute -right-16 -bottom-24 h-80 w-80 rounded-full bg-lilac/50 blur-3xl"
                        data-cta-speed="26"
                    ></div>
                    <div
                        class="absolute top-[10%] right-[12%] h-32 w-32 rounded-full bg-peach/70 blur-2xl"
                        data-cta-speed="10"
                    ></div>
                    <div
                        class="absolute bottom-[8%] left-[10%] h-28 w-28 rounded-full bg-mint/80 blur-2xl"
                        data-cta-speed="14"
                    ></div>
                    <span
                        class="absolute top-[16%] left-[18%] text-2xl text-pix/40"
                        data-cta-speed="8"
                        >✦</span
                    >
                    <span
                        class="absolute right-[20%] bottom-[18%] text-xl text-lilac"
                        data-cta-speed="20"
                        >✦</span
                    >
                    <span
                        class="absolute bottom-[22%] left-[30%] h-2.5 w-2.5 rounded-full bg-pix"
                        data-cta-speed="22"
                    ></span>
                    <span
                        class="absolute top-[22%] right-[32%] h-2 w-2 rounded-full bg-lilac"
                        data-cta-speed="12"
                    ></span>
                </div>

                <div class="relative">
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-pix/20 bg-white/70 px-4 py-2 text-[11px] font-bold tracking-[0.2em] text-pix uppercase backdrop-blur"
                    >
                        Comencemos
                    </span>

                    <h2
                        class="mx-auto mt-8 max-w-3xl font-display text-4xl leading-[1.05] font-extrabold tracking-tight text-ink sm:text-5xl lg:text-7xl"
                    >
                        ¿Tienes una
                        <em class="text-gradient-pink not-italic">idea?</em>
                    </h2>
                    <p
                        class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-ink/60"
                    >
                        Vamos a convertirla en algo extraordinario. Cuéntanos tu
                        proyecto y diseñemos juntos tu próxima gran marca.
                    </p>

                    <div
                        class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row"
                    >
                        <a
                            :href="
                                whatsappHref(L('contacto_whatsapp', '6404467'))
                            "
                            target="_blank"
                            rel="noopener"
                            class="group/btn inline-flex items-center gap-3 rounded-full bg-pix px-10 py-4 text-sm font-bold text-white shadow-xl shadow-pix/30 transition-all duration-300 hover:-translate-y-1 hover:bg-pix-2 hover:shadow-2xl hover:shadow-pix/40"
                        >
                            Solicita tu proyecto
                            <ArrowRight
                                class="h-4 w-4 transition-transform duration-300 group-hover/btn:translate-x-1.5"
                            />
                        </a>
                        <a
                            :href="`mailto:${L('contacto_correo', 'hola@creapix.com')}`"
                            class="inline-flex items-center gap-2 rounded-full border-2 border-ink/10 bg-white/70 px-8 py-4 text-sm font-bold text-ink transition-all duration-300 hover:-translate-y-1 hover:border-pix/30 hover:bg-blush/30"
                        >
                            {{ L('contacto_correo', 'hola@creapix.com') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
