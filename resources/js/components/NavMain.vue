<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    groups: { label: string; items: NavItem[] }[];
}>();

const { isCurrentUrl } = useCurrentUrl();

function hasChildren(item: NavItem): boolean {
    return Array.isArray(item.items) && item.items.length > 0;
}

function isAnyChildActive(item: NavItem): boolean {
    return (
        Array.isArray(item.items) &&
        item.items.some((child) => isCurrentUrl(child.href))
    );
}
</script>

<template>
    <SidebarGroup v-for="group in groups" :key="group.label" class="px-3 py-0">
        <SidebarGroupLabel
            class="px-2 text-[11px] font-bold tracking-[0.16em] text-white/45 uppercase"
        >
            {{ group.label }}
        </SidebarGroupLabel>

        <SidebarMenu>
            <template v-for="item in group.items" :key="item.title">
                <!-- Expandable group with children -->
                <Collapsible
                    v-if="hasChildren(item)"
                    as-child
                    :default-open="isAnyChildActive(item)"
                    class="group/collapsible"
                >
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton
                                :tooltip="item.title"
                                class="group/nav-btn rounded-xl transition-all duration-300 hover:bg-white/10"
                            >
                                <component
                                    :is="item.icon"
                                    class="size-[18px] shrink-0 text-white/60 transition-all duration-300 group-hover/nav-btn:scale-110 group-hover/nav-btn:text-pix-2"
                                />
                                <span
                                    class="flex-1 text-left text-sm font-semibold text-white/70 transition-all duration-300 group-hover/nav-btn:text-white"
                                >
                                    {{ item.title }}
                                </span>
                                <ChevronRight
                                    class="ml-auto size-4 shrink-0 text-white/40 transition-transform duration-300 group-data-[state=open]/collapsible:rotate-90"
                                />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub class="m-0 py-1">
                                <SidebarMenuSubItem
                                    v-for="child in item.items"
                                    :key="child.title"
                                >
                                    <SidebarMenuSubButton
                                        as-child
                                        :is-active="isCurrentUrl(child.href)"
                                        class="rounded-lg py-2 transition-colors duration-300"
                                        :class="
                                            isCurrentUrl(child.href)
                                                ? 'bg-white/15! ring-1 ring-white/25!'
                                                : 'hover:bg-white/10'
                                        "
                                    >
                                        <Link :href="child.href">
                                            <component
                                                :is="child.icon"
                                                class="size-4 shrink-0"
                                                :class="
                                                    isCurrentUrl(child.href)
                                                        ? 'text-pix-2'
                                                        : 'text-white/60'
                                                "
                                            />
                                            <span
                                                class="text-sm font-medium"
                                                :class="
                                                    isCurrentUrl(child.href)
                                                        ? 'text-white'
                                                        : 'text-white/70'
                                                "
                                            >
                                                {{ child.title }}
                                            </span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>

                <!-- Flat link -->
                <SidebarMenuItem v-else class="group/nav-item relative">
                    <div
                        v-if="isCurrentUrl(item.href)"
                        class="absolute top-1/2 left-0 z-10 h-6 w-[3px] -translate-y-1/2 rounded-r-full bg-pix-2 shadow-[0_0_12px_rgba(255,79,145,0.7)] transition-all duration-300"
                    />

                    <SidebarMenuButton
                        as-child
                        :is-active="isCurrentUrl(item.href)"
                        :tooltip="item.title"
                        class="group/nav-btn rounded-xl transition-all duration-300"
                        :class="[
                            isCurrentUrl(item.href)
                                ? 'bg-white/15! shadow-[0_8px_24px_-12px_rgba(0,0,0,0.5)] ring-1 ring-white/25!'
                                : 'hover:bg-white/10',
                        ]"
                    >
                        <Link
                            :href="item.href"
                            class="flex items-center gap-3 rounded-lg px-2 py-2 transition-all duration-300 group-hover/nav-btn:translate-x-1"
                        >
                            <component
                                :is="item.icon"
                                class="size-[18px] shrink-0 transition-all duration-300"
                                :class="[
                                    isCurrentUrl(item.href)
                                        ? 'text-pix-2 drop-shadow-[0_0_6px_rgba(255,79,145,0.45)]'
                                        : 'text-white/60 group-hover/nav-btn:scale-110 group-hover/nav-btn:text-pix-2',
                                ]"
                            />
                            <span
                                class="text-sm font-semibold transition-all duration-300"
                                :class="
                                    isCurrentUrl(item.href)
                                        ? 'text-white'
                                        : 'text-white/70 group-hover/nav-btn:text-white'
                                "
                            >
                                {{ item.title }}
                            </span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
