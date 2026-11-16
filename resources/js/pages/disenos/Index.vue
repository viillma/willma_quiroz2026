<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Upload, CheckCircle, Download, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

import { dashboard } from '@/routes';
import disenos from '@/routes/disenos';
import pedidos from '@/routes/pedidos';

const page = usePage<{
    order: {
        id: number;
        code: string;
        client: { id: number; name: string } | null;
        designFiles: Array<{
            id: number;
            version: number;
            file_path: string;
            file_type: string;
            comments: string;
            is_approved: boolean;
            uploader: { id: number; name: string };
            created_at: string;
        }>;
    };
}>();

defineOptions({
    layout: () => ({
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard().url },
            { title: 'Pedidos', href: pedidos.index().url },
            { title: 'Diseños', href: '#' },
        ],
    }),
});
const order = computed(() => page.props.order);

const fileInput = ref<HTMLInputElement | null>(null);
const uploading = ref(false);

function uploadDesign() {
    const file = fileInput.value?.files?.[0];
    const comments =
        (document.getElementById('comments') as HTMLTextAreaElement | null)
            ?.value ?? '';

    if (!file) {
        return;
    }

    uploading.value = true;
    const formData = new FormData();
    formData.append('file', file);
    formData.append('comments', comments);
    router.post(disenos.store.url({ order: order.value.id }), formData, {
        onFinish: () => {
            uploading.value = false;
        },
    });
}
</script>

<template>
    <Head :title="'Diseños - ' + order.code" />

    <div class="animate-fade-in relative z-10 space-y-6 p-4 md:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div class="animate-slide-up">
                <Heading
                    :title="'Diseños - ' + order.code"
                    :description="
                        'Cliente: ' + (order.client?.name || 'Sin cliente')
                    "
                />
            </div>
            <div class="flex gap-2">
                <Button
                    variant="ghost"
                    size="sm"
                    class="btn-gradient-ghost"
                    as-child
                >
                    <Link :href="pedidos.show({ pedido: order.id }).url">
                        Volver al Pedido
                    </Link>
                </Button>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="glass-card animate-scale-in rounded-2xl p-6">
                    <div class="mb-6 flex items-center gap-3">
                        <div
                            class="h-8 w-1 rounded-full bg-gradient-to-b from-blue-400 to-purple-500"
                        ></div>
                        <h3 class="text-lg font-semibold text-ink">
                            Archivos de Diseño
                        </h3>
                    </div>

                    <div
                        v-if="order.designFiles.length === 0"
                        class="glass-card rounded-2xl p-8 text-center"
                    >
                        <p class="text-ink/50">
                            No hay archivos de diseño subidos aún.
                        </p>
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="file in order.designFiles"
                            :key="file.id"
                            class="glass card-glow animate-fade-in flex items-center justify-between rounded-xl p-4"
                            :class="{ 'border border-mint': file.is_approved }"
                        >
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-ink"
                                        >Versión {{ file.version }}</span
                                    >
                                    <Badge
                                        variant="outline"
                                        class="badge-cyan"
                                        >{{
                                            file.file_type?.toUpperCase()
                                        }}</Badge
                                    >
                                    <Badge
                                        v-if="file.is_approved"
                                        class="badge-green"
                                        >Aprobado</Badge
                                    >
                                </div>
                                <p
                                    v-if="file.comments"
                                    class="mt-1 text-sm text-ink/60"
                                >
                                    {{ file.comments }}
                                </p>
                                <p class="mt-1 text-xs text-ink/40">
                                    Subido por
                                    {{ file.uploader?.name || 'N/A' }} -
                                    {{
                                        new Date(
                                            file.created_at,
                                        ).toLocaleString()
                                    }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="btn-gradient-ghost"
                                    as-child
                                >
                                    <a
                                        :href="
                                            disenos.download.url({
                                                order: order.id,
                                                designFile: file.id,
                                            })
                                        "
                                        target="_blank"
                                    >
                                        <Download class="h-4 w-4" />
                                    </a>
                                </Button>
                                <Button
                                    v-if="!file.is_approved"
                                    variant="ghost"
                                    size="sm"
                                    class="btn-gradient-ghost glow-green"
                                    @click="
                                        router.post(
                                            disenos.approve.url({
                                                order: order.id,
                                                designFile: file.id,
                                            }),
                                        )
                                    "
                                >
                                    <CheckCircle class="h-4 w-4" />
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="btn-gradient-ghost text-red-500 hover:text-red-600"
                                    @click="
                                        router.delete(
                                            disenos.destroy.url({
                                                order: order.id,
                                                designFile: file.id,
                                            }),
                                        )
                                    "
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="glass-card animate-slide-up rounded-2xl p-6">
                    <div class="mb-6 flex items-center gap-3">
                        <div
                            class="h-8 w-1 rounded-full bg-gradient-to-b from-purple-400 to-pink-500"
                        ></div>
                        <h3 class="text-lg font-semibold text-ink">
                            Subir Nuevo Diseño
                        </h3>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label for="file" class="text-ink/80"
                                >Archivo *</Label
                            >
                            <Input
                                ref="fileInput"
                                id="file"
                                name="file"
                                type="file"
                                required
                                accept=".ai,.psd,.pdf,.png,.jpg,.jpeg,.svg,.eps"
                                class="glass"
                            />
                            <p class="text-xs text-ink/40">
                                Formatos: AI, PSD, PDF, PNG, JPG, SVG, EPS (máx.
                                50 MB)
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="comments" class="text-ink/80"
                                >Comentarios</Label
                            >
                            <Textarea
                                id="comments"
                                name="comments"
                                rows="3"
                                placeholder="Describe esta versión..."
                                class="glass"
                            />
                        </div>

                        <Button
                            class="btn-gradient-blue btn-shimmer w-full"
                            :disabled="uploading"
                            @click="uploadDesign"
                        >
                            <Upload class="mr-2 h-4 w-4" /> Subir Diseño
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
