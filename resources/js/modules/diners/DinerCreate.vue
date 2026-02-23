<template>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <router-link
            :to="{ name: 'diners.index', params: { id: diningHallId } }"
            class="inline-block text-sm text-gray-600 hover:text-gray-900 mb-4"
        >
            ← Comensales
        </router-link>
        <h1 class="text-xl font-semibold text-gray-900 mb-6">Nuevo Comensal</h1>
        <DinerForm
            :model-value="{ id_code: '', name: '' }"
            :loading="loading"
            :errors="errors"
            submit-label="Crear"
            @submit="handleSubmit"
            @cancel="goBack"
        />
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import DinerForm from './DinerForm.vue';
import { dinerApi } from './api';

const router = useRouter();
const route = useRoute();
const diningHallId = computed(() => route.params.id);

const loading = ref(false);
const errors = reactive({});

function goBack() {
    router.push({ name: 'diners.index', params: { id: diningHallId.value } });
}

async function handleSubmit(payload) {
    loading.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);
    try {
        await dinerApi.create(diningHallId.value, payload);
        goBack();
    } catch (e) {
        Object.assign(errors, e.response?.data?.errors ?? {});
        if (!Object.keys(errors).length) {
            errors._general = e.response?.data?.message ?? 'Error al crear';
        }
    } finally {
        loading.value = false;
    }
}
</script>
