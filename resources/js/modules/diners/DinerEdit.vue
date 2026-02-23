<template>
    <div class="bg-white rounded-xl shadow-sm p-6 relative">
        <LoadingOverlay :show="loading" message="Actualizando..." />
        <router-link
            :to="{ name: 'diners.index', params: { id: diningHallId } }"
            class="inline-block text-sm text-gray-600 hover:text-gray-900 mb-4"
        >
            ← Comensales
        </router-link>
        <div v-if="loadingData" class="text-gray-500">Cargando...</div>
        <template v-else-if="diner">
            <h1 class="text-xl font-semibold text-gray-900 mb-6">Editar Comensal</h1>
            <DinerForm
                :model-value="diner"
                :loading="loading"
                :errors="errors"
                @submit="handleSubmit"
                @cancel="goBack"
            />
        </template>
        <div v-else class="text-red-600">Comensal no encontrado.</div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import DinerForm from './DinerForm.vue';
import LoadingOverlay from '../../components/LoadingOverlay.vue';
import { dinerApi } from './api';

const router = useRouter();
const route = useRoute();
const diningHallId = computed(() => route.params.id);
const dinerId = computed(() => route.params.dinerId);

const diner = ref(null);
const loadingData = ref(true);
const loading = ref(false);
const errors = reactive({});

function goBack() {
    router.push({ name: 'diners.index', params: { id: diningHallId.value } });
}

async function fetchDiner() {
    loadingData.value = true;
    try {
        const { data } = await dinerApi.getById(diningHallId.value, dinerId.value);
        diner.value = data.data ?? null;
    } catch {
        diner.value = null;
    } finally {
        loadingData.value = false;
    }
}

async function handleSubmit(payload) {
    loading.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);
    try {
        await dinerApi.update(diningHallId.value, dinerId.value, payload);
        goBack();
    } catch (e) {
        Object.assign(errors, e.response?.data?.errors ?? {});
        if (!Object.keys(errors).length) {
            errors._general = e.response?.data?.message ?? 'Error al actualizar';
        }
    } finally {
        loading.value = false;
    }
}

onMounted(fetchDiner);
</script>
