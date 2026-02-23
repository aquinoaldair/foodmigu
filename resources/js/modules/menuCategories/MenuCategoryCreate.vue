<template>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6 relative">
                <LoadingOverlay :show="loading" message="Guardando..." />
                <div v-if="successMessage" class="mb-4 p-4 rounded-md bg-green-50 text-green-800 text-sm">
                    {{ successMessage }}
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Nueva Categoría</h1>
                <MenuCategoryForm
                    :model-value="initialValues"
                    :loading="loading"
                    :errors="errors"
                    submit-label="Crear categoría"
                    @submit="handleSubmit"
                    @cancel="goToList"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import MenuCategoryForm from './MenuCategoryForm.vue';
import LoadingOverlay from '../../components/LoadingOverlay.vue';
import { menuCategoryApi } from './api';

const router = useRouter();

const initialValues = {
    name: '',
    selection_type: 'none',
    is_required: true,
    is_active: true,
    display_order: 0,
};

const loading = ref(false);
const errors = reactive({});
const successMessage = ref('');

function goToList() {
    router.push({ name: 'menu-categories.index' });
}

async function handleSubmit(payload) {
    loading.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);
    try {
        await menuCategoryApi.create(payload);
        successMessage.value = 'Categoría creada correctamente.';
        setTimeout(goToList, 1500);
    } catch (e) {
        const validationErrors = e.response?.data?.errors ?? {};
        Object.assign(errors, validationErrors);
        if (!Object.keys(validationErrors).length) {
            errors._general = e.response?.data?.message ?? 'Error al crear la categoría';
        }
    } finally {
        loading.value = false;
    }
}
</script>
