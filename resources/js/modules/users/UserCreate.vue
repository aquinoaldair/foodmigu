<template>
    <div class="bg-white rounded-xl shadow-sm p-6 relative">
        <LoadingOverlay :show="loading" message="Guardando..." />
        <div v-if="successMessage" class="mb-4 p-4 rounded-md bg-green-50 text-green-800 text-sm">
            {{ successMessage }}
        </div>
        <h1 class="text-xl font-semibold text-gray-900 mb-6">Nuevo Usuario</h1>
        <UserForm
            :model-value="initialValues"
            :loading="loading"
            :errors="errors"
            submit-label="Crear Usuario"
            @submit="handleSubmit"
            @cancel="goToList"
        />
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import UserForm from './UserForm.vue';
import LoadingOverlay from '../../components/LoadingOverlay.vue';
import { userApi } from './api';

const router = useRouter();

const initialValues = {
    name: '',
    email: '',
    password: '',
    role: 'user',
    dining_halls: [],
};

const loading = ref(false);
const errors = reactive({});
const successMessage = ref('');

function goToList() {
    router.push({ name: 'users.index' });
}

async function handleSubmit(payload) {
    loading.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);
    try {
        await userApi.create(payload);
        successMessage.value = 'Usuario creado correctamente.';
        setTimeout(goToList, 1500);
    } catch (e) {
        const validationErrors = e.response?.data?.errors ?? {};
        Object.assign(errors, validationErrors);
        if (!Object.keys(validationErrors).length) {
            errors._general = e.response?.data?.message ?? 'Error al crear el usuario';
        }
    } finally {
        loading.value = false;
    }
}
</script>
