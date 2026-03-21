<template>
    <div class="bg-white rounded-xl shadow-sm p-6 relative">
        <LoadingOverlay :show="loading || loadingInitial" message="Cargando..." />
        <div v-if="successMessage" class="mb-4 p-4 rounded-md bg-green-50 text-green-800 text-sm">
            {{ successMessage }}
        </div>
        <div v-if="error" class="mb-4 p-4 rounded-md bg-red-50 text-red-800 text-sm">
            {{ error }}
        </div>
        <h1 class="text-xl font-semibold text-gray-900 mb-6">Editar Usuario</h1>
        <UserForm
            v-if="!loadingInitial && initialValues"
            :model-value="initialValues"
            :loading="loading"
            :errors="errors"
            :is-editing="true"
            submit-label="Guardar Cambios"
            @submit="handleSubmit"
            @cancel="goToList"
        />
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import UserForm from './UserForm.vue';
import LoadingOverlay from '../../components/LoadingOverlay.vue';
import { userApi } from './api';

const router = useRouter();
const route = useRoute();

const initialValues = ref(null);
const loadingInitial = ref(true);
const loading = ref(false);
const errors = reactive({});
const successMessage = ref('');
const error = ref('');

function goToList() {
    router.push({ name: 'users.index' });
}

async function fetchUser() {
    try {
        const { data } = await userApi.getById(route.params.id);
        const user = data.data;
        initialValues.value = {
            name: user.name,
            email: user.email,
            password: '',
            role: user.roles?.[0]?.name ?? 'user',
            dining_halls: (user.dining_halls ?? []).map(h => h.id),
        };
    } catch (e) {
        error.value = 'Error al cargar el usuario.';
    } finally {
        loadingInitial.value = false;
    }
}

async function handleSubmit(payload) {
    loading.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);
    try {
        await userApi.update(route.params.id, payload);
        successMessage.value = 'Usuario actualizado correctamente.';
        setTimeout(goToList, 1500);
    } catch (e) {
        const validationErrors = e.response?.data?.errors ?? {};
        Object.assign(errors, validationErrors);
        if (!Object.keys(validationErrors).length) {
            errors._general = e.response?.data?.message ?? 'Error al actualizar el usuario';
        }
    } finally {
        loading.value = false;
    }
}

onMounted(fetchUser);
</script>
