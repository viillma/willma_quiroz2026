<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import clientes from '@/routes/clientes';
import { dashboard } from '@/routes';
import type { Team } from '@/types';

const page = usePage<{ currentTeam?: Team | null }>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            { title: 'Dashboard', href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/' },
            { title: 'Clientes', href: props.currentTeam ? clientes.index(props.currentTeam.slug) : '#' },
            { title: 'Nuevo Cliente', href: '#' },
        ],
    }),
});

const currentTeam = page.props.currentTeam;
</script>

<template>
    <Head title="Nuevo Cliente" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 max-w-2xl space-y-6 relative">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500/20 to-pink-500/20 ring-1 ring-white/10">
                <svg class="h-5 w-5 text-purple-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <Heading title="Nuevo Cliente" description="Registra un nuevo cliente en el sistema" />
        </div>

        <div class="glass-card rounded-2xl p-6 md:p-8">
            <Form
                v-if="currentTeam"
                v-bind="clientes.store.form(currentTeam.slug)"
                class="space-y-6"
                #default="{ errors, processing }"
            >
            <div class="space-y-2">
                <Label for="name">Nombre *</Label>
                <Input id="name" name="name" type="text" required placeholder="Nombre completo" class="bg-white/5 border-white/10 text-white placeholder:text-white/40 focus:border-purple-500/50" />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <Label for="phone">Teléfono</Label>
                    <Input id="phone" name="phone" type="text" placeholder="+51 999 999 999" class="bg-white/5 border-white/10 text-white placeholder:text-white/40 focus:border-purple-500/50" />
                    <InputError :message="errors.phone" />
                </div>

                <div class="space-y-2">
                    <Label for="email">Correo Electrónico</Label>
                    <Input id="email" name="email" type="email" placeholder="cliente@correo.com" class="bg-white/5 border-white/10 text-white placeholder:text-white/40 focus:border-purple-500/50" />
                    <InputError :message="errors.email" />
                </div>
            </div>

            <div class="space-y-2">
                <Label for="company">Empresa</Label>
                <Input id="company" name="company" type="text" placeholder="Nombre de la empresa" class="bg-white/5 border-white/10 text-white placeholder:text-white/40 focus:border-purple-500/50" />
                <InputError :message="errors.company" />
            </div>

            <div class="space-y-2">
                <Label for="notes">Notas</Label>
                <Textarea id="notes" name="notes" rows="3" placeholder="Observaciones adicionales" class="bg-white/5 border-white/10 text-white placeholder:text-white/40 focus:border-purple-500/50" />
                <InputError :message="errors.notes" />
            </div>

            <div class="flex gap-4">
                <Button type="submit" :disabled="processing" class="btn-gradient-blue btn-shimmer">Guardar</Button>
                <Button type="button" variant="outline" as-child class="btn-gradient-ghost">
                    <a :href="clientes.index(currentTeam.slug).url">Cancelar</a>
                </Button>
            </div>
                </Form>
        </div>
    </div>
</template>
