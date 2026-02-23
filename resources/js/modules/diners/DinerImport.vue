<template>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <router-link
            :to="{ name: 'diners.index', params: { id: diningHallId } }"
            class="inline-block text-sm text-gray-600 hover:text-gray-900 mb-4"
        >
            ← Comensales
        </router-link>
        <h1 class="text-xl font-semibold text-gray-900 mb-6">Importar Excel</h1>

        <div class="max-w-xl space-y-6">
            <div class="rounded-lg border border-gray-200 p-4 bg-gray-50/50">
                <p class="text-sm text-gray-600 mb-2">
                    El archivo debe tener dos columnas: <strong>ID</strong> y <strong>Nombre</strong>. 
                    Se saltará la primera fila (encabezados). Formatos aceptados: .xlsx, .csv
                </p>
                <ul class="text-xs text-gray-500 list-disc list-inside">
                    <li>Si el ID ya existe → se actualiza el nombre</li>
                    <li>Si no existe → se crea nuevo comensal</li>
                </ul>
            </div>

            <form @submit.prevent="handleImport" class="space-y-4">
                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700">Archivo</label>
                    <input
                        id="file"
                        ref="fileInputRef"
                        type="file"
                        accept=".xlsx,.csv"
                        class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        :class="{ 'border-red-500': errors.file }"
                        @change="onFileChange"
                    />
                    <p v-if="errors.file" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.file) ? errors.file[0] : errors.file }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="loading || !selectedFile"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ loading ? 'Importando...' : 'Importar' }}
                </button>
            </form>

            <div v-if="result" class="p-4 rounded-lg bg-green-50 border border-green-200">
                <h3 class="text-sm font-medium text-green-800">Importación completada</h3>
                <p class="mt-2 text-sm text-green-700">
                    Registros creados: <strong>{{ result.created }}</strong><br />
                    Registros actualizados: <strong>{{ result.updated }}</strong>
                </p>
            </div>

            <div v-if="error" class="p-4 rounded-lg bg-red-50 border border-red-200">
                <p class="text-sm text-red-700">{{ error }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { useRoute } from 'vue-router';
import { dinerApi } from './api';

const route = useRoute();
const diningHallId = computed(() => route.params.id);

const fileInputRef = ref(null);
const selectedFile = ref(null);
const loading = ref(false);
const errors = reactive({});
const result = ref(null);
const error = ref(null);

function onFileChange(e) {
    const file = e.target?.files?.[0];
    selectedFile.value = file ?? null;
    errors.file = '';
    result.value = null;
    error.value = null;
}

async function handleImport() {
    if (!selectedFile.value) {
        errors.file = 'Selecciona un archivo';
        return;
    }

    loading.value = true;
    errors.file = '';
    result.value = null;
    error.value = null;

    try {
        const formData = new FormData();
        formData.append('file', selectedFile.value);

        const { data } = await dinerApi.import(diningHallId.value, formData);
        result.value = { created: data.created ?? 0, updated: data.updated ?? 0 };
        selectedFile.value = null;
        if (fileInputRef.value) fileInputRef.value.value = '';
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Error al importar el archivo';
        Object.assign(errors, e.response?.data?.errors ?? {});
    } finally {
        loading.value = false;
    }
}
</script>
