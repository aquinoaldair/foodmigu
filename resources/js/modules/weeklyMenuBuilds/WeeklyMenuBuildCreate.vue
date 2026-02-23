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
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { weeklyMenuBuildApi } from './api';
import { menuApi } from '../menus/api';

const router = useRouter();
const form = reactive({
    title: '',
    menu_id: '',
    start_date: '',
    end_date: '',
});
const menus = ref([]);
const loading = ref(false);
const errors = reactive({});

async function fetchMenus() {
    try {
        const { data } = await menuApi.getAll();
        menus.value = data.data ?? [];
        if (menus.value.length && !form.menu_id) form.menu_id = String(menus.value[0].id);
    } catch {
        menus.value = [];
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
});
</script>
