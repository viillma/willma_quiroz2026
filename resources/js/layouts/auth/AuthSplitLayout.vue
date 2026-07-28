<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import GlobalBackground from '@/components/GlobalBackground.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-screen">
        <!-- Left Panel: Brand & Design -->
        <div class="relative hidden w-[55%] overflow-hidden lg:flex lg:flex-col bg-gradient-to-br from-[#2563EB] via-[#7C3AED] to-[#EC4899]">
            <!-- Animated gradient orbs -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -left-20 -top-20 h-[500px] w-[500px] animate-pulse rounded-full bg-white/5 blur-3xl" style="animation-duration: 4s;" />
                <div class="absolute -bottom-32 -right-20 h-[600px] w-[600px] animate-pulse rounded-full bg-[#F97316]/15 blur-3xl" style="animation-duration: 6s; animation-delay: 1s;" />
                <div class="absolute left-1/3 top-1/3 h-80 w-80 animate-pulse rounded-full bg-[#FACC15]/10 blur-3xl" style="animation-duration: 5s; animation-delay: 2s;" />
                <div class="absolute bottom-1/4 left-1/4 h-48 w-48 animate-pulse rounded-full bg-white/5 blur-2xl" style="animation-duration: 7s; animation-delay: 0.5s;" />
            </div>

            <!-- Decorative geometric shapes -->
            <div class="absolute inset-0 overflow-hidden opacity-20">
                <svg class="absolute right-10 top-20 h-32 w-32 text-white" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1">
                    <circle cx="50" cy="50" r="45" />
                    <path d="M50 5L50 95M5 50L95 50" />
                </svg>
                <svg class="absolute bottom-32 left-10 h-24 w-24 text-white" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1">
                    <polygon points="50,5 95,90 5,90" />
                </svg>
                <svg class="absolute right-1/3 top-1/2 h-20 w-20 text-white" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1">
                    <rect x="10" y="10" width="80" height="80" rx="10" />
                </svg>
            </div>

            <!-- Content -->
            <div class="relative z-10 flex h-full flex-col justify-between p-16">
                <!-- Logo -->
                <Link :href="home()" class="flex items-center gap-3 text-white">
                    <AppLogoIcon class="h-11 w-11" />
                    <span class="text-2xl font-bold tracking-tight">ServicioDigital</span>
                </Link>

                <!-- Center content -->
                <div class="space-y-8">
                    <div class="space-y-4">
                        <h1 class="text-5xl font-bold leading-tight text-white">
                            {{ title || 'Diseño Gráfico<br>Profesional' }}
                        </h1>
                        <p class="max-w-md text-lg text-white/75">
                            {{ description || 'Soluciones creativas para tu marca: logotipos, banners, tarjetas, fotografía y más.' }}
                        </p>
                    </div>

                    <!-- Design service icons row -->
                    <div class="flex flex-wrap gap-6">
                        <div class="flex flex-col items-center gap-2 text-white/80">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                            </svg>
                            <span class="text-xs">Logotipos</span>
                        </div>
                        <div class="flex flex-col items-center gap-2 text-white/80">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M15 8h.01M3 6a3 3 0 013-3h12a3 3 0 013 3v12a3 3 0 01-3 3H6a3 3 0 01-3-3V6z" />
                                <path d="M3 16l5-5c.928-.893 2.072-.893 3 0l5 5" />
                                <path d="M14 14l1-1c.928-.893 2.072-.893 3 0l3 3" />
                            </svg>
                            <span class="text-xs">Fotografía</span>
                        </div>
                        <div class="flex flex-col items-center gap-2 text-white/80">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M12 20h9" />
                                <path d="M16.376 3.622a1 1 0 013.002 3.002L7.368 18.635a2 2 0 01-.855.506l-2.872.838a.5.5 0 01-.62-.62l.838-2.872a2 2 0 01.506-.854z" />
                            </svg>
                            <span class="text-xs">Edición</span>
                        </div>
                        <div class="flex flex-col items-center gap-2 text-white/80">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                <path d="M3 9h18" />
                                <path d="M9 21V9" />
                            </svg>
                            <span class="text-xs">Banners</span>
                        </div>
                        <div class="flex flex-col items-center gap-2 text-white/80">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M4 7V4h16v3" />
                                <path d="M9 20h6" />
                                <path d="M12 4v16" />
                            </svg>
                            <span class="text-xs">Tarjetas</span>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-sm text-white/50">
                    &copy; {{ new Date().getFullYear() }} ServicioDigital. Todos los derechos reservados.
                </div>
            </div>
        </div>

        <!-- Right Panel: Auth Form -->
        <div class="relative flex flex-1 items-center justify-center p-4 lg:p-8">
            <GlobalBackground />
            <div class="relative z-10 w-full max-w-md">
                <!-- Mobile logo -->
                <div class="mb-8 flex flex-col items-center justify-center lg:hidden">
                    <Link :href="home()" class="flex items-center gap-2">
                        <AppLogoIcon class="h-9 w-9 text-white" />
                        <span class="text-xl font-bold text-white">ServicioDigital</span>
                    </Link>
                </div>

                <!-- Glassmorphism card -->
                <div class="glass-card rounded-2xl p-8 max-w-md w-full lg:p-10">
                    <slot />
                </div>

                <!-- Mobile footer -->
                <div class="mt-6 text-center text-xs text-white/40 lg:hidden">
                    &copy; {{ new Date().getFullYear() }} ServicioDigital
                </div>
            </div>
        </div>
    </div>
</template>