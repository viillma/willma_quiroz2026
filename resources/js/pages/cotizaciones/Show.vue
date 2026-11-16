<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Pencil,
    Trash2,
    Copy,
    ArrowRightCircle,
    Download,
} from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { usePermissions } from '@/composables/usePermissions';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { formatMoney, formatDate } from '@/lib/money';
import cotizaciones from '@/routes/cotizaciones';
import pedidos from '@/routes/pedidos';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage<{
    cotizacion: {
        id: number;
        numero: string;
        client: {
            id: number;
            name: string;
            phone: string;
            email: string;
            company: string;
        } | null;
        fecha: string | null;
        fecha_validez: string | null;
        descuento: number;
        total: number;
        subtotal: number;
        estado: string;
        estado_label: string;
        is_expired: boolean;
        observaciones: string | null;
        condiciones: string | null;
        created_at: string;
        servicios: Array<{
            id: number;
            nombre: string;
            codigo: string | null;
            quantity: number;
            unit_price: number;
            subtotal: number;
        }>;
        order: { id: number; code: string } | null;
    };
}>();

const { can } = usePermissions();

const cotizacion = computed(() => page.props.cotizacion);

const statusBadge: Record<string, string> = {
    borrador: 'badge-gray',
    enviada: 'badge-blue',
    aceptada: 'badge-green',
    rechazada: 'badge-red',
    vencida: 'badge-orange',
};

function convert() {
    router.post(
        cotizaciones.convertir.url({ cotizacion: cotizacion.value.id }),
    );
}

function duplicate() {
    router.post(cotizaciones.duplicar.url({ cotizacion: cotizacion.value.id }));
}

function remove() {
    if (
        window.confirm(`¿Eliminar la cotización "${cotizacion.value.numero}"?`)
    ) {
        router.delete(
            cotizaciones.destroy.url({ cotizacion: cotizacion.value.id }),
        );
    }
}
</script>

