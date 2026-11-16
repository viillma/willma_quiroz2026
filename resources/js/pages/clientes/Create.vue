<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Loader2 } from 'lucide-vue-next';

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
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import clientes from '@/routes/clientes';

defineOptions({
    layout: AppSidebarLayout,
});

const form = useForm({
    name: '',
    tipo_cliente: 'natural',
    ci_nit: '',
    phone: '',
    whatsapp: '',
    email: '',
    direccion: '',
    company: '',
    notes: '',
    activo: true,
});

function submit() {
    form.post(clientes.store.url(), { preserveScroll: true });
}
</script>

<template>
    <Head title="Nuevo Cliente" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <Heading
            title="Nuevo Cliente"
            description="Registra un nuevo cliente en el sistema"
        />

        <div class="glass-card rounded-2xl p-6 md:p-8">
            <form class="space-y-6" @submit.prevent="submit">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="space-y-2">
                        <Label for="name">Nombre completo *</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="Nombre del cliente"
                            class="glass"
                        />
                        <p v-if="form.errors.name" class="text-xs text-red-500">
                            {{ form.errors.name }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label for="tipo_cliente">Tipo de cliente</Label>
                        <Select v-model="form.tipo_cliente">
                            <SelectTrigger class="glass"
                                ><SelectValue
                            /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="natural"
                                    >Persona natural</SelectItem
                                >
                                <SelectItem value="empresa">Empresa</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="space-y-2">
                        <Label for="ci_nit">CI / NIT</Label>
                        <Input
                            id="ci_nit"
                            v-model="form.ci_nit"
                            type="text"
                            placeholder="CI o NIT"
                            class="glass"
                        />
                        <p
                            v-if="form.errors.ci_nit"
                            class="text-xs text-red-500"
                        >
                            {{ form.errors.ci_nit }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="space-y-2">
                        <Label for="phone">Teléfono</Label>
                        <Input
                            id="phone"
                            v-model="form.phone"
                            type="text"
                            placeholder="+591 7 000 0000"
                            class="glass"
                        />
                        <p
                            v-if="form.errors.phone"
                            class="text-xs text-red-500"
                        >
                            {{ form.errors.phone }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label for="whatsapp">WhatsApp</Label>
                        <Input
                            id="whatsapp"
                            v-model="form.whatsapp"
                            type="text"
                            placeholder="59170000000"
                            class="glass"
                        />
                        <p
                            v-if="form.errors.whatsapp"
                            class="text-xs text-red-500"
                        >
                            {{ form.errors.whatsapp }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label for="email">Correo Electrónico</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="cliente@correo.com"
                            class="glass"
                        />
                        <p
                            v-if="form.errors.email"
                            class="text-xs text-red-500"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="company">Empresa</Label>
                        <Input
                            id="company"
                            v-model="form.company"
                            type="text"
                            placeholder="Nombre de la empresa"
                            class="glass"
                        />
                        <p
                            v-if="form.errors.company"
                            class="text-xs text-red-500"
                        >
                            {{ form.errors.company }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label for="direccion">Dirección</Label>
                        <Input
                            id="direccion"
                            v-model="form.direccion"
                            type="text"
                            placeholder="Dirección"
                            class="glass"
                        />
                        <p
                            v-if="form.errors.direccion"
                            class="text-xs text-red-500"
                        >
                            {{ form.errors.direccion }}
                        </p>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="notes">Notas</Label>
                    <Textarea
                        id="notes"
                        v-model="form.notes"
                        rows="3"
                        placeholder="Observaciones adicionales"
                        class="glass"
                    />
                </div>

                <div class="flex items-center gap-3">
                    <input
                        id="activo"
                        v-model="form.activo"
                        type="checkbox"
                        class="h-4 w-4 accent-purple-600"
                    />
                    <Label for="activo">Cliente activo</Label>
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
                        Guardar Cliente
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        as-child
                        class="btn-gradient-ghost"
                    >
                        <a :href="clientes.index().url">Cancelar</a>
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
