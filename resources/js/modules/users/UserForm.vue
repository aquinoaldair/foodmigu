<template>
    <form @submit.prevent="submit" class="space-y-8 max-w-3xl">
        <div v-if="errors._general" class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            {{ errors._general }}
        </div>

        <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
            <div class="sm:col-span-1">
                <label for="name" class="block text-sm font-semibold text-gray-700">Nombre Completo</label>
                <div class="mt-2">
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                    />
                </div>
                <p v-if="errors.name" class="mt-2 text-sm text-red-600">{{ errors.name[0] }}</p>
            </div>

            <div class="sm:col-span-1">
                <label for="email" class="block text-sm font-semibold text-gray-700">Correo Electrónico</label>
                <div class="mt-2">
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                    />
                </div>
                <p v-if="errors.email" class="mt-2 text-sm text-red-600">{{ errors.email[0] }}</p>
            </div>

            <div class="sm:col-span-1">
                <label for="password" class="block text-sm font-semibold text-gray-700">
                    Contraseña <span v-if="isEditing" class="text-gray-500 font-normal">(Dejar en blanco para no cambiar)</span>
                </label>
                <div class="relative mt-2">
                    <input
                        id="password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        :required="!isEditing"
                        class="block w-full rounded-md border-0 py-2.5 px-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none"
                    >
                        <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                <p v-if="errors.password" class="mt-2 text-sm text-red-600">{{ errors.password[0] }}</p>
            </div>

            <div class="sm:col-span-1">
                <label for="role" class="block text-sm font-semibold text-gray-700">Rol de Usuario</label>
                <div class="mt-2">
                    <select
                        id="role"
                        v-model="form.role"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 bg-white"
                    >
                        <option value="admin">Administrador (Acceso Total)</option>
                        <option value="user">Usuario Limitado (Solo Comedores Asignados)</option>
                    </select>
                </div>
                <p v-if="errors.role" class="mt-2 text-sm text-red-600">{{ errors.role[0] }}</p>
            </div>
        </div>

        <div v-if="form.role === 'user'" class="pt-6 border-t border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 mb-1">Comedores Asignados</h3>
            <p class="text-sm text-gray-500 mb-5">Selecciona los comedores a los que este usuario tendrá acceso en su Dashboard.</p>
            <div v-if="loadingHalls" class="text-sm text-gray-500 flex items-center gap-2">
                <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> 
                Cargando comedores...
            </div>
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label v-for="hall in availableHalls" :key="hall.id" class="relative flex items-start py-4 px-4 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors" :class="{'ring-2 ring-blue-500 border-blue-500 bg-blue-50': form.dining_halls.includes(hall.id)}">
                    <div class="min-w-0 flex-1 text-sm leading-6">
                        <span class="font-medium text-gray-900 select-none block">{{ hall.name }}</span>
                    </div>
                    <div class="ml-3 flex h-6 items-center">
                        <input
                            type="checkbox"
                            :value="hall.id"
                            v-model="form.dining_halls"
                            class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-600"
                        />
                    </div>
                </label>
            </div>
            <p v-if="errors.dining_halls" class="mt-2 text-sm text-red-600">{{ errors.dining_halls[0] }}</p>
        </div>

        <div class="pt-6 flex justify-end gap-3 border-t border-gray-200 mt-8">
            <button
                type="button"
                @click="$emit('cancel')"
                class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors"
            >
                Cancelar
            </button>
            <button
                type="submit"
                :disabled="loading"
                class="inline-flex justify-center rounded-md bg-blue-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-50 transition-colors"
            >
                {{ loading ? 'Guardando...' : submitLabel }}
            </button>
        </div>
    </form>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { diningHallApi } from '../diningHalls/api';

const props = defineProps({
    modelValue: { type: Object, required: true },
    loading: { type: Boolean, default: false },
    errors: { type: Object, default: () => ({}) },
    submitLabel: { type: String, default: 'Guardar' },
    isEditing: { type: Boolean, default: false },
});

const emit = defineEmits(['submit', 'cancel']);
const form = ref(JSON.parse(JSON.stringify(props.modelValue)));

const showPassword = ref(false);
const availableHalls = ref([]);
const loadingHalls = ref(false);

async function fetchHalls() {
    loadingHalls.value = true;
    try {
        const { data } = await diningHallApi.getAll();
        availableHalls.value = data.data ?? [];
    } catch {
        // ignore
    } finally {
        loadingHalls.value = false;
    }
}

watch(
    () => props.modelValue,
    (newVal) => {
        form.value = JSON.parse(JSON.stringify(newVal));
    },
    { deep: true }
);

function submit() {
    const payload = JSON.parse(JSON.stringify(form.value));
    if (payload.role === 'admin') {
        payload.dining_halls = [];
    }
    emit('submit', payload);
}

onMounted(() => {
    fetchHalls();
});
</script>
