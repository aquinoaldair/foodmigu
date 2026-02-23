<template>
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">{{ formatDisplayDate(date) }}</h3>
            <button
                v-if="!readonly"
                type="button"
                @click="saveDay"
                :disabled="saving"
                class="px-3 py-1.5 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
            >
                {{ saving ? 'Guardando...' : 'Guardar' }}
            </button>
        </div>

        <div class="space-y-4">
            <div
                v-for="category in categories"
                :key="category.id"
                class="rounded-lg border border-gray-200 overflow-hidden"
            >
                <div class="px-4 py-2 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="font-medium text-gray-900">{{ category.name }}</span>
                    <span class="text-xs text-gray-500">
                        {{ category.selection_type === 'none' ? 'Solo informativo' : '' }}
                        {{ category.selection_type === 'single' ? 'Selección única' : '' }}
                        {{ category.selection_type === 'multiple' ? 'Selección múltiple' : '' }}
                        {{ category.is_required ? ' (Obligatorio)' : '' }}
                    </span>
                </div>
                <div class="p-4 space-y-3">
                    <div
                        v-for="(item, idx) in itemsByCategory[category.id]"
                        :key="item._id"
                        class="flex gap-3 items-start p-3 bg-gray-50 rounded-lg"
                    >
                        <div class="flex-1 space-y-2">
                            <input
                                v-model="item.name"
                                type="text"
                                placeholder="Nombre del platillo"
                                class="block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                :disabled="readonly"
                            />
                            <textarea
                                v-model="item.description"
                                rows="2"
                                placeholder="Descripción"
                                class="block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                :disabled="readonly"
                            />
                            <input
                                v-model.number="item.price"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="Precio"
                                class="block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                :disabled="readonly"
                            />
                        </div>
                        <button
                            v-if="!readonly"
                            type="button"
                            @click="removeItem(category.id, idx)"
                            class="p-1 text-red-600 hover:text-red-800"
                        >
                            ✕
                        </button>
                    </div>
                    <button
                        v-if="!readonly"
                        type="button"
                        @click="addItem(category.id)"
                        class="text-sm text-blue-600 hover:text-blue-800"
                    >
                        + Agregar opción
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, watch, computed } from 'vue';

const props = defineProps({
    date: { type: String, required: true },
    categories: { type: Array, default: () => [] },
    dayData: { type: Object, default: null },
    readonly: { type: Boolean, default: false },
});

const emit = defineEmits(['save']);

const saving = ref(false);

const itemsByCategory = reactive({});

function initItems() {
    props.categories.forEach((c) => {
        if (!itemsByCategory[c.id]) itemsByCategory[c.id] = [];
    });
}

watch(
    () => props.dayData,
    (data) => {
        initItems();
        if (data?.items) {
            const byCat = {};
            data.items.forEach((it) => {
                if (!byCat[it.menu_category_id]) byCat[it.menu_category_id] = [];
                byCat[it.menu_category_id].push({
                    _id: `item-${it.id}-${Math.random()}`,
                    id: it.id,
                    menu_category_id: it.menu_category_id,
                    name: it.name,
                    description: it.description ?? '',
                    price: it.price ?? null,
                    display_order: it.display_order ?? 0,
                });
            });
            props.categories.forEach((c) => {
                itemsByCategory[c.id] = byCat[c.id] ?? [];
            });
        } else {
            props.categories.forEach((c) => {
                itemsByCategory[c.id] = [];
            });
        }
    },
    { immediate: true, deep: true }
);

watch(() => props.categories, initItems, { immediate: true });

function addItem(categoryId) {
    if (!itemsByCategory[categoryId]) itemsByCategory[categoryId] = [];
    itemsByCategory[categoryId].push({
        _id: `new-${Date.now()}-${Math.random()}`,
        menu_category_id: categoryId,
        name: '',
        description: '',
        price: null,
        display_order: itemsByCategory[categoryId].length,
    });
}

function removeItem(categoryId, index) {
    itemsByCategory[categoryId].splice(index, 1);
}

function formatDisplayDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr + 'T12:00:00');
    return d.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
}

function buildPayload() {
    const items = [];
    props.categories.forEach((c) => {
        (itemsByCategory[c.id] || []).forEach((item, i) => {
            if (item.name?.trim()) {
                items.push({
                    menu_category_id: c.id,
                    name: item.name.trim(),
                    description: item.description?.trim() || null,
                    price: item.price ? Number(item.price) : null,
                    display_order: i,
                });
            }
        });
    });
    return { date: props.date, items };
}

function saveDay() {
    emit('save', buildPayload());
}

defineExpose({ buildPayload });
</script>
