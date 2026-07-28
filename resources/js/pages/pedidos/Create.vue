<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Trash2, Plus } from 'lucide-vue-next';
import pedidos from '@/routes/pedidos';
import { dashboard } from '@/routes';
import type { Team } from '@/types';

const page = usePage<{
    currentTeam?: Team | null;
    clients: Array<{ id: number; name: string; phone: string; company: string }>;
    servicios: Array<{ id: number; nombre: string; precio: string }>;
    designers: Array<{ id: number; name: string }>;
}>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            { title: 'Dashboard', href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/' },
            { title: 'Pedidos', href: props.currentTeam ? pedidos.index(props.currentTeam.slug) : '#' },
            { title: 'Nuevo Pedido', href: '#' },
        ],
    }),
});

const currentTeam = page.props.currentTeam;

interface LineItem {
    key: number;
    servicio_id: number | null;
    quantity: number;
    unit_price: number;
}

let nextKey = 1;
const lineItems = ref<LineItem[]>([{ key: nextKey++, servicio_id: null, quantity: 1, unit_price: 0 }]);

const selectedServicio = computed(() => {
    return (id: number | null) => page.props.servicios.find((s) => s.id === id);
});

function addLine() {
    lineItems.value.push({ key: nextKey++, servicio_id: null, quantity: 1, unit_price: 0 });
}

function removeLine(key: number) {
    if (lineItems.value.length > 1) {
        lineItems.value = lineItems.value.filter((l) => l.key !== key);
    }
}

function onServicioChange(key: number, servicioId: string) {
    const item = lineItems.value.find((l) => l.key === key);
    if (item) {
        item.servicio_id = Number(servicioId);
        const servicio = selectedServicio.value(item.servicio_id);
        if (servicio) {
            item.unit_price = Number(servicio.precio);
        }
    }
}

const total = computed(() =>
    lineItems.value.reduce((sum, item) => sum + (item.quantity * item.unit_price || 0), 0),
);
</script>

<template>
    <Head title="Nuevo Pedido" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 max-w-4xl space-y-6">
        <Heading title="Nuevo Pedido" description="Registra un nuevo pedido de diseño" />

        <div class="glass-card rounded-2xl p-6 md:p-8">
            <Form
                    v-if="currentTeam"
                    v-bind="pedidos.store.form(currentTeam.slug)"
                    class="space-y-6"
                    #default="{ errors, processing, setData }"
                >
            <input type="hidden" name="status" value="pendiente" />

            <div class="space-y-2">
                <Label for="client_id">Cliente *</Label>
                <Select name="client_id" required>
                    <SelectTrigger class="bg-white/5 border-white/10 text-white">
                        <SelectValue placeholder="Seleccionar cliente" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="client in page.props.clients"
                            :key="client.id"
                            :value="String(client.id)"
                        >
                            {{ client.name }} {{ client.company ? `(${client.company})` : '' }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.client_id" />
            </div>

            <div class="space-y-2">
                <Label for="designer_id">Diseñador Responsable</Label>
                <Select name="designer_id">
                    <SelectTrigger class="bg-white/5 border-white/10 text-white">
                        <SelectValue placeholder="Seleccionar diseñador" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="undefined">Sin asignar</SelectItem>
                        <SelectItem
                            v-for="designer in page.props.designers"
                            :key="designer.id"
                            :value="String(designer.id)"
                        >
                            {{ designer.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.designer_id" />
            </div>

            <div class="space-y-2">
                <Label for="delivery_date">Fecha de Entrega Estimada</Label>
                <Input id="delivery_date" name="delivery_date" type="date" class="bg-white/5 border-white/10 text-white placeholder:text-white/40" />
                <InputError :message="errors.delivery_date" />
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <Label>Servicios *</Label>
                    <Button type="button" size="sm" @click="addLine" class="btn-gradient-purple">
                        <Plus class="mr-1 h-3 w-3" /> Agregar Servicio
                    </Button>
                </div>

                <div
                    v-for="(item, index) in lineItems"
                    :key="item.key"
                    class="flex items-end gap-2 glass rounded-xl p-3"
                >
                    <div class="flex-1 space-y-2">
                        <Label :for="'servicio_' + item.key">Servicio</Label>
                        <Select
                            :name="'servicios[' + index + '][servicio_id]'"
                            :model-value="String(item.servicio_id ?? '')"
                            @update:model-value="(val: string) => onServicioChange(item.key, val)"
                            required
                        >
                            <SelectTrigger class="bg-white/5 border-white/10 text-white">
                                <SelectValue placeholder="Seleccionar" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="servicio in page.props.servicios"
                                    :key="servicio.id"
                                    :value="String(servicio.id)"
                                >
                                    {{ servicio.nombre }} - S/ {{ servicio.precio }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="w-24 space-y-2">
                        <Label :for="'qty_' + item.key">Cant.</Label>
                        <Input
                            :id="'qty_' + item.key"
                            :name="'servicios[' + index + '][quantity]'"
                            type="number"
                            min="1"
                            :default-value="item.quantity"
                            class="bg-white/5 border-white/10 text-white placeholder:text-white/40"
                            @input="(e: Event) => { const t = e.target as HTMLInputElement; item.quantity = Number(t.value); }"
                        />
                    </div>

                    <div class="w-32 space-y-2">
                        <Label :for="'price_' + item.key">Precio Unit.</Label>
                        <Input
                            :id="'price_' + item.key"
                            :name="'servicios[' + index + '][unit_price]'"
                            type="number"
                            step="0.01"
                            min="0"
                            :default-value="item.unit_price"
                            class="bg-white/5 border-white/10 text-white placeholder:text-white/40"
                            @input="(e: Event) => { const t = e.target as HTMLInputElement; item.unit_price = Number(t.value); }"
                        />
                    </div>

                    <div class="flex h-10 items-center pt-2">
                        <Button type="button" variant="ghost" size="sm" class="text-red-600" @click="removeLine(item.key)">
                            <Trash2 class="h-4 w-4" />
                        </Button>
                    </div>
                </div>

                <div class="text-right text-lg font-semibold text-white/90">
                    Total: S/ {{ total.toFixed(2) }}
                </div>
                <InputError :message="errors['servicios']" />
            </div>

            <div class="space-y-2">
                <Label for="notes">Notas</Label>
                <Textarea id="notes" name="notes" rows="3" placeholder="Observaciones del pedido" class="bg-white/5 border-white/10 text-white placeholder:text-white/40" />
                <InputError :message="errors.notes" />
            </div>

            <div class="flex gap-4">
                <Button type="submit" :disabled="processing" class="btn-gradient-blue">Guardar Pedido</Button>
                <Button type="button" variant="outline" as-child class="btn-gradient-ghost">
                    <a :href="pedidos.index(currentTeam.slug).url">Cancelar</a>
                </Button>
            </div>
                </Form>
        </div>
    </div>
</template>
