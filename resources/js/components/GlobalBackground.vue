<script setup lang="ts">
import { onMounted, ref } from 'vue';

const reduced = ref(false);

onMounted(() => {
    reduced.value = window.matchMedia(
        '(prefers-reduced-motion: reduce)',
    ).matches;
});
</script>

<template>
    <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
        <!-- Warm beige/peach base -->
        <div
            class="absolute inset-0 bg-gradient-to-br from-[#FBEFE4] via-[#F7EDF0] to-[#F3EDF7]"
        />

        <!-- Soft peach tint wash -->
        <div
            class="absolute inset-0 opacity-40"
            style="
                background: radial-gradient(
                    ellipse at 20% 0%,
                    rgba(255, 200, 214, 0.55) 0%,
                    transparent 55%
                );
            "
        />

        <!-- Soft pastel orbs -->
        <div
            class="absolute -top-56 -left-40 h-[900px] w-[900px] rounded-full opacity-70 blur-3xl"
            :class="reduced ? '' : 'animate-pulse'"
            style="
                background: radial-gradient(
                    circle,
                    #ffd6e5 0%,
                    transparent 70%
                );
                animation-duration: 9s;
            "
        />
        <div
            class="absolute -right-40 -bottom-64 h-[1000px] w-[1000px] rounded-full opacity-70 blur-3xl"
            :class="reduced ? '' : 'animate-pulse'"
            style="
                background: radial-gradient(
                    circle,
                    #dccbff 0%,
                    transparent 70%
                );
                animation-duration: 11s;
                animation-delay: 1.5s;
            "
        />
        <div
            class="absolute top-1/4 left-1/3 h-[900px] w-[900px] -translate-x-1/2 rounded-full opacity-60 blur-3xl"
            :class="reduced ? '' : 'animate-pulse'"
            style="
                background: radial-gradient(
                    circle,
                    #ede5ff 0%,
                    transparent 70%
                );
                animation-duration: 10s;
                animation-delay: 2.5s;
            "
        />
        <div
            class="absolute right-1/4 bottom-1/3 h-[900px] w-[900px] rounded-full opacity-65 blur-3xl"
            :class="reduced ? '' : 'animate-pulse'"
            style="
                background: radial-gradient(
                    circle,
                    #ffd8c2 0%,
                    transparent 70%
                );
                animation-duration: 12s;
                animation-delay: 0.8s;
            "
        />
        <div
            class="absolute top-1/2 left-3/4 h-[800px] w-[800px] -translate-y-1/2 rounded-full opacity-60 blur-3xl"
            :class="reduced ? '' : 'animate-pulse'"
            style="
                background: radial-gradient(
                    circle,
                    #d8f3e8 0%,
                    transparent 70%
                );
                animation-duration: 8s;
                animation-delay: 3.5s;
            "
        />
        <div
            class="absolute top-3/4 left-1/5 h-[700px] w-[700px] rounded-full opacity-40 blur-3xl"
            :class="reduced ? '' : 'animate-pulse'"
            style="
                background: radial-gradient(
                    circle,
                    #fff7fa 0%,
                    transparent 70%
                );
                animation-duration: 13s;
                animation-delay: 4s;
            "
        />

        <!-- Subtle grid pattern -->
        <div
            class="absolute inset-0 opacity-[0.035]"
            style="
                background-image:
                    linear-gradient(
                        rgba(23, 26, 43, 0.06) 1px,
                        transparent 1px
                    ),
                    linear-gradient(
                        90deg,
                        rgba(23, 26, 43, 0.06) 1px,
                        transparent 1px
                    );
                background-size: 60px 60px;
            "
        />

        <!-- Floating pastel dots -->
        <div
            class="absolute top-[15%] left-[30%] h-[5px] w-[5px] rounded-full"
            :class="reduced ? '' : 'animate-float'"
            style="background: #f43f7a; opacity: 0.35"
        />
        <div
            class="absolute right-[25%] bottom-[25%] h-[5px] w-[5px] rounded-full"
            :class="reduced ? '' : 'animate-float'"
            style="background: #dccbff; opacity: 0.55; animation-delay: 1s"
        />
        <div
            class="absolute top-[55%] left-[12%] h-[4px] w-[4px] rounded-full"
            :class="reduced ? '' : 'animate-float'"
            style="background: #ffd8c2; opacity: 0.7; animation-delay: 0.5s"
        />
        <div
            class="absolute top-[38%] right-[16%] h-[4px] w-[4px] rounded-full"
            :class="reduced ? '' : 'animate-float'"
            style="background: #d8f3e8; opacity: 0.9; animation-delay: 2s"
        />
    </div>
</template>
