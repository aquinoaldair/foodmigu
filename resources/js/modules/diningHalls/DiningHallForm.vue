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
            <label for="code" class="block text-sm font-medium text-gray-700">Código</label>
            <input
                id="code"
                v-model="form.code"
                type="text"
                class="mt-1 block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                :class="{ 'border-red-500': errors.code }"
                placeholder="Ej: norte, empresa-x"
            />
            <p v-if="errors.code" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.code) ? errors.code[0] : errors.code }}</p>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
            <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="mt-1 block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                :class="{ 'border-red-500': errors.description }"
            />
            <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ Array.isArray(errors.description) ? errors.description[0] : errors.description }}</p>
        </div>

        <div class="flex items-center">
            <label class="flex items-center">
                <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span class="ml-2 text-sm text-gray-700">Activo</span>
            </label>
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
        default: () => ({
            name: '',
            code: '',
            description: '',
            is_active: true,
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

const form = reactive({
    name: props.modelValue?.name ?? '',
    code: props.modelValue?.code ?? '',
    description: props.modelValue?.description ?? '',
    is_active: props.modelValue?.is_active ?? true,
});

watch(
    () => props.modelValue,
    (newVal) => {
        if (newVal) {
            form.name = newVal.name ?? '';
            form.code = newVal.code ?? '';
            form.description = newVal.description ?? '';
            form.is_active = newVal.is_active ?? true;
        }
    },
    { deep: true }
);

watch(form, (val) => emit('update:modelValue', val), { deep: true });

function handleSubmit() {
    const payload = {
        name: form.name.trim(),
        code: form.code.trim(),
        description: form.description?.trim() || null,
        is_active: Boolean(form.is_active),
    };
    emit('submit', payload);
}
</script>
