<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Trash2, Plus, Loader2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useInvoiceCalculator } from '@/composables/useInvoiceCalculator';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { formatMoney } from '@/lib/money';
import cotizaciones from '@/routes/cotizaciones';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage<{
    clients: Array<{
        id: number;
        name: string;
        phone: string;
        company: string;
    }>;
    servicios: Array<{
        id: number;
        nombre: string;
        codigo: string | null;
        precio: number;
        unidad: string | null;
    }>;
    statuses: Record<string, string>;
}>();

const services = ref<
    Array<{
        key: number;
        servicio_id: number | null;
        quantity: number;
        unit_price: number;
    }>
>([{ key: 1, servicio_id: null, quantity: 1, unit_price: 0 }]);
let nextKey = 2;

const { subtotal } = useInvoiceCalculator(services);

const form = useForm({
    client_id: '',
    fecha: new Date().toISOString().slice(0, 10),
    fecha_validez: '',
    estado: 'borrador',
    descuento: '',
    observaciones: '',
    condiciones: '',
    servicios: [] as {
        servicio_id: number;
        quantity: number;
        unit_price: number;
    }[],
});

const total = computed(() =>
    Math.max(0, subtotal.value - Number(form.descuento || 0)),
);
const availableServices = computed(() => page.props.servicios ?? []);

function addLine() {
    services.value.push({
        key: nextKey++,
        servicio_id: null,
        quantity: 1,
        unit_price: 0,
    });
}

function removeLine(key: number) {
    if (services.value.length > 1) {
        services.value = services.value.filter((l) => l.key !== key);
    }
}

function onServicioChange(
    line: { servicio_id: number | null; unit_price: number },
    value: string,
) {
    line.servicio_id = Number(value);
    const s = availableServices.value.find((x) => x.id === line.servicio_id);
    line.unit_price = s ? Number(s.precio) : 0;
}

