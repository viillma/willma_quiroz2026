<script setup lang="ts">
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { ArrowRight } from 'lucide-vue-next';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { scrollToSection } from '@/composables/useSmoothScroll';

gsap.registerPlugin(ScrollTrigger);

const props = defineProps<{ landing?: Record<string, string> }>();

const L = (key: string, fallback: string) => props.landing?.[key] || fallback;

function whatsappHref(num: string | number) {
    return `https://wa.me/${String(num).replace(/\D/g, '')}`;
}

const sectionRef = ref<HTMLElement | null>(null);
const visualRef = ref<HTMLElement | null>(null);

let mm: ReturnType<typeof gsap.matchMedia> | null = null;
let mouseCleanup: (() => void) | null = null;

function goTo(hash: string) {
    scrollToSection(hash);
}

function setupEntrance() {
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' }, delay: 0.2 });

    tl.fromTo(
        '.hero-badge',
        { y: 22, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.7 },
        0,
    )
        .fromTo(
            '.hero-title',
            { yPercent: 120 },
            { yPercent: 0, duration: 1.05, stagger: 0.12 },
            0.15,
        )
        .fromTo(
            '.hero-subtitle',
            { y: 28, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.8 },
            '-=0.55',
        )
        .fromTo(
            '.hero-buttons',
            { y: 28, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.8 },
            '-=0.45',
        )
        .fromTo(
            '.hero-trust',
            { y: 18, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.7 },
            '-=0.35',
        );

    gsap.fromTo(
        '.hero-mockup',
        { y: 64, opacity: 0, scale: 0.9 },
        {
            y: 0,
            opacity: 1,
            scale: 1,
            duration: 1.15,
            stagger: 0.12,
            ease: 'power3.out',
            delay: 0.45,
        },
    );

    gsap.fromTo(
        '.hero-float',
        { opacity: 0, scale: 0.4, y: 18 },
        {
            opacity: 1,
            scale: 1,
            y: 0,
            duration: 0.7,
            stagger: 0.07,
            ease: 'back.out(1.6)',
            delay: 1.05,
        },
    );
}

function setupFloaters() {
    const floats = sectionRef.value?.querySelectorAll('.hero-float');

    if (!floats?.length) {
        return;
    }

    floats.forEach((el, i) => {
        gsap.to(el, {
            y: i % 2 === 0 ? -10 : 8,
            x: i % 3 === 0 ? 6 : -5,
            rotation: i % 2 === 0 ? 4 : -4,
            duration: 3.6 + (i % 3) * 0.7,
            ease: 'sine.inOut',
            yoyo: true,
            repeat: -1,
        });
    });
}

function setupParallax() {
    const section = sectionRef.value;

    if (!section) {
        return;
    }

    gsap.utils
        .toArray<HTMLElement>('[data-hero-speed]', section)
        .forEach((el) => {
            const speed = parseFloat(el.dataset.heroSpeed ?? '0');

            gsap.to(el, {
                yPercent: speed,
                ease: 'none',
                scrollTrigger: {
                    trigger: section,
                    start: 'top top',
                    end: 'bottom top',
                    scrub: true,
                },
            });
        });
}

function setupMouseParallax() {
    const visual = visualRef.value;

    if (!visual) {
        return;
    }

    const move = (e: MouseEvent) => {
        const x = (e.clientX / window.innerWidth - 0.5) * 2;
        const y = (e.clientY / window.innerHeight - 0.5) * 2;

        gsap.to(visual, {
            x: x * 8,
            y: y * 8,
            duration: 1.6,
            ease: 'power2.out',
        });
    };

    window.addEventListener('mousemove', move, { passive: true });
    mouseCleanup = () => window.removeEventListener('mousemove', move);
}

