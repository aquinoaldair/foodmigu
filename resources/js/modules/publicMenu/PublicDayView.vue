<template>
    <div class="min-h-screen bg-gray-50 px-4 py-6 pb-24" spellcheck="false" autocorrect="off">
        <LoadingOverlay :show="saving" message="Guardando selección..." />
        <div class="max-w-md mx-auto">
            <router-link
                :to="{ name: 'public.menus', params: { code } }"
                class="flex items-center justify-center gap-2 py-3 px-4 mb-6 rounded-xl text-base font-semibold bg-gray-200 text-gray-700 active:scale-95 transition"
                spellcheck="false"
                autocorrect="off"
            >
                ← Menús
            </router-link>

            <div v-if="loading" class="flex items-center justify-center py-24 transition-all duration-200">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin" />
                    <p class="text-base text-gray-500" spellcheck="false" autocorrect="off">Cargando...</p>
                </div>
            </div>
            <div v-else-if="error" class="bg-white rounded-2xl shadow-sm p-5 mb-4">
                <div class="bg-red-50 border border-red-200 rounded-xl p-5">
                    <p class="text-red-700" spellcheck="false" autocorrect="off">{{ error }}</p>
                </div>
            </div>
            <div v-else-if="!diner" class="bg-white rounded-2xl shadow-sm p-5 mb-4">
                <p class="text-amber-800 mb-4" spellcheck="false" autocorrect="off">Debes identificarte primero.</p>
                <router-link
                    :to="{ name: 'public.identify', params: { code } }"
                    class="inline-flex items-center justify-center w-full py-3 rounded-xl text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 transition"
                    spellcheck="false"
                    autocorrect="off"
                >
                    Identificarse
                </router-link>
            </div>
            <template v-else>
                <div class="mb-6">
                    <h1 class="text-xl font-bold text-gray-900" spellcheck="false" autocorrect="off">
                        {{ day ? formatDate(day.date) : '' }}
                    </h1>
                    <p v-if="day?.weekly_menu_build?.title" class="text-gray-500 text-sm mt-1" spellcheck="false" autocorrect="off">
                        {{ day.weekly_menu_build.title }}
                    </p>
                </div>

                <div v-if="deadlinePassed" class="mb-6 rounded-xl px-4 py-3 bg-red-50 border border-red-200">
                    <p class="text-red-700 font-medium" spellcheck="false" autocorrect="off">
                        El plazo para modificar la selección ha finalizado.
                    </p>
                </div>

                <div v-if="selectionLocked && !deadlinePassed" class="mb-6 rounded-xl px-4 py-3 bg-amber-50 border border-amber-200">
                    <p class="text-amber-800 font-medium" spellcheck="false" autocorrect="off">
                        Ya has guardado tu selección para este día. No es posible modificarla.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6 transition-all duration-200">
                    <div
                        v-for="group in byCategory"
                        :key="group.category.id"
                        class="bg-white rounded-2xl shadow-sm p-5 mb-4"
                    >
                        <h2 class="text-lg font-semibold text-gray-900 mb-3" spellcheck="false" autocorrect="off">
                            {{ group.category.name }}
                            <span v-if="group.category.is_required" class="text-red-500">*</span>
                        </h2>
                        <p
                            v-if="group.category.selection_type === 'none' && !group.category.is_required"
                            class="text-sm text-gray-500 mb-3"
                            spellcheck="false"
                            autocorrect="off"
                        >
                            Opcional. Márcalo si lo vas a requerir.
                        </p>

                        <div
                            v-if="group.category.selection_type === 'none' && !group.category.is_required"
                            class="space-y-2"
                        >
                            <div
                                v-for="item in group.items"
                                :key="item.id"
                                class="flex justify-between items-center gap-3 p-4 rounded-xl border transition"
                                :class="[
                                    selectionMultiple(group.category.id).includes(item.id)
                                        ? 'border-blue-500 bg-blue-50'
                                        : 'border-gray-200 bg-white',
                                    (deadlinePassed || selectionLocked) ? 'cursor-not-allowed opacity-70' : 'cursor-pointer active:scale-95'
                                ]"
                                @click="!(deadlinePassed || selectionLocked) && onCardClick($event, group, item)"
                            >
                                <span class="flex-1 flex items-start gap-3 min-w-0">
                                    <button
                                        v-if="item.image?.url"
                                        type="button"
                                        class="shrink-0 w-12 h-12 sm:w-14 sm:h-14 rounded-lg overflow-hidden border border-gray-200 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @click="openLightbox(item.image.url)"
                                    >
                                        <img
                                            :src="item.image.url"
                                            :alt="item.name"
                                            class="w-full h-full object-cover"
                                        />
                                    </button>
                                    <span class="min-w-0 flex-1 flex flex-col">
                                        <span class="font-medium text-gray-900 block" spellcheck="false" autocorrect="off">{{ item.name }}</span>
                                        <span v-if="item.description" class="text-sm text-gray-500 block" spellcheck="false" autocorrect="off">{{ item.description }}</span>
                                    </span>
                                </span>
                                <input
                                    type="checkbox"
                                    :value="item.id"
                                    :checked="selectionMultiple(group.category.id).includes(item.id)"
                                    :disabled="deadlinePassed || selectionLocked"
                                    class="h-5 w-5 rounded border-gray-300 text-blue-600 shrink-0"
                                    spellcheck="false"
                                    autocorrect="off"
                                    @change="toggleMultiple(group.category.id, item.id)"
                                />
                            </div>
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
                                <span class="flex-1 flex items-start gap-3 min-w-0">
                                    <button
                                        v-if="item.image?.url"
                                        type="button"
                                        class="shrink-0 w-12 h-12 sm:w-14 sm:h-14 rounded-lg overflow-hidden border border-gray-200 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @click="openLightbox(item.image.url)"
                                    >
                                        <img
                                            :src="item.image.url"
                                            :alt="item.name"
                                            class="w-full h-full object-cover"
                                        />
                                    </button>
                                    <span class="min-w-0">
                                        <span class="font-medium text-gray-900 block" spellcheck="false" autocorrect="off">{{ item.name }}</span>
                                        <span v-if="item.description" class="text-sm text-gray-500" spellcheck="false" autocorrect="off">{{ item.description }}</span>
                                    </span>
                                </span>
                                <input type="checkbox" :checked="true" disabled class="h-5 w-5 rounded border-gray-300 text-blue-600 shrink-0" spellcheck="false" autocorrect="off" />
                            </div>
                        </div>

                        <div v-if="group.category.selection_type === 'single'" class="space-y-2">
                            <div
                                v-for="item in group.items"
                                :key="item.id"
                                class="flex justify-between items-center gap-3 p-4 rounded-xl border transition"
                                :class="[
                                    selectionSingle(group.category.id) === item.id
                                        ? 'border-blue-500 bg-blue-50'
                                        : 'border-gray-200 bg-white',
                                    (deadlinePassed || selectionLocked) ? 'cursor-not-allowed opacity-70' : 'cursor-pointer active:scale-95'
                                ]"
                                @click="!(deadlinePassed || selectionLocked) && onCardClick($event, group, item)"
                            >
                                <span class="flex-1 flex items-start gap-3 min-w-0">
                                    <button
                                        v-if="item.image?.url"
                                        type="button"
                                        class="shrink-0 w-12 h-12 sm:w-14 sm:h-14 rounded-lg overflow-hidden border border-gray-200 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @click="openLightbox(item.image.url)"
                                    >
                                        <img
                                            :src="item.image.url"
                                            :alt="item.name"
                                            class="w-full h-full object-cover"
                                        />
                                    </button>
                                    <span class="min-w-0 flex-1 flex flex-col">
                                        <span class="font-medium text-gray-900 block" spellcheck="false" autocorrect="off">{{ item.name }}</span>
                                        <span v-if="item.description" class="text-sm text-gray-500 block" spellcheck="false" autocorrect="off">{{ item.description }}</span>
                                    </span>
                                </span>
                                <input
                                    type="radio"
                                    :name="`cat_${group.category.id}`"
                                    :value="item.id"
                                    :checked="selectionSingle(group.category.id) === item.id"
                                    :disabled="deadlinePassed || selectionLocked"
                                    class="h-5 w-5 text-blue-600 shrink-0"
                                    spellcheck="false"
                                    autocorrect="off"
                                    @change="setSingle(group.category.id, item.id)"
                                />
                            </div>
                        </div>

                        <div v-if="group.category.selection_type === 'multiple'" class="space-y-2">
                            <div
                                v-for="item in group.items"
                                :key="item.id"
                                class="flex justify-between items-center gap-3 p-4 rounded-xl border transition"
                                :class="[
                                    selectionMultiple(group.category.id).includes(item.id)
                                        ? 'border-blue-500 bg-blue-50'
                                        : 'border-gray-200 bg-white',
                                    (deadlinePassed || selectionLocked) ? 'cursor-not-allowed opacity-70' : 'cursor-pointer active:scale-95'
                                ]"
                                @click="!(deadlinePassed || selectionLocked) && onCardClick($event, group, item)"
                            >
                                <span class="flex-1 flex items-start gap-3 min-w-0">
                                    <button
                                        v-if="item.image?.url"
                                        type="button"
                                        class="shrink-0 w-12 h-12 sm:w-14 sm:h-14 rounded-lg overflow-hidden border border-gray-200 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @click="openLightbox(item.image.url)"
                                    >
                                        <img
                                            :src="item.image.url"
                                            :alt="item.name"
                                            class="w-full h-full object-cover"
                                        />
                                    </button>
                                    <span class="min-w-0 flex-1 flex flex-col">
                                        <span class="font-medium text-gray-900 block" spellcheck="false" autocorrect="off">{{ item.name }}</span>
                                        <span v-if="item.description" class="text-sm text-gray-500 block" spellcheck="false" autocorrect="off">{{ item.description }}</span>
                                    </span>
                                </span>
                                <input
                                    type="checkbox"
                                    :value="item.id"
                                    :checked="selectionMultiple(group.category.id).includes(item.id)"
                                    :disabled="deadlinePassed || selectionLocked"
                                    class="h-5 w-5 rounded border-gray-300 text-blue-600 shrink-0"
                                    spellcheck="false"
                                    autocorrect="off"
                                    @change="toggleMultiple(group.category.id, item.id)"
                                />
                            </div>
                        </div>
                    </div>

                    <div v-if="submitError" class="text-red-600 text-sm" spellcheck="false" autocorrect="off">{{ submitError }}</div>
                </form>
            </template>
        </div>

        <Teleport to="body">
            <div
                ref="lightboxRef"
                v-if="lightboxImage"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90"
                role="dialog"
                aria-modal="true"
                aria-label="Ver imagen"
                tabindex="-1"
                @click.self="closeLightbox"
                @keydown="onLightboxKeydown"
            >
                <button
                    type="button"
                    class="absolute top-4 right-4 z-10 w-12 h-12 flex items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/30 active:scale-95 transition touch-manipulation"
                    aria-label="Cerrar"
                    @click="closeLightbox"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <img
                    :src="lightboxImage"
                    alt="Imagen ampliada"
                    class="max-w-full max-h-[85vh] w-auto h-auto object-contain rounded-lg"
                    @click.stop
                />
            </div>
        </Teleport>

        <div
            v-if="saved"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            @click.self="onSavedAccept"
        >
            <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                    <span class="text-3xl text-green-600" spellcheck="false" autocorrect="off">✓</span>
                </div>
                <p class="text-lg font-semibold text-gray-900 mb-2" spellcheck="false" autocorrect="off">¡Guardado!</p>
                <p class="text-gray-600 mb-6" spellcheck="false" autocorrect="off">Selección guardada correctamente</p>
                <button
                    type="button"
                    @click="onSavedAccept"
                    class="w-full py-3 rounded-xl text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 transition"
                    spellcheck="false"
                    autocorrect="off"
                >
                    Aceptar
                </button>
            </div>
        </div>

        <!-- Confirmation dialog -->
        <div
            v-if="showConfirmDialog"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            @click.self="showConfirmDialog = false"
        >
            <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-amber-100 flex items-center justify-center">
                    <span class="text-3xl text-amber-600" spellcheck="false" autocorrect="off">⚠</span>
                </div>
                <p class="text-lg font-semibold text-gray-900 mb-2" spellcheck="false" autocorrect="off">¿Confirmar selección?</p>
                <p class="text-gray-600 mb-6" spellcheck="false" autocorrect="off">
                    Una vez guardada tu selección, <strong>no podrás modificarla</strong>. ¿Estás seguro de que deseas continuar?
                </p>
                <div class="flex gap-3">
                    <button
                        type="button"
                        @click="showConfirmDialog = false"
                        class="flex-1 py-3 rounded-xl text-lg font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 active:scale-95 transition"
                        spellcheck="false"
                        autocorrect="off"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        @click="confirmSubmit"
                        class="flex-1 py-3 rounded-xl text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 transition"
                        spellcheck="false"
                        autocorrect="off"
                    >
                        Sí, guardar
                    </button>
                </div>
            </div>
        </div>

        <div
            v-if="diner && !loading && !error && day && !deadlinePassed && !selectionLocked"
            class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-lg"
        >
            <button
                type="button"
                :disabled="saving"
                @click="requestConfirmation"
                class="w-full py-4 rounded-xl text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 transition disabled:opacity-50 disabled:cursor-not-allowed"
                spellcheck="false"
                autocorrect="off"
            >
                {{ saving ? 'Guardando...' : 'Guardar selección' }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { publicMenuApi, getStoredDiner } from './api';
import LoadingOverlay from '../../components/LoadingOverlay.vue';

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
const selectionLocked = ref(false);
const showConfirmDialog = ref(false);

const selectionState = reactive({ single: {}, multiple: {} });
const lightboxImage = ref(null);
const lightboxRef = ref(null);

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

function openLightbox(url) {
    lightboxImage.value = url;
}

function closeLightbox() {
    lightboxImage.value = null;
}

function onSavedAccept() {
    saved.value = false;
    router.push({ name: 'public.menus', params: { code: code.value } });
}

function onLightboxKeydown(e) {
    if (e.key === 'Escape') closeLightbox();
}

function formatDate(d) {
    if (!d) return '';
    let date;
    if (typeof d === 'string') {
        const parts = d.split('T')[0].split('-').map(Number);
        date = parts.length >= 3 ? new Date(parts[0], parts[1] - 1, parts[2]) : new Date(d);
    } else {
        date = d instanceof Date ? d : new Date(d);
    }
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

function onCardClick(e, group, item) {
    if (e.target.closest('button') || e.target.closest('input')) return;
    const cat = group.category;
    if (cat.selection_type === 'single') {
        setSingle(cat.id, item.id);
    } else if (cat.selection_type === 'multiple' || (cat.selection_type === 'none' && !cat.is_required)) {
        toggleMultiple(cat.id, item.id);
    }
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

function requestConfirmation() {
    if (deadlinePassed.value || selectionLocked.value) return;
    showConfirmDialog.value = true;
}

async function confirmSubmit() {
    showConfirmDialog.value = false;
    await submit();
}

async function submit() {
    if (deadlinePassed.value || selectionLocked.value) return;
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
        selectionLocked.value = true;
        syncSelectionsFromApi().catch(() => {});
    } catch (e) {
        submitError.value = e.response?.data?.message ?? 'Error al guardar';
    } finally {
        saving.value = false;
    }
}

async function syncSelectionsFromApi() {
    if (!diner.value || !day.value) return;
    try {
        const { data } = await publicMenuApi.mySelections(dayId.value, diner.value.id, { _t: Date.now() });
        const ids = data.selections ?? [];
        Object.keys(selectionState.single).forEach((k) => delete selectionState.single[k]);
        Object.keys(selectionState.multiple).forEach((k) => delete selectionState.multiple[k]);
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
    } catch {
        // Ignore sync errors; UI keeps current state
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

        // Lock the form if the user already has saved selections
        if (ids.length > 0) {
            selectionLocked.value = true;
        }

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

watch(lightboxImage, (url) => {
    if (url) {
        setTimeout(() => lightboxRef.value?.focus(), 50);
    }
});

watch(dayId, () => {
    Object.keys(selectionState.single).forEach((k) => delete selectionState.single[k]);
    Object.keys(selectionState.multiple).forEach((k) => delete selectionState.multiple[k]);
    fetchDay();
});

onMounted(fetchDay);
</script>
