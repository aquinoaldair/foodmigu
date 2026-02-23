<template>
    <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 shrink-0">
        <div class="flex items-center gap-4">
            <button
                type="button"
                @click="$emit('toggle-sidebar')"
                class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 md:hidden"
                aria-label="Abrir menú"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
            <h1 class="text-lg font-semibold text-gray-900">{{ pageTitle }}</h1>
        </div>
        <div
            v-click-outside="() => (menuOpen = false)"
            class="relative"
        >
            <button
                type="button"
                @click.stop="menuOpen = !menuOpen"
                class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors"
            >
                <span>{{ userName }}</span>
                <svg
                    :class="['w-4 h-4 transition-transform', menuOpen && 'rotate-180']"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
            <Transition name="dropdown">
                <div
                    v-if="menuOpen"
                    class="absolute right-0 mt-1 py-1 w-48 bg-white rounded-xl shadow-lg border border-gray-100 z-50"
                >
                    <router-link
                        to="/profile"
                        class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                        @click="menuOpen = false"
                    >
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Mi Perfil
                    </router-link>
                    <button
                        type="button"
                        class="flex items-center gap-2 w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors text-left"
                        @click="handleLogout"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Cerrar sesión
                    </button>
                </div>
            </Transition>
        </div>
    </header>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

defineEmits(['toggle-sidebar']);

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const menuOpen = ref(false);

const titles = {
    dashboard: 'Dashboard',
    'menu-categories.index': 'Categorías de Menú',
    'menu-categories.create': 'Nueva Categoría',
    'menu-categories.edit': 'Editar Categoría',
    'menus.index': 'Menús Base',
    'menus.create': 'Nuevo Menú',
    'menus.edit': 'Editar Menú',
    'weekly-menu-builds.index': 'Construcción Semanal',
    'weekly-menu-builds.create': 'Nueva Semana',
    'weekly-menu-builds.edit': 'Editar Semana',
    'dining-halls.index': 'Comedores',
    'dining-halls.create': 'Nuevo Comedor',
    'dining-halls.edit': 'Editar Comedor',
    'diners.index': 'Comensales',
    'diners.create': 'Nuevo Comensal',
    'diners.edit': 'Editar Comensal',
    'diners.import': 'Importar Excel',
    profile: 'Mi Perfil',
};

const pageTitle = computed(() => titles[route.name] ?? 'Panel');

const userName = computed(() => authStore.userName || 'Usuario');

const vClickOutside = {
    mounted(el, binding) {
        el._clickOutside = (e) => {
            if (!(el === e.target || el.contains(e.target))) {
                binding.value();
            }
        };
        document.addEventListener('click', el._clickOutside);
    },
    unmounted(el) {
        document.removeEventListener('click', el._clickOutside);
    },
};

async function handleLogout() {
    menuOpen.value = false;
    await authStore.logout();
    router.push({ name: 'login' });
}
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
    transition: opacity 0.15s ease, transform 0.15s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}
</style>
