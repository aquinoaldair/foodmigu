<template>
    <div class="min-h-screen bg-gray-50 px-4 py-6 pb-24">
        <LoadingOverlay :show="saving" message="Guardando selección..." />
        <div class="max-w-md mx-auto">
            <router-link
                :to="{ name: 'public.menus', params: { code } }"
                class="flex items-center justify-center gap-2 py-3 px-4 mb-6 rounded-xl text-base font-semibold bg-gray-200 text-gray-700 active:scale-95 transition"
            >
                ← Menús
            </router-link>

            <div v-if="loading" class="flex items-center justify-center py-24 transition-all duration-200">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin" />
                    <p class="text-base text-gray-500">Cargando...</p>
                </div>
            </div>
            <div v-else-if="error" class="bg-white rounded-2xl shadow-sm p-5 mb-4">
                <div class="bg-red-50 border border-red-200 rounded-xl p-5">
                    <p class="text-red-700">{{ error }}</p>
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
            <template v-else>
                <div class="mb-6">
                    <h1 class="text-xl font-bold text-gray-900">
                        {{ day ? formatDate(day.date) : '' }}
                    </h1>
                    <p v-if="day?.weekly_menu_build?.title" class="text-gray-500 text-sm mt-1">
                        {{ day.weekly_menu_build.title }}
                    </p>
                </div>

                <div v-if="deadlinePassed" class="mb-6 rounded-xl px-4 py-3 bg-red-50 border border-red-200">
                    <p class="text-red-700 font-medium">
                        El plazo para modificar la selección ha finalizado.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6 transition-all duration-200">
                    <div
                        v-for="group in byCategory"
                        :key="group.category.id"
                        class="bg-white rounded-2xl shadow-sm p-5 mb-4"
                    >
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">
                            {{ group.category.name }}
                            <span v-if="group.category.is_required" class="text-red-500">*</span>
                        </h2>
                        <p
                            v-if="group.category.selection_type === 'none' && !group.category.is_required"
                            class="text-sm text-gray-500 mb-3"
                        >
                            Opcional. Márcalo si lo vas a requerir.
                        </p>

                        <div
                            v-if="group.category.selection_type === 'none' && !group.category.is_required"
                            class="space-y-2"
                        >
                            <label
                                v-for="item in group.items"
                                :key="item.id"
                                class="flex justify-between items-center gap-3 p-4 rounded-xl border cursor-pointer transition active:scale-95"
                                :class="
                                    selectionMultiple(group.category.id).includes(item.id)
                                        ? 'border-blue-500 bg-blue-50'
                                        : 'border-gray-200 bg-white'
                                "
                            >
                                <span class="flex-1">
                                    <span class="font-medium text-gray-900 block">{{ item.name }}</span>
                                    <span v-if="item.description" class="text-sm text-gray-500">
                                        {{ item.description }}
                                    </span>
                                </span>
                                <input
                                    type="checkbox"
                                    :value="item.id"
                                    :checked="selectionMultiple(group.category.id).includes(item.id)"
                                    :disabled="deadlinePassed"
                                    class="h-5 w-5 rounded border-gray-300 text-blue-600 shrink-0"
                                    @change="toggleMultiple(group.category.id, item.id)"
                                />
                            </label>
                        </div>

                        <div
                            v-if="group.category.selection_type === 'none' && group.category.is_required"
                            class="space-y-2"
                        >
                            <div
                                v-for="item in group.items"
                                :key="item.id"
                                class="flex justify-between items-center gap-3 p-4 rounded-xl border border-gray-200 bg-gray-50 cursor-not-allowed"
                            >
                                <span class="flex-1">
                                    <span class="font-medium text-gray-900 block">{{ item.name }}</span>
                                    <span v-if="item.description" class="text-sm text-gray-500">
                                        {{ item.description }}
                                    </span>
                                </span>
                                <input type="checkbox" :checked="true" disabled class="h-5 w-5 rounded border-gray-300 text-blue-600 shrink-0" />
                            </div>
                        </div>

                        <div v-if="group.category.selection_type === 'single'" class="space-y-2">
                            <label
                                v-for="item in group.items"
                                :key="item.id"
                                class="flex justify-between items-center gap-3 p-4 rounded-xl border cursor-pointer transition active:scale-95"
                                :class="
                                    selectionSingle(group.category.id) === item.id
                                        ? 'border-blue-500 bg-blue-50'
                                        : 'border-gray-200 bg-white'
                                "
                            >
                                <span class="flex-1">
                                    <span class="font-medium text-gray-900 block">{{ item.name }}</span>
                                    <span v-if="item.description" class="text-sm text-gray-500">
                                        {{ item.description }}
                                    </span>
                                </span>
                                <input
                                    type="radio"
                                    :name="`cat_${group.category.id}`"
                                    :value="item.id"
                                    :checked="selectionSingle(group.category.id) === item.id"
                                    :disabled="deadlinePassed"
                                    class="h-5 w-5 text-blue-600 shrink-0"
                                    @change="setSingle(group.category.id, item.id)"
                                />
                            </label>
                        </div>

                        <div v-if="group.category.selection_type === 'multiple'" class="space-y-2">
                            <label
                                v-for="item in group.items"
                                :key="item.id"
                                class="flex justify-between items-center gap-3 p-4 rounded-xl border cursor-pointer transition active:scale-95"
                                :class="
                                    selectionMultiple(group.category.id).includes(item.id)
                                        ? 'border-blue-500 bg-blue-50'
                                        : 'border-gray-200 bg-white'
                                "
                            >
                                <span class="flex-1">
                                    <span class="font-medium text-gray-900 block">{{ item.name }}</span>
                                    <span v-if="item.description" class="text-sm text-gray-500">
                                        {{ item.description }}
                                    </span>
                                </span>
                                <input
                                    type="checkbox"
                                    :value="item.id"
                                    :checked="selectionMultiple(group.category.id).includes(item.id)"
                                    :disabled="deadlinePassed"
                                    class="h-5 w-5 rounded border-gray-300 text-blue-600 shrink-0"
                                    @change="toggleMultiple(group.category.id, item.id)"
                                />
                            </label>
                        </div>
                    </div>

                    <div v-if="submitError" class="text-red-600 text-sm">{{ submitError }}</div>
                </form>
            </template>
        </div>

        <div
            v-if="saved"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            @click.self="saved = false"
        >
            <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                    <span class="text-3xl text-green-600">✓</span>
                </div>
                <p class="text-lg font-semibold text-gray-900 mb-2">¡Guardado!</p>
                <p class="text-gray-600 mb-6">Selección guardada correctamente</p>
                <button
                    type="button"
                    @click="saved = false"
                    class="w-full py-3 rounded-xl text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 transition"
                >
                    Aceptar
                </button>
            </div>
        </div>

        <div
            v-if="diner && !loading && !error && day && !deadlinePassed"
            class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-lg"
        >
            <button
                type="button"
                :disabled="saving"
                @click="submit"
                class="w-full py-4 rounded-xl text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ saving ? 'Guardando...' : 'Guardar selección' }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { publicMenuApi, getStoredDiner } from './api';
