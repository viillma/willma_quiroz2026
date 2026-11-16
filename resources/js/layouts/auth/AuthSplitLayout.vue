<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

import GlobalBackground from '@/components/GlobalBackground.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-screen overflow-hidden bg-cream">
        <!-- Left Panel: Brand & Design -->
        <div
            class="relative hidden w-[55%] overflow-hidden lg:flex lg:flex-col"
        >
            <!-- Pastel background -->
            <div class="absolute inset-0 bg-[#FFFDFB]">
                <div
                    class="absolute -top-32 -left-32 h-[520px] w-[520px] rounded-full bg-blush/60 blur-3xl"
                ></div>
                <div
                    class="absolute top-[15%] -right-20 h-[460px] w-[460px] rounded-full bg-lilac/50 blur-3xl"
                ></div>
                <div
                    class="absolute bottom-[-10%] left-[25%] h-[420px] w-[420px] rounded-full bg-peach/50 blur-3xl"
                ></div>
                <div
                    class="absolute right-[12%] bottom-[15%] h-[300px] w-[300px] rounded-full bg-mint/60 blur-3xl"
                ></div>
            </div>

            <!-- Decorative shapes -->
            <div class="pointer-events-none absolute inset-0 opacity-30">
                <svg
                    class="absolute top-24 right-12 h-28 w-28 text-pix/40"
                    viewBox="0 0 100 100"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.2"
                >
                    <circle cx="50" cy="50" r="44" />
                    <path d="M50 6L50 94M6 50L94 50" />
                </svg>
                <svg
                    class="absolute bottom-36 left-10 h-20 w-20 text-lilac"
                    viewBox="0 0 100 100"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.2"
                >
                    <polygon points="50,5 95,90 5,90" />
                </svg>
                <svg
                    class="absolute top-1/2 left-1/3 h-16 w-16 text-peach/70"
                    viewBox="0 0 100 100"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.2"
                >
                    <rect x="12" y="12" width="76" height="76" rx="18" />
                </svg>
            </div>

            <!-- Content -->
            <div
                class="relative z-10 flex h-full flex-col justify-between p-14 xl:p-16"
            >
                <!-- Logo -->
                <Link :href="home()" class="flex w-fit items-center gap-3">
                    <span
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-ink font-display text-xl font-extrabold text-cream"
                    >
                        C<span class="text-pix">.</span>
                    </span>
                    <span
                        class="font-display text-2xl font-extrabold tracking-tight text-ink"
                    >
                        CREAPIX<span class="text-pix">.</span>
                    </span>
                </Link>

                <!-- Center content -->
                <div class="space-y-10">
                    <div class="space-y-5">
                        <h1
                            class="font-display text-5xl leading-[1.05] font-extrabold tracking-tight text-ink"
                        >
                            {{ title || 'Diseño Gráfico<br />Profesional' }}
                        </h1>
                        <p class="max-w-md text-lg leading-relaxed text-ink/55">
                            {{
                                description ||
                                'Estrategia, creatividad y diseño para marcas que inspiran, conectan y venden.'
                            }}
                        </p>
                    </div>

                    <!-- Creative composition -->
                    <div class="relative h-56 max-w-lg">
                        <div
                            class="absolute top-0 right-4 w-40 rounded-2xl bg-white p-4 shadow-xl shadow-ink/10"
                            style="transform: rotate(3deg)"
                        >
                            <span
                                class="text-[10px] font-bold tracking-[0.2em] text-ink/40 uppercase"
                                >Paleta</span
                            >
                            <div class="mt-2.5 flex gap-1.5">
                                <span class="h-8 w-8 rounded-lg bg-pix"></span>
                                <span
                                    class="h-8 w-8 rounded-lg bg-lilac"
                                ></span>
                                <span class="h-8 w-8 rounded-lg bg-mint"></span>
                                <span
                                    class="h-8 w-8 rounded-lg bg-peach"
                                ></span>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 w-52 rounded-2xl bg-white p-5 shadow-xl shadow-ink/10"
                            style="transform: rotate(-4deg)"
                        >
                            <span
                                class="font-display text-2xl font-extrabold text-ink"
                                >CREAPIX<span class="text-pix">.</span></span
                            >
                            <div
                                class="mt-3 h-1.5 w-24 rounded-full bg-ink/10"
                            ></div>
                            <div
                                class="mt-1.5 h-1.5 w-32 rounded-full bg-ink/5"
                            ></div>
                        </div>
                        <span
                            class="absolute top-6 left-1/2 h-20 w-20 rounded-full bg-pix text-cream shadow-lg shadow-pix/30"
                            style="
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            "
                        >
                            <svg
                                class="h-8 w-8"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M12 2L2 7l10 5 10-5-10-5z" />
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-sm font-medium text-ink/40">
                    &copy; {{ new Date().getFullYear() }} CREAPIX. Estudio
                    creativo.
                </div>
            </div>
        </div>

        <!-- Right Panel: Auth Form -->
        <div
            class="relative flex flex-1 items-center justify-center p-4 lg:p-8"
        >
            <GlobalBackground />
            <div class="relative z-10 w-full max-w-md">
                <!-- Mobile logo -->
                <div
                    class="mb-8 flex flex-col items-center justify-center lg:hidden"
                >
                    <Link :href="home()" class="flex items-center gap-2">
                        <span
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-ink font-display text-lg font-extrabold text-cream"
                        >
                            C<span class="text-pix">.</span>
                        </span>
                        <span
                            class="font-display text-xl font-extrabold tracking-tight text-ink"
                        >
                            CREAPIX<span class="text-pix">.</span>
                        </span>
                    </Link>
                </div>

                <!-- Card -->
                <div
                    class="w-full rounded-[28px] border border-ink/5 bg-white/90 p-8 shadow-[0_24px_60px_-24px_rgba(23,26,43,0.25)] backdrop-blur-xl lg:p-10"
                >
                    <slot />
                </div>

                <!-- Mobile footer -->
                <div
                    class="mt-6 text-center text-xs font-medium text-ink/40 lg:hidden"
                >
                    &copy; {{ new Date().getFullYear() }} CREAPIX. Estudio
                    creativo.
                </div>
            </div>
        </div>
    </div>
</template>
