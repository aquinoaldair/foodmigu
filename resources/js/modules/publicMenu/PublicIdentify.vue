<template>
    <div class="min-h-screen bg-gray-50 px-4 py-6 pb-24">
        <LoadingOverlay :show="loading" message="Buscando..." />
        <div class="max-w-md mx-auto">
            <router-link
                :to="{ name: 'public.landing', params: { code } }"
                class="inline-flex items-center justify-center w-full py-3 mb-6 rounded-xl text-lg font-semibold bg-gray-200 text-gray-700 active:scale-95 transition"
            >
                ← Volver
            </router-link>

            <div class="bg-white rounded-2xl shadow-sm p-5 mb-4">
                <h1 class="text-xl font-bold text-gray-900 mb-6">Identificación</h1>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label for="id_code" class="block text-sm text-gray-500 mb-1">Código / ID</label>
                        <input
                            id="id_code"
                            v-model="form.id_code"
                            type="text"
                            required
                            class="block w-full px-4 py-3 rounded-xl border border-gray-300 text-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Ej: 12345"
                            :disabled="loading"
                        />
                    </div>
                    <div>
                        <label for="name" class="block text-sm text-gray-500 mb-1">Nombre</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            class="block w-full px-4 py-3 rounded-xl border border-gray-300 text-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Ej: Juan Pérez"
                            :disabled="loading"
                        />
                    </div>
                    <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-3 rounded-xl text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ loading ? 'Buscando...' : 'Identificarse' }}
                    </button>
                </form>

                <div v-if="multipleResults" class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500 mb-3">Varios resultados. Selecciona uno:</p>
                    <div class="space-y-2">
                        <button
                            v-for="d in diners"
                            :key="d.id"
                            type="button"
                            @click="selectDiner(d)"
                            class="w-full flex justify-between items-center py-4 px-4 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 active:scale-95 transition text-left text-gray-900"
                        >
                            <span>{{ d.name }}</span>
                            <span class="text-sm text-gray-500">({{ d.id_code }})</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import LoadingOverlay from '../../components/LoadingOverlay.vue';
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
