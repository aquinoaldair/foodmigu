<template>
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        <div v-if="loading" class="flex items-center justify-center py-24">
            <p class="text-lg text-gray-500">Cargando...</p>
        </div>
        <div v-else-if="error" class="max-w-md mx-auto">
            <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
                <p class="text-red-700">{{ error }}</p>
                <router-link
                    :to="{ name: 'public.identify', params: { code } }"
                    class="mt-4 inline-block px-6 py-3 text-sm font-medium text-red-800 underline"
                >
                    Reintentar
                </router-link>
            </div>
        </div>
        <div v-else class="max-w-lg mx-auto">
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ hall?.name }}</h1>
                <p v-if="hall?.description" class="text-gray-600 mb-8">{{ hall.description }}</p>
                <router-link
                    :to="{ name: 'public.identify', params: { code } }"
                    class="inline-flex items-center justify-center w-full py-4 px-6 text-lg font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Identificarse
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { publicMenuApi } from './api';

const route = useRoute();
const code = computed(() => route.params.code);

const hall = ref(null);
const loading = ref(true);
const error = ref(null);

async function fetchHall() {
    loading.value = true;
    error.value = null;
    try {
        const { data } = await publicMenuApi.hall(code.value);
        hall.value = data;
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Comedor no encontrado';
    } finally {
        loading.value = false;
    }
}

onMounted(fetchHall);
</script>
