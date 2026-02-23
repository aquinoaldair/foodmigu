<template>
    <div class="bg-white rounded-xl shadow-sm p-6 relative">
        <LoadingOverlay :show="loading" message="Actualizando..." />
        <div v-if="successMessage" class="mb-4 p-4 rounded-md bg-green-50 text-green-800 text-sm">
            {{ successMessage }}
        </div>
        <div v-if="loadingData" class="text-gray-500">
            Cargando...
        </div>
        <template v-else-if="hall">
            <h1 class="text-xl font-semibold text-gray-900 mb-6">Editar Comedor</h1>
            <DiningHallForm
                :model-value="hall"
                :loading="loading"
                :errors="errors"
                mode="edit"
                @submit="handleSubmit"
                @cancel="goToList"
            />
        </template>
        <div v-else class="text-red-600">
            Comedor no encontrado.
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import DiningHallForm from './DiningHallForm.vue';
import LoadingOverlay from '../../components/LoadingOverlay.vue';
import { diningHallApi } from './api';

const router = useRouter();
const route = useRoute();

const hall = ref(null);
const loadingData = ref(true);
const loading = ref(false);
const errors = reactive({});
const successMessage = ref('');

const hallId = computed(() => route.params.id);

function goToList() {
    router.push({ name: 'dining-halls.index' });
}

async function fetchHall() {
    loadingData.value = true;
    try {
        const { data } = await diningHallApi.getById(hallId.value);
        hall.value = data.data;
    } catch {
        hall.value = null;
    } finally {
        loadingData.value = false;
    }
}

async function handleSubmit(payload) {
    loading.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);
    try {
        await diningHallApi.update(hallId.value, payload);
        successMessage.value = 'Comedor actualizado correctamente.';
        setTimeout(goToList, 1500);
    } catch (e) {
        const validationErrors = e.response?.data?.errors ?? {};
        Object.assign(errors, validationErrors);
        if (!Object.keys(validationErrors).length) {
            errors._general = e.response?.data?.message ?? 'Error al actualizar el comedor';
        }
    } finally {
        loading.value = false;
    }
}

onMounted(fetchHall);
</script>
