<template>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div v-if="successMessage" class="mb-4 p-4 rounded-md bg-green-50 text-green-800 text-sm">
                    {{ successMessage }}
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Editar Menú</h1>
                <div v-if="loadingData" class="text-gray-500">
                    Cargando...
                </div>
                <template v-else-if="menu">
                    <MenuForm
                        :model-value="menu"
                        :loading="loading"
                        :errors="errors"
                        @submit="handleSubmit"
                        @cancel="goToList"
                    />
                </template>
                <div v-else class="text-red-600">
                    Menú no encontrado.
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import MenuForm from './MenuForm.vue';
import { menuApi } from './api';

const router = useRouter();
const route = useRoute();

const menu = ref(null);
const loadingData = ref(true);
const loading = ref(false);
const errors = reactive({});
const successMessage = ref('');

const menuId = computed(() => route.params.id);

function goToList() {
    router.push({ name: 'menus.index' });
}

async function fetchMenu() {
    loadingData.value = true;
    try {
        const { data } = await menuApi.getById(menuId.value);
        menu.value = data.data;
    } catch {
        menu.value = null;
    } finally {
        loadingData.value = false;
    }
}

async function handleSubmit(payload) {
    loading.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);
    try {
        await menuApi.update(menuId.value, payload);
        successMessage.value = 'Menú actualizado correctamente.';
        setTimeout(goToList, 1500);
    } catch (e) {
        const validationErrors = e.response?.data?.errors ?? {};
        Object.assign(errors, validationErrors);
        if (!Object.keys(validationErrors).length) {
            errors._general = e.response?.data?.message ?? 'Error al actualizar el menú';
        }
    } finally {
        loading.value = false;
    }
}

onMounted(fetchMenu);
</script>
