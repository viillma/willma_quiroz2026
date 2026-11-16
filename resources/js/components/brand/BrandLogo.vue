<script setup lang="ts">
import { computed } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

type Props = {
    logo?: string | null;
    name?: string;
    mode?: 'full' | 'compact' | 'icon';
    iconClass?: string;
    invert?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    logo: null,
    name: 'CREAPIX',
    mode: 'full',
    iconClass: 'h-10 w-10',
    invert: false,
});

const onlyIcon = computed(() => props.mode === 'icon');

const wordClass = computed(() => ({
    'font-display font-extrabold tracking-tight': true,
    'text-xl': props.mode === 'full',
    'text-lg': props.mode === 'compact',
    'text-ink': true,
    'text-white': props.invert,
}));
</script>

<template>
    <span
        v-if="logo"
        class="inline-flex items-center gap-3"
        :class="{ 'rounded-2xl bg-white/80 p-2 backdrop-blur': !onlyIcon }"
    >
        <img
            :src="logo"
            :alt="name"
            class="max-w-[150px] object-contain"
            :class="iconClass"
        />
    </span>

    <span v-else class="inline-flex items-center gap-2.5">
        <AppLogoIcon :class="iconClass" />
        <span v-if="!onlyIcon" class="group grid text-left leading-none">
            <span :class="wordClass">
                {{ name }}<span class="text-pix">.</span>
            </span>
            <span
                class="mt-1 text-[10px] font-semibold tracking-[0.18em] uppercase"
                :class="invert ? 'text-white/60' : 'text-ink/45'"
            >
                Estudio creativo
            </span>
        </span>
    </span>
</template>
