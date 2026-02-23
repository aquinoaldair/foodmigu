<template>
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <router-link
                    :to="{ name: 'public.menus', params: { code } }"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Menús
                </router-link>
                <button
                    type="button"
                    @click="salir"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    Salir
                </button>
            </div>

            <div v-if="loading" class="py-12 text-center text-gray-500">Cargando...</div>
            <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-xl p-6">
                <p class="text-red-700">{{ error }}</p>
            </div>
            <div v-else-if="!diner" class="bg-amber-50 border border-amber-200 rounded-xl p-6">
                <p class="text-amber-800">Debes identificarte primero.</p>
                <router-link
                    :to="{ name: 'public.identify', params: { code } }"
                    class="mt-3 inline-block px-4 py-2 text-sm font-medium text-amber-800 bg-amber-100 rounded-lg"
                >
                    Identificarse
                </router-link>
            </div>
            <template v-else>
                <div class="mb-6">
                    <h1 class="text-xl font-bold text-gray-900">
                        {{ day ? formatDate(day.date) : '' }}
                    </h1>
                    <p v-if="day?.weekly_menu_build?.title" class="text-gray-600 text-sm mt-1">
                        {{ day.weekly_menu_build.title }}
                    </p>
                </div>

                <div v-if="deadlinePassed" class="bg-amber-50 border border-amber-200 rounded-xl p-6 mb-6">
                    <p class="text-amber-800 font-medium">
                        El plazo para modificar la selección ha finalizado.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div
                        v-for="group in byCategory"
                        :key="group.category.id"
                        class="bg-white rounded-2xl shadow-sm p-6"
                    >
                        <h2 class="text-lg font-semibold text-gray-900 mb-1">
                            {{ group.category.name }}
                            <span v-if="group.category.is_required" class="text-red-500">*</span>
                        </h2>
                        <p
                            v-if="group.category.selection_type === 'none' && !group.category.is_required"
                            class="text-sm text-gray-500 mb-3"
                        >
                            Opcional. Márcalo únicamente si lo necesitas.
                        </p>

                        <div
                            v-if="group.category.selection_type === 'none' && !group.category.is_required"
                            class="space-y-2 mt-2"
                        >
                            <label
                                v-for="item in group.items"
                                :key="item.id"
                                class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer hover:bg-gray-50"
                                :class="
                                    selectionMultiple(group.category.id).includes(item.id)
                                        ? 'border-blue-500 bg-blue-50'
                                        : 'border-gray-200'
                                "
                            >
                                <input
                                    type="checkbox"
                                    :value="item.id"
                                    :checked="selectionMultiple(group.category.id).includes(item.id)"
                                    :disabled="deadlinePassed"
                                    class="mt-1 h-5 w-5 rounded border-gray-300 text-blue-600"
                                    @change="toggleMultiple(group.category.id, item.id)"
                                />
                                <span class="flex-1">
                                    <span class="font-medium text-gray-900">{{ item.name }}</span>
                                    <span v-if="item.description" class="block text-sm text-gray-500">
                                        {{ item.description }}
                                    </span>
                                </span>
                            </label>
                        </div>

                        <div
                            v-if="group.category.selection_type === 'none' && group.category.is_required"
                            class="space-y-2 mt-2"
                        >
                            <div
                                v-for="item in group.items"
                                :key="item.id"
                                class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 bg-gray-50 cursor-not-allowed"
                            >
                                <input
                                    type="checkbox"
                                    :checked="true"
                                    disabled
                                    class="h-5 w-5 rounded border-gray-300 text-blue-600"
                                />
                                <span class="flex-1">
                                    <span class="font-medium text-gray-900">{{ item.name }}</span>
                                    <span v-if="item.description" class="block text-sm text-gray-500">
                                        {{ item.description }}
                                    </span>
                                </span>
                            </div>
                        </div>

                        <div
                            v-if="group.category.selection_type === 'single'"
                            class="space-y-2 mt-2"
                        >
                            <label
                                v-for="item in group.items"
                                :key="item.id"
                                class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer hover:bg-gray-50"
                                :class="
                                    selectionSingle(group.category.id) === item.id
                                        ? 'border-blue-500 bg-blue-50'
                                        : 'border-gray-200'
                                "
                            >
                                <input
                                    type="radio"
                                    :name="`cat_${group.category.id}`"
                                    :value="item.id"
                                    :checked="selectionSingle(group.category.id) === item.id"
                                    :disabled="deadlinePassed"
                                    class="mt-1 h-5 w-5 text-blue-600"
                                    @change="setSingle(group.category.id, item.id)"
                                />
                                <span class="flex-1">
                                    <span class="font-medium text-gray-900">{{ item.name }}</span>
                                    <span v-if="item.description" class="block text-sm text-gray-500">
                                        {{ item.description }}
                                    </span>
                                </span>
                            </label>
                        </div>

                        <div
                            v-if="group.category.selection_type === 'multiple'"
                            class="space-y-2 mt-2"
                        >
                            <label
                                v-for="item in group.items"
                                :key="item.id"
                                class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer hover:bg-gray-50"
                                :class="
                                    selectionMultiple(group.category.id).includes(item.id)
                                        ? 'border-blue-500 bg-blue-50'
                                        : 'border-gray-200'
                                "
                            >
                                <input
                                    type="checkbox"
                                    :value="item.id"
                                    :checked="selectionMultiple(group.category.id).includes(item.id)"
                                    :disabled="deadlinePassed"
                                    class="mt-1 h-5 w-5 rounded border-gray-300 text-blue-600"
                                    @change="toggleMultiple(group.category.id, item.id)"
                                />
                                <span class="flex-1">
                                    <span class="font-medium text-gray-900">{{ item.name }}</span>
                                    <span v-if="item.description" class="block text-sm text-gray-500">
                                        {{ item.description }}
                                    </span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div v-if="submitError" class="text-red-600 text-sm">{{ submitError }}</div>

                    <button
                        type="submit"
                        :disabled="saving || deadlinePassed"
                        class="w-full py-4 px-6 text-lg font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ saving ? 'Guardando...' : 'Guardar selección' }}
                    </button>
                </form>

                <div
                    v-if="saved"
                    class="mt-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-center"
                >
                    ✓ Selección guardada correctamente
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { publicMenuApi, getStoredDiner, clearStoredDiner } from './api';

const route = useRoute();
const router = useRouter();
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

function salir() {
    clearStoredDiner(code.value);
    router.push({ name: 'public.identify', params: { code: code.value } });
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
