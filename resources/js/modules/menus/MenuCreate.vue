<template>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div v-if="successMessage" class="mb-4 p-4 rounded-md bg-green-50 text-green-800 text-sm">
                    {{ successMessage }}
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Nuevo Menú</h1>
                <MenuForm
                    :loading="loading"
                    :errors="errors"
                    submit-label="Crear menú"
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
import MenuForm from './MenuForm.vue';
import { menuApi } from './api';

const router = useRouter();
const loading = ref(false);
const errors = reactive({});
const successMessage = ref('');

function goToList() {
    router.push({ name: 'menus.index' });
}

async function handleSubmit(payload) {
    loading.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);
    try {
        await menuApi.create(payload);
        successMessage.value = 'Menú creado correctamente.';
        setTimeout(goToList, 1500);
    } catch (e) {
        const validationErrors = e.response?.data?.errors ?? {};
        Object.assign(errors, validationErrors);
        if (!Object.keys(validationErrors).length) {
            errors._general = e.response?.data?.message ?? 'Error al crear el menú';
        }
    } finally {
        loading.value = false;
    }
}
</script>
