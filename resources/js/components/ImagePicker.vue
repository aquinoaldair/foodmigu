<template>
    <div class="space-y-2">
        <div class="flex items-center gap-2">
            <div
                v-if="modelValue"
                class="relative w-20 h-20 rounded-lg overflow-hidden border border-gray-200 bg-gray-50 flex-shrink-0"
            >
                <img
                    v-if="effectivePreviewUrl"
                    :src="effectivePreviewUrl"
                    alt="Preview"
                    class="w-full h-full object-cover"
                    @error="onPreviewError"
                />
                <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-xs">
                    Imagen
                </div>
                <button
                    v-if="!readonly"
                    type="button"
                    @click="clearSelection"
                    class="absolute top-1 right-1 p-1 rounded-full bg-red-500 text-white hover:bg-red-600 text-xs"
                    aria-label="Quitar imagen"
                >
                    ✕
                </button>
            </div>
            <div v-else class="w-20 h-20 rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center flex-shrink-0">
                <span class="text-gray-400 text-xs">Sin imagen</span>
            </div>
            <div v-if="!readonly" class="flex gap-2">
                <label class="cursor-pointer">
                    <input
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        class="sr-only"
                        @change="onFileSelect"
                    />
                    <span
                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                    >
                        {{ uploading ? 'Subiendo...' : 'Subir' }}
                    </span>
                </label>
                <button
                    type="button"
                    @click="showGallery = true"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
                >
                    Galería
                </button>
            </div>
        </div>
        <p v-if="uploadError" class="text-sm text-red-600">{{ uploadError }}</p>

        <Teleport to="body">
            <div
                v-if="showGallery"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
                @click.self="showGallery = false"
            >
                <div class="absolute inset-0 bg-gray-900/60" />
                <div class="relative z-10 bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] flex flex-col">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Seleccionar imagen</h3>
                        <button
                            type="button"
                            @click="showGallery = false"
                            class="p-1 text-gray-500 hover:text-gray-700"
                            aria-label="Cerrar"
                        >
                            ✕
                        </button>
                    </div>
                    <div class="p-4 overflow-y-auto flex-1">
                        <div v-if="loadingGallery" class="text-center py-8 text-gray-500">
                            Cargando imágenes...
                        </div>
                        <div v-else-if="galleryImages.length === 0" class="text-center py-8 text-gray-500">
                            No hay imágenes en la galería. Sube una nueva.
                        </div>
                        <div v-else class="grid grid-cols-4 sm:grid-cols-5 gap-3">
                            <button
                                v-for="img in galleryImages"
                                :key="img.id"
                                type="button"
                                @click="selectFromGallery(img)"
                                class="aspect-square rounded-lg overflow-hidden border-2 transition-colors"
                                :class="modelValue === img.id ? 'border-blue-600 ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300'"
                            >
                                <img
                                    :src="img.url"
                                    :alt="img.name || 'Imagen'"
                                    class="w-full h-full object-cover"
                                />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { imageApi } from '../modules/images/api';

const props = defineProps({
    modelValue: { type: [Number, null], default: null },
    previewUrl: { type: String, default: '' },
    readonly: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue', 'update:previewUrl']);

const showGallery = ref(false);
const galleryImages = ref([]);
const loadingGallery = ref(false);
const uploading = ref(false);
const uploadError = ref('');

const effectivePreviewUrl = computed(() => props.previewUrl || '');

watch(showGallery, (visible) => {
    if (visible) fetchGallery();
});

async function fetchGallery() {
    loadingGallery.value = true;
    uploadError.value = '';
    try {
        const { data } = await imageApi.getAll({ per_page: 48 });
        const payload = data.data ?? data;
        galleryImages.value = Array.isArray(payload) ? payload : (payload?.data ?? []);
    } catch {
        galleryImages.value = [];
    } finally {
        loadingGallery.value = false;
    }
}

function selectFromGallery(img) {
    emit('update:modelValue', img.id);
    emit('update:previewUrl', img.url ?? `/storage/${img.path}`);
    showGallery.value = false;
}

function clearSelection() {
    emit('update:modelValue', null);
    emit('update:previewUrl', '');
}

function onPreviewError() {
    // Silently ignore - preview might be invalid
}

async function onFileSelect(event) {
    const file = event.target.files?.[0];
    if (!file) return;

    uploadError.value = '';
    uploading.value = true;
    try {
        const { data } = await imageApi.upload(file);
        const img = data.data;
        emit('update:modelValue', img.id);
        emit('update:previewUrl', img.url ?? `/storage/${img.path}`);
    } catch (err) {
        const msg = err.response?.data?.errors?.image?.[0]
            ?? err.response?.data?.message
            ?? 'Error al subir la imagen.';
        uploadError.value = msg;
    } finally {
        uploading.value = false;
        event.target.value = '';
    }
}
</script>
