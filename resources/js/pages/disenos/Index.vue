<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Upload, CheckCircle, Download, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import pedidos from '@/routes/pedidos';
import disenos from '@/routes/disenos';
import { dashboard } from '@/routes';
import type { Team } from '@/types';

const page = usePage<{
    currentTeam?: Team | null;
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
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            { title: 'Dashboard', href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/' },
            { title: 'Pedidos', href: props.currentTeam ? pedidos.index(props.currentTeam.slug) : '#' },
            { title: 'Diseños', href: '#' },
        ],
    }),
});

const currentTeam = page.props.currentTeam as Team | null;
const order = computed(() => page.props.order);

const fileInput = ref<HTMLInputElement | null>(null);
const uploading = ref(false);
</script>

<template>
    <Head :title="'Diseños - ' + order.code" />

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 space-y-6 relative z-10">
        <div class="flex items-center justify-between">
            <div class="animate-slide-up">
                <Heading
                    :title="'Diseños - ' + order.code"
                    :description="'Cliente: ' + (order.client?.name || 'Sin cliente')"
                />
            </div>
            <div class="flex gap-2">
                <Button v-if="currentTeam" variant="ghost" size="sm" class="btn-gradient-ghost" as-child>
                    <Link :href="pedidos.show({ current_team: currentTeam.slug, pedido: order.id }).url">
                        Volver al Pedido
                    </Link>
                </Button>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="glass-card rounded-2xl p-6 animate-scale-in">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-8 w-1 bg-gradient-to-b from-blue-400 to-purple-500 rounded-full"></div>
                        <h3 class="text-lg font-semibold text-white">Archivos de Diseño</h3>
                    </div>

                    <div v-if="order.designFiles.length === 0" class="glass-card rounded-2xl p-8 text-center">
                        <p class="text-white/50">No hay archivos de diseño subidos aún.</p>
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="file in order.designFiles"
                            :key="file.id"
                            class="glass rounded-xl p-4 card-glow flex items-center justify-between animate-fade-in"
                            :class="{ 'border border-green-500/30': file.is_approved }"
                        >
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-white">Versión {{ file.version }}</span>
                                    <Badge variant="outline" class="badge-cyan">{{ file.file_type?.toUpperCase() }}</Badge>
                                    <Badge v-if="file.is_approved" class="badge-green">Aprobado</Badge>
                                </div>
                                <p v-if="file.comments" class="mt-1 text-sm text-white/60">{{ file.comments }}</p>
                                <p class="mt-1 text-xs text-white/40">
                                    Subido por {{ file.uploader?.name || 'N/A' }} - {{ new Date(file.created_at).toLocaleString() }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <Button
                                    v-if="currentTeam"
                                    variant="ghost"
                                    size="sm"
                                    class="btn-gradient-ghost"
                                    as-child
                                >
                                    <a
                                        :href="disenos.download.url({ current_team: currentTeam.slug, order: order.id, designFile: file.id })"
                                        target="_blank"
                                    >
                                        <Download class="h-4 w-4" />
                                    </a>
                                </Button>
                                <Button
                                    v-if="currentTeam && !file.is_approved"
                                    variant="ghost"
                                    size="sm"
                                    class="btn-gradient-ghost glow-green"
                                    @click="router.post(disenos.approve.url({ current_team: currentTeam.slug, order: order.id, designFile: file.id }))"
                                >
                                    <CheckCircle class="h-4 w-4" />
                                </Button>
                                <Button
                                    v-if="currentTeam"
                                    variant="ghost"
                                    size="sm"
                                    class="btn-gradient-ghost text-red-400 hover:text-red-300"
                                    @click="router.delete(disenos.destroy.url({ current_team: currentTeam.slug, order: order.id, designFile: file.id }))"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="glass-card rounded-2xl p-6 animate-slide-up">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-8 w-1 bg-gradient-to-b from-purple-400 to-pink-500 rounded-full"></div>
                        <h3 class="text-lg font-semibold text-white">Subir Nuevo Diseño</h3>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label for="file" class="text-white/80">Archivo *</Label>
                            <Input
                                ref="fileInput"
                                id="file"
                                name="file"
                                type="file"
                                required
                                accept=".ai,.psd,.pdf,.png,.jpg,.jpeg,.svg,.eps"
                                class="glass"
                            />
                            <p class="text-xs text-white/40">
                                Formatos: AI, PSD, PDF, PNG, JPG, SVG, EPS (máx. 50 MB)
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="comments" class="text-white/80">Comentarios</Label>
                            <Textarea id="comments" name="comments" rows="3" placeholder="Describe esta versión..." class="glass" />
                        </div>

                        <Button
                            class="btn-gradient-blue w-full btn-shimmer"
                            :disabled="uploading"
                            @click="() => {
                                const input = fileInput;
                                const file = input?.files?.[0];
                                const comments = (document.getElementById('comments') as HTMLTextAreaElement)?.value;
                                if (!file || !currentTeam) return;
                                uploading = true;
                                const formData = new FormData();
                                formData.append('file', file);
                                formData.append('comments', comments || '');
                                router.post(
                                    disenos.store.url({ current_team: currentTeam.slug, order: order.id }),
                                    formData,
                                    { onFinish: () => { uploading = false; } },
                                );
                            }"
                        >
                            <Upload class="mr-2 h-4 w-4" /> Subir Diseño
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
