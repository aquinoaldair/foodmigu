<template>
    <Teleport to="body">
        <Transition name="fade">
            <div
                v-if="show"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/50"
                role="status"
                aria-live="polite"
                aria-label="Procesando"
            >
                <div class="bg-white rounded-2xl shadow-xl p-8 flex flex-col items-center gap-4 min-w-[200px]">
                    <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin" />
                    <p class="text-gray-700 font-medium">{{ message }}</p>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
defineProps({
    show: { type: Boolean, default: false },
    message: { type: String, default: 'Procesando...' },
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
