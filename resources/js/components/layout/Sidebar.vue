<template>
    <aside
        :class="[
            'fixed inset-y-0 left-0 z-40 flex flex-col w-64 bg-gray-900 text-gray-200 transition-transform duration-200 ease-in-out',
            'md:translate-x-0',
            isOpen ? 'translate-x-0' : '-translate-x-full'
        ]"
    >
        <div class="flex items-center h-16 px-4 border-b border-gray-800 shrink-0">
            <span class="text-lg font-semibold text-white">Foodmigu</span>
        </div>
        <nav class="flex-1 overflow-y-auto py-4 px-3">
            <ul class="space-y-1">
                <li>
                    <NavItem to="/dashboard" label="Dashboard" icon="chart" :exact="true" />
                </li>
                <li>
                    <NavItem to="/dining-halls" label="Comedores" icon="building" />
                </li>
                <li>
                    <NavItem to="/menu-categories" label="Categorías de Menú" icon="folder" />
                </li>
                <li>
                    <NavItem to="/menus" label="Menús Base" icon="menu" />
                </li>
                <li>
                    <NavItem to="/weekly-menu-builds" label="Construcción Semanal" icon="calendar" />
                </li>
                <li>
                    <NavItem to="/guia-del-sistema" label="Guía del sistema" icon="book" />
                </li>
                <li>
                    <NavItem to="/profile" label="Mi Perfil" icon="user" />
                </li>
            </ul>
        </nav>
    </aside>
</template>

<script setup>
import NavItem from './NavItem.vue';

defineProps({
    isOpen: { type: Boolean, default: true },
});
</script>
