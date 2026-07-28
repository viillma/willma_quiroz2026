<template>
    <div class="fixed inset-0 z-0 pointer-events-none">
        <!-- DEBUG: remove this after testing -->
        <div class="fixed top-5 left-5 z-[9999] bg-red-600 px-4 py-2 text-sm font-bold text-white shadow-lg rounded-lg">
            FONDO ACTIVO
        </div>

        <!-- Base dark color -->
        <div class="absolute inset-0 bg-[#050916]" />

        <!-- Massive animated gradient blobs with blur-3xl -->
        <div
            class="absolute -top-60 -left-40 h-[1100px] w-[1100px] rounded-full opacity-50 blur-3xl animate-pulse"
            style="background: radial-gradient(circle, #7C3AED 0%, transparent 70%); animation-duration: 7s;"
        />
        <div
            class="absolute -bottom-60 -right-40 h-[1200px] w-[1200px] rounded-full opacity-55 blur-3xl animate-pulse"
            style="background: radial-gradient(circle, #3B82F6 0%, transparent 70%); animation-duration: 9s; animation-delay: 1.5s;"
        />
        <div
            class="absolute top-1/4 left-1/3 h-[1000px] w-[1000px] -translate-x-1/2 rounded-full opacity-50 blur-3xl animate-pulse"
            style="background: radial-gradient(circle, #EC4899 0%, transparent 70%); animation-duration: 8s; animation-delay: 2.5s;"
        />
        <div
            class="absolute bottom-1/3 right-1/4 h-[1000px] w-[1000px] rounded-full opacity-50 blur-3xl animate-pulse"
            style="background: radial-gradient(circle, #06B6D4 0%, transparent 70%); animation-duration: 10s; animation-delay: 0.8s;"
        />
        <div
            class="absolute top-1/2 left-3/4 h-[900px] w-[900px] -translate-y-1/2 rounded-full opacity-45 blur-3xl animate-pulse"
            style="background: radial-gradient(circle, #7C3AED 0%, transparent 70%); animation-duration: 6s; animation-delay: 3.5s;"
        />
        <div
            class="absolute top-3/4 left-1/5 h-[800px] w-[800px] rounded-full opacity-40 blur-3xl animate-pulse"
            style="background: radial-gradient(circle, #3B82F6 0%, transparent 70%); animation-duration: 11s; animation-delay: 4s;"
        />

        <!-- Subtle blur to blend the blobs -->
        <div class="absolute inset-0 backdrop-blur-[100px]" />

        <!-- Subtle grid pattern -->
        <div
            class="absolute inset-0 opacity-[0.04]"
            style="background-image: linear-gradient(rgba(255,255,255,0.08) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.08) 1px, transparent 1px); background-size: 60px 60px;"
        />

        <!-- Floating glow dots -->
        <div
            class="absolute top-[15%] left-[30%] h-[4px] w-[4px] rounded-full"
            style="background: #7C3AED; box-shadow: 0 0 12px #7C3AED, 0 0 40px rgba(124, 58, 237, 0.6); animation: float 4s ease-in-out infinite;"
        />
        <div
            class="absolute bottom-[25%] right-[25%] h-[4px] w-[4px] rounded-full"
            style="background: #EC4899; box-shadow: 0 0 12px #EC4899, 0 0 40px rgba(236, 72, 153, 0.6); animation: float 5s ease-in-out infinite 1s;"
        />

        <!-- Neon lines -->
        <div
            class="absolute top-[15%] left-0 right-0 h-[1px] opacity-[0.15]"
            style="background: linear-gradient(90deg, transparent 0%, #7C3AED 20%, #3B82F6 50%, #7C3AED 80%, transparent 100%); box-shadow: 0 0 20px rgba(124, 58, 237, 0.3);"
        />
        <div
            class="absolute bottom-[25%] left-0 right-0 h-[1px] opacity-[0.12]"
            style="background: linear-gradient(90deg, transparent 0%, #EC4899 30%, #06B6D4 70%, transparent 100%); box-shadow: 0 0 20px rgba(236, 72, 153, 0.3);"
        />
        <div
            class="absolute top-[45%] left-0 right-0 h-[1px] opacity-[0.10]"
            style="background: linear-gradient(90deg, transparent 0%, #3B82F6 40%, #06B6D4 60%, transparent 100%); box-shadow: 0 0 15px rgba(59, 130, 246, 0.2);"
        />
    </div>
</template>
