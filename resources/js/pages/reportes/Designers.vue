<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';

import { dashboard } from '@/routes';
import reportes from '@/routes/reportes';

const page = usePage<{
    designers: Array<{
        id: number;
        name: string;
        orders_count: number;
        email: string;
    }>;
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard().url },
            { title: 'Reportes', href: reportes.index().url },
            { title: 'Diseñadores', href: '#' },
        ],
    }),
});
const designers = computed(() => page.props.designers ?? []);
</script>

<template>
    <Head title="Productividad por Diseñador" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <Heading
                title="Productividad por Diseñador"
                description="Pedidos asignados por diseñador"
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
                            Diseñador
                        </th>
                        <th
                            class="px-4 py-3.5 text-left text-sm font-semibold text-white/60"
                        >
                            Correo
                        </th>
                        <th
                            class="px-4 py-3.5 text-right text-sm font-semibold text-white/60"
                        >
                            Pedidos Asignados
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr
                        v-for="(designer, index) in designers"
                        :key="designer.id"
                        class="text-white/70 transition-all duration-200 hover:bg-white/5"
                    >
                        <td
                            class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium text-white"
                        >
                            <span
                                class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-pink-500/20 to-purple-500/20 text-xs font-bold text-white/60"
                                >{{ index + 1 }}</span
                            >
                            {{ designer.name }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-white/50">
                            {{ designer.email }}
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <span
                                class="kpi-pink rounded-md px-2.5 py-1 text-sm font-semibold"
                                >{{ designer.orders_count }}</span
                            >
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
