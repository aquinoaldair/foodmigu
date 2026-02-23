<template>
    <form @submit.prevent="handleSubmit" class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Sección informativa (izquierda) -->
        <div class="lg:col-span-1 order-2 lg:order-1">
            <div class="lg:sticky lg:top-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
                <h3 class="text-sm font-semibold text-blue-900 mb-3">Cómo configurar los tipos de selección</h3>
                <p class="text-sm text-blue-800 mb-4">
                    Elige el tipo según cómo los comensales interactuarán con las opciones del menú.
                </p>
                <div class="space-y-2">
                    <div class="bg-white border border-blue-100 rounded-lg p-3 text-sm">
                        <p class="font-medium text-gray-900 mb-0.5">Solo informativo</p>
                        <p class="text-gray-600 mb-1">El comensal solo verá el elemento, no podrá elegirlo.</p>
                        <p class="text-xs text-gray-500 italic">Ejemplo: Sopa del día que se sirve a todos.</p>
                    </div>
                    <div class="bg-white border border-blue-100 rounded-lg p-3 text-sm">
                        <p class="font-medium text-gray-900 mb-0.5">Selección única</p>
                        <p class="text-gray-600 mb-1">El comensal debe elegir solo una opción.</p>
                        <p class="text-xs text-gray-500 italic">Ejemplo: Elegir entre pollo o carne.</p>
                    </div>
                    <div class="bg-white border border-blue-100 rounded-lg p-3 text-sm">
                        <p class="font-medium text-gray-900 mb-0.5">Selección múltiple</p>
                        <p class="text-gray-600 mb-1">El comensal puede elegir varias opciones.</p>
                        <p class="text-xs text-gray-500 italic">Ejemplo: Elegir guarniciones: arroz, frijoles, ensalada.</p>
                    </div>
                    <div class="bg-white border border-blue-100 rounded-lg p-3 text-sm">
                        <p class="font-medium text-gray-900 mb-0.5">Opcional (no obligatorio)</p>
                        <p class="text-gray-600 mb-1">El elemento se muestra pero el comensal puede no elegirlo.</p>
                        <p class="text-xs text-gray-500 italic">Ejemplo: Postre opcional.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario (derecha) -->
        <div class="lg:col-span-2 order-1 lg:order-2 space-y-4">
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
            <label for="selection_type" class="flex items-center gap-1.5 text-sm font-medium text-gray-700">
                Tipo de selección
                <div class="relative group">
                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-gray-200 text-gray-600 text-xs cursor-help">?</span>
                    <div class="absolute left-0 bottom-full mb-1 hidden group-hover:block z-10 w-48 p-2 text-xs text-gray-700 bg-gray-800 text-white rounded-lg shadow-lg">
                        <p class="font-medium mb-1">Tipos:</p>
                        <p><strong>Visible</strong> → solo informativo</p>
                        <p><strong>Única</strong> → una opción</p>
                        <p><strong>Múltiple</strong> → varias opciones</p>
                        <p class="mt-1"><strong>Opcional</strong> → no obligatorio (checkbox)</p>
                    </div>
                </div>
            </label>
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
            <p v-if="selectionTypeHelp" class="mt-1 text-xs text-gray-500">{{ selectionTypeHelp }}</p>
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

        <div class="flex flex-wrap gap-3 pt-4">
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
                @click="showPreview = true"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Previsualizar
            </button>
            <button
                type="button"
                @click="$emit('cancel')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                Cancelar
            </button>
        </div>
        </div>
    </form>

    <!-- Modal Previsualización -->
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="showPreview"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                @click.self="showPreview = false"
            >
                <div class="bg-white rounded-2xl shadow-xl max-w-md w-full max-h-[90vh] overflow-hidden flex flex-col">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Vista previa (comensal)</h3>
                        <button
                            type="button"
                            @click="showPreview = false"
                            class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6 overflow-y-auto bg-gray-50">
                        <p class="text-xs text-gray-500 mb-4">Así verá el comensal esta categoría al elegir su menú:</p>
                        <div class="bg-white rounded-2xl shadow-sm p-5">
                            <h2 class="text-lg font-semibold text-gray-900 mb-3">
                                {{ form.name || 'Nombre de categoría' }}
                                <span v-if="form.is_required" class="text-red-500">*</span>
                            </h2>
                            <p
                                v-if="form.selection_type === 'none' && !form.is_required"
                                class="text-sm text-gray-500 mb-3"
                            >
                                Opcional. Márcalo si lo vas a requerir.
                            </p>
                            <div class="space-y-2">
                                <template v-if="form.selection_type === 'none' && form.is_required">
                                    <div
                                        v-for="item in previewItemsNoneRequired"
                                        :key="item.id"
                                        class="flex justify-between items-center gap-3 p-4 rounded-xl border border-gray-200 bg-gray-50 cursor-not-allowed"
                                    >
                                        <span class="flex-1">
                                            <span class="font-medium text-gray-900 block">{{ item.name }}</span>
                                            <span v-if="item.description" class="text-sm text-gray-500">{{ item.description }}</span>
                                        </span>
                                        <input type="checkbox" :checked="true" disabled class="h-5 w-5 rounded border-gray-300 text-blue-600 shrink-0" />
                                    </div>
                                </template>
                                <template v-else-if="form.selection_type === 'none' && !form.is_required">
                                    <label
                                        v-for="item in previewItemsNone"
                                        :key="item.id"
                                        class="flex justify-between items-center gap-3 p-4 rounded-xl border border-gray-200 bg-white cursor-pointer"
                                    >
                                        <span class="flex-1">
                                            <span class="font-medium text-gray-900 block">{{ item.name }}</span>
                                            <span v-if="item.description" class="text-sm text-gray-500">{{ item.description }}</span>
                                        </span>
                                        <input type="checkbox" class="h-5 w-5 rounded border-gray-300 text-blue-600 shrink-0" />
                                    </label>
                                </template>
                                <template v-else-if="form.selection_type === 'single'">
                                    <label
                                        v-for="item in previewItemsSingle"
                                        :key="item.id"
                                        class="flex justify-between items-center gap-3 p-4 rounded-xl border border-gray-200 bg-white cursor-pointer"
                                    >
                                        <span class="flex-1">
                                            <span class="font-medium text-gray-900 block">{{ item.name }}</span>
                                            <span v-if="item.description" class="text-sm text-gray-500">{{ item.description }}</span>
                                        </span>
                                        <input type="radio" name="preview_single" class="h-5 w-5 text-blue-600 shrink-0" />
                                    </label>
                                </template>
                                <template v-else-if="form.selection_type === 'multiple'">
                                    <label
                                        v-for="item in previewItemsMultiple"
                                        :key="item.id"
                                        class="flex justify-between items-center gap-3 p-4 rounded-xl border border-gray-200 bg-white cursor-pointer"
                                    >
                                        <span class="flex-1">
                                            <span class="font-medium text-gray-900 block">{{ item.name }}</span>
                                            <span v-if="item.description" class="text-sm text-gray-500">{{ item.description }}</span>
                                        </span>
                                        <input type="checkbox" class="h-5 w-5 rounded border-gray-300 text-blue-600 shrink-0" />
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, reactive, watch, computed } from 'vue';
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

const typeHelpMap = {
    none: 'El comensal solo verá el elemento, no podrá elegirlo. Ejemplo: Sopa del día que se sirve a todos.',
    single: 'Este tipo permite que el comensal elija solo una opción. Ejemplo: Elegir proteína principal.',
    multiple: 'El comensal puede elegir varias opciones. Ejemplo: Guarniciones: arroz, frijoles, ensalada.',
};

const selectionTypeHelp = computed(() => typeHelpMap[form.selection_type] ?? '');

const showPreview = ref(false);

const previewItemsNoneRequired = [
    { id: 1, name: 'Sopa del día', description: 'Se sirve a todos' },
];
const previewItemsNone = [
    { id: 1, name: 'Postre opcional', description: '' },
];
const previewItemsSingle = [
    { id: 1, name: 'Pollo asado', description: '' },
    { id: 2, name: 'Carne a la plancha', description: '' },
];
const previewItemsMultiple = [
    { id: 1, name: 'Arroz', description: '' },
    { id: 2, name: 'Frijoles', description: '' },
    { id: 3, name: 'Ensalada', description: '' },
];

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

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>
