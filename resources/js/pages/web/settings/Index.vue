<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import {
    ImagePlus,
    Save,
    Smartphone,
    TrendingUp,
    Users,
    Clock,
    Trash2,
    RefreshCw,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import adminWeb from '@/routes/admin-web';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage();

const settings = computed<Record<string, string>>(
    () => (page.props.settings || {}) as Record<string, string>,
);

const configuracion = computed<Record<string, string | null>>(
    () => (page.props.configuracion || {}) as Record<string, string | null>,
);

const form = useForm({
    hero_titulo_linea1: '',
    hero_titulo_linea2: '',
    hero_titulo_linea3: '',
    hero_subtitulo: '',
    hero_imagen: '',
    hero_imagen_archivo: null as File | null,
    boton_primario_texto: '',
    boton_secundario_texto: '',
    stats_proyectos: '',
    stats_clientes: '',
    stats_anios: '',
    contacto_whatsapp: '',
    contacto_correo: '',
    contacto_facebook: '',
    contacto_instagram: '',
    contacto_tiktok: '',
    nosotros_texto: '',
    nombre_empresa: '',
    logo_archivo: null as File | null,
});

const sync = ref(false);

watch(
    [settings],
    ([s]) => {
        if (!Object.keys(s).length || sync.value) {
            return;
        }

        form.hero_titulo_linea1 = s.hero_titulo_linea1 ?? '';
        form.hero_titulo_linea2 = s.hero_titulo_linea2 ?? '';
        form.hero_titulo_linea3 = s.hero_titulo_linea3 ?? '';
        form.hero_subtitulo = s.hero_subtitulo ?? '';
        form.hero_imagen = s.hero_imagen ?? '';
        form.boton_primario_texto = s.boton_primario_texto ?? '';
        form.boton_secundario_texto = s.boton_secundario_texto ?? '';
        form.stats_proyectos = s.stats_proyectos ?? '';
        form.stats_clientes = s.stats_clientes ?? '';
        form.stats_anios = s.stats_anios ?? '';
        form.contacto_whatsapp = s.contacto_whatsapp ?? '';
        form.contacto_correo = s.contacto_correo ?? '';
        form.contacto_facebook = s.contacto_facebook ?? '';
        form.contacto_instagram = s.contacto_instagram ?? '';
        form.contacto_tiktok = s.contacto_tiktok ?? '';
        form.nosotros_texto = s.nosotros_texto ?? '';
        sync.value = true;
    },
    { immediate: true, deep: true },
);

const configSynced = ref(false);

watch(
    [configuracion],
    ([c]) => {
        if (!Object.keys(c).length || configSynced.value) {
            return;
        }

        form.nombre_empresa = c.nombre_empresa ?? '';
        currentLogoUrl.value = (c.logo_url as string | null) || null;
        configSynced.value = true;
    },
    { immediate: true, deep: true },
);

const heroPreview = ref<string | null>(null);
const logoPreview = ref<string | null>(null);
const currentLogoUrl = ref<string | null>(null);

function onLogoChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.logo_archivo = file;
        logoPreview.value = URL.createObjectURL(file);
        form.errors.logo_archivo = undefined as never;
    }
}

function clearLogoSelection() {
    form.logo_archivo = null;
    logoPreview.value = null;
}

function removeLogo() {
    if (!window.confirm('¿Eliminar el logotipo actual?')) {
        return;
    }

    router.delete(adminWeb.settings.logo.destroy.url(), {
        preserveScroll: true,
        onSuccess: () => {
            currentLogoUrl.value = null;
            clearLogoSelection();
        },
    });
}

function onHeroChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.hero_imagen_archivo = file;
        heroPreview.value = URL.createObjectURL(file);
    }
}

