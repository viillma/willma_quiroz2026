<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
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
    order: {
        id: number;
        code: string;
        client_id: number;
        designer_id: number | null;
        status: string;
        notes: string;
        total: string;
        delivery_date: string | null;
        servicios: Array<{
            id: number;
            pivot: { quantity: number; unit_price: string };
        }>;
    };
    clients: Array<{ id: number; name: string; phone: string; company: string }>;
    servicios: Array<{ id: number; nombre: string; precio: string }>;
    designers: Array<{ id: number; name: string }>;
}>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            { title: 'Dashboard', href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/' },
            { title: 'Pedidos', href: props.currentTeam ? pedidos.index(props.currentTeam.slug) : '#' },
            { title: 'Editar Pedido', href: '#' },
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
const lineItems = ref<LineItem[]>(
    page.props.order.servicios.map((s) => ({
        key: nextKey++,
        servicio_id: s.id,
        quantity: s.pivot.quantity,
        unit_price: Number(s.pivot.unit_price),
    })),
);

function addLine() {
    lineItems.value.push({ key: nextKey++, servicio_id: null, quantity: 1, unit_price: 0 });
}

function removeLine(key: number) {
    if (lineItems.value.length > 1) {
        lineItems.value = lineItems.value.filter((l) => l.key !== key);
    }
}

const total = ref(Number(page.props.order.total));
function updateTotal() {
    total.value = lineItems.value.reduce((sum, item) => sum + (item.quantity * item.unit_price || 0), 0);
}
</script>

<template>
    <Head :title="'Editar ' + page.props.order.code" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 max-w-4xl space-y-6">
        <Heading :title="'Editar ' + page.props.order.code" description="Modifica los datos del pedido" />

        <div class="glass-card rounded-2xl p-6 md:p-8">
            <Form
                    v-if="currentTeam"
                    v-bind="
                        pedidos.update.form({
                            current_team: currentTeam.slug,
                            pedido: page.props.order.id,
                        })
                    "
                    class="space-y-6"
                    #default="{ errors, processing }"
                >
            <div class="space-y-2">
                <Label for="client_id">Cliente *</Label>
                <Select name="client_id" :default-value="String(page.props.order.client_id)" required>
                    <SelectTrigger class="bg-white/5 border-white/10 text-white"><SelectValue placeholder="Seleccionar cliente" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="client in page.props.clients" :key="client.id" :value="String(client.id)">
                            {{ client.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.client_id" />
            </div>

            <div class="space-y-2">
                <Label for="designer_id">Diseñador</Label>
                <Select name="designer_id" :default-value="String(page.props.order.designer_id ?? '')">
                    <SelectTrigger class="bg-white/5 border-white/10 text-white"><SelectValue placeholder="Sin asignar" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="undefined">Sin asignar</SelectItem>
                        <SelectItem v-for="d in page.props.designers" :key="d.id" :value="String(d.id)">
                            {{ d.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="space-y-2">
                <Label for="status">Estado</Label>
                <Select name="status" :default-value="page.props.order.status" required>
                    <SelectTrigger class="bg-white/5 border-white/10 text-white"><SelectValue :placeholder="page.props.order.status" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="pendiente">Pendiente</SelectItem>
                        <SelectItem value="en_diseno">En Diseño</SelectItem>
                        <SelectItem value="en_revision">En Revisión</SelectItem>
                        <SelectItem value="aprobado">Aprobado</SelectItem>
                        <SelectItem value="entregado">Entregado</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="space-y-2">
                <Label for="delivery_date">Fecha de Entrega</Label>
                <Input id="delivery_date" name="delivery_date" type="date" :default-value="page.props.order.delivery_date ?? ''" class="bg-white/5 border-white/10 text-white placeholder:text-white/40" />
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <Label>Servicios</Label>
                    <Button type="button" size="sm" @click="addLine" class="btn-gradient-purple">
                        <Plus class="mr-1 h-3 w-3" /> Agregar
                    </Button>
                </div>

                <div v-for="(item, index) in lineItems" :key="item.key" class="flex items-end gap-2 glass rounded-xl p-3">
                    <div class="flex-1 space-y-2">
                        <Label>Servicio</Label>
                        <Select :name="'servicios[' + index + '][servicio_id]'" :default-value="String(item.servicio_id ?? '')" required>
                            <SelectTrigger class="bg-white/5 border-white/10 text-white"><SelectValue placeholder="Seleccionar" /></SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="s in page.props.servicios" :key="s.id" :value="String(s.id)">
                                    {{ s.nombre }} - S/ {{ s.precio }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="w-24 space-y-2">
                        <Label>Cant.</Label>
                        <Input :name="'servicios[' + index + '][quantity]'" type="number" min="1" :default-value="item.quantity" class="bg-white/5 border-white/10 text-white placeholder:text-white/40" />
                    </div>
                    <div class="w-32 space-y-2">
                        <Label>Precio</Label>
                        <Input :name="'servicios[' + index + '][unit_price]'" type="number" step="0.01" min="0" :default-value="item.unit_price" class="bg-white/5 border-white/10 text-white placeholder:text-white/40" />
                    </div>
                    <div class="flex h-10 items-center pt-2">
                        <Button type="button" variant="ghost" size="sm" class="text-red-600" @click="removeLine(item.key)">
                            <Trash2 class="h-4 w-4" />
                        </Button>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <Label for="notes">Notas</Label>
                <Textarea id="notes" name="notes" rows="3" :default-value="page.props.order.notes ?? ''" class="bg-white/5 border-white/10 text-white placeholder:text-white/40" />
            </div>

            <div class="flex gap-4">
                <Button type="submit" :disabled="processing" class="btn-gradient-blue">Actualizar</Button>
                <Button type="button" variant="outline" as-child class="btn-gradient-ghost">
                    <a :href="pedidos.index(currentTeam.slug).url">Cancelar</a>
                </Button>
            </div>
                </Form>
        </div>
    </div>
</template>
