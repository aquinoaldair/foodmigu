<template>
    <div class="bg-white rounded-xl shadow-sm p-6 relative">
        <LoadingOverlay :show="deleting" message="Eliminando..." />
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-semibold text-gray-900">Usuarios</h1>
            <router-link
                :to="{ name: 'users.create' }"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
            >
                Nuevo Usuario
            </router-link>
        </div>

        <div v-if="loading" class="py-12 text-center text-gray-500">Cargando...</div>
        <div v-else-if="error" class="py-6"><p class="text-red-600">{{ error }}</p></div>
        <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comedores Asignados</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="u in users" :key="u.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ u.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ u.email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span :class="u.roles?.[0]?.name === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 text-xs font-medium rounded-full">
                                {{ u.roles?.[0]?.name === 'admin' ? 'Administrador' : 'Usuario Limitado' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" :title="u.roles?.[0]?.name === 'admin' ? 'Todos (Acceso Total)' : u.dining_halls?.map(h => h.name).join(', ')">
                            <span v-if="u.roles?.[0]?.name === 'admin'" class="text-gray-400 italic">Todos (Acceso Total)</span>
                            <span v-else-if="u.dining_halls?.length">
                                {{ u.dining_halls.map(h => h.name).join(', ') }}
                            </span>
                            <span v-else class="text-gray-400 italic">Ninguno</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <router-link :to="{ name: 'users.edit', params: { id: u.id } }" class="text-blue-600 hover:text-blue-900 mr-4">
                                Editar
                            </router-link>
                            <button type="button" @click="confirmDelete(u)" class="text-red-600 hover:text-red-900">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!users.length">
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">No hay usuarios registrados.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="userToDelete" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900">Confirmar eliminación</h3>
                    <p class="mt-2 text-sm text-gray-500">¿Estás seguro de eliminar el usuario <strong>{{ userToDelete.name }}</strong>?</p>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="button" @click="executeDelete" :disabled="deleting" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 sm:col-start-2 disabled:opacity-50 sm:text-sm">
                            {{ deleting ? 'Eliminando...' : 'Eliminar' }}
                        </button>
                        <button type="button" @click="userToDelete = null" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
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
import { userApi } from './api';
import LoadingOverlay from '../../components/LoadingOverlay.vue';

const users = ref([]);
const loading = ref(true);
const error = ref(null);
const userToDelete = ref(null);
const deleting = ref(false);

async function fetchUsers() {
    loading.value = true;
    error.value = null;
    try {
        const { data } = await userApi.getAll();
        users.value = data.data ?? [];
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Error al cargar usuarios';
    } finally {
        loading.value = false;
    }
}

function confirmDelete(u) {
    userToDelete.value = u;
}

async function executeDelete() {
    if (!userToDelete.value) return;
    deleting.value = true;
    try {
        await userApi.delete(userToDelete.value.id);
        users.value = users.value.filter(u => u.id !== userToDelete.value.id);
        userToDelete.value = null;
    } catch (e) {
        alert(e.response?.data?.message ?? 'Error al eliminar');
    } finally {
        deleting.value = false;
    }
}

onMounted(fetchUsers);
</script>
