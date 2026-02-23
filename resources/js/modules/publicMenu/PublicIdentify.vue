<template>
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md mx-auto">
            <router-link
                :to="{ name: 'public.landing', params: { code } }"
                class="inline-block text-sm text-gray-600 hover:text-gray-900 mb-6"
            >
                ← Volver
            </router-link>

            <div class="bg-white rounded-2xl shadow-sm p-8">
                <h1 class="text-xl font-bold text-gray-900 mb-6">Identificación</h1>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label for="id_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Código / ID
                        </label>
                        <input
                            id="id_code"
                            v-model="form.id_code"
                            type="text"
                            required
                            class="block w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                            placeholder="Ej: 12345"
                            :disabled="loading"
                        />
                    </div>
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            class="block w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                            placeholder="Ej: Juan Pérez"
                            :disabled="loading"
                        />
                    </div>
                    <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-4 px-6 text-lg font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ loading ? 'Buscando...' : 'Identificarse' }}
                    </button>
                </form>

                <div v-if="multipleResults" class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm font-medium text-gray-700 mb-3">Varios resultados. Selecciona uno:</p>
                    <div class="space-y-2">
                        <button
                            v-for="d in diners"
                            :key="d.id"
                            type="button"
                            @click="selectDiner(d)"
                            class="w-full block py-3 px-4 text-left rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-900"
                        >
                            {{ d.name }} ({{ d.id_code }})
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { publicMenuApi, setStoredDiner } from './api';

const router = useRouter();
const route = useRoute();
const code = computed(() => route.params.code);

const form = reactive({ id_code: '', name: '' });
const loading = ref(false);
const error = ref(null);
const diners = ref([]);
const multipleResults = ref(false);

async function submit() {
    loading.value = true;
    error.value = null;
    diners.value = [];
    multipleResults.value = false;
    try {
        const { data } = await publicMenuApi.identify(code.value, {
            id_code: form.id_code,
            name: form.name,
        });
        if (data.multiple) {
            diners.value = data.diners ?? [];
            multipleResults.value = true;
        } else {
            setStoredDiner(code.value, data.diner);
            router.push({ name: 'public.menus', params: { code: code.value } });
        }
    } catch (e) {
        error.value = e.response?.data?.message ?? 'No se encontró ningún comensal';
    } finally {
        loading.value = false;
    }
}

function selectDiner(diner) {
    setStoredDiner(code.value, diner);
    router.push({ name: 'public.menus', params: { code: code.value } });
}

onMounted(() => {
    error.value = null;
    diners.value = [];
});
</script>
