<template>
    <form @submit.prevent="handleSubmit" class="space-y-4">
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

        <div>
            <label for="selection_type" class="block text-sm font-medium text-gray-700">Tipo de selección</label>
            <select
                id="selection_type"
                v-model="form.selection_type"
                class="mt-1 block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                :class="{ 'border-red-500': errors.selection_type }"
            >
                <option
                    v-for="(label, value) in selectionTypes"
                    :key="value"
                    :value="value"
                >
                    {{ label }}
                </option>
            </select>
            <p v-if="errors.selection_type" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.selection_type) ? errors.selection_type[0] : errors.selection_type }}</p>
        </div>

        <div class="flex items-center gap-6">
            <label class="flex items-center">
                <input
                    v-model="form.is_required"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span class="ml-2 text-sm text-gray-700">Obligatorio</span>
            </label>
            <label class="flex items-center">
                <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span class="ml-2 text-sm text-gray-700">Activo</span>
            </label>
        </div>

        <div>
            <label for="display_order" class="block text-sm font-medium text-gray-700">Orden de visualización</label>
            <input
                id="display_order"
                v-model.number="form.display_order"
                type="number"
                min="0"
                class="mt-1 block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                :class="{ 'border-red-500': errors.display_order }"
            />
            <p v-if="errors.display_order" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.display_order) ? errors.display_order[0] : errors.display_order }}</p>
        </div>

        <div class="flex gap-3 pt-4">
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
import { reactive, watch } from 'vue';
import { SELECTION_TYPES } from './constants';

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({
            name: '',
            selection_type: 'none',
            is_required: true,
            is_active: true,
            display_order: 0,
        }),
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

const emit = defineEmits(['submit', 'cancel', 'update:modelValue']);

const selectionTypes = SELECTION_TYPES;

const form = reactive({
    name: props.modelValue?.name ?? '',
    selection_type: props.modelValue?.selection_type ?? 'none',
    is_required: props.modelValue?.is_required ?? true,
    is_active: props.modelValue?.is_active ?? true,
    display_order: props.modelValue?.display_order ?? 0,
});

watch(
    () => props.modelValue,
    (newVal) => {
        if (newVal) {
            form.name = newVal.name ?? '';
            form.selection_type = newVal.selection_type ?? 'none';
            form.is_required = newVal.is_required ?? true;
            form.is_active = newVal.is_active ?? true;
            form.display_order = newVal.display_order ?? 0;
        }
    },
    { deep: true }
);

watch(form, (val) => emit('update:modelValue', val), { deep: true });

function handleSubmit() {
    const payload = {
        name: form.name.trim(),
        selection_type: form.selection_type,
        is_required: Boolean(form.is_required),
        is_active: Boolean(form.is_active),
        display_order: Number(form.display_order) || 0,
    };
    emit('submit', payload);
}
</script>
