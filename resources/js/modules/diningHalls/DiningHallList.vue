<template>
    <div class="bg-white rounded-xl shadow-sm p-6 relative">
        <LoadingOverlay :show="deleting" message="Eliminando..." />
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-semibold text-gray-900">Comedores</h1>
            <router-link
                :to="{ name: 'dining-halls.create' }"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                Nuevo Comedor
            </router-link>
        </div>

        <div v-if="loading" class="py-12 text-center text-gray-500">
            Cargando...
        </div>

        <div v-else-if="error" class="py-6">
            <p class="text-red-600">{{ error }}</p>
        </div>

        <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comensales</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="hall in halls" :key="hall.id" class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ hall.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ hall.code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                :class="hall.is_active
                                    ? 'px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800'
                                    : 'px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600'"
                            >
                                {{ hall.is_active ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <router-link
                                :to="{ name: 'diners.index', params: { id: hall.id } }"
                                class="text-blue-600 hover:text-blue-900"
                            >
                                Ver Comensales
                            </router-link>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <router-link
                                :to="{ name: 'dining-halls.edit', params: { id: hall.id } }"
                                class="text-blue-600 hover:text-blue-900 mr-4"
                            >
                                Editar
                            </router-link>
                            <button
                                type="button"
                                @click="confirmDelete(hall)"
                                class="text-red-600 hover:text-red-900"
                            >
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!halls.length">
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No hay comedores registrados. Crea uno para comenzar.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="hallToDelete"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" />
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Confirmar eliminación
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            ¿Estás seguro de eliminar el comedor <strong>{{ hallToDelete.name }}</strong>?
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button
                            type="button"
                            @click="executeDelete"
                            :disabled="deleting"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 sm:col-start-2 sm:text-sm disabled:opacity-50"
                        >
                            {{ deleting ? 'Eliminando...' : 'Eliminar' }}
                        </button>
                        <button
                            type="button"
                            @click="hallToDelete = null"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm"
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
import { ref, onMounted } from 'vue';
import { diningHallApi } from './api';
import LoadingOverlay from '../../components/LoadingOverlay.vue';

const halls = ref([]);
const loading = ref(true);
const error = ref(null);
const hallToDelete = ref(null);
const deleting = ref(false);

async function fetchHalls() {
    loading.value = true;
    error.value = null;
    try {
        const { data } = await diningHallApi.getAll();
        halls.value = data.data ?? [];
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Error al cargar los comedores';
    } finally {
        loading.value = false;
    }
}

function confirmDelete(hall) {
    hallToDelete.value = hall;
}

async function executeDelete() {
    if (!hallToDelete.value) return;
    deleting.value = true;
    try {
        await diningHallApi.delete(hallToDelete.value.id);
        halls.value = halls.value.filter((h) => h.id !== hallToDelete.value.id);
        hallToDelete.value = null;
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Error al eliminar el comedor';
    } finally {
        deleting.value = false;
    }
}

onMounted(fetchHalls);
</script>