<template>
    <Head :title="cotizacion.numero" />

    <div class="animate-fade-in space-y-6 p-4 md:p-6 lg:p-8">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <Heading
                    :title="cotizacion.numero"
                    :description="
                        'Creada el ' + formatDate(cotizacion.created_at)
                    "
                />
                <div class="mt-1 flex items-center gap-2">
                    <span
                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                        :class="statusBadge[cotizacion.estado] || 'badge-gray'"
                    >
                        {{ cotizacion.estado_label }}
                    </span>
                    <span
                        v-if="cotizacion.is_expired"
                        class="rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-600"
                        >Vencida</span
                    >
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <Button size="sm" class="btn-gradient-blue" as-child>
                    <a
                        :href="
                            cotizaciones.pdf.url({ cotizacion: cotizacion.id })
                        "
                        target="_blank"
                    >
                        <Download class="mr-1 h-4 w-4" /> Descargar PDF
                    </a>
                </Button>
                <Button size="sm" class="btn-gradient-ghost" as-child>
                    <Link
                        :href="
                            cotizaciones.edit({ cotizacion: cotizacion.id }).url
                        "
                    >
                        <Pencil class="mr-1 h-4 w-4" /> Editar
                    </Link>
                </Button>
                <Button
                    v-if="
                        cotizacion.estado === 'aceptada' &&
                        !cotizacion.order &&
                        can('cotizaciones.gestionar')
                    "
                    size="sm"
                    class="btn-gradient-blue btn-shimmer"
                    @click="convert"
                >
                    <ArrowRightCircle class="mr-1 h-4 w-4" /> Convertir en
                    Pedido
                </Button>
                <Button
                    v-if="
                        cotizacion.estado !== 'aceptada' &&
                        can('cotizaciones.gestionar')
                    "
                    size="sm"
                    class="btn-gradient-ghost"
                    @click="duplicate"
                >
                    <Copy class="mr-1 h-4 w-4" /> Duplicar
                </Button>
            </div>
        </div>

        <div
            v-if="cotizacion.order"
            class="rounded-xl bg-green-50 p-4 text-sm text-ink/80"
        >
            Esta cotización fue convertida en el pedido
            <Link
                :href="pedidos.show({ pedido: cotizacion.order.id }).url"
                class="font-bold text-green-600 hover:underline"
            >
                {{ cotizacion.order.code }}
            </Link>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="glass-card rounded-2xl p-6">
                    <h3 class="mb-4 text-lg font-bold text-ink">Información</h3>
                    <div class="grid gap-4 md:grid-cols-4">
                        <div>
                            <p class="text-sm text-ink/60">Cliente</p>
                            <p class="font-semibold text-ink">
                                {{ cotizacion.client?.name || '—' }}
                            </p>
                            <p
                                v-if="cotizacion.client?.company"
                                class="text-sm text-ink/60"
                            >
                                {{ cotizacion.client.company }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-ink/60">Fecha</p>
                            <p class="font-semibold text-ink">
                                {{
                                    cotizacion.fecha
                                        ? formatDate(cotizacion.fecha)
                                        : '—'
                                }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-ink/60">Válida hasta</p>
                            <p class="font-semibold text-ink">
                                {{
                                    cotizacion.fecha_validez
                                        ? formatDate(cotizacion.fecha_validez)
                                        : '—'
                                }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-ink/60">Total</p>
                            <p class="text-lg font-bold text-pix">
                                {{ formatMoney(cotizacion.total) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <h3 class="mb-4 text-lg font-bold text-ink">Servicios</h3>
                    <div class="overflow-x-auto">
                        <table class="table-glow w-full">
                            <thead>
                                <tr
                                    class="border-b border-ink/10 text-sm text-ink/60"
                                >
                                    <th class="pb-2 text-left">Servicio</th>
                                    <th class="pb-2 text-center">Cant.</th>
                                    <th class="pb-2 text-right">
                                        Precio Unit.
                                    </th>
                                    <th class="pb-2 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="servicio in cotizacion.servicios"
                                    :key="servicio.id"
                                    class="border-b border-ink/5 text-sm text-ink/80 transition-colors hover:bg-pink-50/70"
                                >
                                    <td class="py-2">
                                        <p class="font-semibold text-ink">
                                            {{ servicio.nombre }}
                                        </p>
                                        <p
                                            v-if="servicio.codigo"
                                            class="text-xs text-ink/50"
                                        >
                                            {{ servicio.codigo }}
                                        </p>
                                    </td>
                                    <td class="py-2 text-center">
                                        {{ servicio.quantity }}
                                    </td>
                                    <td class="py-2 text-right">
                                        {{ formatMoney(servicio.unit_price) }}
                                    </td>
                                    <td
                                        class="py-2 text-right font-semibold text-ink"
                                    >
                                        {{ formatMoney(servicio.subtotal) }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="text-sm text-ink/70">
                                    <td colspan="3" class="pt-3 text-right">
                                        Subtotal:
                                    </td>
                                    <td
                                        class="pt-3 text-right font-semibold text-ink"
                                    >
                                        {{ formatMoney(cotizacion.subtotal) }}
                                    </td>
                                </tr>
                                <tr
                                    v-if="cotizacion.descuento > 0"
                                    class="text-sm text-ink/70"
                                >
                                    <td colspan="3" class="pt-1 text-right">
                                        Descuento:
                                    </td>
                                    <td
                                        class="pt-1 text-right font-semibold text-red-600"
                                    >
                                        -{{ formatMoney(cotizacion.descuento) }}
                                    </td>
                                </tr>
                                <tr class="text-lg font-bold text-ink">
                                    <td colspan="3" class="pt-2 text-right">
                                        Total:
                                    </td>
                                    <td class="pt-2 text-right">
                                        {{ formatMoney(cotizacion.total) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-ink/60">Observaciones</p>
                            <p class="text-sm text-ink/80">
                                {{
                                    cotizacion.observaciones ||
                                    'Sin observaciones'
                                }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-ink/60">Condiciones</p>
                            <p class="text-sm text-ink/80">
                                {{
                                    cotizacion.condiciones || 'Sin condiciones'
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div
                    v-if="can('cotizaciones.gestionar')"
                    class="glass-card rounded-2xl p-6"
                >
                    <Button
                        size="sm"
                        variant="ghost"
                        class="btn-gradient-ghost w-full text-red-600 hover:text-red-700"
                        @click="remove"
                    >
                        <Trash2 class="mr-1.5 h-4 w-4" /> Eliminar cotización
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