function initAnimations() {
    if (typeof window === 'undefined') {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const media = gsap.matchMedia();
    mm = media;

    media.add('(min-width: 0px)', () => {
        setupEntrance();
        setupFloaters();

        return () => {
            gsap.killTweensOf('.hero-float');
        };
    });

    media.add('(min-width: 768px)', () => {
        setupParallax();
    });

    media.add('(min-width: 1024px)', () => {
        setupMouseParallax();
    });
}

onMounted(() => {
    initAnimations();
});

onBeforeUnmount(() => {
    if (mouseCleanup) {
        mouseCleanup();
    }

    if (mm) {
        mm.revert();
    }
});
</script>

<template>
    <section
        id="inicio"
        ref="sectionRef"
        class="relative overflow-hidden pt-32 pb-24 lg:pt-44 lg:pb-32"
    >
        <!-- Background layers -->
        <div class="pointer-events-none absolute inset-0" data-hero-speed="16">
            <div
                class="absolute top-[-12%] -left-40 h-[560px] w-[560px] rounded-full bg-blush/70 blur-3xl"
            ></div>
            <div
                class="absolute top-[4%] right-[-12%] h-[480px] w-[480px] rounded-full bg-lilac/60 blur-3xl"
            ></div>
            <div
                class="absolute bottom-[-18%] left-[22%] h-[440px] w-[440px] rounded-full bg-peach/60 blur-3xl"
            ></div>
            <div
                class="absolute right-[18%] bottom-[6%] h-[340px] w-[340px] rounded-full bg-mint/70 blur-3xl"
            ></div>
        </div>
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(rgba(23,26,43,0.055)_1px,transparent_1px)] [background-size:26px_26px] opacity-70"
        ></div>

        <div
            class="relative mx-auto grid max-w-7xl items-center gap-16 px-6 lg:grid-cols-[1.05fr_0.95fr] lg:gap-8 lg:px-8"
        >
            <!-- Left content -->
            <div class="relative z-10 text-center lg:text-left">
                <div
                    class="hero-badge inline-flex items-center gap-2 rounded-full border border-pix/15 bg-white/70 px-4 py-2 backdrop-blur"
                >
                    <span
                        class="h-2 w-2 animate-pulse rounded-full bg-pix"
                    ></span>
                    <span
                        class="text-[11px] font-bold tracking-[0.18em] text-pix uppercase"
                        >Estudio creativo · Diseño & branding</span
                    >
                </div>

                <h1
                    class="mt-8 font-display text-[2.7rem] leading-[1.02] font-extrabold tracking-tight text-ink sm:text-6xl lg:text-[4.4rem]"
                >
                    <span class="block overflow-hidden pb-1"
                        ><span class="hero-title block">{{
                            L('hero_titulo_linea1', 'CREAMOS IDEAS,')
                        }}</span></span
                    >
                    <span class="block overflow-hidden pb-1"
                        ><span class="hero-title block">{{
                            L('hero_titulo_linea2', 'CAPTURAMOS MOMENTOS,')
                        }}</span></span
                    >
                    <span class="block overflow-hidden pb-2"
                        ><span class="hero-title block">{{
                            L('hero_titulo_linea3', 'TRANSFORMAMOS MARCAS')
                        }}</span></span
                    >
                </h1>

                <p
                    class="hero-subtitle mx-auto mt-7 max-w-lg text-lg leading-relaxed text-ink/60 lg:mx-0"
                >
                    {{
                        L(
                            'hero_subtitulo',
                            'Soluciones creativas en diseño gráfico, fotografía y producción audiovisual para impulsar tu marca.',
                        )
                    }}
                </p>

                <div
                    class="hero-buttons mt-10 flex flex-col items-center gap-4 sm:flex-row sm:justify-center lg:justify-start"
                >
                    <a
                        href="#carrusel"
                        class="group inline-flex items-center justify-center gap-2 rounded-full bg-pix px-8 py-4 text-sm font-bold text-white shadow-xl shadow-pix/25 transition-all duration-300 hover:-translate-y-1 hover:bg-pix-2 hover:shadow-2xl hover:shadow-pix/30"
                        @click.prevent="goTo('#carrusel')"
                    >
                        {{ L('boton_primario_texto', 'Ver servicios') }}
                        <ArrowRight
                            class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
                        />
                    </a>
                    <a
                        :href="whatsappHref(L('contacto_whatsapp', '6404467'))"
                        target="_blank"
                        rel="noopener"
                        class="group inline-flex items-center justify-center gap-2 rounded-full border-2 border-ink/10 bg-white/60 px-8 py-4 text-sm font-bold text-ink backdrop-blur transition-all duration-300 hover:-translate-y-1 hover:border-pix/30 hover:bg-blush/30"
                    >
                        {{
                            L(
                                'boton_secundario_texto',
                                'Contactar por WhatsApp',
                            )
                        }}
                    </a>
                </div>

                <div
                    class="hero-trust mt-12 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 lg:justify-start"
                >
                    <div
                        class="flex items-center gap-2 text-sm font-semibold text-ink/50"
                    >
                        <span
                            class="font-display text-2xl font-extrabold text-ink"
                            >{{ L('stats_proyectos', '150+') }}</span
                        >
                        proyectos
                    </div>
                    <div class="h-4 w-px bg-ink/10"></div>
                    <div
                        class="flex items-center gap-2 text-sm font-semibold text-ink/50"
                    >
                        <span
                            class="font-display text-2xl font-extrabold text-ink"
                            >{{ L('stats_clientes', '100+') }}</span
                        >
                        clientes
                    </div>
                    <div class="h-4 w-px bg-ink/10"></div>
                    <div
                        class="flex items-center gap-2 text-sm font-semibold text-ink/50"
                    >
                        <span
                            class="font-display text-2xl font-extrabold text-ink"
                            >{{ L('stats_anios', '8') }}+</span
                        >
                        años creando
                    </div>
                </div>
            </div>

            <!-- Creative composition -->
            <div
                ref="visualRef"
                class="relative z-10 mx-auto h-[420px] w-full max-w-[640px] sm:h-[500px] lg:h-[600px]"
            >
                <!-- Soft glow behind -->
                <div
                    class="absolute top-1/2 left-1/2 h-[85%] w-[85%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-gradient-to-br from-blush/60 via-lilac/40 to-peach/50 blur-2xl"
                    data-hero-speed="-4"
                ></div>
                <div
                    class="absolute top-[5%] right-[3%] h-24 w-24 bg-[radial-gradient(rgba(23,26,43,0.12)_1px,transparent_1px)] [background-size:12px_12px] opacity-60"
                    data-hero-speed="22"
                ></div>

                <!-- Main brand board -->
                <div
                    class="absolute top-1/2 left-1/2 z-10 w-[72%] max-w-[500px] -translate-x-1/2 -translate-y-1/2"
                    data-hero-speed="-9"
                >
                    <div
                        class="hero-mockup relative rounded-[30px] bg-white p-6 shadow-2xl shadow-ink/15 sm:p-8"
                        style="transform: rotate(-3deg)"
                    >
                        <div class="flex items-center justify-between">
                            <span
                                class="font-display text-base font-extrabold tracking-tight text-ink"
                                >CREAPIX<span class="text-pix">.</span></span
                            >
                            <span
                                class="rounded-full bg-blush/70 px-3 py-1 text-[10px] font-bold tracking-widest text-pix uppercase"
                                >Branding · 2026</span
                            >
                        </div>

                        <div
                            class="relative mt-5 overflow-hidden rounded-2xl bg-[#FFF7F9]"
                            style="aspect-ratio: 4 / 3"
                        >
                            <div
                                class="absolute -top-12 -right-10 h-48 w-48 rounded-full bg-gradient-to-br from-pix/80 to-pix-2/70 blur-xl"
                            ></div>
                            <div
                                class="absolute -bottom-8 -left-8 h-36 w-36 rounded-full bg-lilac/90 blur-2xl"
                            ></div>
                            <div
                                class="absolute top-[18%] right-[16%] h-20 w-20 rounded-full border-2 border-ink/10"
                            ></div>
                            <div
                                class="absolute top-[20%] left-[10%] font-display text-6xl font-extrabold text-ink"
                            >
                                A
                            </div>
                            <div
                                class="absolute top-[60%] left-[10%] h-2 w-16 rounded-full bg-pix"
                            ></div>
                            <div
                                class="absolute top-[70%] left-[10%] h-2 w-24 rounded-full bg-ink/15"
                            ></div>
                            <div
                                class="absolute top-[80%] left-[10%] h-2 w-20 rounded-full bg-ink/10"
                            ></div>
                            <div
                                class="absolute bottom-[12%] left-[10%] flex items-center gap-1.5"
                            >
                                <span
                                    class="h-2 w-2 rounded-full bg-pix"
                                ></span>
                                <span
                                    class="h-2 w-2 rounded-full bg-lilac"
                                ></span>
                                <span
                                    class="h-2 w-2 rounded-full bg-mint"
                                ></span>
                                <span
                                    class="h-2 w-2 rounded-full bg-peach"
                                ></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business cards -->
                <div
                    class="absolute bottom-[4%] left-0 z-20 w-[36%] max-w-[240px]"
                    data-hero-speed="-13"
                >
                    <div class="relative">
                        <div
                            class="absolute inset-0 rounded-2xl bg-lilac shadow-xl"
                            style="
                                transform: translate(10px, -8px) rotate(6deg);
                            "
                        ></div>
                        <div
                            class="hero-mockup relative rounded-2xl bg-white p-4 shadow-xl"
                            style="transform: rotate(-5deg)"
                        >
                            <div class="flex items-center gap-2">
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-lg bg-ink font-display text-xs font-extrabold text-cream"
                                    >C<span class="text-pix">.</span></span
                                >
                                <span
                                    class="text-[11px] font-bold tracking-tight text-ink"
                                    >CREAPIX</span
                                >
                            </div>
                            <div
                                class="mt-3 h-1.5 w-16 rounded-full bg-ink/10"
                            ></div>
                            <div
                                class="mt-1.5 h-1.5 w-20 rounded-full bg-ink/5"
                            ></div>
                            <div class="mt-3 flex items-center justify-between">
                                <span
                                    class="text-[8px] font-bold tracking-widest text-ink/40 uppercase"
                                    >Diseño · Branding</span
                                >
                                <span
                                    class="h-3 w-3 rounded-full bg-pix"
                                ></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Color palette -->
                <div
                    class="absolute top-[8%] right-0 z-20 w-[26%] max-w-[190px]"
                    data-hero-speed="-6"
                >
                    <div
                        class="hero-mockup rounded-2xl bg-white p-4 shadow-xl"
                        style="transform: rotate(5deg)"
                    >
                        <span
                            class="text-[9px] font-bold tracking-[0.2em] text-ink/50 uppercase"
                            >Paleta</span
                        >
                        <div class="mt-3 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="h-7 w-9 rounded-md bg-pix"></span>
                                <span
                                    class="text-[9px] font-semibold text-ink/40"
                                    >#F43F7A</span
                                >
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="h-7 w-9 rounded-md bg-lilac"
                                ></span>
                                <span
                                    class="text-[9px] font-semibold text-ink/40"
                                    >#DCCBFF</span
                                >
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="h-7 w-9 rounded-md bg-mint"></span>
                                <span
                                    class="text-[9px] font-semibold text-ink/40"
                                    >#D8F3E8</span
                                >
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="h-7 w-9 rounded-md bg-peach"
                                ></span>
                                <span
                                    class="text-[9px] font-semibold text-ink/40"
                                    >#FFD8C2</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Typography card -->
                <div
                    class="absolute top-[4%] left-[2%] z-20 w-[28%] max-w-[200px]"
                    data-hero-speed="16"
                >
                    <div
                        class="hero-mockup rounded-2xl bg-ink p-4 shadow-xl"
                        style="transform: rotate(-7deg)"
                    >
                        <span
                            class="block font-display text-4xl leading-none font-extrabold text-cream"
                            >Aa</span
                        >
                        <span
                            class="mt-2 block text-[8px] font-bold tracking-[0.25em] text-cream/50 uppercase"
                            >Display · ExtraBold</span
                        >
                    </div>
                </div>

                <!-- Packaging -->
                <div
                    class="absolute right-[1%] bottom-[5%] z-20 w-[30%] max-w-[210px]"
                    data-hero-speed="-11"
                >
                    <div class="hero-mockup relative">
                        <div
                            class="absolute inset-x-0 top-0 h-2 rounded-t-xl bg-blush"
                        ></div>
                        <div
                            class="rounded-xl bg-gradient-to-br from-peach to-blush p-4 pt-6 shadow-xl"
                            style="transform: rotate(7deg)"
                        >
                            <span
                                class="text-[8px] font-bold tracking-[0.2em] text-pix uppercase"
                                >Nuevo</span
                            >
                            <div class="mt-1 flex items-center gap-1.5">
                                <span
                                    class="h-1.5 w-10 rounded bg-ink/15"
                                ></span>
                                <span
                                    class="h-1.5 w-6 rounded bg-ink/10"
                                ></span>
                            </div>
                            <div
                                class="mt-3 flex h-10 w-10 items-center justify-center rounded-full bg-white/70"
                            >
                                <span
                                    class="font-display text-lg font-extrabold text-pix"
                                    >P</span
                                >
                            </div>
                            <span
                                class="mt-3 block text-[8px] font-bold tracking-widest text-ink/40"
                                >PACKAGING</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Rotating badge -->
                <div
                    class="absolute top-[-1%] right-[26%] z-30 h-28 w-28"
                    data-hero-speed="20"
                >
                    <div class="relative h-full w-full">
                        <svg
                            viewBox="0 0 100 100"
                            class="h-full w-full animate-[spin_16s_linear_infinite]"
                        >
                            <defs>
                                <path
                                    id="creapix-hero-circle"
                                    d="M50,50 m-37,0 a37,37 0 1,1 74,0 a37,37 0 1,1 -74,0"
                                />
                            </defs>
                            <text
                                class="fill-ink/50 text-[8.5px] font-bold"
                                style="letter-spacing: 0.22em"
                            >
                                <textPath href="#creapix-hero-circle">
                                    CREAPIX STUDIO ✦ CREATIVE DESIGN ✦
                                </textPath>
                            </text>
                        </svg>
                        <div
                            class="absolute inset-0 flex items-center justify-center"
                        >
                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-pix text-sm text-cream shadow-lg shadow-pix/30"
                                >✦</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Floating accents -->
                <span
                    class="hero-float absolute top-[2%] left-[42%] text-xl text-pix"
                    >✦</span
                >
                <span
                    class="hero-float absolute bottom-[30%] left-[1%] text-sm text-ink/25"
                    >✦</span
                >
                <span
                    class="hero-float absolute top-[42%] right-[14%] text-lg text-pix/50"
                    >✦</span
                >
                <span
                    class="hero-float absolute bottom-[16%] left-[24%] h-2.5 w-2.5 rounded-full bg-mint"
                ></span>
                <span
                    class="hero-float absolute top-[16%] right-[40%] h-2 w-2 rounded-full bg-peach"
                ></span>
            </div>
        </div>

        <!-- Scroll indicator -->
        <div
            class="pointer-events-none absolute bottom-7 left-1/2 hidden -translate-x-1/2 flex-col items-center gap-2 lg:flex"
        >
            <span
                class="text-[10px] font-bold tracking-[0.3em] text-ink/35 uppercase"
                >Scroll</span
            >
            <span class="relative h-10 w-5 rounded-full border border-ink/15">
                <span
                    class="absolute top-2 left-1/2 h-2 w-1 -translate-x-1/2 rounded-full bg-pix"
                    style="animation: hero-wheel 2s ease-in-out infinite"
                ></span>
            </span>
        </div>
    </section>
</template>

<style scoped>
@keyframes hero-wheel {
    0% {
        opacity: 0;
        transform: translate(-50%, 0);
    }
    40% {
        opacity: 1;
    }
    100% {
        opacity: 0;
        transform: translate(-50%, 14px);
    }
}
</style>
