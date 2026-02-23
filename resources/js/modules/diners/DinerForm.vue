<template>
    <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
            <label for="id_code" class="block text-sm font-medium text-gray-700">ID (código)</label>
            <input
                id="id_code"
                v-model="form.id_code"
                type="text"
                class="mt-1 block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                :class="{ 'border-red-500': errors.id_code }"
                placeholder="Ej: EMP001"
            />
            <p v-if="errors.id_code" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.id_code) ? errors.id_code[0] : errors.id_code }}</p>
        </div>

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

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({ id_code: '', name: '' }),
    },
    loading: { type: Boolean, default: false },
    errors: { type: Object, default: () => ({}) },
    submitLabel: { type: String, default: 'Guardar' },
});

const emit = defineEmits(['submit', 'cancel']);

const form = reactive({
    id_code: props.modelValue?.id_code ?? '',
    name: props.modelValue?.name ?? '',
});

watch(() => props.modelValue, (v) => {
    if (v) {
        form.id_code = v.id_code ?? '';
        form.name = v.name ?? '';
    }
}, { deep: true });

function handleSubmit() {
    emit('submit', {
        id_code: form.id_code.trim(),
        name: form.name.trim(),
    });
}
</script>
