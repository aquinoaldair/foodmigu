<template>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ viewTitle }}
                </h1>
                <div v-if="view !== 'weeks'" class="flex gap-2">
                    <button
                        type="button"
                        @click="goBack"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
                    >
                        ← Volver
                    </button>
                </div>
            </div>

            <div v-if="loadingData" class="flex items-center justify-center py-24">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin" />
                    <p class="text-gray-500">Cargando...</p>
                </div>
            </div>

            <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-xl p-6">
                <p class="text-red-700">{{ error }}</p>
            </div>

            <template v-else>
                <!-- Vista: Lista semanas + comedor -->
                <div v-if="view === 'weeks'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="item in weeks"
                        :key="`${item.weekly_menu_build_id}-${item.dining_hall?.id}`"
                        class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition cursor-pointer"
                        @click="openWeek(item)"
                    >
                        <h3 class="text-lg font-bold text-gray-900 mb-1">{{ item.title }}</h3>
                        <p class="text-sm text-gray-500 mb-2">Comedor: {{ item.dining_hall?.name ?? '-' }}</p>
                        <span
                            :class="item.status === 'published'
                                ? 'inline-block px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800'
                                : 'inline-block px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800'"
                        >
                            {{ item.status === 'published' ? 'Publicado' : 'Borrador' }}
                        </span>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ item.published_at ? formatDate(item.published_at) : 'Sin publicar' }}
                        </p>
                        <button
                            type="button"
                            class="mt-4 w-full py-2 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700"
                            @click.stop="openWeek(item)"
                        >
                            Ver detalle
                        </button>
                    </div>
                </div>

                <!-- Vista: Detalle semana (días) -->
                <div v-else-if="view === 'week'" class="space-y-4">
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 mb-2">{{ weekDetail?.title }}</h2>
                                <p class="text-sm text-gray-500">Comedor: {{ weekDetail?.dining_hall?.name ?? '-' }}</p>
                                <p class="text-gray-500 mt-1">{{ weekDetail?.total_diners ?? 0 }} comensales</p>
                            </div>
                            <button
                                type="button"
                                :disabled="downloadingPdf"
                                @click="downloadPdfWeek"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                            >
                                {{ downloadingPdf ? 'Descargando...' : 'Descargar Resumen Semanal' }}
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="d in weekDetail?.days ?? []"
                            :key="d.id"
                            class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition cursor-pointer"
                            @click="openDay(d.id)"
                        >
                            <p class="text-base font-semibold text-gray-900 mb-2">{{ formatDayDate(d.date) }}</p>
                            <p class="text-sm text-gray-600">Total: {{ d.total_diners }}</p>
                            <p class="text-sm text-gray-600">Ya eligieron: {{ d.total_selected }}</p>
                            <p class="text-sm text-gray-600">Pendientes: {{ d.total_diners - d.total_selected }}</p>
                            <div class="mt-3 h-2 rounded-full bg-gray-200 overflow-hidden">
                                <div
                                    class="h-full rounded-full transition"
                                    :class="progressColor(d)"
                                    :style="{ width: progressWidth(d) + '%' }"
                                />
                            </div>
                            <button
                                type="button"
                                class="mt-4 w-full py-2 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700"
                                @click.stop="openDay(d.id)"
                            >
                                Ver día
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Vista: Detalle día -->
                <div v-else-if="view === 'day'" class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 mb-2">
                                    {{ dayDetail?.day ? formatDayDate(dayDetail.day.date) : '' }}
                                </h2>
                                <p class="text-gray-500">Comedor: {{ dayDetail?.dining_hall?.name ?? '-' }}</p>
                                <p class="text-gray-500 text-sm">{{ dayDetail?.day?.build_title }}</p>
                            </div>
                            <button
                                type="button"
                                :disabled="downloadingPdf"
                                @click="downloadPdfDay"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                            >
                                {{ downloadingPdf ? 'Descargando...' : 'Descargar PDF Día' }}
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="rounded-lg bg-gray-50 p-4">
                                <p class="text-sm text-gray-500">Total comensales</p>
                                <p class="text-2xl font-bold text-gray-900">{{ dayDetail?.summary?.total_diners ?? 0 }}</p>
                            </div>
                            <div class="rounded-lg bg-green-50 p-4">
                                <p class="text-sm text-gray-500">Ya eligieron</p>
                                <p class="text-2xl font-bold text-green-700">{{ dayDetail?.summary?.total_selected ?? 0 }}</p>
                            </div>
                            <div class="rounded-lg bg-amber-50 p-4">
                                <p class="text-sm text-gray-500">Pendientes</p>
                                <p class="text-2xl font-bold text-amber-700">{{ dayDetail?.summary?.total_pending ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Conteo por opción</h3>
                        <div class="space-y-4">
                            <div
                                v-for="cat in groupByCategory(dayDetail?.count_by_option ?? [])"
                                :key="cat.category_id"
                                class="border border-gray-200 rounded-xl p-4"
                            >
                                <h4 class="font-medium text-gray-900 mb-3">{{ cat.category_name }}</h4>
                                <div class="space-y-2">
                                    <div
                                        v-for="opt in cat.options"
                                        :key="opt.item_id"
                                        class="flex justify-between items-center py-1"
                                    >
                                        <span class="text-gray-700">{{ opt.item_name }}</span>
                                        <span class="font-semibold text-gray-900">{{ opt.total_selections }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Totales por categoría</h3>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="c in (dayDetail?.total_by_category ?? [])"
                                :key="c.menu_category_id"
                                class="inline-block px-4 py-2 rounded-lg bg-blue-100 text-blue-800 text-sm font-medium"
                            >
                                {{ c.menu_category_name }}: {{ c.total_selections }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex gap-2 mb-4">
                            <button
                                type="button"
                                :class="activeTab === 'selected' ? 'border-blue-600 text-blue-600' : 'border-gray-300 text-gray-600'"
                                class="px-4 py-2 border-b-2 font-medium"
                                @click="activeTab = 'selected'"
                            >
                                Ya eligieron ({{ dayDetail?.diners_selected?.length ?? 0 }})
                            </button>
                            <button
                                type="button"
                                :class="activeTab === 'pending' ? 'border-blue-600 text-blue-600' : 'border-gray-300 text-gray-600'"
                                class="px-4 py-2 border-b-2 font-medium"
                                @click="activeTab = 'pending'"
                            >
                                Pendientes ({{ dayDetail?.diners_pending?.length ?? 0 }})
                            </button>
                        </div>

                        <div v-if="activeTab === 'selected'" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Opciones elegidas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="d in dayDetail?.diners_selected ?? []" :key="d.diner_id">
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ d.id_code }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ d.name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            <div class="space-y-1">
                                                <span
                                                    v-for="(cat, i) in d.options_by_category"
                                                    :key="i"
                                                    class="block"
                                                >
                                                    <strong>{{ cat.category_name }}:</strong>
                                                    {{ cat.items?.join(', ') || '-' }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="d in dayDetail?.diners_pending ?? []" :key="d.diner_id">
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ d.id_code }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ d.name }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { dashboardApi } from '../modules/dashboard/api';
const loadingData = ref(false);
const error = ref(null);

const view = ref('weeks');
const selectedWeekId = ref(null);
const selectedDiningHallId = ref(null);
const weeks = ref([]);
const weekDetail = ref(null);
const dayDetail = ref(null);
const activeTab = ref('selected');
const downloadingPdf = ref(false);

const viewTitle = computed(() => {
    if (view.value === 'weeks') return 'Métricas de Menús Semanales';
    if (view.value === 'week') return weekDetail.value?.title ?? 'Detalle semana';
    return dayDetail.value ? `Día: ${formatDayDate(dayDetail.value.day?.date)}` : 'Detalle día';
});

function formatDate(d) {
    if (!d) return '';
    return new Date(d).toLocaleDateString('es-ES');
}

function formatDayDate(d) {
    if (!d) return '';
    const date = typeof d === 'string' ? new Date(d + 'T12:00:00') : d;
    return date.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' });
}

function progressWidth(d) {
    if (!d || d.total_diners === 0) return 0;
    return Math.round((d.total_selected / d.total_diners) * 100);
}

function progressColor(d) {
    const pct = progressWidth(d);
    if (pct >= 70) return 'bg-green-500';
    if (pct >= 40) return 'bg-amber-500';
    return 'bg-red-400';
}

function groupByCategory(options) {
    if (!options?.length) return [];
    const byCat = {};
    for (const opt of options) {
        const cid = opt.menu_category_id ?? 'uncategorized';
        if (!byCat[cid]) {
            byCat[cid] = { category_id: cid, category_name: opt.menu_category_name ?? '-', options: [] };
        }
        byCat[cid].options.push(opt);
    }
    return Object.values(byCat);
}

async function fetchWeeks() {
    loadingData.value = true;
    error.value = null;
    try {
        const { data } = await dashboardApi.weeks();
        weeks.value = data.data ?? [];
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Error al cargar';
    } finally {
        loadingData.value = false;
    }
}

function openWeek(item) {
    selectedWeekId.value = item.weekly_menu_build_id;
    selectedDiningHallId.value = item.dining_hall?.id;
    view.value = 'week';
    loadingData.value = true;
    error.value = null;
    dashboardApi
        .week(item.weekly_menu_build_id, item.dining_hall?.id)
        .then(({ data }) => {
            weekDetail.value = data.data;
        })
        .catch((e) => {
            error.value = e.response?.data?.message ?? 'Error al cargar';
        })
        .finally(() => {
            loadingData.value = false;
        });
}

async function openDay(dayId) {
    view.value = 'day';
    loadingData.value = true;
    error.value = null;
    try {
        const { data } = await dashboardApi.day(dayId, selectedDiningHallId.value);
        dayDetail.value = data.data;
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Error al cargar';
    } finally {
        loadingData.value = false;
    }
}

function triggerBlobDownload(blob, filename) {
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename || 'documento.pdf';
    a.click();
    URL.revokeObjectURL(url);
}

async function downloadPdfDay() {
    if (!selectedDiningHallId.value || !dayDetail.value?.day?.id) return;
    downloadingPdf.value = true;
    error.value = null;
    try {
        const res = await dashboardApi.downloadPdfDay(
            dayDetail.value.day.id,
            selectedDiningHallId.value
        );
        const blob = res.data;
        if (blob.type === 'application/json') {
            const text = await blob.text();
            const json = JSON.parse(text);
            error.value = json.message ?? 'Error al descargar PDF';
            return;
        }
        const disposition = res.headers?.['content-disposition'];
        const filenameMatch = disposition?.match(/filename\*?=(?:UTF-8'')?"?([^";\n]+)"?/i)
            || disposition?.match(/filename="?([^";\n]+)"?/i);
        const filename = filenameMatch ? filenameMatch[1].trim() : `dia_${dayDetail.value.day.date}_${(dayDetail.value.dining_hall?.name ?? 'comedor').replace(/\s+/g, '_')}.pdf`;
        triggerBlobDownload(blob, filename);
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Error al descargar PDF';
    } finally {
        downloadingPdf.value = false;
    }
}

