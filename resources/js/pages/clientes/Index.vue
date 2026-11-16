<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Search, Eye, Power } from 'lucide-vue-next';
import { computed, ref } from 'vue';
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
import clientes from '@/routes/clientes';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage<{
    clients: Array<{
        id: number;
        code: string | null;
        name: string;
        tipo_cliente: string | null;
        ci_nit: string | null;
        phone: string | null;
        whatsapp: string | null;
        email: string | null;
        direccion: string | null;
        company: string | null;
        activo: boolean;
        orders_count: number;
        cotizaciones_count: number;
        created_at: string | null;
    }>;
}>();

const { can } = usePermissions();

const search = ref('');
const tipoFilter = ref('todos');
const estadoFilter = ref('todos');

const clients_data = computed(() => page.props.clients ?? []);

const filteredClients = computed(() => {
    const q = search.value.toLowerCase();

    return clients_data.value.filter((c) => {
        if (
            tipoFilter.value !== 'todos' &&
            c.tipo_cliente !== tipoFilter.value
        ) {
            return false;
        }

        if (estadoFilter.value === 'activos' && !c.activo) {
            return false;
        }

        if (estadoFilter.value === 'inactivos' && c.activo) {
            return false;
        }

        if (!q) {
            return true;
        }

        return (
            c.name.toLowerCase().includes(q) ||
            (c.phone && c.phone.toLowerCase().includes(q)) ||
            (c.email && c.email.toLowerCase().includes(q)) ||
            (c.whatsapp && c.whatsapp.toLowerCase().includes(q)) ||
            (c.code && c.code.toLowerCase().includes(q))
        );
    });
});

function toggle(client: { id: number; activo: boolean }) {
    router.post(
        clientes.toggle.url({ cliente: client.id }),
        {},
        { preserveScroll: true },
    );
}

