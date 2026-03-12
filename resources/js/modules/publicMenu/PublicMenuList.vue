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

            <div v-if="diner" class="flex flex-col gap-4 mb-6">
                <p class="text-base text-gray-600">
                    Hola, <strong class="text-gray-900">{{ diner.name }}</strong>
                </p>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button
                        v-if="hasAnySelection"
                        type="button"
                        @click="showSelectionsModal = true"
                        class="inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-base font-semibold bg-green-100 text-green-700 border border-green-300 hover:bg-green-200 active:scale-95 transition"
                    >
                        Ver mis selecciones
                        <span v-if="lastUpdateOverall" class="text-xs font-normal opacity-90">
                            (actualizado {{ formatUpdatedAt(lastUpdateOverall) }})
                        </span>
                    </button>
                    <button
                        v-if="hasAnySelection"
                        type="button"
                        @click="openPdfSelections"
                        class="inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-base font-semibold bg-blue-100 text-blue-700 border border-blue-300 hover:bg-blue-200 active:scale-95 transition"
                    >
                        Generar PDF
                    </button>
                </div>
            </div>

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
                            <span class="flex flex-col gap-0.5">
                                <span class="flex items-center gap-2">
                                    {{ formatDateShort(day.date) }}
                                    <span v-if="hasSelection(day.id)">✓</span>
                                </span>
                                <span
                                    v-if="hasSelection(day.id) && getUpdatedAt(day.id)"
                                    class="text-xs font-normal opacity-90"
                                >
                                    Actualizado: {{ formatUpdatedAt(getUpdatedAt(day.id)) }}
                                </span>
                            </span>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="showSelectionsModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            @click.self="showSelectionsModal = false"
        >
            <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full max-h-[85vh] flex flex-col">
                <h3 class="text-lg font-bold text-gray-900 mb-1">
                    Resumen de mis selecciones
                </h3>
                <p v-if="lastUpdateOverall" class="text-sm text-gray-500 mb-4">
                    Última actualización: {{ formatUpdatedAt(lastUpdateOverall) }}
                </p>
                <div class="flex-1 overflow-y-auto space-y-6 mb-6">
                    <div
                        v-for="dayGroup in allSelectionsByDay"
                        :key="dayGroup.day.id"
                        class="border-b border-gray-100 pb-4 last:border-0 last:pb-0"
                    >
                        <h4 class="text-base font-semibold text-gray-900 mb-2">
                            {{ formatDateShort(dayGroup.day.date) }}
                            <span v-if="dayGroup.updatedAt" class="text-xs font-normal text-gray-500">
                                ({{ formatUpdatedAt(dayGroup.updatedAt) }})
                            </span>
                        </h4>
                        <div class="space-y-3">
                            <div
                                v-for="group in dayGroup.byCategory"
                                :key="`${dayGroup.day.id}-${group.category.id}`"
                            >
                                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                    {{ group.category.name }}
                                </p>
                                <ul class="space-y-1 mt-1">
                                    <li
                                        v-for="item in group.items"
                                        :key="item.id"
                                        class="text-sm text-gray-700"
                                    >
                                        • {{ item.name }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <button
                    type="button"
                    @click="showSelectionsModal = false"
                    class="w-full py-3 rounded-xl text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 transition"
                >
                    Cerrar
                </button>
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
const showSelectionsModal = ref(false);

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

function formatUpdatedAt(isoString) {
    if (!isoString) return '';
    const d = new Date(isoString);
    return d.toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function hasSelection(dayId) {
    const cache = selectionCache.value[dayId];
    return cache ? (cache.ids?.length ?? 0) > 0 : false;
}

function getUpdatedAt(dayId) {
    return selectionCache.value[dayId]?.updatedAt ?? null;
}

const hasAnySelection = computed(() => {
    return Object.values(selectionCache.value).some((c) => (c.ids?.length ?? 0) > 0);
});

const lastUpdateOverall = computed(() => {
    const dates = Object.values(selectionCache.value)
        .map((c) => c.updatedAt)
        .filter(Boolean);
    if (dates.length === 0) return null;
    return dates.sort().pop();
});

const allSelectionsByDay = computed(() => {
    const result = [];
    for (const build of menuBuilds.value) {
        for (const day of build.days ?? []) {
            const cache = selectionCache.value[day.id];
            const ids = cache?.ids ?? [];
            if (ids.length === 0) continue;
            const selectedItems = (day.items ?? []).filter((item) => ids.includes(item.id));
            const groups = {};
            for (const item of selectedItems) {
                const cat = item.menu_category;
                if (!cat) continue;
                if (!groups[cat.id]) {
                    groups[cat.id] = { category: cat, items: [] };
                }
                groups[cat.id].items.push(item);
            }
            const byCategory = Object.values(groups).sort(
                (a, b) => (a.category.display_order ?? 0) - (b.category.display_order ?? 0)
            );
            result.push({
                day,
                updatedAt: cache?.updatedAt ?? null,
                byCategory,
            });
        }
    }
    return result;
});

function openPdfSelections() {
    if (!diner.value) return;
    const url = publicMenuApi.selectionsPdfUrl(code.value, diner.value.id);
    window.open(url, '_blank', 'noopener,noreferrer');
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
                    const ids = sel.selections ?? [];
                    selectionCache.value[day.id] = {
                        ids,
                        updatedAt: sel.updated_at ?? null,
                    };
                } catch {
                    selectionCache.value[day.id] = { ids: [], updatedAt: null };
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
