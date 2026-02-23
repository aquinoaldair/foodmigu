<template>
    <form @submit.prevent="handleSubmit" class="space-y-6">
        <div v-if="errors._general" class="p-4 rounded-md bg-red-50 text-red-700 text-sm">
            {{ errors._general }}
        </div>

        <div>
            <h2 class="text-lg font-medium text-gray-900 mb-4">Información básica</h2>
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        :class="{ 'border-red-500': errors.name }"
                    />
                    <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.name) ? errors.name[0] : errors.name }}</p>
                </div>
                <label class="flex items-center">
                    <input
                        v-model="form.is_active"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <span class="ml-2 text-sm text-gray-700">Activo</span>
                </label>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-medium text-gray-900 mb-4">Categorías</h2>
            <p class="text-sm text-gray-500 mb-4">Selecciona las categorías que componen este menú y define su orden.</p>

            <div v-if="loadingCategories" class="text-gray-500 text-sm">Cargando categorías...</div>

            <div v-else class="space-y-3">
                <div
                    v-for="category in availableCategories"
                    :key="category.id"
                    class="flex items-center gap-4 p-3 rounded-lg border border-gray-200 hover:bg-gray-50"
                >
                    <label class="flex items-center flex-1 cursor-pointer">
                        <input
                            v-model="selectedCategoryIds"
                            :value="category.id"
                            type="checkbox"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                        <span class="ml-2 text-sm font-medium text-gray-900">{{ category.name }}</span>
                    </label>
                    <div v-if="selectedCategoryIds.includes(category.id)" class="flex items-center gap-2">
                        <label :for="`order-${category.id}`" class="text-xs text-gray-500">Orden:</label>
                        <input
                            :id="`order-${category.id}`"
                            v-model.number="displayOrders[category.id]"
                            type="number"
                            min="0"
                            class="w-20 px-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        />
                    </div>
                </div>
                <p v-if="!availableCategories.length" class="text-sm text-gray-500">
                    No hay categorías activas. Crea algunas en Categorías del Menú primero.
                </p>
            </div>
        </div>

        <div class="flex gap-3 pt-4 border-t border-gray-200">
            <button
                type="submit"
                :disabled="loading"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
            >
                <span v-if="loading">Guardando...</span>
                <span v-else>{{ submitLabel }}</span>
            </button>
            <button
                type="button"
                @click="$emit('cancel')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                Cancelar
            </button>
        </div>
    </form>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue';
import { menuCategoryApi } from '../menuCategories/api';
import { menuApi } from './api';

const props = defineProps({
    modelValue: {
        type: Object,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    submitLabel: {
        type: String,
        default: 'Guardar',
    },
});

const emit = defineEmits(['submit', 'cancel']);

const form = reactive({
    name: props.modelValue?.name ?? '',
    is_active: props.modelValue?.is_active ?? true,
});

const availableCategories = ref([]);
const loadingCategories = ref(true);
const selectedCategoryIds = ref([]);
const displayOrders = reactive({});

watch(
    () => props.modelValue,
    (newVal) => {
        if (newVal) {
            form.name = newVal.name ?? '';
            form.is_active = newVal.is_active ?? true;
            if (newVal.categories && newVal.categories.length) {
                selectedCategoryIds.value = newVal.categories.map((c) => c.id);
                const orders = {};
                newVal.categories.forEach((c, index) => {
                    orders[c.id] = c.pivot?.display_order ?? index + 1;
                });
                Object.assign(displayOrders, orders);
            }
        }
    },
    { immediate: true, deep: true }
);

async function fetchCategories() {
    loadingCategories.value = true;
    try {
        const { data } = await menuCategoryApi.getAll();
        const categories = data.data ?? [];
        availableCategories.value = categories.filter((c) => c.is_active);
        availableCategories.value.forEach((c) => {
            if (!(c.id in displayOrders)) {
                displayOrders[c.id] = 0;
            }
        });
    } catch {
        availableCategories.value = [];
    } finally {
        loadingCategories.value = false;
    }
}

function handleSubmit() {
    const categories = selectedCategoryIds.value
        .map((id) => ({
            id,
            display_order: Number(displayOrders[id]) || 0,
        }))
        .sort((a, b) => a.display_order - b.display_order);

    const payload = {
        name: form.name.trim(),
        is_active: Boolean(form.is_active),
        categories,
    };
    emit('submit', payload);
}

onMounted(fetchCategories);
</script>