function remove(client: { id: number; name: string }) {
    if (window.confirm(`¿Eliminar al cliente "${client.name}"?`)) {
        router.delete(clientes.destroy.url({ cliente: client.id }), {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <Head title="Clientes" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500/20 to-pink-500/20"
                >
                    <svg
                        class="h-5 w-5 text-purple-500"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <Heading
                    title="Clientes"
                    description="Gestiona los clientes de la empresa"
                />
            </div>
            <Button
                v-if="can('clientes.gestionar')"
                as-child
                class="btn-gradient-blue btn-shimmer w-full sm:w-auto"
            >
                <Link :href="clientes.create().url">
                    <Plus class="mr-2 h-4 w-4" />
                    Nuevo Cliente
                </Link>
            </Button>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div
                class="kpi-blue kpi-card glow-blue animate-slide-up rounded-2xl p-5 shadow-lg"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Total clientes
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ clients_data.length }}
                </p>
            </div>
            <div
                class="kpi-green kpi-card glow-green animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.1s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Activos
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ clients_data.filter((c) => c.activo).length }}
                </p>
            </div>
            <div
                class="kpi-purple kpi-card animate-slide-up rounded-2xl p-5 shadow-lg"
                style="animation-delay: 0.2s"
            >
                <p
                    class="text-xs font-medium tracking-wider text-ink/70 uppercase"
                >
                    Empresas
                </p>
                <p class="mt-1 text-2xl font-bold text-ink">
                    {{ clients_data.filter((c) => c.company).length }}
                </p>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center">
                <div class="relative flex-1">
                    <Search
                        class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink/50"
                    />
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Buscar por nombre, teléfono, código..."
                        class="glass pl-9"
                    />
                </div>
                <div class="flex flex-wrap gap-2">
                    <Select v-model="tipoFilter">
                        <SelectTrigger class="glass w-40">
                            <SelectValue placeholder="Tipo" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="todos"
                                >Todos los tipos</SelectItem
                            >
                            <SelectItem value="natural"
                                >Persona natural</SelectItem
                            >
                            <SelectItem value="empresa">Empresa</SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="estadoFilter">
                        <SelectTrigger class="glass w-36">
                            <SelectValue placeholder="Estado" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="todos">Todos</SelectItem>
                            <SelectItem value="activos">Activos</SelectItem>
                            <SelectItem value="inactivos">Inactivos</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>
        </div>

        <div v-if="filteredClients.length > 0">
            <div class="glass-card hidden overflow-hidden rounded-2xl md:block">
                <div class="overflow-x-auto">
                    <table class="table-glow w-full">
                        <thead>
                            <tr class="border-b border-ink/10">
                                <th
                                    class="px-5 py-4 text-left text-xs font-semibold tracking-wider text-ink/75 uppercase"
                                >
                                    Cliente
                                </th>
                                <th
                                    class="px-5 py-4 text-left text-xs font-semibold tracking-wider text-ink/75 uppercase"
                                >
                                    Tipo
                                </th>
                                <th
                                    class="px-5 py-4 text-left text-xs font-semibold tracking-wider text-ink/75 uppercase"
                                >
                                    Contacto
                                </th>
                                <th
                                    class="px-5 py-4 text-left text-xs font-semibold tracking-wider text-ink/75 uppercase"
                                >
                                    Pedidos
                                </th>
                                <th
                                    class="px-5 py-4 text-left text-xs font-semibold tracking-wider text-ink/75 uppercase"
                                >
                                    Estado
                                </th>
                                <th
                                    class="px-5 py-4 text-right text-xs font-semibold tracking-wider text-ink/75 uppercase"
                                >
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ink/5">
                            <tr
                                v-for="client in filteredClients"
                                :key="client.id"
                                class="transition-colors hover:bg-blush/30"
                            >
                                <td class="px-5 py-4">
                                    <Link
                                        :href="
                                            clientes.show({
                                                cliente: client.id,
                                            }).url
                                        "
                                        class="group flex items-center gap-3"
                                    >
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-purple-500/20 to-pink-500/20 text-sm font-bold text-purple-600"
                                        >
                                            {{
                                                client.name
                                                    .charAt(0)
                                                    .toUpperCase()
                                            }}
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-bold text-ink group-hover:text-pix"
                                            >
                                                {{ client.name }}
                                            </p>
                                            <p class="text-xs text-ink/50">
                                                {{
                                                    client.code ||
                                                    client.email ||
                                                    '—'
                                                }}
                                            </p>
                                        </div>
                                    </Link>
                                </td>
                                <td class="px-5 py-4 text-sm text-ink/70">
                                    <span
                                        v-if="client.tipo_cliente"
                                        class="rounded-full bg-ink/5 px-3 py-1 text-xs font-semibold capitalize"
                                        >{{ client.tipo_cliente }}</span
                                    >
                                    <span v-else class="text-ink/40">—</span>
                                </td>
                                <td class="px-5 py-4 text-sm text-ink/70">
                                    <p>{{ client.phone || '—' }}</p>
                                    <p class="text-xs text-ink/50">
                                        {{ client.company || '' }}
                                    </p>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="rounded-full bg-ink/5 px-3 py-1 text-xs font-bold text-ink/70"
                                        >{{ client.orders_count || 0 }}</span
                                    >
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        :class="
                                            client.activo
                                                ? 'badge-green'
                                                : 'badge-gray'
                                        "
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                    >
                                        {{
                                            client.activo
                                                ? 'Activo'
                                                : 'Inactivo'
                                        }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-1">
                                        <Button
                                            size="sm"
                                            class="btn-gradient-ghost"
                                            as-child
                                        >
                                            <Link
                                                :href="
                                                    clientes.show({
                                                        cliente: client.id,
                                                    }).url
                                                "
                                            >
                                                <Eye class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button
                                            v-if="can('clientes.gestionar')"
                                            size="sm"
                                            class="btn-gradient-ghost"
                                            as-child
                                        >
                                            <Link
                                                :href="
                                                    clientes.edit({
                                                        cliente: client.id,
                                                    }).url
                                                "
                                            >
                                                <Pencil class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button
                                            v-if="can('clientes.gestionar')"
                                            size="sm"
                                            class="btn-gradient-ghost"
                                            @click="toggle(client)"
                                        >
                                            <Power
                                                class="h-4 w-4"
                                                :class="
                                                    client.activo
                                                        ? 'text-orange-500'
                                                        : 'text-green-600'
                                                "
                                            />
                                        </Button>
                                        <Button
                                            v-if="can('clientes.gestionar')"
                                            size="sm"
                                            class="btn-gradient-ghost text-red-500 hover:text-red-600"
                                            @click="remove(client)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 grid gap-4 md:hidden">
                <div
                    v-for="client in filteredClients"
                    :key="client.id"
                    class="glass-card animate-slide-up rounded-2xl p-4"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <Link :href="clientes.show({ cliente: client.id }).url">
                            <span class="text-sm font-semibold text-ink">{{
                                client.name
                            }}</span>
                            <p class="text-xs text-ink/50">
                                {{ client.code || '' }}
                            </p>
                        </Link>
                        <span
                            :class="
                                client.activo ? 'badge-green' : 'badge-gray'
                            "
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs"
                            >{{ client.activo ? 'Activo' : 'Inactivo' }}</span
                        >
                    </div>
                    <div class="space-y-1 text-sm text-ink/60">
                        <div class="flex justify-between">
                            <span>Teléfono</span>
                            <span>{{ client.phone || '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tipo</span>
                            <span>{{ client.tipo_cliente || '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Pedidos</span>
                            <span>{{ client.orders_count || 0 }}</span>
                        </div>
                    </div>
                    <div
                        class="flex justify-end gap-1 border-t border-ink/10 pt-3"
                    >
                        <Button size="sm" class="btn-gradient-ghost" as-child>
                            <Link
                                :href="
                                    clientes.edit({ cliente: client.id }).url
                                "
                            >
                                <Pencil class="mr-1.5 h-4 w-4" /> Editar
                            </Link>
                        </Button>
                        <Button
                            size="sm"
                            class="btn-gradient-ghost text-red-500 hover:text-red-600"
                            @click="remove(client)"
                        >
                            <Trash2 class="mr-1.5 h-4 w-4" /> Eliminar
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-else-if="false"
            class="glass-card flex flex-col items-center justify-center rounded-2xl px-6 py-16 text-center"
        >
            <p class="text-base font-medium text-ink/60">Cargando...</p>
        </div>

        <div
            v-else
            class="glass-card flex flex-col items-center justify-center rounded-2xl px-6 py-16 text-center"
        >
            <div
                class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-ink/[0.04]"
            >
                <Search class="h-6 w-6 text-ink/40" />
            </div>
            <p class="text-base font-medium text-ink/60">
                {{
                    clients_data.length === 0
                        ? 'No hay clientes registrados'
                        : 'No se encontraron clientes'
                }}
            </p>
            <p
                v-if="clients_data.length === 0"
                class="mt-1 text-sm text-ink/40"
            >
                Comienza agregando un nuevo cliente
            </p>
            <Button
                v-if="clients_data.length === 0 && can('clientes.gestionar')"
                class="btn-gradient-blue btn-shimmer mt-4"
                as-child
            >
                <Link :href="clientes.create().url">
                    <Plus class="mr-2 h-4 w-4" /> Crear primer cliente
                </Link>
            </Button>
        </div>
    </div>
</template>
