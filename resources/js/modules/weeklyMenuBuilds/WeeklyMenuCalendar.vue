<template>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex justify-between items-center mb-4">
            <button
                type="button"
                @click="prevMonth"
                class="p-2 rounded-md hover:bg-gray-100 text-gray-600"
            >
                ←
            </button>
            <h2 class="text-lg font-semibold text-gray-900">{{ monthLabel }}</h2>
            <button
                type="button"
                @click="nextMonth"
                class="p-2 rounded-md hover:bg-gray-100 text-gray-600"
            >
                →
            </button>
        </div>
        <div class="grid grid-cols-7 gap-1">
            <div v-for="d in weekDays" :key="d" class="text-center text-xs font-medium text-gray-500 py-2">{{ d }}</div>
            <template v-for="(cell, idx) in calendarCells" :key="idx">
                <button
                    v-if="cell"
                    type="button"
                    @click="cell.isInRange && $emit('select-day', cell)"
                    :disabled="!cell.isInRange"
                    :class="[
                        'py-2 rounded-md text-sm transition-colors',
                        cell.isToday ? 'ring-2 ring-blue-500 font-semibold' : '',
                        cell.isConfigured ? 'bg-blue-100 text-blue-800 hover:bg-blue-200' : 'hover:bg-gray-100',
                        !cell.isCurrentMonth ? 'text-gray-300' : 'text-gray-900',
                        !cell.isInRange ? 'opacity-40 cursor-not-allowed hover:bg-transparent' : ''
                    ]"
                >
                    {{ cell.day }}
                </button>
                <div v-else class="py-2" />
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    configuredDates: { type: Array, default: () => [] },
    startDate: { type: String, default: null },
    endDate: { type: String, default: null },
    initialMonth: { type: String, default: null },
});

defineEmits(['select-day']);

const weekDays = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

const currentDate = ref(new Date());

function toYyyyMmDd(v) {
    if (!v || typeof v !== 'string') return null;
    const m = v.match(/(\d{4})-(\d{2})-(\d{2})/);
    return m ? m[0] : null;
}

function isInRange(dateStr) {
    const start = toYyyyMmDd(props.startDate);
    const end = toYyyyMmDd(props.endDate);
    if (!start || !end) return true;
    return dateStr >= start && dateStr <= end;
}

watch(
    () => props.initialMonth,
    (val) => {
        if (val) {
            const [y, m] = val.split('-').map(Number);
            currentDate.value = new Date(y, (m || 1) - 1, 1);
        }
    },
    { immediate: true }
);

const monthLabel = computed(() => {
    return currentDate.value.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
});

const configuredSet = computed(() => {
    return new Set(props.configuredDates.map((d) => (typeof d === 'string' ? d : d.date ?? d).split('T')[0]));
});

const calendarCells = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    const first = new Date(year, month, 1);
    const last = new Date(year, month + 1, 0);
    const startOffset = first.getDay() === 0 ? 6 : first.getDay() - 1;
    const prevMonthLast = new Date(year, month, 0).getDate();
    const today = new Date();
    const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

    const prevYear = month === 0 ? year - 1 : year;
    const prevMonthNum = month === 0 ? 12 : month;
    const cells = [];
    for (let i = 0; i < startOffset; i++) {
        const d = prevMonthLast - startOffset + i + 1;
        const dateStr = `${prevYear}-${String(prevMonthNum).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        cells.push({
            day: d,
            date: dateStr,
            isCurrentMonth: false,
            isToday: false,
            isConfigured: configuredSet.value.has(dateStr),
            isInRange: isInRange(dateStr),
        });
    }
    for (let d = 1; d <= last.getDate(); d++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        cells.push({
            day: d,
            date: dateStr,
            isCurrentMonth: true,
            isToday: dateStr === todayStr,
            isConfigured: configuredSet.value.has(dateStr),
            isInRange: isInRange(dateStr),
        });
    }
    const remaining = 42 - cells.length;
    const nextMonthStart = 1;
    for (let i = 0; i < remaining; i++) {
        const d = nextMonthStart + i;
        const nextYear = month === 11 ? year + 1 : year;
        const nextMonth = month === 11 ? 0 : month + 1;
        const dateStr = `${nextYear}-${String(nextMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        cells.push({
            day: d,
            date: dateStr,
            isCurrentMonth: false,
            isToday: false,
            isConfigured: configuredSet.value.has(dateStr),
            isInRange: isInRange(dateStr),
        });
    }
    return cells;
});

function prevMonth() {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1);
}

function nextMonth() {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1);
}
</script>
