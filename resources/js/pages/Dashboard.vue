<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center">
          <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
          <button
            @click="handleLogout"
            :disabled="loading"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
          >
            <span v-if="loading">Cerrando sesión...</span>
            <span v-else>Cerrar sesión</span>
          </button>
        </div>
        <div class="mt-6 border-t border-gray-200 pt-6">
          <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <dt class="text-sm font-medium text-gray-500">Nombre</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ authStore.userName }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Rol</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ authStore.userRole }}</dd>
            </div>
          </dl>
        </div>
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
const loading = ref(false);

const handleLogout = async () => {
  loading.value = true;
  try {
    await authStore.logout();
    router.push({ name: 'login' });
  } finally {
    loading.value = false;
  }
};
</script>
