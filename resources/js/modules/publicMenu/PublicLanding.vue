<template>
    <div class="min-h-screen bg-gray-50 px-4 py-6 pb-24">
        <div class="max-w-md mx-auto">
            <div v-if="loading" class="flex items-center justify-center py-24 transition-all duration-200">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin" />
                    <p class="text-base text-gray-500">Cargando...</p>
                </div>
            </div>
            <div v-else-if="error" class="transition-all duration-200">
                <div class="bg-white rounded-2xl shadow-sm p-5 mb-4">
                    <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-center">
                        <p class="text-red-700">{{ error }}</p>
                        <router-link
                            :to="{ name: 'public.identify', params: { code } }"
                            class="mt-4 inline-flex items-center justify-center w-full py-3 rounded-xl text-lg font-semibold bg-blue-600 text-white hover:bg-blue-700 active:scale-95 transition"
                        >
                            Reintentar
                        </router-link>
                    </div>
                </div>
            </div>
            <div v-else class="transition-all duration-200">
                <div class="bg-white rounded-2xl shadow-sm p-5 mb-4 text-center">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ hall?.name }}</h1>
                    <p v-if="hall?.description" class="text-gray-500 text-sm mb-8">{{ hall.description }}</p>
                    <router-link
                        :to="{ name: 'public.identify', params: { code } }"
                        class="inline-flex items-center justify-center w-full py-3 rounded-xl text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 transition"
                    >
                        Identificarse
                    </router-link>
                </div>
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
