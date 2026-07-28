<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 40 40"
        :class="className"
        v-bind="$attrs"
        fill="none"
    >
        <defs>
            <linearGradient id="logo-grad" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#2563EB" />
                <stop offset="50%" stop-color="#7C3AED" />
                <stop offset="100%" stop-color="#EC4899" />
            </linearGradient>
        </defs>
        <rect width="40" height="40" rx="10" fill="url(#logo-grad)" />
        <path
            d="M14 12C14 10.8954 14.8954 10 16 10H24C25.1046 10 26 10.8954 26 12V14C26 15.1046 25.1046 16 24 16H16C14.8954 16 14 15.1046 14 14V12Z"
            fill="white"
            opacity="0.9"
        />
        <circle cx="20" cy="22" r="8" fill="white" opacity="0.9" />
        <path
            d="M20 16L22.5 19.5H17.5L20 16ZM20 28L17.5 24.5H22.5L20 28Z"
            fill="#7C3AED"
            opacity="0.8"
        />
        <circle cx="17" cy="22" r="1.5" fill="#2563EB" />
        <circle cx="23" cy="22" r="1.5" fill="#EC4899" />
    </svg>
</template>
