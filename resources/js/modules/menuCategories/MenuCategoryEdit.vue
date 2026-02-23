<template>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div v-if="successMessage" class="mb-4 p-4 rounded-md bg-green-50 text-green-800 text-sm">
                    {{ successMessage }}
                </div>
                <div v-if="loadingData" class="text-gray-500">
                    Cargando...
                </div>
                <template v-else-if="category">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">Editar Categoría</h1>
                    <MenuCategoryForm
                        :model-value="category"
                        :loading="loading"
                        :errors="errors"
                        @submit="handleSubmit"
                        @cancel="goToList"
                    />
                </template>
                <div v-else class="text-red-600">
                    Categoría no encontrada.
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import MenuCategoryForm from './MenuCategoryForm.vue';
import { menuCategoryApi } from './api';

const router = useRouter();
const route = useRoute();

const category = ref(null);
const loadingData = ref(true);
const loading = ref(false);
const errors = reactive({});
const successMessage = ref('');

const categoryId = computed(() => route.params.id);

function goToList() {
    router.push({ name: 'menu-categories.index' });
}

async function fetchCategory() {
    loadingData.value = true;
    try {
        const { data } = await menuCategoryApi.getById(categoryId.value);
        category.value = data.data;
    } catch {
        category.value = null;
    } finally {
        loadingData.value = false;
    }
}

async function handleSubmit(payload) {
    loading.value = true;
    errors._general = '';
    Object.keys(errors).forEach((k) => delete errors[k]);
    try {
        await menuCategoryApi.update(categoryId.value, payload);
        successMessage.value = 'Categoría actualizada correctamente.';
        setTimeout(goToList, 1500);
    } catch (e) {
        const validationErrors = e.response?.data?.errors ?? {};
        Object.assign(errors, validationErrors);
        if (!Object.keys(validationErrors).length) {
            errors._general = e.response?.data?.message ?? 'Error al actualizar la categoría';
        }
    } finally {
        loading.value = false;
    }
}

onMounted(fetchCategory);
</script>
