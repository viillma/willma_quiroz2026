<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

import { useForm } from '@inertiajs/vue3';
import { Mail, MapPin, MessageCircle, Phone, Send } from 'lucide-vue-next';
import { ref } from 'vue';
import BrandLogo from '@/components/brand/BrandLogo.vue';
import { enviar as contactoEnviar } from '@/routes/public/contacto';

type Site = {
    nombre: string;
    logo: string | null;
    whatsapp: string;
    email: string;
    telefono: string;
    direccion: string;
    ciudad: string;
    nombre_pais: string;
    social: Record<string, string>;
};

defineProps<{
    site: Site;
}>();

const form = useForm({
    nombre: '',
    telefono: '',
    correo: '',
    asunto: '',
    mensaje: '',
});

const enviado = ref(false);

function submit() {
    form.post(contactoEnviar().url, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            enviado.value = true;
            setTimeout(() => (enviado.value = false), 5000);
        },
    });
}
</script>

<template>
    <Head title="Contacto" />

    <div class="min-h-screen bg-cream font-sans text-ink antialiased">
        <header
            class="fixed inset-x-0 top-0 z-50 border-b border-ink/5 bg-cream/80 backdrop-blur-md"
        >
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4"
            >
                <Link href="/" class="flex items-center gap-3">
                    <BrandLogo
                        :logo="site.logo"
                        :name="site.nombre"
                        mode="compact"
                    />
                </Link>
                <nav class="hidden items-center gap-7 md:flex">
                    <Link
                        href="/"
                        class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix"
                        >Inicio</Link
                    >
                    <Link
                        href="/servicios"
                        class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix"
                        >Servicios</Link
                    >
                    <Link
                        href="/catalogo"
                        class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix"
                        >Catálogo</Link
                    >
                    <Link
                        href="/portafolio"
                        class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix"
                        >Portafolio</Link
                    >
                    <Link
                        href="/nosotros"
                        class="text-sm font-semibold text-ink/70 transition-colors hover:text-pix"
                        >Nosotros</Link
                    >
                </nav>
            </div>
        </header>

        <section class="relative overflow-hidden pt-32">
            <div
                class="pointer-events-none absolute top-16 -left-24 h-72 w-72 rounded-full bg-peach/50 blur-3xl"
            ></div>
            <div
                class="pointer-events-none absolute top-32 -right-24 h-72 w-72 rounded-full bg-blush/50 blur-3xl"
            ></div>

            <div class="relative mx-auto max-w-7xl px-5 md:px-8">
                <p
                    class="text-xs font-bold tracking-[0.18em] text-pix uppercase"
                >
                    Contacto
                </p>
                <h1
                    class="mt-3 max-w-2xl font-display text-4xl leading-tight font-extrabold tracking-tight md:text-5xl"
                >
                    Hablemos de tu próximo proyecto
                </h1>
                <p class="mt-4 max-w-xl text-ink/60">
                    Completa el formulario o escríbenos directamente por
                    WhatsApp. Respondemos a la brevedad.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-14 md:px-8">
            <div class="grid gap-10 lg:grid-cols-5">
                <div class="space-y-5 lg:col-span-2">
                    <div
                        class="rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
                    >
                        <div class="flex items-center gap-4">
                            <span
                                class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blush/60 text-pix"
                            >
                                <MessageCircle class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-sm font-bold">WhatsApp</p>
                                <a
                                    :href="`https://wa.me/${site.whatsapp}`"
                                    target="_blank"
                                    rel="noopener"
                                    class="text-sm text-ink/55 hover:text-pix"
                                >
                                    {{ site.whatsapp }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="site.email"
                        class="rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
                    >
                        <div class="flex items-center gap-4">
                            <span
                                class="flex h-11 w-11 items-center justify-center rounded-2xl bg-lilac/50 text-[#7b5ee0]"
                            >
                                <Mail class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-sm font-bold">Correo</p>
                                <p class="text-sm text-ink/55">
                                    {{ site.email }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="site.telefono"
                        class="rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
                    >
                        <div class="flex items-center gap-4">
                            <span
                                class="flex h-11 w-11 items-center justify-center rounded-2xl bg-mint/60 text-[#17805a]"
                            >
                                <Phone class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-sm font-bold">Teléfono</p>
                                <p class="text-sm text-ink/55">
                                    {{ site.telefono }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="site.direccion"
                        class="rounded-3xl border border-ink/5 bg-white p-6 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.14)]"
                    >
                        <div class="flex items-center gap-4">
                            <span
                                class="flex h-11 w-11 items-center justify-center rounded-2xl bg-peach/60 text-[#cf6c0b]"
                            >
                                <MapPin class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-sm font-bold">Dirección</p>
                                <p class="text-sm text-ink/55">
                                    {{ site.direccion
                                    }}{{
                                        site.ciudad ? `, ${site.ciudad}` : ''
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <form
                        @submit.prevent="submit"
                        class="rounded-3xl border border-ink/5 bg-white p-8 shadow-[0_14px_40px_-18px_rgba(23,26,43,0.16)]"
                    >
                        <div
                            v-if="enviado"
                            class="mb-6 rounded-2xl bg-green-50 p-4 text-sm font-semibold text-green-700"
                        >
                            ¡Gracias! Tu mensaje fue enviado correctamente. Te
                            responderemos a la brevedad.
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-bold text-ink/70"
                                    >Nombre *</label
                                >
                                <input
                                    v-model="form.nombre"
                                    type="text"
                                    required
                                    placeholder="Tu nombre"
                                    class="w-full rounded-2xl border border-ink/10 bg-warm px-4 py-3 text-sm transition-colors outline-none placeholder:text-ink/30 focus:border-pix"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-bold text-ink/70"
                                    >WhatsApp / Teléfono</label
                                >
                                <input
                                    v-model="form.telefono"
                                    type="text"
                                    placeholder="Ej: 64 904 467"
                                    class="w-full rounded-2xl border border-ink/10 bg-warm px-4 py-3 text-sm transition-colors outline-none placeholder:text-ink/30 focus:border-pix"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-bold text-ink/70"
                                    >Correo</label
                                >
                                <input
                                    v-model="form.correo"
                                    type="email"
                                    placeholder="tucorreo@ejemplo.com"
                                    class="w-full rounded-2xl border border-ink/10 bg-warm px-4 py-3 text-sm transition-colors outline-none placeholder:text-ink/30 focus:border-pix"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-bold text-ink/70"
                                    >Asunto</label
                                >
                                <input
                                    v-model="form.asunto"
                                    type="text"
                                    placeholder="¿En qué podemos ayudarte?"
                                    class="w-full rounded-2xl border border-ink/10 bg-warm px-4 py-3 text-sm transition-colors outline-none placeholder:text-ink/30 focus:border-pix"
                                />
                            </div>
                        </div>

                        <div class="mt-5">
                            <label
                                class="mb-2 block text-sm font-bold text-ink/70"
                                >Mensaje *</label
                            >
                            <textarea
                                v-model="form.mensaje"
                                required
                                rows="5"
                                placeholder="Cuéntanos sobre tu proyecto..."
                                class="w-full resize-none rounded-2xl border border-ink/10 bg-warm px-4 py-3 text-sm transition-colors outline-none placeholder:text-ink/30 focus:border-pix"
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="mt-6 inline-flex items-center gap-2 rounded-full bg-pix px-8 py-3.5 text-sm font-bold text-white shadow-xl shadow-pix/25 transition-transform hover:-translate-y-0.5 disabled:opacity-50"
                        >
                            <Send class="h-4 w-4" />
                            {{
                                form.processing
                                    ? 'Enviando...'
                                    : 'Enviar mensaje'
                            }}
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</template>
