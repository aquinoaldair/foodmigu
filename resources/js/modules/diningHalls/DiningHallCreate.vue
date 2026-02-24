<template>
    <div class="bg-white rounded-xl shadow-sm p-6 relative">
        <LoadingOverlay :show="loading" message="Guardando..." />
        <div v-if="successMessage" class="mb-4 p-4 rounded-md bg-green-50 text-green-800 text-sm">
            {{ successMessage }}
        </div>
        <h1 class="text-xl font-semibold text-gray-900 mb-6">Nuevo Comedor</h1>
        <DiningHallForm
            :model-value="initialValues"
            :loading="loading"
            :errors="errors"
            submit-label="Crear comedor"
            @submit="handleSubmit"
            @cancel="goToList"
        />
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import DiningHallForm from './DiningHallForm.vue';
import LoadingOverlay from '../../components/LoadingOverlay.vue';
import { diningHallApi } from './api';

const router = useRouter();

const initialValues = {
    name: '',
    description: '',
    is_active: true,
};

const loading = ref(false);
const errors = reactive({});
const successMessage = ref('');

function goToList() {
    router.push({ name: 'dining-halls.index' });
}

async function handleSubmit(payload) {
    loading.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);
    try {
        await diningHallApi.create(payload);
        successMessage.value = 'Comedor creado correctamente.';
        setTimeout(goToList, 1500);
    } catch (e) {
        const validationErrors = e.response?.data?.errors ?? {};
        Object.assign(errors, validationErrors);
        if (!Object.keys(validationErrors).length) {
            errors._general = e.response?.data?.message ?? 'Error al crear el comedor';
        }
    } finally {
        loading.value = false;
    }
}
</script>
