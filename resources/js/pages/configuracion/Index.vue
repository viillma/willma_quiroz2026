<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import {
    Building2,
    Phone,
    Receipt,
    Upload,
    Trash2,
    Save,
    Loader2,
    ImageIcon,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { usePermissions } from '@/composables/usePermissions';
import { dashboard } from '@/routes';
import configuracion from '@/routes/configuracion';

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard().url },
            { title: 'Configuración', href: '#' },
        ],
    }),
});

type Settings = {
    business_name: string;
    business_description: string | null;
    business_logo: string | null;
    business_nit: string;
    business_phone: string;
    business_whatsapp: string;
    business_email: string;
    business_address: string;
    business_city: string;
    business_country: string;
    business_social: string;
    currency: string;
    currency_symbol: string;
    tax_percent: number | string | null;
    date_format: string;
    order_prefix: string;
    quote_prefix: string;
};

const page = usePage<{
    settings: Settings;
}>();

const { can } = usePermissions();
const settings = page.props.settings;

const canManage = computed(() => can('configuracion.gestionar'));

const form = useForm({
    business_name: settings.business_name ?? '',
    business_description: settings.business_description ?? '',
    business_nit: settings.business_nit ?? '',
    business_phone: settings.business_phone ?? '',
    business_whatsapp: settings.business_whatsapp ?? '',
    business_email: settings.business_email ?? '',
    business_address: settings.business_address ?? '',
    business_city: settings.business_city ?? '',
    business_country: settings.business_country ?? 'Bolivia',
    business_social: settings.business_social ?? '',
    currency: settings.currency ?? 'BOB',
    currency_symbol: settings.currency_symbol ?? 'Bs',
    tax_percent: settings.tax_percent ?? '',
    date_format: settings.date_format ?? 'd/m/Y',
    order_prefix: settings.order_prefix ?? 'PED',
    quote_prefix: settings.quote_prefix ?? 'COT',
    business_logo: null as File | null,
});

const logoPreview = ref<string | null>(
    settings.business_logo ? `/storage/${settings.business_logo}` : null,
);

function onLogoChange(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form.business_logo = file;

    if (file) {
        logoPreview.value = URL.createObjectURL(file);
    }
}

function submit() {
    form.post(configuracion.update.url(), {
        forceFormData: true,
        preserveScroll: true,
    });
}

function removeLogo() {
    router.delete(configuracion.logo.destroy.url(), { preserveScroll: true });
}

const dateFormats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'm/d/Y'];
const currencies = [
    { code: 'BOB', symbol: 'Bs' },
    { code: 'USD', symbol: '$' },
    { code: 'EUR', symbol: '€' },
    { code: 'ARS', symbol: '$' },
    { code: 'PEN', symbol: 'S/' },
    { code: 'CLP', symbol: '$' },
    { code: 'COP', symbol: '$' },
];
</script>

