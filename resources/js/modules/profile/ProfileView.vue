<template>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-8">Mi Perfil</h1>

            <div class="bg-white rounded-2xl shadow-sm p-6 space-y-8">
                <!-- A) Información básica -->
                <section>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Información básica</h2>
                    <form @submit.prevent="handleUpdateInfo" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                :class="{ 'border-red-500': infoErrors.name }"
                            />
                            <p v-if="infoErrors.name" class="mt-1 text-sm text-red-600">{{ Array.isArray(infoErrors.name) ? infoErrors.name[0] : infoErrors.name }}</p>
                            <p v-if="infoErrors._general" class="mt-1 text-sm text-red-600">{{ infoErrors._general }}</p>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Correo electrónico</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                readonly
                                class="block w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-600 cursor-not-allowed"
                            />
                        </div>
                        <div v-if="infoSuccess" class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">
                            Información actualizada correctamente.
                        </div>
                        <button
                            type="submit"
                            :disabled="infoLoading"
                            class="inline-flex items-center px-6 py-3 rounded-xl text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 transition-colors"
                        >
                            {{ infoLoading ? 'Guardando...' : 'Actualizar Información' }}
                        </button>
                    </form>
                </section>

                <div class="border-t border-gray-200" />

                <!-- B) Cambiar contraseña -->
                <section>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Cambiar contraseña</h2>
                    <form @submit.prevent="handleUpdatePassword" class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña actual</label>
                            <input
                                id="current_password"
                                v-model="passwordForm.current_password"
                                type="password"
                                class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                :class="{ 'border-red-500': passwordErrors.current_password }"
                            />
                            <p v-if="passwordErrors.current_password" class="mt-1 text-sm text-red-600">{{ Array.isArray(passwordErrors.current_password) ? passwordErrors.current_password[0] : passwordErrors.current_password }}</p>
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Nueva contraseña</label>
                            <input
                                id="password"
                                v-model="passwordForm.password"
                                type="password"
                                class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                :class="{ 'border-red-500': passwordErrors.password }"
                            />
                            <p v-if="passwordErrors.password" class="mt-1 text-sm text-red-600">{{ Array.isArray(passwordErrors.password) ? passwordErrors.password[0] : passwordErrors.password }}</p>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar contraseña</label>
                            <input
                                id="password_confirmation"
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                            />
                        </div>
                        <div v-if="passwordSuccess" class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">
                            Contraseña actualizada correctamente.
                        </div>
                        <button
                            type="submit"
                            :disabled="passwordLoading"
                            class="inline-flex items-center px-6 py-3 rounded-xl text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 transition-colors"
                        >
                            {{ passwordLoading ? 'Guardando...' : 'Cambiar Contraseña' }}
                        </button>
                    </form>
                </section>

                <div class="border-t border-gray-200" />

                <!-- C) Cerrar sesión -->
                <section>
                    <button
                        type="button"
                        @click="handleLogout"
                        :disabled="logoutLoading"
                        class="w-full py-3 rounded-xl text-base font-semibold text-white bg-red-600 hover:bg-red-700 disabled:opacity-50 transition-colors"
                    >
                        {{ logoutLoading ? 'Saliendo...' : 'Cerrar sesión' }}
                    </button>
                </section>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { profileApi } from './api';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({ name: '', email: '' });
const infoErrors = reactive({});
const infoSuccess = ref(false);
const infoLoading = ref(false);

const passwordForm = reactive({
    current_password: '',
    password: '',
    password_confirmation: '',
});
const passwordErrors = reactive({});
const passwordSuccess = ref(false);
const passwordLoading = ref(false);

const logoutLoading = ref(false);

function resetInfoErrors() {
    Object.keys(infoErrors).forEach((k) => delete infoErrors[k]);
}

function resetPasswordErrors() {
    Object.keys(passwordErrors).forEach((k) => delete passwordErrors[k]);
}

async function fetchProfile() {
    try {
        const { data } = await profileApi.get();
        form.name = data.data.name ?? '';
        form.email = data.data.email ?? '';
    } catch {
        router.push({ name: 'login' });
    }
}

async function handleUpdateInfo() {
    resetInfoErrors();
    infoSuccess.value = false;
    infoLoading.value = true;
    try {
        const { data } = await profileApi.update({ name: form.name.trim() });
        form.name = data.data.name ?? form.name;
        authStore.setUser({ ...authStore.user, name: data.data.name });
        infoSuccess.value = true;
    } catch (e) {
        const errors = e.response?.data?.errors ?? {};
        Object.assign(infoErrors, errors);
        if (e.response?.data?.message && !Object.keys(errors).length) {
            infoErrors._general = e.response.data.message;
        }
    } finally {
        infoLoading.value = false;
    }
}

async function handleUpdatePassword() {
    resetPasswordErrors();
    passwordSuccess.value = false;
    passwordLoading.value = true;
    try {
        await profileApi.updatePassword({
            current_password: passwordForm.current_password,
            password: passwordForm.password,
            password_confirmation: passwordForm.password_confirmation,
        });
        passwordSuccess.value = true;
        passwordForm.current_password = '';
        passwordForm.password = '';
        passwordForm.password_confirmation = '';
    } catch (e) {
        const errors = e.response?.data?.errors ?? {};
        Object.assign(passwordErrors, errors);
        if (e.response?.data?.message && !Object.keys(errors).length) {
            passwordErrors.current_password = e.response.data.message;
        }
    } finally {
        passwordLoading.value = false;
    }
}

async function handleLogout() {
    logoutLoading.value = true;
    try {
        await profileApi.logout();
    } catch {
        // Session may already be invalid
    } finally {
        authStore.$patch({ user: null, authenticated: false, userFetchAttempted: true });
        router.push({ name: 'login' });
        logoutLoading.value = false;
    }
}

onMounted(fetchProfile);
</script>
