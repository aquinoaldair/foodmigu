
<template>
    <div class="min-h-screen bg-gray-50">
        <LoadingOverlay :show="saving || publishing" :message="publishing ? 'Publicando...' : 'Procesando...'" />
        <div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
            <div v-if="loadingData" class="text-center py-12 text-gray-500">Cargando...</div>
            <template v-else-if="build">
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <router-link
                        :to="{ name: 'weekly-menu-builds.index' }"
                        class="inline-block text-sm text-gray-600 hover:text-gray-900 mb-4"
                    >
                        ← Regresar al listado
                    </router-link>
                    <div class="flex flex-wrap items-center gap-4">
                        <input
                            v-model="localTitle"
                            type="text"
                            class="text-xl font-bold text-gray-900 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            :class="{ 'border-0 bg-transparent': build.status === 'published' }"
                            :disabled="build.status === 'published'"
                        />
                        <span
                            :class="build.status === 'published'
                                ? 'px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800'
                                : 'px-3 py-1 text-sm font-medium rounded-full bg-amber-100 text-amber-800'"
                        >
                            {{ build.status === 'published' ? 'Publicado' : 'Borrador' }}
                        </span>
                        <template v-if="build.status === 'draft'">
                            <button
                                type="button"
                                @click="saveTitle"
                                :disabled="saving"
                                class="px-4 py-2 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                            >
                                {{ saving ? 'Guardando...' : 'Guardar' }}
                            </button>
                            <button
                                type="button"
                                @click="confirmPublish"
                                :disabled="saving"
                                class="px-4 py-2 text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 disabled:opacity-50"
                            >
                                Publicar
                            </button>
                        </template>
                    </div>
                    <p v-if="build.menu" class="mt-2 text-sm text-gray-500">Menú base: {{ build.menu.name }}</p>
                    <p v-if="build.start_date && build.end_date" class="mt-1 text-sm text-gray-500">
                        Período: {{ formatDateRange(build.start_date, build.end_date) }}
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div>
                        <WeeklyMenuCalendar
                            :configured-dates="configuredDates"
                            :start-date="build.start_date ? (typeof build.start_date === 'string' ? build.start_date : build.start_date.split('T')[0]) : null"
                            :end-date="build.end_date ? (typeof build.end_date === 'string' ? build.end_date : build.end_date.split('T')[0]) : null"
                            :initial-month="build.start_date ? (typeof build.start_date === 'string' ? build.start_date : build.start_date.split('T')[0]) : null"
                            @select-day="onSelectDay"
                        />
                    </div>
                    <div class="lg:col-span-2">
                        <div v-if="selectedDate" class="bg-white shadow rounded-lg p-6">
                            <WeeklyMenuDayEditor
                                ref="dayEditorRef"
                                :date="selectedDate"
                                :categories="categories"
                                :day-data="selectedDayData"
                                :readonly="build.status === 'published'"
                                @save="onSaveDay"
                            />
                        </div>
                        <div v-else class="bg-white shadow rounded-lg p-12 text-center text-gray-500">
                            Haz clic en un día del calendario para configurar los platillos.
                        </div>
                    </div>
                </div>
            </template>
            <div v-else class="text-center py-12 text-red-600">No encontrado.</div>

        <Teleport to="body">
            <div
                v-if="showPublishConfirm"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
                @click.self="showPublishConfirm = false"
            >
                <div class="absolute inset-0 bg-gray-900/60 pointer-events-none"></div>
                <div class="relative z-10 bg-white rounded-lg shadow-xl max-w-lg w-full p-6 pointer-events-auto">
                    <h3 class="text-lg font-medium text-gray-900">Confirmar publicación</h3>
                    <p class="mt-2 text-sm text-gray-500">Al publicar no podrás modificar este menú semanal. ¿Continuar?</p>
                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            type="button"
                            @click="showPublishConfirm = false"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            @click="doPublish"
                            :disabled="publishing"
                            class="px-4 py-2 rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 disabled:opacity-50"
                        >
                            {{ publishing ? 'Publicando...' : 'Sí, publicar' }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { weeklyMenuBuildApi } from './api';
import LoadingOverlay from '../../components/LoadingOverlay.vue';
import WeeklyMenuCalendar from './WeeklyMenuCalendar.vue';
import WeeklyMenuDayEditor from './WeeklyMenuDayEditor.vue';

const route = useRoute();
const build = ref(null);
const loadingData = ref(true);
const saving = ref(false);
const publishing = ref(false);
const showPublishConfirm = ref(false);
const selectedDate = ref(null);
const dayEditorRef = ref(null);

const buildId = computed(() => route.params.id);

const categories = computed(() => build.value?.menu?.categories ?? []);

const configuredDates = computed(() => build.value?.days ?? []);

const selectedDayData = computed(() => {
    if (!selectedDate.value || !build.value?.days) return null;
    return build.value.days.find((d) => (d.date ?? '').toString().startsWith(selectedDate.value.split('T')[0]));
});

async function fetchBuild() {
    loadingData.value = true;
    try {
        const { data } = await weeklyMenuBuildApi.getById(buildId.value);
        build.value = data.data;
        if (build.value?.title) {
            localTitle.value = build.value.title;
        }
        const halls = build.value?.dining_halls ?? build.value?.diningHalls ?? [];
        if (halls.length) {
            selectedDiningHallIds.value = halls.map((h) => Number(h.id));
        }
    } catch {
        build.value = null;
    } finally {
        loadingData.value = false;
    }
}

const localTitle = ref('');

function formatDateRange(start, end) {
    const extractDate = (v) => {
        if (!v) return null;
        const str = typeof v === 'string' ? v : String(v);
        const match = str.match(/(\d{4})-(\d{2})-(\d{2})/);
        return match ? match[0] : null;
    };
    const s = extractDate(start);
    const e = extractDate(end);
    if (!s || !e) return '';
    const [y1, m1, d1] = s.split('-');
    const [y2, m2, d2] = e.split('-');
    const fmt = (y, m, d) => `${d.padStart(2, '0')}/${m.padStart(2, '0')}/${y}`;
    return `${fmt(y1, m1, d1)} - ${fmt(y2, m2, d2)}`;
}

function onSelectDay(cell) {
    selectedDate.value = cell.date;
}

async function saveTitle() {
    saving.value = true;
    try {
        const { data } = await weeklyMenuBuildApi.update(buildId.value, { title: localTitle.value });
        build.value = data.data;
    } finally {
        saving.value = false;
    }
}

async function onSaveDay(payload) {
    saving.value = true;
    try {
        const { data } = await weeklyMenuBuildApi.update(buildId.value, { days: [payload] });
        build.value = data.data;
        selectedDate.value = null;
    } finally {
        saving.value = false;
    }
}

function confirmPublish() {
    showPublishConfirm.value = true;
}

async function doPublish() {
    publishing.value = true;
    try {
        const { data } = await weeklyMenuBuildApi.publish(buildId.value);
        build.value = data.data;
        showPublishConfirm.value = false;
    } finally {
        publishing.value = false;
    }
}

onMounted(fetchBuild);
</script>
