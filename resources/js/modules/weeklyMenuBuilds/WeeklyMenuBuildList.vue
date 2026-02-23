<template>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Menús Semanales</h1>
                    <router-link
                        :to="{ name: 'weekly-menu-builds.create' }"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Nueva Semana
                    </router-link>
                </div>

                <div v-if="loading" class="p-12 text-center text-gray-500">Cargando...</div>
                <div v-else-if="error" class="p-6">
                    <p class="text-red-600">{{ error }}</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menú base</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha publicación</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="build in builds" :key="build.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ build.title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ build.menu?.name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="build.status === 'published'
                                            ? 'px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800'
                                            : 'px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800'"
                                    >
                                        {{ build.status === 'published' ? 'Publicado' : 'Borrador' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ build.published_at ? formatDate(build.published_at) : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <router-link
                                        :to="{ name: 'weekly-menu-builds.edit', params: { id: build.id } }"
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        {{ build.status === 'published' ? 'Ver' : 'Editar' }}
                                    </router-link>
                                    <span class="mx-2 text-gray-300">|</span>
                                    <button
                                        type="button"
                                        @click="confirmArchive(build)"
                                        class="text-amber-600 hover:text-amber-900"
                                    >
                                        Archivar
                                    </button>
                                    <span class="mx-2 text-gray-300">|</span>
                                    <button
                                        type="button"
                                        @click="confirmDelete(build)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!builds.length">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">No hay menús semanales.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <div
                v-if="actionTarget"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
                @click.self="actionTarget = null"
            >
                <div class="absolute inset-0 bg-gray-900/60 pointer-events-none"></div>
                <div class="relative z-10 bg-white rounded-lg shadow-xl max-w-lg w-full p-6 pointer-events-auto">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ actionTarget.type === 'archive' ? 'Confirmar archivado' : 'Confirmar eliminación' }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        <template v-if="actionTarget.type === 'archive'">
                            ¿Archivar <strong>{{ actionTarget.build.title }}</strong>? Se ocultará del listado.
                        </template>
                        <template v-else>
                            ¿Eliminar <strong>{{ actionTarget.build.title }}</strong>? Esta acción no se puede deshacer.
                        </template>
                    </p>
                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            type="button"
                            @click="actionTarget = null"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            @click="executeAction"
                            :disabled="actionLoading"
                            :class="[
                                'px-4 py-2 rounded-md text-sm font-medium text-white disabled:opacity-50',
                                actionTarget.type === 'archive' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-red-600 hover:bg-red-700'
                            ]"
                        >
                            {{ actionLoading ? 'Procesando...' : (actionTarget.type === 'archive' ? 'Archivar' : 'Eliminar') }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { weeklyMenuBuildApi } from './api';

const builds = ref([]);
const loading = ref(true);
const error = ref(null);
const actionTarget = ref(null);
const actionLoading = ref(false);

function formatDate(val) {
    if (!val) return '-';
    const d = new Date(val);
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

async function fetchBuilds() {
    loading.value = true;
    error.value = null;
    try {
        const { data } = await weeklyMenuBuildApi.getAll();
        builds.value = data.data ?? [];
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Error al cargar';
    } finally {
        loading.value = false;
    }
}

function confirmArchive(build) {
    actionTarget.value = { type: 'archive', build };
}

function confirmDelete(build) {
    actionTarget.value = { type: 'delete', build };
}

async function executeAction() {
    if (!actionTarget.value) return;
    actionLoading.value = true;
    try {
        if (actionTarget.value.type === 'archive') {
            await weeklyMenuBuildApi.archive(actionTarget.value.build.id);
            builds.value = builds.value.filter((b) => b.id !== actionTarget.value.build.id);
        } else {
            await weeklyMenuBuildApi.delete(actionTarget.value.build.id);
            builds.value = builds.value.filter((b) => b.id !== actionTarget.value.build.id);
        }
        actionTarget.value = null;
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Error al procesar';
    } finally {
        actionLoading.value = false;
    }
}

onMounted(fetchBuilds);
</script>
