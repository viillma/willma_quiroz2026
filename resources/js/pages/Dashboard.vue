<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    Palette,
    Users,
    FolderKanban,
    TrendingUp,
    MessageSquare,
    MonitorSmartphone,
    Sparkles,
    ShieldCheck,
    ArrowUpRight,
    Activity,
    BarChart3,
} from 'lucide-vue-next';

import { dashboard } from '@/routes';
import type { Team } from '@/types';

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam
                    ? dashboard(props.currentTeam.slug)
                    : '/',
            },
        ],
    }),
});
</script>

<template>
    <Head title="Dashboard Diseño Gráfico" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 space-y-6 relative">
        <!-- Background radial effects -->
        <!-- Hero Section -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-900/60 via-blue-900/40 to-purple-900/60 border border-white/10 p-1 animate-slide-up">
            <div class="absolute inset-0 bg-grid-subtle"></div>
            <div class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-purple-500/10 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-blue-500/10 blur-3xl"></div>

            <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6 rounded-[calc(1.5rem-4px)] bg-[#0B1120]/60 backdrop-blur-sm px-6 py-6 md:px-8 md:py-7">
                <div class="flex items-start gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-500/30 to-blue-500/30 ring-1 ring-white/20 shadow-lg shadow-purple-500/20 animate-float">
                        <Sparkles class="h-7 w-7 text-purple-300" />
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">
                            Panel de Control
                        </h1>
                        <p class="mt-1 text-sm md:text-base text-white/60 max-w-xl">
                            Panel administrativo creativo y moderno — monitorea el rendimiento de tu estudio de diseño.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2.5 rounded-2xl bg-white/5 px-4 py-2.5 ring-1 ring-white/10">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75 animate-ping"></span>
                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-green-400"></span>
                        </span>
                        <span class="text-xs font-medium text-white/60">Sistema activo</span>
                    </div>
                    <button class="btn-gradient-blue btn-shimmer px-5 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-blue-500/20">
                        Nuevo Proyecto
                    </button>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div class="kpi-blue rounded-2xl p-5 kpi-card shadow-lg glow-blue animate-slide-up">
                <div class="relative z-10 flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-blue-200/80">Proyectos</p>
                        <h2 class="mt-1 text-3xl font-black text-white">120</h2>
                        <p class="mt-1 flex items-center gap-1 text-xs font-medium text-green-300">
                            <ArrowUpRight class="h-3 w-3" />
                            +12% este mes
                        </p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm ring-1 ring-white/20">
                        <FolderKanban class="h-7 w-7 text-white" />
                    </div>
                </div>
                <div class="relative z-10 mt-3 h-1 w-full rounded-full bg-white/10">
                    <div class="h-1 w-3/4 rounded-full bg-white/30"></div>
                </div>
            </div>

            <div class="kpi-purple rounded-2xl p-5 kpi-card shadow-lg glow-purple animate-slide-up" style="animation-delay: 0.1s">
                <div class="relative z-10 flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-purple-200/80">Clientes</p>
                        <h2 class="mt-1 text-3xl font-black text-white">85</h2>
                        <p class="mt-1 flex items-center gap-1 text-xs font-medium text-green-300">
                            <ArrowUpRight class="h-3 w-3" />
                            +8 nuevos
                        </p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm ring-1 ring-white/20">
                        <Users class="h-7 w-7 text-white" />
                    </div>
                </div>
                <div class="relative z-10 mt-3 h-1 w-full rounded-full bg-white/10">
                    <div class="h-1 w-4/5 rounded-full bg-white/30"></div>
                </div>
            </div>

            <div class="kpi-pink rounded-2xl p-5 kpi-card shadow-lg glow-pink animate-slide-up" style="animation-delay: 0.2s">
                <div class="relative z-10 flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-pink-200/80">Diseños</p>
                        <h2 class="mt-1 text-3xl font-black text-white">340</h2>
                        <p class="mt-1 flex items-center gap-1 text-xs font-medium text-green-300">
                            <ArrowUpRight class="h-3 w-3" />
                            +25 nuevos
                        </p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm ring-1 ring-white/20">
                        <Palette class="h-7 w-7 text-white" />
                    </div>
                </div>
                <div class="relative z-10 mt-3 h-1 w-full rounded-full bg-white/10">
                    <div class="h-1 w-3/5 rounded-full bg-white/30"></div>
                </div>
            </div>

            <div class="kpi-cyan rounded-2xl p-5 kpi-card shadow-lg glow-cyan animate-slide-up" style="animation-delay: 0.3s">
                <div class="relative z-10 flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-cyan-200/80">Ganancias</p>
                        <h2 class="mt-1 text-3xl font-black text-white">$5K</h2>
                        <p class="mt-1 flex items-center gap-1 text-xs font-medium text-green-300">
                            <ArrowUpRight class="h-3 w-3" />
                            +18% crecimiento
                        </p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm ring-1 ring-white/20">
                        <TrendingUp class="h-7 w-7 text-white" />
                    </div>
                </div>
                <div class="relative z-10 mt-3 h-1 w-full rounded-full bg-white/10">
                    <div class="h-1 w-2/3 rounded-full bg-white/30"></div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Left Panel: Recent Activity -->
            <div class="glass-card rounded-2xl p-6 lg:col-span-2 animate-slide-up relative overflow-hidden" style="animation-delay: 0.1s">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-blue-500/5 to-transparent rounded-full blur-2xl"></div>

                <div class="relative flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-white">Actividad Reciente</h2>
                        <p class="text-sm text-white/50">Últimos movimientos del sistema</p>
                    </div>
                    <div class="rounded-2xl bg-gradient-to-br from-purple-500/30 to-blue-500/30 p-3 ring-1 ring-white/10">
                        <Activity class="h-5 w-5 text-purple-300" />
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="group flex items-center justify-between rounded-xl bg-white/[0.03] p-4 ring-1 ring-white/[0.04] transition-all duration-300 hover:bg-white/[0.06] hover:ring-white/[0.08] hover:translate-x-1">
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500/20 to-pink-500/20 ring-1 ring-purple-500/20">
                                <Palette class="h-5 w-5 text-purple-300" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-white text-sm">Nuevo diseño de branding</h3>
                                <p class="text-xs text-white/50">Cliente: Tech Studio</p>
                            </div>
                        </div>
                        <span class="shrink-0 rounded-full bg-white/[0.04] px-3 py-1 text-xs font-medium text-cyan-400">Hace 2h</span>
                    </div>

                    <div class="group flex items-center justify-between rounded-xl bg-white/[0.03] p-4 ring-1 ring-white/[0.04] transition-all duration-300 hover:bg-white/[0.06] hover:ring-white/[0.08] hover:translate-x-1">
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500/20 to-blue-500/20 ring-1 ring-cyan-500/20">
                                <MessageSquare class="h-5 w-5 text-cyan-300" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-white text-sm">Nuevo mensaje recibido</h3>
                                <p class="text-xs text-white/50">Consulta sobre diseño web</p>
                            </div>
                        </div>
                        <span class="shrink-0 rounded-full bg-white/[0.04] px-3 py-1 text-xs font-medium text-cyan-400">Hace 4h</span>
                    </div>

                    <div class="group flex items-center justify-between rounded-xl bg-white/[0.03] p-4 ring-1 ring-white/[0.04] transition-all duration-300 hover:bg-white/[0.06] hover:ring-white/[0.08] hover:translate-x-1">
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-green-500/20 to-emerald-500/20 ring-1 ring-green-500/20">
                                <TrendingUp class="h-5 w-5 text-green-300" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-white text-sm">Incremento de ventas</h3>
                                <p class="text-xs text-white/50">+18% este mes</p>
                            </div>
                        </div>
                        <span class="shrink-0 rounded-full bg-white/[0.04] px-3 py-1 text-xs font-medium text-green-400">Hoy</span>
                    </div>

                    <div class="group flex items-center justify-between rounded-xl bg-white/[0.03] p-4 ring-1 ring-white/[0.04] transition-all duration-300 hover:bg-white/[0.06] hover:ring-white/[0.08] hover:translate-x-1">
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500/20 to-amber-500/20 ring-1 ring-orange-500/20">
                                <ShieldCheck class="h-5 w-5 text-orange-300" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-white text-sm">Nuevo usuario registrado</h3>
                                <p class="text-xs text-white/50">Diseñador: María García</p>
                            </div>
                        </div>
                        <span class="shrink-0 rounded-full bg-white/[0.04] px-3 py-1 text-xs font-medium text-orange-400">Ayer</span>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Statistics -->
            <div class="glass-card rounded-2xl p-6 animate-slide-up relative overflow-hidden" style="animation-delay: 0.2s">
                <div class="absolute top-0 left-0 w-32 h-32 bg-gradient-to-br from-purple-500/5 to-transparent rounded-full blur-2xl"></div>

                <div class="relative">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-white">Estadísticas</h2>
                        <div class="rounded-xl bg-gradient-to-br from-cyan-500/30 to-blue-500/30 p-2.5 ring-1 ring-white/10">
                            <BarChart3 class="h-4 w-4 text-cyan-300" />
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <div class="mb-2 flex justify-between text-sm">
                                <span class="text-white/60">Branding</span>
                                <span class="font-semibold text-cyan-400">85%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill w-[85%] bg-gradient-to-r from-purple-500 to-cyan-500"></div>
                            </div>
                        </div>

                        <div>
                            <div class="mb-2 flex justify-between text-sm">
                                <span class="text-white/60">Diseño Web</span>
                                <span class="font-semibold text-cyan-400">70%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill w-[70%] bg-gradient-to-r from-cyan-500 to-blue-500"></div>
                            </div>
                        </div>

                        <div>
                            <div class="mb-2 flex justify-between text-sm">
                                <span class="text-white/60">Motion Graphics</span>
                                <span class="font-semibold text-cyan-400">60%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill w-[60%] bg-gradient-to-r from-orange-500 to-pink-500"></div>
                            </div>
                        </div>

                        <div>
                            <div class="mb-2 flex justify-between text-sm">
                                <span class="text-white/60">Fotografía</span>
                                <span class="font-semibold text-cyan-400">45%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill w-[45%] bg-gradient-to-r from-green-500 to-teal-500"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 rounded-2xl bg-gradient-to-br from-purple-600/40 via-blue-600/30 to-pink-600/40 p-5 text-white ring-1 ring-white/10 shadow-lg">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/10">
                                <Sparkles class="h-4 w-4 text-purple-300" />
                            </div>
                            <h3 class="font-bold text-lg">Creative Studio</h3>
                        </div>
                        <p class="text-sm text-white/60">Sistema moderno de administración de diseño gráfico.</p>
                        <div class="mt-3 flex gap-2">
                            <span class="badge-purple text-xs px-2.5 py-0.5 rounded-full">Premium</span>
                            <span class="badge-cyan text-xs px-2.5 py-0.5 rounded-full">En vivo</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
