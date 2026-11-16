<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Plus,
    Search,
    Pencil,
    Eye,
    EyeOff,
    CalendarDays,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { usePermissions } from '@/composables/usePermissions';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { formatDate } from '@/lib/money';
import portafolio from '@/routes/portafolio';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage<{
    portafolios: Array<{
        id: number;
        titulo: string;
        client: { id: number; name: string } | null;
        servicio: { id: number; nombre: string } | null;
        categoria: string | null;
        descripcion: string | null;
        imagen_principal: string | null;
        fecha: string | null;
        publicar: boolean;
        images: Array<{ id: number; url: string }>;
        created_at: string;
    }>;
    categorias: string[];
    clients: Array<{ id: number; name: string }>;
    filters: {
        search: string;
        categoria: string;
        cliente: string;
        fecha: string;
    };
}>();

const { can } = usePermissions();

const items = computed(() => page.props.portafolios ?? []);
const categorias = computed(() => page.props.categorias ?? []);

const search = ref(page.props.filters?.search ?? '');
const categoria = ref(page.props.filters?.categoria ?? '');
const cliente = ref(page.props.filters?.cliente ?? '');
const fecha = ref(page.props.filters?.fecha ?? '');

const kpis = computed(() => ({
    total: items.value.length,
    publicados: items.value.filter((p) => p.publicar).length,
    ocultos: items.value.length - items.value.filter((p) => p.publicar).length,
}));

let searchTimer: ReturnType<typeof setTimeout> | null = null;
function applyFilters(preserveSearch = false) {
    if (searchTimer) {
        clearTimeout(searchTimer);
    }

    searchTimer = setTimeout(
        () => {
            router.get(
                portafolio.index().url,
                {
                    search: search.value || undefined,
                    categoria: categoria.value || undefined,
                    cliente: cliente.value || undefined,
                    fecha: fecha.value || undefined,
                },
                { preserveState: true, preserveScroll: true, replace: true },
            );
        },
        preserveSearch ? 350 : 0,
    );
}

watch(search, () => applyFilters(true));
watch(categoria, () => applyFilters());
watch(cliente, () => applyFilters());
watch(fecha, () => applyFilters());
</script>

<template>
    <Head title="Portafolio" />

    <div class="animate-fade-in space-y-6 p-4 md:p-6 lg:p-8">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading
                title="Diseños"
                description="Portafolio de trabajos realizados"
            />
            <Button
                v-if="can('diseños.gestionar')"
                as-child
                class="btn-gradient-blue"
            >
                <Link :href="portafolio.create().url">
                    <Plus class="mr-2 h-4 w-4" />
                    Nuevo Trabajo
                </Link>
            </Button>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div
                class="kpi-blue kpi-card glow-blue animate-slide-up rounded-2xl p-5 shadow-lg"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Total
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">{{ kpis.total }}</p>
            </div>
            <div
                class="kpi-green kpi-card glow-green animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.1s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Publicados
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ kpis.publicados }}
                </p>
            </div>
            <div
                class="kpi-orange kpi-card glow-orange animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.2s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Ocultos
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ kpis.ocultos }}
                </p>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <div class="relative flex-1">
                    <Search
                        class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink/50"
                    />
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Buscar por título, descripción o cliente..."
                        class="glass pl-9"
                    />
                </div>
                <div class="flex flex-wrap gap-2">
                    <Select v-model="categoria">
                        <SelectTrigger class="glass w-40">
                            <SelectValue placeholder="Categoría" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Todas</SelectItem>
                            <SelectItem
                                v-for="c in categorias"
                                :key="c"
                                :value="c"
                                >{{ c }}</SelectItem
                            >
                        </SelectContent>
                    </Select>
                    <Select v-model="cliente">
                        <SelectTrigger class="glass w-44">
                            <SelectValue placeholder="Cliente" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Todos los clientes</SelectItem>
                            <SelectItem
                                v-for="c in page.props.clients"
                                :key="c.id"
                                :value="String(c.id)"
                                >{{ c.name }}</SelectItem
                            >
                        </SelectContent>
                    </Select>
                    <Input v-model="fecha" type="date" class="glass w-40" />
                </div>
            </div>
        </div>

        <div
            v-if="items.length === 0"
            class="glass-card rounded-2xl p-10 text-center text-ink/50"
        >
            No se encontraron trabajos en el portafolio
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <div
                v-for="item in items"
                :key="item.id"
                class="glass-card group overflow-hidden rounded-2xl"
            >
                <Link
                    :href="portafolio.edit({ portafolio: item.id }).url"
                    class="block"
                >
                    <div
                        class="relative aspect-square w-full overflow-hidden bg-ink/10"
                    >
                        <img
                            v-if="item.imagen_principal"
                            :src="item.imagen_principal"
                            :alt="item.titulo"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            loading="lazy"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center"
                        >
                            <span class="text-4xl text-ink/25">{{
                                item.titulo.charAt(0).toUpperCase()
                            }}</span>
                        </div>
                        <span
                            v-if="!item.publicar"
                            class="absolute top-2 right-2 inline-flex items-center gap-1 rounded-full bg-orange-100 px-2.5 py-1 text-xs font-semibold text-orange-600 shadow"
                        >
                            <EyeOff class="h-3 w-3" /> Oculto
                        </span>
                    </div>
                </Link>
                <div class="p-4">
                    <p class="font-bold text-ink">{{ item.titulo }}</p>
                    <p class="text-sm text-ink/60">
                        {{ item.client?.name || 'Sin cliente'
                        }}{{
                            item.servicio ? ` · ${item.servicio.nombre}` : ''
                        }}
                    </p>
                    <div class="mt-2 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span
                                v-if="item.categoria"
                                class="rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-semibold text-purple-600"
                                >{{ item.categoria }}</span
                            >
                            <span
                                v-if="item.fecha"
                                class="flex items-center gap-1 text-xs text-ink/50"
                            >
                                <CalendarDays class="h-3 w-3" />
                                {{ formatDate(item.fecha) }}
                            </span>
                        </div>
                        <div class="flex gap-1">
                            <Button
                                v-if="item.publicar"
                                size="sm"
                                variant="ghost"
                                class="btn-gradient-ghost"
                                title="Ocultar"
                                @click.stop="
                                    router.post(
                                        portafolio.publicar.url({
                                            portafolio: item.id,
                                        }),
                                    )
                                "
                            >
                                <EyeOff class="h-4 w-4" />
                            </Button>
                            <Button
                                v-if="!item.publicar"
                                size="sm"
                                variant="ghost"
                                class="btn-gradient-ghost glow-green"
                                title="Publicar"
                                @click.stop="
                                    router.post(
                                        portafolio.publicar.url({
                                            portafolio: item.id,
                                        }),
                                    )
                                "
                            >
                                <Eye class="h-4 w-4" />
                            </Button>
                            <Button
                                size="sm"
                                variant="ghost"
                                class="btn-gradient-ghost"
                                as-child
                            >
                                <Link
                                    :href="
                                        portafolio.edit({ portafolio: item.id })
                                            .url
                                    "
                                >
                                    <Pencil class="h-4 w-4" />
                                </Link>
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
