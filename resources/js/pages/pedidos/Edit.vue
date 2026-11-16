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
import pedidos from '@/routes/pedidos';

defineOptions({
    layout: AppSidebarLayout,
});

const page = usePage<{
    order: {
        id: number;
        code: string;
        client: { id: number; name: string; company: string } | null;
        designer: { id: number; name: string } | null;
        status: string;
        notes: string | null;
        delivery_date: string | null;
        descuento: number;
        adelanto: number;
        forma_pago: string;
        responsable: string | null;
        servicios: Array<{
            id: number;
            nombre: string;
            codigo: string | null;
            quantity: number;
            unit_price: number;
            subtotal: number;
        }>;
    };
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
    designers: Array<{ id: number; name: string }>;
    statuses: Record<string, string>;
}>();

const order = page.props.order;

const services = ref<
    Array<{
        key: number;
        servicio_id: number | null;
        quantity: number;
        unit_price: number;
    }>
>(
    order.servicios.map((s, i) => ({
        key: i + 1,
        servicio_id: s.id,
        quantity: s.quantity,
        unit_price: s.unit_price,
    })),
);
let nextKey = services.value.length + 1;

const { subtotal } = useInvoiceCalculator(services);

const form = useForm({
    client_id: String(order.client?.id ?? ''),
    designer_id: order.designer ? String(order.designer.id) : '',
    status: order.status,
    delivery_date: order.delivery_date ?? '',
    notes: order.notes ?? '',
    descuento: String(order.descuento ?? 0),
    adelanto: String(order.adelanto ?? 0),
    forma_pago: order.forma_pago || 'efectivo',
    responsable: order.responsable ?? '',
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
        designer_id: form.designer_id || undefined,
        status: form.status,
        delivery_date: form.delivery_date || undefined,
        notes: form.notes,
        descuento: form.descuento,
        adelanto: form.adelanto,
        forma_pago: form.forma_pago,
        responsable: form.responsable,
        servicios: services.value.map((l) => ({
            servicio_id: l.servicio_id!,
            quantity: l.quantity,
            unit_price: l.unit_price,
        })),
    };
    form.transform(() => payload).post(
        pedidos.update.url({ pedido: order.id }),
        {
            preserveScroll: true,
        },
    );
}
</script>

<template>
    <Head :title="'Editar ' + order.code" />

    <div class="animate-fade-in max-w-4xl space-y-6 p-4 md:p-6 lg:p-8">
        <Heading
            :title="'Editar ' + order.code"
            description="Modifica el pedido"
        />

        <div class="glass-card rounded-2xl p-6 md:p-8">
            <form class="space-y-6" @submit.prevent="submit">
                <div class="grid gap-4 md:grid-cols-2">
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
                        <Label for="designer_id">Diseñador Responsable</Label>
                        <Select v-model="form.designer_id">
                            <SelectTrigger class="glass"
                                ><SelectValue placeholder="Sin asignar"
                            /></SelectTrigger>
                            <SelectContent>
                                <SelectItem :value="''">Sin asignar</SelectItem>
                                <SelectItem
                                    v-for="designer in page.props.designers"
                                    :key="designer.id"
                                    :value="String(designer.id)"
                                >
                                    {{ designer.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p
                            v-if="form.errors.designer_id"
                            class="text-xs text-red-500"
                        >
                            {{ form.errors.designer_id }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="space-y-2">
                        <Label for="status">Estado</Label>
                        <Select v-model="form.status">
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
                        <Label for="delivery_date"
                            >Fecha de Entrega Estimada</Label
                        >
                        <Input
                            id="delivery_date"
                            v-model="form.delivery_date"
                            type="date"
                            class="glass"
                        />
                        <p
                            v-if="form.errors.delivery_date"
                            class="text-xs text-red-500"
                        >
                            {{ form.errors.delivery_date }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label for="responsable">Responsable</Label>
                        <Input
                            id="responsable"
                            v-model="form.responsable"
                            type="text"
                            placeholder="Nombre de quien gestiona"
                            class="glass"
                        />
                        <p
                            v-if="form.errors.responsable"
                            class="text-xs text-red-500"
                        >
                            {{ form.errors.responsable }}
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
                    </div>
                    <div class="space-y-2">
                        <Label for="adelanto">Adelanto / Anticipo (Bs)</Label>
                        <Input
                            id="adelanto"
                            v-model="form.adelanto"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            class="glass"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="forma_pago">Forma de pago</Label>
                        <Select v-model="form.forma_pago">
                            <SelectTrigger class="glass"
                                ><SelectValue
                            /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="efectivo"
                                    >Efectivo</SelectItem
                                >
                                <SelectItem value="qr">QR</SelectItem>
                                <SelectItem value="transferencia"
                                    >Transferencia</SelectItem
                                >
                                <SelectItem value="tarjeta">Tarjeta</SelectItem>
                                <SelectItem value="otro">Otro</SelectItem>
                            </SelectContent>
                        </Select>
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

                <div class="space-y-2">
                    <Label for="notes">Notas</Label>
                    <Textarea
                        id="notes"
                        v-model="form.notes"
                        rows="3"
                        placeholder="Observaciones del pedido"
                        class="glass"
                    />
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
                        Guardar Cambios
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        as-child
                        class="btn-gradient-ghost"
                    >
                        <a :href="pedidos.index().url">Cancelar</a>
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
