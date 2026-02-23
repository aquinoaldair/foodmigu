<template>
  <div class="min-h-screen flex">
    <!-- Panel izquierdo: Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-blue-600 relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-blue-700 to-blue-800 opacity-90" />
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');" />
      </div>
      <div class="relative flex flex-col justify-between p-12 w-full">
        <a href="/" class="inline-flex items-center gap-2 text-white/90 hover:text-white transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          <span class="text-sm font-medium">Volver al inicio</span>
        </a>
        <div>
          <h1 class="text-3xl font-bold text-white tracking-tight">
            Foodmigu
          </h1>
          <p class="mt-4 text-lg text-blue-100 max-w-sm">
            Plataforma de gestión para comedores empresariales e industriales.
          </p>
        </div>
        <div class="flex gap-6">
          <div class="flex items-center gap-3 text-white/80">
            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </div>
            <div>
              <p class="text-sm font-medium text-white">Acceso seguro</p>
              <p class="text-xs text-blue-200">Datos protegidos</p>
            </div>
          </div>
          <div class="flex items-center gap-3 text-white/80">
            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
            <div>
              <p class="text-sm font-medium text-white">Tiempo real</p>
              <p class="text-xs text-blue-200">Actualizaciones instantáneas</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Panel derecho: Formulario -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-12 bg-gray-50">
      <div class="w-full max-w-md">
        <div class="lg:hidden mb-8">
          <a href="/" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al inicio
          </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 p-8 sm:p-10">
          <div class="lg:hidden mb-6">
            <h1 class="text-xl font-bold text-gray-900">Foodmigu</h1>
          </div>
          <h2 class="text-2xl font-bold text-gray-900">
            Iniciar sesión
          </h2>
          <p class="mt-2 text-gray-600">
            Ingresa tus credenciales para acceder al sistema
          </p>

          <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
            <div v-if="errorMessage" class="rounded-xl bg-red-50 border border-red-100 p-4 flex items-start gap-3">
              <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <p class="text-sm text-red-800">{{ errorMessage }}</p>
            </div>

            <div class="space-y-5">
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                  Correo electrónico
                </label>
                <input
                  id="email"
                  v-model="email"
                  type="email"
                  autocomplete="email"
                  required
                  class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors"
                  placeholder="correo@empresa.com"
                />
              </div>
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                  Contraseña
                </label>
                <input
                  id="password"
                  v-model="password"
                  type="password"
                  autocomplete="current-password"
                  required
                  class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors"
                  placeholder="••••••••"
                />
              </div>
            </div>

            <div class="flex items-center">
              <input
                id="remember"
                v-model="remember"
                type="checkbox"
                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              />
              <label for="remember" class="ml-3 block text-sm text-gray-600">
                Mantener sesión iniciada
              </label>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full flex justify-center items-center gap-2 py-3.5 px-6 text-base font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-lg shadow-blue-600/25"
            >
              <svg v-if="loading" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
              </svg>
              <span v-if="loading">Accediendo...</span>
              <span v-else>Iniciar sesión</span>
            </button>
          </form>
        </div>

        <p class="mt-8 text-center text-sm text-gray-500">
          ¿Necesitas una cuenta?
          <a href="/#contacto" class="font-medium text-blue-600 hover:text-blue-700">Contáctanos</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const remember = ref(false);
const loading = ref(false);
const errorMessage = ref('');

const handleLogin = async () => {
  errorMessage.value = '';
  loading.value = true;
  try {
    await authStore.login(email.value, password.value, remember.value);
    router.push({ name: 'dashboard' });
  } catch (err) {
    errorMessage.value = err.response?.data?.errors?.email?.[0] ?? err.response?.data?.message ?? 'Error al iniciar sesión';
  } finally {
    loading.value = false;
  }
};
</script>
