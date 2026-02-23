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
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-500">{{ userPlaceholder }}</span>
        </div>
    </header>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

defineEmits(['toggle-sidebar']);

const route = useRoute();

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
};

const pageTitle = computed(() => titles[route.name] ?? 'Panel');

const userPlaceholder = computed(() => 'Usuario');
</script>
