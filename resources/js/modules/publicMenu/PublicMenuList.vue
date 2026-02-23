<template>
    <div class="min-h-screen bg-gray-50 px-4 py-6 pb-24">
        <div class="max-w-md mx-auto">
            <button
                type="button"
                @click="salir"
                class="inline-flex items-center justify-center w-full py-3 mb-6 rounded-xl text-lg font-semibold bg-gray-200 text-gray-700 active:scale-95 transition"
            >
                ← Salir
            </button>

            <p v-if="diner" class="text-base text-gray-600 mb-6">
                Hola, <strong class="text-gray-900">{{ diner.name }}</strong>
            </p>

            <div v-if="loading" class="flex items-center justify-center py-24 transition-all duration-200">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin" />
                    <p class="text-base text-gray-500">Cargando menús...</p>
                </div>
            </div>
            <div v-else-if="!diner" class="bg-white rounded-2xl shadow-sm p-5 mb-4">
                <p class="text-amber-800 mb-4">Debes identificarte primero.</p>
                <router-link
                    :to="{ name: 'public.identify', params: { code } }"
                    class="inline-flex items-center justify-center w-full py-3 rounded-xl text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 transition"
                >
                    Identificarse
                </router-link>
            </div>
            <div v-else-if="menuBuilds.length === 0" class="bg-white rounded-2xl shadow-sm p-5 mb-4 text-center">
                <p class="text-gray-600">No hay menús publicados disponibles.</p>
            </div>
            <div v-else class="space-y-4 transition-all duration-200">
                <div
                    v-for="build in menuBuilds"
                    :key="build.id"
                    class="bg-white rounded-2xl shadow-sm p-5 mb-4"
                >
                    <h2 class="text-lg font-bold text-gray-900 mb-1">{{ build.title }}</h2>
                    <p class="text-sm text-gray-500 mb-4">Selecciona un día</p>
                    <div class="flex flex-col gap-2">
                        <router-link
                            v-for="day in build.days"
                            :key="day.id"
                            :to="{ name: 'public.day', params: { code, dayId: day.id } }"
                            class="flex items-center justify-between w-full px-4 py-3 rounded-xl text-base font-medium transition active:scale-95"
                            :class="
                                hasSelection(day.id)
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-blue-100 text-blue-700'
                            "
                        >
                            <span>{{ formatDateShort(day.date) }}</span>
                            <span v-if="hasSelection(day.id)">✓</span>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { publicMenuApi, getStoredDiner, clearStoredDiner } from './api';

const route = useRoute();
const router = useRouter();
const code = computed(() => route.params.code);

const diner = ref(null);
const menuBuilds = ref([]);
const loading = ref(true);
const selectionCache = ref({});

function formatDateShort(d) {
    if (!d) return '';
    let date;
    if (typeof d === 'string') {
        const parts = d.split('T')[0].split('-');
        const y = parseInt(parts[0], 10);
        const m = parseInt(parts[1], 10) - 1;
        const day = parseInt(parts[2], 10);
        date = new Date(y, m, day);
    } else {
        date = d instanceof Date ? d : new Date(d);
    }
    return date.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric', month: 'short' });
}

function hasSelection(dayId) {
    return selectionCache.value[dayId] ?? false;
}

function salir() {
    clearStoredDiner(code.value);
    router.push({ name: 'public.identify', params: { code: code.value } });
}

async function fetchMenus() {
    loading.value = true;
    diner.value = getStoredDiner(code.value);
    if (!diner.value) {
        loading.value = false;
        return;
    }
    try {
        const { data } = await publicMenuApi.menus(code.value);
        menuBuilds.value = data.menu_builds ?? [];
        for (const build of menuBuilds.value) {
            for (const day of build.days ?? []) {
                try {
                    const { data: sel } = await publicMenuApi.mySelections(
                        day.id,
                        diner.value.id
                    );
                    selectionCache.value[day.id] =
                        (sel.selections ?? []).length > 0;
                } catch {
                    selectionCache.value[day.id] = false;
                }
            }
        }
    } catch {
        menuBuilds.value = [];
    } finally {
        loading.value = false;
    }
}

onMounted(fetchMenus);
</script>