async function downloadPdfWeek() {
    if (!selectedDiningHallId.value || !selectedWeekId.value) return;
    downloadingPdf.value = true;
    error.value = null;
    try {
        const res = await dashboardApi.downloadPdfWeek(
            selectedWeekId.value,
            selectedDiningHallId.value
        );
        const blob = res.data;
        if (blob.type === 'application/json') {
            const text = await blob.text();
            const json = JSON.parse(text);
            error.value = json.message ?? 'Error al descargar PDF';
            return;
        }
        const disposition = res.headers?.['content-disposition'];
        const filenameMatch = disposition?.match(/filename\*?=(?:UTF-8'')?"?([^";\n]+)"?/i)
            || disposition?.match(/filename="?([^";\n]+)"?/i);
        const filename = filenameMatch ? filenameMatch[1].trim() : `resumen_semana_${(weekDetail.value?.title ?? 'semana').replace(/\s+/g, '_')}_${(weekDetail.value?.dining_hall?.name ?? 'comedor').replace(/\s+/g, '_')}.pdf`;
        triggerBlobDownload(blob, filename);
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Error al descargar PDF';
    } finally {
        downloadingPdf.value = false;
    }
}

function goBack() {
    if (view.value === 'day') {
        view.value = 'week';
        dayDetail.value = null;
        loadingData.value = true;
        dashboardApi
            .week(selectedWeekId.value, selectedDiningHallId.value)
            .then(({ data }) => {
                weekDetail.value = data.data;
            })
            .catch(() => {})
            .finally(() => {
                loadingData.value = false;
            });
    } else if (view.value === 'week') {
        view.value = 'weeks';
        weekDetail.value = null;
        selectedWeekId.value = null;
        selectedDiningHallId.value = null;
        fetchWeeks();
    }
}

onMounted(() => {
    if (view.value === 'weeks') fetchWeeks();
});
</script>
