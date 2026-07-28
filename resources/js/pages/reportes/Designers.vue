<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import reportes from '@/routes/reportes';
import { dashboard } from '@/routes';
import type { Team } from '@/types';

const page = usePage<{
    currentTeam?: Team | null;
    designers: Array<{ id: number; name: string; orders_count: number; email: string }>;
}>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            { title: 'Dashboard', href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/' },
            { title: 'Reportes', href: props.currentTeam ? reportes.index(props.currentTeam.slug).url : '#' },
            { title: 'Diseñadores', href: '#' },
        ],
    }),
});

const currentTeam = page.props.currentTeam as Team | null;
</script>

<template>
    <Head title="Productividad por Diseñador" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 space-y-6 relative">
        <div class="flex items-center justify-between">
            <Heading title="Productividad por Diseñador" description="Pedidos asignados por diseñador" />
            <Button v-if="currentTeam" variant="ghost" size="sm" class="btn-gradient-ghost" as-child>
                <Link :href="reportes.index(currentTeam.slug).url">Volver</Link>
            </Button>
        </div>

        <div class="glass-card rounded-2xl overflow-hidden card-glow">
            <table class="table-glow w-full">
                <thead>
                    <tr class="border-b border-white/10 bg-gradient-to-r from-purple-500/10 to-cyan-500/5">
                        <th class="px-4 py-3.5 text-left text-sm font-semibold text-white/60">Diseñador</th>
                        <th class="px-4 py-3.5 text-left text-sm font-semibold text-white/60">Correo</th>
                        <th class="px-4 py-3.5 text-right text-sm font-semibold text-white/60">Pedidos Asignados</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr v-for="(designer, index) in designers" :key="designer.id" class="transition-all duration-200 text-white/70 hover:bg-white/5">
                        <td class="px-4 py-3.5 text-sm font-medium text-white flex items-center gap-2">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-pink-500/20 to-purple-500/20 text-xs font-bold text-white/60">{{ index + 1 }}</span>
                            {{ designer.name }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-white/50">{{ designer.email }}</td>
                        <td class="px-4 py-3.5 text-right"><span class="kpi-pink px-2.5 py-1 rounded-md text-sm font-semibold">{{ designer.orders_count }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