function save() {
    form.post(adminWeb.settings.update.url(), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Configuración de la Web" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <div class="flex items-center gap-3">
            <div
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-pix/20 to-purple-500/20 ring-1 ring-white/10"
            >
                <svg
                    class="h-5 w-5 text-pix"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M12 20h9" />
                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" />
                </svg>
            </div>
            <Heading
                title="Configuración de la Web"
                description="Edita el hero, estadísticas, contacto y redes sociales de tu página"
                variant="small"
            />
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-5 flex items-center gap-2">
                        <svg
                            class="h-5 w-5 text-pix"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M3 11v8a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-8"
                            />
                            <path d="M7 4h10l3 7H4l3-7z" />
                        </svg>
                        <h3 class="font-semibold text-white">Hero principal</h3>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Imagen de fondo</Label>
                            <div class="flex flex-col gap-4 sm:flex-row">
                                <label
                                    class="flex aspect-video w-full max-w-xs cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-white/10 bg-white/5 transition-colors hover:border-pix/40"
                                >
                                    <img
                                        v-if="
                                            heroPreview ||
                                            (form.hero_imagen && !heroPreview)
                                        "
                                        :src="heroPreview || form.hero_imagen"
                                        class="h-full w-full object-cover"
                                        alt="Hero"
                                    />
                                    <div
                                        v-else
                                        class="flex flex-col items-center gap-2 text-white/40"
                                    >
                                        <ImagePlus class="h-7 w-7" />
                                        <span class="text-xs"
                                            >Subir imagen</span
                                        >
                                    </div>
                                    <input
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="hidden"
                                        @change="onHeroChange"
                                    />
                                </label>
                                <div class="flex-1 space-y-2">
                                    <Label for="hero_imagen"
                                        >URL de la imagen</Label
                                    >
                                    <Input
                                        id="hero_imagen"
                                        v-model="form.hero_imagen"
                                        type="text"
                                        placeholder="https://..."
                                        class="border-white/10 bg-white/5 text-white placeholder:text-white/40 focus:border-pix/50"
                                    />
                                    <p class="text-xs text-white/40">
                                        Pega una URL o sube un archivo; el
                                        archivo tiene prioridad.
                                    </p>
                                    <InputError
                                        :message="form.errors.hero_imagen"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="hero_titulo_linea1"
                                >Título - línea 1</Label
                            >
                            <Input
                                id="hero_titulo_linea1"
                                v-model="form.hero_titulo_linea1"
                                type="text"
                                class="border-white/10 bg-white/5 text-white focus:border-pix/50"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="hero_titulo_linea2"
                                >Título - línea 2</Label
                            >
                            <Input
                                id="hero_titulo_linea2"
                                v-model="form.hero_titulo_linea2"
                                type="text"
                                class="border-white/10 bg-white/5 text-white focus:border-pix/50"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="hero_titulo_linea3"
                                >Título - línea 3</Label
                            >
                            <Input
                                id="hero_titulo_linea3"
                                v-model="form.hero_titulo_linea3"
                                type="text"
                                class="border-white/10 bg-white/5 text-white focus:border-pix/50"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="hero_subtitulo">Subtítulo</Label>
                            <Textarea
                                id="hero_subtitulo"
                                v-model="form.hero_subtitulo"
                                rows="3"
                                class="border-white/10 bg-white/5 text-white focus:border-pix/50"
                            />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="boton_primario_texto"
                                    >Botón principal</Label
                                >
                                <Input
                                    id="boton_primario_texto"
                                    v-model="form.boton_primario_texto"
                                    type="text"
                                    class="border-white/10 bg-white/5 text-white focus:border-pix/50"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="boton_secundario_texto"
                                    >Botón secundario</Label
                                >
                                <Input
                                    id="boton_secundario_texto"
                                    v-model="form.boton_secundario_texto"
                                    type="text"
                                    class="border-white/10 bg-white/5 text-white focus:border-pix/50"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-5 flex items-center gap-2">
                        <svg
                            class="h-5 w-5 text-pix"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="12" cy="12" r="10" />
                            <circle cx="12" cy="12" r="4" />
                            <circle cx="12" cy="12" r="1" />
                        </svg>
                        <h3 class="font-semibold text-white">
                            Identidad de marca
                        </h3>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Logotipo</Label>
                            <div
                                class="flex flex-col gap-4 sm:flex-row sm:items-center"
                            >
                                <label
                                    class="flex h-28 w-28 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-white/10 bg-white/5 transition-colors hover:border-pix/40"
                                >
                                    <img
                                        v-if="logoPreview || currentLogoUrl"
                                        :src="logoPreview || currentLogoUrl!"
                                        class="h-full w-full object-contain p-2"
                                        alt="Logo"
                                    />
                                    <div
                                        v-else
                                        class="flex flex-col items-center gap-2 text-white/40"
                                    >
                                        <ImagePlus class="h-6 w-6" />
                                        <span class="text-xs">Subir logo</span>
                                    </div>
                                    <input
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp,image/svg+xml"
                                        class="hidden"
                                        @change="onLogoChange"
                                    />
                                </label>
                                <div class="flex flex-1 flex-col gap-2">
                                    <p class="text-xs text-white/40">
                                        PNG, JPG, WEBP o SVG. Se muestra en la
                                        barra de navegación y el footer de la
                                        página pública.
                                    </p>
                                    <div
                                        v-if="
                                            form.logo_archivo || currentLogoUrl
                                        "
                                        class="flex flex-wrap gap-2"
                                    >
                                        <Button
                                            v-if="form.logo_archivo"
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            class="border-white/10 text-white hover:bg-white/10"
                                            @click="clearLogoSelection"
                                        >
                                            <RefreshCw class="mr-1 h-3 w-3" />
                                            Cancelar selección
                                        </Button>
                                        <Button
                                            v-if="
                                                currentLogoUrl &&
                                                !form.logo_archivo
                                            "
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            class="border-white/10 text-red-300 hover:bg-white/10"
                                            @click="removeLogo"
                                        >
                                            <Trash2 class="mr-1 h-3 w-3" />
                                            Eliminar logo
                                        </Button>
                                    </div>
                                    <InputError
                                        :message="form.errors.logo_archivo"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <Label for="nombre_empresa"
                                >Nombre de la empresa</Label
                            >
                            <Input
                                id="nombre_empresa"
                                v-model="form.nombre_empresa"
                                type="text"
                                placeholder="Nombre de tu empresa"
                                class="border-white/10 bg-white/5 text-white focus:border-pix/50"
                            />
                            <InputError :message="form.errors.nombre_empresa" />
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-5 flex items-center gap-2">
                        <TrendingUp class="h-5 w-5 text-pix" />
                        <h3 class="font-semibold text-white">Estadísticas</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="space-y-2">
                            <Label for="stats_proyectos">
                                <span class="flex items-center gap-1"
                                    ><Users class="h-3 w-3" /> Proyectos</span
                                >
                            </Label>
                            <Input
                                id="stats_proyectos"
                                v-model="form.stats_proyectos"
                                type="text"
                                class="border-white/10 bg-white/5 text-white focus:border-pix/50"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="stats_clientes">
                                <span class="flex items-center gap-1"
                                    ><Smartphone class="h-3 w-3" />
                                    Clientes</span
                                >
                            </Label>
                            <Input
                                id="stats_clientes"
                                v-model="form.stats_clientes"
                                type="text"
                                class="border-white/10 bg-white/5 text-white focus:border-pix/50"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="stats_anios">
                                <span class="flex items-center gap-1"
                                    ><Clock class="h-3 w-3" /> Años</span
                                >
                            </Label>
                            <Input
                                id="stats_anios"
                                v-model="form.stats_anios"
                                type="text"
                                class="border-white/10 bg-white/5 text-white focus:border-pix/50"
                            />
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-5 flex items-center gap-2">
                        <svg
                            class="h-5 w-5 text-pix"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M17 3a2.8 2.8 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"
                            />
                        </svg>
                        <h3 class="font-semibold text-white">Nosotros</h3>
                    </div>
                    <div class="space-y-2">
                        <Label for="nosotros_texto">Texto principal</Label>
                        <Textarea
                            id="nosotros_texto"
                            v-model="form.nosotros_texto"
                            rows="4"
                            class="border-white/10 bg-white/5 text-white focus:border-pix/50"
                        />
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-5 flex items-center gap-2">
                        <svg
                            class="h-5 w-5 text-pix"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.8a2 2 0 0 1-.5 2.1L8 10a16 16 0 0 0 6 6l1.4-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.8.7a2 2 0 0 1 1.7 2Z"
                            />
                        </svg>
                        <h3 class="font-semibold text-white">Contacto</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label for="contacto_whatsapp">WhatsApp</Label>
                            <Input
                                id="contacto_whatsapp"
                                v-model="form.contacto_whatsapp"
                                type="text"
                                class="border-white/10 bg-white/5 text-white focus:border-pix/50"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="contacto_correo">Correo</Label>
                            <Input
                                id="contacto_correo"
                                v-model="form.contacto_correo"
                                type="text"
                                class="border-white/10 bg-white/5 text-white focus:border-pix/50"
                            />
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-5 flex items-center gap-2">
                        <svg
                            class="h-5 w-5 text-pix"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"
                            />
                        </svg>
                        <h3 class="font-semibold text-white">Redes sociales</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label for="contacto_facebook">Facebook</Label>
                            <Input
                                id="contacto_facebook"
                                v-model="form.contacto_facebook"
                                type="text"
                                placeholder="https://facebook.com/..."
                                class="border-white/10 bg-white/5 text-white placeholder:text-white/40 focus:border-pix/50"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="contacto_instagram">Instagram</Label>
                            <Input
                                id="contacto_instagram"
                                v-model="form.contacto_instagram"
                                type="text"
                                placeholder="https://instagram.com/..."
                                class="border-white/10 bg-white/5 text-white placeholder:text-white/40 focus:border-pix/50"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="contacto_tiktok">TikTok</Label>
                            <Input
                                id="contacto_tiktok"
                                v-model="form.contacto_tiktok"
                                type="text"
                                placeholder="https://tiktok.com/@..."
                                class="border-white/10 bg-white/5 text-white placeholder:text-white/40 focus:border-pix/50"
                            />
                        </div>
                    </div>
                </div>

                <Button
                    class="btn-gradient-blue btn-shimmer w-full"
                    :disabled="form.processing"
                    @click="save"
                >
                    <Save class="mr-2 h-4 w-4" />
                    {{
                        form.processing
                            ? 'Guardando...'
                            : 'Guardar configuración'
                    }}
                </Button>
            </div>
        </div>
    </div>
</template>
