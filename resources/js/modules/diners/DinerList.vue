<template>
    <div class="bg-white rounded-xl shadow-sm p-6 relative">
        <LoadingOverlay :show="deleting" message="Eliminando..." />
        <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
            <div class="flex items-center gap-3">
                <router-link
                    :to="{ name: 'dining-halls.index' }"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Comedores
                </router-link>
                <h1 class="text-xl font-semibold text-gray-900">Comensales — {{ diningHall?.name ?? '...' }}</h1>
            </div>
            <div class="flex gap-2">
                <router-link
                    :to="{ name: 'diners.import', params: { id: diningHallId } }"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                    Importar Excel
                </router-link>
                <router-link
                    :to="{ name: 'diners.create', params: { id: diningHallId } }"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                >
                    Nuevo Comensal
                </router-link>
            </div>
        </div>

        <div class="mb-4">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Buscar por ID o Nombre..."
                class="block w-full max-w-md px-4 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                @input="onSearchInput"
            />
        </div>

        <div v-if="loading" class="py-12 text-center text-gray-500">Cargando...</div>
        <div v-else-if="error" class="py-6">
            <p class="text-red-600">{{ error }}</p>
        </div>
        <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="diner in diners" :key="diner.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ diner.id_code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ diner.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <router-link
                                :to="{ name: 'diners.edit', params: { id: diningHallId, dinerId: diner.id } }"
                                class="text-blue-600 hover:text-blue-900 mr-4"
                            >
                                Editar
                            </router-link>
                            <button type="button" @click="confirmDelete(diner)" class="text-red-600 hover:text-red-900">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!diners.length">
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                            {{ searchQuery ? 'No hay resultados para la búsqueda.' : 'No hay comensales. Crea uno o importa desde Excel.' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="!loading && meta.total > 0" class="mt-4 flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-gray-600">
                Mostrando {{ Math.min((meta.current_page - 1) * meta.per_page + 1, meta.total) }} - {{ Math.min(meta.current_page * meta.per_page, meta.total) }} de {{ meta.total }}
            </p>
            <div class="flex gap-2">
                <button
                    type="button"
                    :disabled="meta.current_page <= 1"
                    class="px-3 py-1 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    @click="goToPage(meta.current_page - 1)"
                >
                    Anterior
                </button>
                <button
                    type="button"
                    :disabled="meta.current_page >= meta.last_page"
                    class="px-3 py-1 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    @click="goToPage(meta.current_page + 1)"
                >
                    Siguiente
                </button>
            </div>
        </div>

        <div v-if="dinerToDelete" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" />
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900">Confirmar eliminación</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        ¿Eliminar al comensal <strong>{{ dinerToDelete.name }}</strong> ({{ dinerToDelete.id_code }})?
                    </p>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3">
                        <button
                            type="button"
                            @click="executeDelete"
                            :disabled="deleting"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 disabled:opacity-50 sm:text-sm"
                        >
                            {{ deleting ? 'Eliminando...' : 'Eliminar' }}
                        </button>
                        <button
                            type="button"
                            @click="dinerToDelete = null"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:text-sm"
                        >
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { dinerApi } from './api';
import LoadingOverlay from '../../components/LoadingOverlay.vue';
import { diningHallApi } from '../diningHalls/api';

const route = useRoute();
const diningHallId = computed(() => route.params.id);

const diners = ref([]);
const diningHall = ref(null);
const loading = ref(true);
const error = ref(null);
const dinerToDelete = ref(null);
const deleting = ref(false);
const searchQuery = ref('');
const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
});

let searchTimeout = null;

async function fetchDiningHall() {
    try {
        const { data } = await diningHallApi.getById(diningHallId.value);
        diningHall.value = data.data;
    } catch {
        diningHall.value = null;
    }
}

async function fetchDiners(page = 1) {
    loading.value = true;
    error.value = null;
    try {
        const params = { page };
        if (searchQuery.value.trim()) params.search = searchQuery.value.trim();
        const { data } = await dinerApi.getAll(diningHallId.value, params);
        diners.value = data.data ?? [];
        meta.value = data.meta ?? { current_page: 1, last_page: 1, per_page: 15, total: 0 };
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Error al cargar comensales';
    } finally {
        loading.value = false;
    }
}

function goToPage(page) {
    if (page < 1 || page > meta.value.last_page) return;
    fetchDiners(page);
}

function onSearchInput() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => fetchDiners(1), 300);
}

function confirmDelete(diner) {
    dinerToDelete.value = diner;
}

async function executeDelete() {
    if (!dinerToDelete.value) return;
    deleting.value = true;
    try {
        await dinerApi.delete(diningHallId.value, dinerToDelete.value.id);
        dinerToDelete.value = null;
        await fetchDiners(meta.value.current_page);
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Error al eliminar';
    } finally {
        deleting.value = false;
    }
}

watch(diningHallId, () => {
    fetchDiningHall();
    fetchDiners();
});

onMounted(() => {
    fetchDiningHall();
    fetchDiners();
});
</script>
