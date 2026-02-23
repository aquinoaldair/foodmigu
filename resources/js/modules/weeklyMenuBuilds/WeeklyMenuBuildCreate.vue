<template>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <router-link
                    :to="{ name: 'weekly-menu-builds.index' }"
                    class="inline-block text-sm text-gray-600 hover:text-gray-900 mb-4"
                >
                    ← Regresar al listado
                </router-link>
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Nueva Semana</h1>
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            required
                            class="mt-1 block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            :class="{ 'border-red-500': errors.title }"
                        />
                        <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.title) ? errors.title[0] : errors.title }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Fecha inicio</label>
                            <input
                                id="start_date"
                                v-model="form.start_date"
                                type="date"
                                required
                                class="mt-1 block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                :class="{ 'border-red-500': errors.start_date }"
                            />
                            <p v-if="errors.start_date" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.start_date) ? errors.start_date[0] : errors.start_date }}</p>
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Fecha fin</label>
                            <input
                                id="end_date"
                                v-model="form.end_date"
                                type="date"
                                required
                                class="mt-1 block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                :class="{ 'border-red-500': errors.end_date }"
                            />
                            <p v-if="errors.end_date" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.end_date) ? errors.end_date[0] : errors.end_date }}</p>
                        </div>
                    </div>
                    <div>
                        <label for="menu_id" class="block text-sm font-medium text-gray-700">Menú base</label>
                        <select
                            id="menu_id"
                            v-model="form.menu_id"
                            required
                            class="mt-1 block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            :class="{ 'border-red-500': errors.menu_id }"
                        >
                            <option value="">Seleccionar menú</option>
                            <option v-for="m in menus" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                        <p v-if="errors.menu_id" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.menu_id) ? errors.menu_id[0] : errors.menu_id }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 p-4 bg-gray-50/50">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Comedores asignados</h3>
                        <p v-if="loadingDiningHalls" class="text-sm text-gray-500">Cargando comedores...</p>
                        <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label
                                v-for="hall in activeDiningHalls"
                                :key="hall.id"
                                class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer"
                            >
                                <input
                                    v-model="form.dining_halls"
                                    type="checkbox"
                                    :value="hall.id"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 shrink-0"
                                />
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900">{{ hall.name }}</div>
                                    <div class="text-xs text-gray-500">{{ hall.code }}</div>
                                </div>
                            </label>
                            <p v-if="!loadingDiningHalls && !activeDiningHalls.length" class="col-span-2 text-sm text-gray-500">
                                No hay comedores activos disponibles.
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button
                            type="submit"
                            :disabled="loading"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                        >
                            {{ loading ? 'Creando...' : 'Crear' }}
                        </button>
                        <router-link
                            :to="{ name: 'weekly-menu-builds.index' }"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Cancelar
                        </router-link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { weeklyMenuBuildApi } from './api';
import { menuApi } from '../menus/api';
import { diningHallApi } from '../diningHalls/api';

const router = useRouter();
const form = reactive({
    title: '',
    menu_id: '',
    start_date: '',
    end_date: '',
    dining_halls: [],
});
const menus = ref([]);
const diningHalls = ref([]);
const loadingDiningHalls = ref(false);
const loading = ref(false);
const errors = reactive({});

const activeDiningHalls = computed(() => (diningHalls.value ?? []).filter((h) => h.is_active));

async function fetchMenus() {
    try {
        const { data } = await menuApi.getAll();
        menus.value = data.data ?? [];
        if (menus.value.length && !form.menu_id) form.menu_id = String(menus.value[0].id);
    } catch {
        menus.value = [];
    }
}

async function fetchDiningHalls() {
    loadingDiningHalls.value = true;
    try {
        const { data } = await diningHallApi.getAll();
        diningHalls.value = data.data ?? [];
    } catch {
        diningHalls.value = [];
    } finally {
        loadingDiningHalls.value = false;
    }
}

function getDefaultDates() {
    const today = new Date();
    const monday = new Date(today);
    const day = today.getDay();
    const diff = day === 0 ? -6 : 1 - day;
    monday.setDate(today.getDate() + diff);
    const sunday = new Date(monday);
    sunday.setDate(monday.getDate() + 6);
    return {
        start: monday.toISOString().slice(0, 10),
        end: sunday.toISOString().slice(0, 10),
    };
}

async function handleSubmit() {
    loading.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);
    try {
        const { data } = await weeklyMenuBuildApi.create({
            title: form.title,
            menu_id: Number(form.menu_id),
            start_date: form.start_date,
            end_date: form.end_date,
            dining_halls: (form.dining_halls ?? []).map(Number),
        });
        router.push({ name: 'weekly-menu-builds.edit', params: { id: data.data.id } });
    } catch (e) {
        const v = e.response?.data?.errors ?? {};
        Object.assign(errors, v);
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    const def = getDefaultDates();
    if (!form.start_date) form.start_date = def.start;
    if (!form.end_date) form.end_date = def.end;
    fetchMenus();
    fetchDiningHalls();
});
</script>