<template>
    <Head title="Configuración" />

    <div class="animate-fade-in relative space-y-6 p-4 md:p-6 lg:p-8">
        <Heading
            title="Configuración"
            description="Identidad del negocio, contacto y parámetros de facturación"
        />

        <form class="space-y-6" @submit.prevent="submit">
            <!-- Identity -->
            <div class="glass-card rounded-2xl p-6">
                <div class="mb-5 flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blush/60 text-pix"
                    >
                        <Building2 class="h-5 w-5" />
                    </div>
                    <div>
                        <h2
                            class="font-display text-lg font-extrabold text-ink"
                        >
                            Identidad del negocio
                        </h2>
                        <p class="text-sm text-ink/50">
                            Logo, nombre y datos fiscales
                        </p>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Logo -->
                    <div class="space-y-3">
                        <Label class="text-sm font-semibold text-ink/80"
                            >Logo</Label
                        >
                        <div
                            class="flex h-40 items-center justify-center overflow-hidden rounded-2xl border border-dashed border-ink/15 bg-warm"
                        >
                            <img
                                v-if="logoPreview"
                                :src="logoPreview"
                                alt="Logo"
                                class="max-h-32 max-w-full object-contain"
                            />
                            <div
                                v-else
                                class="flex flex-col items-center gap-2 text-ink/35"
                            >
                                <ImageIcon class="h-8 w-8" />
                                <span class="text-xs font-semibold"
                                    >Sin logo</span
                                >
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <label
                                class="inline-flex h-9 flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl border border-ink/10 bg-white text-xs font-bold text-ink transition hover:bg-warm"
                            >
                                <Upload class="h-3.5 w-3.5" /> Subir logo
                                <input
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    :disabled="!canManage"
                                    @change="onLogoChange"
                                />
                            </label>
                            <button
                                v-if="logoPreview"
                                type="button"
                                class="inline-flex h-9 items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3 text-xs font-bold text-red-600 transition hover:bg-red-100"
                                :disabled="!canManage"
                                @click="removeLogo"
                            >
                                <Trash2 class="h-3.5 w-3.5" />
                            </button>
                        </div>
                        <InputError :message="form.errors.business_logo" />
                    </div>

                    <!-- Name / description / nit -->
                    <div class="space-y-4 lg:col-span-2">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label
                                    for="business_name"
                                    class="text-sm font-semibold text-ink/80"
                                    >Nombre del negocio</Label
                                >
                                <Input
                                    id="business_name"
                                    v-model="form.business_name"
                                    :disabled="!canManage"
                                    class="h-11 rounded-xl border-ink/10 bg-warm focus:border-pix/40 focus:ring-pix/20"
                                />
                                <InputError
                                    :message="form.errors.business_name"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label
                                    for="business_nit"
                                    class="text-sm font-semibold text-ink/80"
                                    >NIT / RUC</Label
                                >
                                <Input
                                    id="business_nit"
                                    v-model="form.business_nit"
                                    :disabled="!canManage"
                                    class="h-11 rounded-xl border-ink/10 bg-warm focus:border-pix/40 focus:ring-pix/20"
                                />
                                <InputError
                                    :message="form.errors.business_nit"
                                />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <Label
                                for="business_description"
                                class="text-sm font-semibold text-ink/80"
                                >Descripción</Label
                            >
                            <Textarea
                                id="business_description"
                                v-model="form.business_description"
                                :disabled="!canManage"
                                rows="2"
                                class="rounded-xl border-ink/10 bg-warm focus:border-pix/40 focus:ring-pix/20"
                            />
                            <InputError
                                :message="form.errors.business_description"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact -->
            <div class="glass-card rounded-2xl p-6">
                <div class="mb-5 flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-mint/60 text-[#17805a]"
                    >
                        <Phone class="h-5 w-5" />
                    </div>
                    <div>
                        <h2
                            class="font-display text-lg font-extrabold text-ink"
                        >
                            Contacto
                        </h2>
                        <p class="text-sm text-ink/50">
                            Canales de comunicación y ubicación
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-2">
                        <Label
                            for="business_phone"
                            class="text-sm font-semibold text-ink/80"
                            >Teléfono</Label
                        >
                        <Input
                            id="business_phone"
                            v-model="form.business_phone"
                            :disabled="!canManage"
                            class="h-11 rounded-xl border-ink/10 bg-warm focus:border-pix/40 focus:ring-pix/20"
                        />
                        <InputError :message="form.errors.business_phone" />
                    </div>
                    <div class="space-y-2">
                        <Label
                            for="business_whatsapp"
                            class="text-sm font-semibold text-ink/80"
                            >WhatsApp</Label
                        >
                        <Input
                            id="business_whatsapp"
                            v-model="form.business_whatsapp"
                            :disabled="!canManage"
                            class="h-11 rounded-xl border-ink/10 bg-warm focus:border-pix/40 focus:ring-pix/20"
                        />
                        <InputError :message="form.errors.business_whatsapp" />
                    </div>
                    <div class="space-y-2">
                        <Label
                            for="business_email"
                            class="text-sm font-semibold text-ink/80"
                            >Email</Label
                        >
                        <Input
                            id="business_email"
                            type="email"
                            v-model="form.business_email"
                            :disabled="!canManage"
                            class="h-11 rounded-xl border-ink/10 bg-warm focus:border-pix/40 focus:ring-pix/20"
                        />
                        <InputError :message="form.errors.business_email" />
                    </div>
                    <div class="space-y-2">
                        <Label
                            for="business_address"
                            class="text-sm font-semibold text-ink/80"
                            >Dirección</Label
                        >
                        <Input
                            id="business_address"
                            v-model="form.business_address"
                            :disabled="!canManage"
                            class="h-11 rounded-xl border-ink/10 bg-warm focus:border-pix/40 focus:ring-pix/20"
                        />
                        <InputError :message="form.errors.business_address" />
                    </div>
                    <div class="space-y-2">
                        <Label
                            for="business_city"
                            class="text-sm font-semibold text-ink/80"
                            >Ciudad</Label
                        >
                        <Input
                            id="business_city"
                            v-model="form.business_city"
                            :disabled="!canManage"
                            class="h-11 rounded-xl border-ink/10 bg-warm focus:border-pix/40 focus:ring-pix/20"
                        />
                        <InputError :message="form.errors.business_city" />
                    </div>
                    <div class="space-y-2">
                        <Label
                            for="business_country"
                            class="text-sm font-semibold text-ink/80"
                            >País</Label
                        >
                        <Input
                            id="business_country"
                            v-model="form.business_country"
                            :disabled="!canManage"
                            class="h-11 rounded-xl border-ink/10 bg-warm focus:border-pix/40 focus:ring-pix/20"
                        />
                        <InputError :message="form.errors.business_country" />
                    </div>
                    <div class="space-y-2 sm:col-span-2 lg:col-span-3">
                        <Label
                            for="business_social"
                            class="text-sm font-semibold text-ink/80"
                            >Redes sociales</Label
                        >
                        <Input
                            id="business_social"
                            v-model="form.business_social"
                            :disabled="!canManage"
                            placeholder="@creapix"
                            class="h-11 rounded-xl border-ink/10 bg-warm focus:border-pix/40 focus:ring-pix/20"
                        />
                        <InputError :message="form.errors.business_social" />
                    </div>
                </div>
            </div>

            <!-- Billing -->
            <div class="glass-card rounded-2xl p-6">
                <div class="mb-5 flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-lavender/70 text-[#7b5ee0]"
                    >
                        <Receipt class="h-5 w-5" />
                    </div>
                    <div>
                        <h2
                            class="font-display text-lg font-extrabold text-ink"
                        >
                            Facturación
                        </h2>
                        <p class="text-sm text-ink/50">
                            Moneda, impuestos, formato de fecha y prefijos
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-2">
                        <Label
                            for="currency"
                            class="text-sm font-semibold text-ink/80"
                            >Moneda</Label
                        >
                        <select
                            id="currency"
                            v-model="form.currency"
                            :disabled="!canManage"
                            class="h-11 w-full rounded-xl border border-ink/10 bg-warm px-3 text-sm text-ink focus:border-pix/40 focus:outline-none"
                            @change="
                                form.currency_symbol =
                                    currencies.find(
                                        (c) => c.code === form.currency,
                                    )?.symbol ?? form.currency_symbol
                            "
                        >
                            <option
                                v-for="c in currencies"
                                :key="c.code"
                                :value="c.code"
                            >
                                {{ c.code }} ({{ c.symbol }})
                            </option>
                        </select>
                        <InputError :message="form.errors.currency" />
                    </div>
                    <div class="space-y-2">
                        <Label
                            for="currency_symbol"
                            class="text-sm font-semibold text-ink/80"
                            >Símbolo</Label
                        >
                        <Input
                            id="currency_symbol"
                            v-model="form.currency_symbol"
                            :disabled="!canManage"
                            class="h-11 rounded-xl border-ink/10 bg-warm focus:border-pix/40 focus:ring-pix/20"
                        />
                        <InputError :message="form.errors.currency_symbol" />
                    </div>
                    <div class="space-y-2">
                        <Label
                            for="tax_percent"
                            class="text-sm font-semibold text-ink/80"
                            >IVA / Impuesto (%)</Label
                        >
                        <Input
                            id="tax_percent"
                            type="number"
                            step="0.01"
                            v-model="form.tax_percent"
                            :disabled="!canManage"
                            class="h-11 rounded-xl border-ink/10 bg-warm focus:border-pix/40 focus:ring-pix/20"
                        />
                        <InputError :message="form.errors.tax_percent" />
                    </div>
                    <div class="space-y-2">
                        <Label
                            for="date_format"
                            class="text-sm font-semibold text-ink/80"
                            >Formato de fecha</Label
                        >
                        <select
                            id="date_format"
                            v-model="form.date_format"
                            :disabled="!canManage"
                            class="h-11 w-full rounded-xl border border-ink/10 bg-warm px-3 text-sm text-ink focus:border-pix/40 focus:outline-none"
                        >
                            <option
                                v-for="f in dateFormats"
                                :key="f"
                                :value="f"
                            >
                                {{ f }}
                            </option>
                        </select>
                        <InputError :message="form.errors.date_format" />
                    </div>
                    <div class="space-y-2">
                        <Label
                            for="order_prefix"
                            class="text-sm font-semibold text-ink/80"
                            >Prefijo de pedidos</Label
                        >
                        <Input
                            id="order_prefix"
                            v-model="form.order_prefix"
                            :disabled="!canManage"
                            class="h-11 rounded-xl border-ink/10 bg-warm focus:border-pix/40 focus:ring-pix/20"
                        />
                        <InputError :message="form.errors.order_prefix" />
                    </div>
                    <div class="space-y-2">
                        <Label
                            for="quote_prefix"
                            class="text-sm font-semibold text-ink/80"
                            >Prefijo de cotizaciones</Label
                        >
                        <Input
                            id="quote_prefix"
                            v-model="form.quote_prefix"
                            :disabled="!canManage"
                            class="h-11 rounded-xl border-ink/10 bg-warm focus:border-pix/40 focus:ring-pix/20"
                        />
                        <InputError :message="form.errors.quote_prefix" />
                    </div>
                </div>
            </div>

            <div v-if="canManage" class="flex justify-end">
                <Button
                    type="submit"
                    :disabled="form.processing"
                    class="btn-gradient-blue btn-shimmer h-11 px-6"
                >
                    <Loader2
                        v-if="form.processing"
                        class="mr-2 h-4 w-4 animate-spin"
                    />
                    <Save v-else class="mr-2 h-4 w-4" />
                    Guardar cambios
                </Button>
            </div>
        </form>
    </div>
</template>
