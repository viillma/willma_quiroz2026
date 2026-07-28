<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    groups: { label: string; items: NavItem[] }[];
}>();

const { isCurrentUrl } = useCurrentUrl();

const itemColors: Record<string, {
    bar: string;
    barShadow: string;
    bg: string;
    icon: string;
    iconActive: string;
    hover: string;
}> = {
    Dashboard: {
        bar: 'bg-blue-400',
        barShadow: 'shadow-blue-400/60',
        bg: 'from-blue-600/30 to-blue-800/20',
        icon: 'text-blue-400',
        iconActive: 'text-blue-300',
        hover: 'hover:bg-blue-500/10 hover:border-blue-500/20',
    },
    Servicios: {
        bar: 'bg-cyan-400',
        barShadow: 'shadow-cyan-400/60',
        bg: 'from-cyan-600/30 to-blue-800/20',
        icon: 'text-cyan-400',
        iconActive: 'text-cyan-300',
        hover: 'hover:bg-cyan-500/10 hover:border-cyan-500/20',
    },
    Clientes: {
        bar: 'bg-emerald-400',
        barShadow: 'shadow-emerald-400/60',
        bg: 'from-emerald-600/30 to-teal-800/20',
        icon: 'text-emerald-400',
        iconActive: 'text-emerald-300',
        hover: 'hover:bg-emerald-500/10 hover:border-emerald-500/20',
    },
    Catálogo: {
        bar: 'bg-amber-400',
        barShadow: 'shadow-amber-400/60',
        bg: 'from-amber-600/30 to-orange-800/20',
        icon: 'text-amber-400',
        iconActive: 'text-amber-300',
        hover: 'hover:bg-amber-500/10 hover:border-amber-500/20',
    },
    Pedidos: {
        bar: 'bg-purple-400',
        barShadow: 'shadow-purple-400/60',
        bg: 'from-purple-600/30 to-violet-800/20',
        icon: 'text-purple-400',
        iconActive: 'text-purple-300',
        hover: 'hover:bg-purple-500/10 hover:border-purple-500/20',
    },
    Diseños: {
        bar: 'bg-pink-400',
        barShadow: 'shadow-pink-400/60',
        bg: 'from-pink-600/30 to-rose-800/20',
        icon: 'text-pink-400',
        iconActive: 'text-pink-300',
        hover: 'hover:bg-pink-500/10 hover:border-pink-500/20',
    },
    Pagos: {
        bar: 'bg-green-400',
        barShadow: 'shadow-green-400/60',
        bg: 'from-green-600/30 to-emerald-800/20',
        icon: 'text-green-400',
        iconActive: 'text-green-300',
        hover: 'hover:bg-green-500/10 hover:border-green-500/20',
    },
    Reportes: {
        bar: 'bg-orange-400',
        barShadow: 'shadow-orange-400/60',
        bg: 'from-orange-600/30 to-amber-800/20',
        icon: 'text-orange-400',
        iconActive: 'text-orange-300',
        hover: 'hover:bg-orange-500/10 hover:border-orange-500/20',
    },
    Usuarios: {
        bar: 'bg-indigo-400',
        barShadow: 'shadow-indigo-400/60',
        bg: 'from-indigo-600/30 to-blue-800/20',
        icon: 'text-indigo-400',
        iconActive: 'text-indigo-300',
        hover: 'hover:bg-indigo-500/10 hover:border-indigo-500/20',
    },
    Roles: {
        bar: 'bg-violet-400',
        barShadow: 'shadow-violet-400/60',
        bg: 'from-violet-600/30 to-purple-800/20',
        icon: 'text-violet-400',
        iconActive: 'text-violet-300',
        hover: 'hover:bg-violet-500/10 hover:border-violet-500/20',
    },
    Configuración: {
        bar: 'bg-slate-400',
        barShadow: 'shadow-slate-400/60',
        bg: 'from-slate-600/30 to-gray-800/20',
        icon: 'text-slate-400',
        iconActive: 'text-slate-300',
        hover: 'hover:bg-slate-500/10 hover:border-slate-500/20',
    },
};
</script>

<template>
    <SidebarGroup v-for="group in groups" :key="group.label" class="px-3 py-0">
        <SidebarGroupLabel class="px-2 text-[11px] font-semibold tracking-widest uppercase text-sidebar-foreground/40">
            {{ group.label }}
        </SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem
                v-for="item in group.items"
                :key="item.title"
                class="group/nav-item relative"
            >
                <!-- Accent bar with per-item color -->
                <div
                    v-if="isCurrentUrl(item.href)"
                    class="absolute left-0 top-1/2 -translate-y-1/2 z-10 h-7 w-[4px] rounded-r-full transition-all duration-300"
                    :class="[
                        (itemColors[item.title] || itemColors['Dashboard']).bar,
                        `shadow-[0_0_12px_3px] ${(itemColors[item.title] || itemColors['Dashboard']).barShadow}`,
                    ]"
                />

                <SidebarMenuButton
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                    class="group/nav-btn sidebar-item-shine"
                    :class="[
                        (itemColors[item.title] || itemColors['Dashboard']).hover,
                        isCurrentUrl(item.href) && [
                            `bg-gradient-to-r`,
                            (itemColors[item.title] || itemColors['Dashboard']).bg,
                            'shadow-lg ring-1 ring-white/10',
                        ],
                    ]"
                >
                    <Link
                        :href="item.href"
                        class="flex items-center gap-3 px-2 py-2 rounded-lg transition-all duration-300 group-hover/nav-btn:translate-x-1"
                    >
                        <component
                            :is="item.icon"
                            class="size-[18px] shrink-0 transition-all duration-300"
                            :class="[
                                isCurrentUrl(item.href)
                                    ? [(itemColors[item.title] || itemColors['Dashboard']).iconActive, 'icon-glow']
                                    : 'text-sidebar-foreground/60 group-hover/nav-btn:scale-110',
                            ]"
                        />
                        <span
                            class="text-sm font-medium transition-all duration-300"
                            :class="isCurrentUrl(item.href) ? 'text-white' : 'text-sidebar-foreground/70 group-hover/nav-btn:text-white'"
                        >
                            {{ item.title }}
                        </span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>

<style scoped>
[data-active="true"] svg {
    filter: drop-shadow(0 0 6px currentColor);
}
[data-active="true"] span {
    color: #fff;
}
</style>
