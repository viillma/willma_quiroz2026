<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { TrendingUp, BarChart3, Users, Palette, AlertTriangle } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import reportes from '@/routes/reportes';
import { dashboard } from '@/routes';
import type { Team } from '@/types';

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            { title: 'Dashboard', href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/' },
            { title: 'Reportes', href: '#' },
        ],
    }),
});

const page = usePage();
const currentTeam = page.props.currentTeam as Team | null;

const reportCards = [
    {
        title: 'Ingresos',
        description: 'Reporte de ingresos por período',
        icon: TrendingUp,
        routeName: 'income',
        color: 'from-green-500 to-emerald-500',
    },
    {
        title: 'Servicios más solicitados',
        description: 'Ranking de servicios más vendidos',
        icon: BarChart3,
        routeName: 'services',
        color: 'from-blue-500 to-cyan-500',
    },
    {
        title: 'Clientes frecuentes',
        description: 'Top 10 clientes con más pedidos',
        icon: Users,
        routeName: 'clients',
        color: 'from-purple-500 to-violet-500',
    },
    {
        title: 'Diseñadores',
        description: 'Productividad por diseñador',
        icon: Palette,
        routeName: 'designers',
        color: 'from-orange-500 to-amber-500',
    },
    {
        title: 'Clientes Morosos',
        description: 'Pedidos con saldo pendiente',
        icon: AlertTriangle,
        routeName: 'morosos',
        color: 'from-red-500 to-pink-500',
    },
];
</script>

<template>
    <Head title="Reportes" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 space-y-6 relative">
        <Heading title="Reportes" description="Indicadores y estadísticas del negocio" />

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="card in reportCards"
                :key="card.route"
                class="glass-card rounded-2xl p-6 card-glow group cursor-pointer transition-all duration-200 hover:scale-[1.02] animate-slide-up"
            >
                <Link v-if="currentTeam" :href="reportes[card.routeName](currentTeam.slug).url">
                    <div
                        class="mb-4 inline-flex rounded-xl bg-gradient-to-r p-3 animate-float"
                        :class="card.color"
                    >
                        <component :is="card.icon" class="h-6 w-6 text-white" />
                    </div>
                    <h3 class="text-lg font-semibold text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-fuchsia-400 group-hover:to-cyan-400 group-hover:bg-clip-text transition-all">{{ card.title }}</h3>
                    <p class="mt-1 text-sm text-white/50">{{ card.description }}</p>
                </Link>
            </div>
        </div>
    </div>
</template>