function submit() {
    const payload = {
        client_id: form.client_id,
        fecha: form.fecha,
        fecha_validez: form.fecha_validez || undefined,
        estado: form.estado,
        descuento: form.descuento,
        observaciones: form.observaciones,
        condiciones: form.condiciones,
        servicios: services.value.map((l) => ({
            servicio_id: l.servicio_id!,
            quantity: l.quantity,
            unit_price: l.unit_price,
        })),
    };
    form.transform(() => payload).post(cotizaciones.store.url(), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Nueva Cotización" />

    <div class="animate-fade-in max-w-4xl space-y-6 p-4 md:p-6 lg:p-8">
        <Heading
            title="Nueva Cotización"
            description="Crea una cotización para un cliente"
        />

        <div class="glass-card rounded-2xl p-6 md:p-8">
            <form class="space-y-6" @submit.prevent="submit">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="space-y-2">
                        <Label for="client_id">Cliente *</Label>
                        <Select v-model="form.client_id">
                            <SelectTrigger class="glass"
                                ><SelectValue placeholder="Seleccionar cliente"
                            /></SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="client in page.props.clients"
                                    :key="client.id"
                                    :value="String(client.id)"
                                >
                                    {{ client.name
                                    }}{{
                                        client.company
                                            ? ` (${client.company})`
                                            : ''
                                    }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p
                            v-if="form.errors.client_id"
                            class="text-xs text-red-500"
                        >
                            {{ form.errors.client_id }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label for="fecha">Fecha *</Label>
                        <Input
                            id="fecha"
                            v-model="form.fecha"
                            type="date"
                            class="glass"
                        />
                        <p
                            v-if="form.errors.fecha"
                            class="text-xs text-red-500"
                        >
                            {{ form.errors.fecha }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label for="fecha_validez">Válida hasta</Label>
                        <Input
                            id="fecha_validez"
                            v-model="form.fecha_validez"
                            type="date"
                            class="glass"
                        />
                        <p
                            v-if="form.errors.fecha_validez"
                            class="text-xs text-red-500"
                        >
                            {{ form.errors.fecha_validez }}
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <Label>Servicios *</Label>
                        <Button
                            type="button"
                            size="sm"
                            @click="addLine"
                            class="btn-gradient-purple"
                        >
                            <Plus class="mr-1 h-3 w-3" /> Agregar Servicio
                        </Button>
                    </div>

                    <div
                        v-for="item in services"
                        :key="item.key"
                        class="glass flex items-end gap-2 rounded-xl p-3"
                    >
                        <div class="flex-1 space-y-1.5">
                            <Label :for="'servicio_' + item.key"
                                >Servicio</Label
                            >
                            <Select
                                :model-value="
                                    item.servicio_id
                                        ? String(item.servicio_id)
                                        : ''
                                "
                                @update:model-value="
                                    (val) => onServicioChange(item, String(val))
                                "
                                required
                            >
                                <SelectTrigger
                                    class="border-ink/10 bg-ink/5 text-ink"
                                >
                                    <SelectValue placeholder="Seleccionar" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="servicio in availableServices"
                                        :key="servicio.id"
                                        :value="String(servicio.id)"
                                    >
                                        {{ servicio.nombre }} -
                                        {{ formatMoney(servicio.precio) }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="w-24 space-y-1.5">
                            <Label :for="'qty_' + item.key">Cant.</Label>
                            <Input
                                :id="'qty_' + item.key"
                                v-model.number="item.quantity"
                                type="number"
                                min="1"
                                class="border-ink/10 bg-ink/5 text-ink"
                            />
                        </div>

                        <div class="w-32 space-y-1.5">
                            <Label :for="'price_' + item.key"
                                >Precio Unit. (Bs)</Label
                            >
                            <Input
                                :id="'price_' + item.key"
                                v-model.number="item.unit_price"
                                type="number"
                                step="0.01"
                                min="0"
                                class="border-ink/10 bg-ink/5 text-ink"
                            />
                        </div>

                        <div class="flex h-10 items-center pt-2">
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                class="text-red-600"
                                @click="removeLine(item.key)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>

                    <p
                        v-if="form.errors.servicios"
                        class="text-xs text-red-500"
                    >
                        {{ form.errors.servicios }}
                    </p>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="space-y-2">
                        <Label for="estado">Estado</Label>
                        <Select v-model="form.estado">
                            <SelectTrigger class="glass"
                                ><SelectValue
                            /></SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="(label, key) in page.props.statuses"
                                    :key="key"
                                    :value="key"
                                    >{{ label }}</SelectItem
                                >
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="space-y-2">
                        <Label for="descuento">Descuento (Bs)</Label>
                        <Input
                            id="descuento"
                            v-model="form.descuento"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            class="glass"
                        />
                        <p
                            v-if="form.errors.descuento"
                            class="text-xs text-red-500"
                        >
                            {{ form.errors.descuento }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="observaciones">Observaciones</Label>
                        <Textarea
                            id="observaciones"
                            v-model="form.observaciones"
                            rows="3"
                            placeholder="Detalles de la cotización"
                            class="glass"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="condiciones">Condiciones</Label>
                        <Textarea
                            id="condiciones"
                            v-model="form.condiciones"
                            rows="3"
                            placeholder="Condiciones de pago o entrega"
                            class="glass"
                        />
                    </div>
                </div>

                <div class="rounded-xl bg-ink/5 p-4 text-right">
                    <p class="text-sm text-ink/60">
                        Subtotal:
                        <span class="font-semibold text-ink">{{
                            formatMoney(subtotal)
                        }}</span>
                    </p>
                    <p
                        v-if="Number(form.descuento || 0) > 0"
                        class="text-sm text-ink/60"
                    >
                        Descuento:
                        <span class="font-semibold text-red-600"
                            >-{{ formatMoney(form.descuento) }}</span
                        >
                    </p>
                    <p class="mt-1 text-lg font-bold text-ink">
                        Total: {{ formatMoney(total) }}
                    </p>
                </div>

                <div class="flex gap-4 border-t border-ink/10 pt-5">
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="btn-gradient-blue btn-shimmer"
                    >
                        <Loader2
                            v-if="form.processing"
                            class="mr-2 h-4 w-4 animate-spin"
                        />
                        Guardar Cotización
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        as-child
                        class="btn-gradient-ghost"
                    >
                        <a :href="cotizaciones.index().url">Cancelar</a>
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
