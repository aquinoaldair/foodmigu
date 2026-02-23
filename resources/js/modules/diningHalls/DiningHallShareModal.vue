<template>
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
        role="dialog"
        aria-modal="true"
        aria-labelledby="share-modal-title"
        @click.self="$emit('close')"
    >
        <div class="bg-white rounded-2xl shadow-xl p-6 max-w-md w-full">
            <div class="flex justify-between items-center mb-6">
                <h2 id="share-modal-title" class="text-xl font-bold text-gray-900">Compartir comedor</h2>
                <button
                    type="button"
                    @click="$emit('close')"
                    class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100"
                    aria-label="Cerrar"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div v-if="loading" class="py-8 text-center text-gray-500">
                Cargando...
            </div>
            <template v-else>
                <!-- Sección 1: Enlace público -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Enlace público</label>
                    <div class="flex gap-2">
                        <input
                            :value="publicUrl"
                            type="text"
                            readonly
                            class="flex-1 px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 text-sm"
                        />
                        <button
                            type="button"
                            @click="copyUrl"
                            :class="[
                                'shrink-0 px-4 py-3 rounded-xl text-sm font-medium transition-colors',
                                copied ? 'bg-green-600 text-white' : 'bg-blue-600 text-white hover:bg-blue-700'
                            ]"
                        >
                            {{ copied ? 'Copiado' : 'Copiar enlace' }}
                        </button>
                    </div>
                </div>

                <!-- Sección 2: Código QR -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Código QR</label>
                    <div class="flex justify-center p-6 bg-gray-50 rounded-xl">
                        <canvas ref="qrCanvasRef" />
                    </div>
                </div>

                <!-- Botón Descargar QR -->
                <button
                    type="button"
                    @click="downloadQr"
                    class="w-full py-3 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors"
                >
                    Descargar QR
                </button>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue';
import QRCode from 'qrcode';
import { diningHallApi } from './api';

const props = defineProps({
    diningHall: { type: Object, required: true },
});

defineEmits(['close']);

const publicUrl = ref('');
const loading = ref(true);
const copied = ref(false);
const qrCanvasRef = ref(null);

async function fetchPublicUrl() {
    if (!props.diningHall?.id) return;
    loading.value = true;
    try {
        const { data } = await diningHallApi.getPublicUrl(props.diningHall.id);
        publicUrl.value = data.data?.url ?? '';
    } catch {
        publicUrl.value = '';
    } finally {
        loading.value = false;
    }
}

async function renderQr() {
    if (!publicUrl.value || !qrCanvasRef.value) return;
    try {
        await QRCode.toCanvas(qrCanvasRef.value, publicUrl.value, {
            width: 200,
            margin: 2,
        });
    } catch {
        // Ignore
    }
}

async function copyUrl() {
    if (!publicUrl.value) return;
    try {
        await navigator.clipboard.writeText(publicUrl.value);
        copied.value = true;
        setTimeout(() => { copied.value = false; }, 2000);
    } catch {
        // Fallback
    }
}

function downloadQr() {
    if (!qrCanvasRef.value || !publicUrl.value) return;
    const code = props.diningHall?.code ?? 'comedor';
    const link = document.createElement('a');
    link.download = `qr_comedor_${code}.png`;
    link.href = qrCanvasRef.value.toDataURL('image/png');
    link.click();
}

watch(() => props.diningHall?.id, () => {
    if (props.diningHall?.id) fetchPublicUrl();
});

watch(publicUrl, async (url) => {
    if (url) {
        await nextTick();
        renderQr();
    }
});

onMounted(() => {
    fetchPublicUrl();
});
</script>
