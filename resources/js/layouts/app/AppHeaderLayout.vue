<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import GlobalBackground from '@/components/GlobalBackground.vue';
import { Toaster } from '@/components/ui/sonner';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="header">
        <GlobalBackground />

        <AppHeader :breadcrumbs="breadcrumbs" />
        <AppContent variant="header" class="relative z-10">
            <slot />
        </AppContent>
        <Toaster />
    </AppShell>
</template>
