<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';

import { dashboard } from '@/routes';
import reportes from '@/routes/reportes';

const page = usePage<{
    topClients: Array<{
        id: number;
        name: string;
        orders_count: number;
        phone: string;
        company: string;
    }>;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard().url },
            { title: 'Reportes', href: reportes.index().url },
            { title: 'Clientes Frecuentes', href: '#' },
        ],
    }),
});
const topClients = computed(() => page.props.topClients ?? []);
</script>

<template>
    <Head title="Clientes Frecuentes" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <Heading
                title="Top 10 Clientes Frecuentes"
                description="Clientes con más pedidos"
            />
            <Button
                variant="ghost"
                size="sm"
                class="btn-gradient-ghost"
                as-child
            >
                <Link :href="reportes.index().url">Volver</Link>
            </Button>
        </div>

        <div class="glass-card card-glow overflow-hidden rounded-2xl">
            <table class="table-glow w-full">
                <thead>
                    <tr
                        class="border-b border-white/10 bg-gradient-to-r from-purple-500/10 to-cyan-500/5"
                    >
                        <th
                            class="px-4 py-3.5 text-left text-sm font-semibold text-white/60"
                        >
                            Rank
                        </th>
                        <th
                            class="px-4 py-3.5 text-left text-sm font-semibold text-white/60"
                        >
                            Cliente
                        </th>
                        <th
                            class="px-4 py-3.5 text-left text-sm font-semibold text-white/60"
                        >
                            Empresa
                        </th>
                        <th
                            class="px-4 py-3.5 text-left text-sm font-semibold text-white/60"
                        >
                            Teléfono
                        </th>
                        <th
                            class="px-4 py-3.5 text-right text-sm font-semibold text-white/60"
                        >
                            Pedidos
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr
                        v-for="(client, index) in topClients"
                        :key="client.id"
                        class="text-white/70 transition-all duration-200 hover:bg-white/5"
                    >
                        <td class="px-4 py-3.5">
                            <span
                                v-if="index === 0"
                                class="badge-purple px-2.5 py-1 text-xs font-bold"
                                >{{ index + 1 }}</span
                            >
                            <span
                                v-else-if="index === 1"
                                class="badge-cyan px-2.5 py-1 text-xs font-bold"
                                >{{ index + 1 }}</span
                            >
                            <span
                                v-else-if="index === 2"
                                class="badge-yellow px-2.5 py-1 text-xs font-bold"
                                >{{ index + 1 }}</span
                            >
                            <span
                                v-else
                                class="inline-flex items-center justify-center rounded-md bg-white/10 px-2.5 py-1 text-xs font-bold text-white/50"
                                >{{ index + 1 }}</span
                            >
                        </td>
                        <td class="px-4 py-3.5 text-sm font-medium text-white">
                            {{ client.name }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="badge-gray px-2.5 py-1 text-sm">{{
                                client.company || '-'
                            }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-white/50">
                            {{ client.phone || '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <span
                                class="kpi-purple rounded-md px-2.5 py-1 text-sm font-semibold"
                                >{{ client.orders_count }}</span
                            >
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