import LoadingOverlay from '../../components/LoadingOverlay.vue';

const route = useRoute();
const code = computed(() => route.params.code);
const dayId = computed(() => route.params.dayId);

const diner = ref(null);
const day = ref(null);
const loading = ref(true);
const error = ref(null);
const deadlinePassed = ref(false);
const saving = ref(false);
const saved = ref(false);
const submitError = ref(null);

const selectionState = reactive({ single: {}, multiple: {} });

const byCategory = computed(() => {
    if (!day.value?.items) return [];
    const groups = {};
    for (const item of day.value.items) {
        const cat = item.menu_category;
        if (!cat) continue;
        if (!groups[cat.id]) {
            groups[cat.id] = { category: cat, items: [] };
        }
        groups[cat.id].items.push(item);
    }
    return Object.values(groups).sort(
        (a, b) => (a.category.display_order ?? 0) - (b.category.display_order ?? 0)
    );
});

function formatDate(d) {
    if (!d) return '';
    const date = typeof d === 'string' ? new Date(d) : d;
    return date.toLocaleDateString('es-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    });
}

function selectionSingle(catId) {
    return selectionState.single[catId] ?? null;
}

function selectionMultiple(catId) {
    return selectionState.multiple[catId] ?? [];
}

function setSingle(catId, itemId) {
    selectionState.single[catId] = itemId;
}

function toggleMultiple(catId, itemId) {
    const arr = selectionState.multiple[catId] ?? [];
    const idx = arr.indexOf(itemId);
    if (idx >= 0) arr.splice(idx, 1);
    else arr.push(itemId);
    selectionState.multiple[catId] = [...arr];
}

function buildSelections() {
    const ids = [];
    for (const v of Object.values(selectionState.single)) {
        if (v) ids.push(v);
    }
    for (const arr of Object.values(selectionState.multiple)) {
        ids.push(...arr);
    }
    return ids;
}

async function submit() {
    if (deadlinePassed.value) return;
    saving.value = true;
    submitError.value = null;
    saved.value = false;
    try {
        await publicMenuApi.select(dayId.value, {
            diner_id: diner.value.id,
            selections: buildSelections(),
        });
        saved.value = true;
        submitError.value = null;
    } catch (e) {
        submitError.value = e.response?.data?.message ?? 'Error al guardar';
    } finally {
        saving.value = false;
    }
}

async function fetchDay() {
    loading.value = true;
    error.value = null;
    diner.value = getStoredDiner(code.value);
    if (!diner.value) {
        loading.value = false;
        return;
    }
    try {
        const { data } = await publicMenuApi.dayDetail(dayId.value);
        day.value = data.day;
        deadlinePassed.value = data.deadline_passed ?? false;

        const { data: sel } = await publicMenuApi.mySelections(dayId.value, diner.value.id);
        const ids = sel.selections ?? [];

        for (const item of day.value?.items ?? []) {
            const cat = item.menu_category;
            if (!cat) continue;
            if (cat.selection_type === 'single') {
                if (ids.includes(item.id)) selectionState.single[cat.id] = item.id;
            } else if (cat.selection_type === 'multiple' || (cat.selection_type === 'none' && !cat.is_required)) {
                if (ids.includes(item.id)) {
                    const arr = selectionState.multiple[cat.id] ?? [];
                    arr.push(item.id);
                    selectionState.multiple[cat.id] = arr;
                }
            } else if (cat.selection_type === 'none' && cat.is_required) {
                const arr = selectionState.multiple[cat.id] ?? [];
                arr.push(item.id);
                selectionState.multiple[cat.id] = arr;
            }
        }
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Error al cargar';
    } finally {
        loading.value = false;
    }
}

watch(dayId, () => {
    Object.keys(selectionState.single).forEach((k) => delete selectionState.single[k]);
    Object.keys(selectionState.multiple).forEach((k) => delete selectionState.multiple[k]);
    fetchDay();
});

onMounted(fetchDay);
</script>
