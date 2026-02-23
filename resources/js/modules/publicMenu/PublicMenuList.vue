<template>
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <button
                type="button"
                @click="salir"
                class="inline-block text-sm text-gray-600 hover:text-gray-900 mb-6"
            >
                ← Salir
            </button>

            <p v-if="diner" class="text-sm text-gray-600 mb-6">
                Hola, <strong>{{ diner.name }}</strong>
            </p>

            <div v-if="loading" class="py-12 text-center text-gray-500">Cargando menús...</div>
            <div v-else-if="!diner" class="bg-amber-50 border border-amber-200 rounded-xl p-6">
                <p class="text-amber-800">Debes identificarte primero.</p>
                <router-link
                    :to="{ name: 'public.identify', params: { code } }"
                    class="mt-3 inline-block px-4 py-2 text-sm font-medium text-amber-800 bg-amber-100 rounded-lg"
                >
                    Identificarse
                </router-link>
            </div>
            <div v-else-if="menuBuilds.length === 0" class="bg-white rounded-2xl shadow-sm p-8 text-center">
                <p class="text-gray-600">No hay menús publicados disponibles.</p>
            </div>
            <div v-else class="space-y-4">
                <div
                    v-for="build in menuBuilds"
                    :key="build.id"
                    class="bg-white rounded-2xl shadow-sm overflow-hidden"
                >
                    <div class="p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">{{ build.title }}</h2>
                        <div class="space-y-3">
                            <router-link
                                v-for="day in build.days"
                                :key="day.id"
                                :to="{ name: 'public.day', params: { code, dayId: day.id } }"
                                class="flex items-center justify-between p-4 rounded-xl border border-gray-200 hover:bg-gray-50"
                            >
                                <span class="font-medium text-gray-900">
                                    {{ formatDate(day.date) }}
                                </span>
                                <span
                                    v-if="hasSelection(day.id)"
                                    class="text-sm text-green-600 font-medium"
                                >
                                    ✓ Seleccionado
                                </span>
                                <span v-else class="text-sm text-gray-500">Seleccionar</span>
                            </router-link>
                        </div>
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

function formatDate(d) {
    if (!d) return '';
    const date = typeof d === 'string' ? new Date(d) : d;
    return date.toLocaleDateString('es-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    });
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
