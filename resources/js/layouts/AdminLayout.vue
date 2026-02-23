<template>
    <div class="min-h-screen bg-gray-100">
        <Sidebar :is-open="sidebarOpen" />
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 bg-black/50 z-30 md:hidden"
            aria-hidden="true"
            @click="sidebarOpen = false"
        />
        <div class="flex flex-col min-h-screen md:ml-64">
            <Topbar @toggle-sidebar="sidebarOpen = !sidebarOpen" />
            <main class="flex-1 p-6 overflow-auto">
                <router-view />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import Sidebar from '../components/layout/Sidebar.vue';
import Topbar from '../components/layout/Topbar.vue';

const sidebarOpen = ref(false);
</script>
