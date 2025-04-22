<script lang="ts" setup>
import { useAlertStore } from '@/stores/alertState';
import { computed, watch } from 'vue';

const messageStore = useAlertStore();

const alertClass = computed(() => {
    if (messageStore.tipo === 'success') {
        return 'bg-green-500 text-white';
    }
    if (messageStore.tipo === 'alert') {
        return 'bg-yellow-500 text-black';
    }
    return 'bg-red-500 text-white';
});

const clearMessage = () => {
    messageStore.clearMessage();
};

// Observa mudanças na mensagem e inicia o timeout
watch(
    () => messageStore.mensagem,
    (newVal) => {
        if (newVal !== '') {
            setTimeout(() => {
                messageStore.clearMessage();
            }, 3000);
        }
    }
);
</script>

<template>
    <div v-if="messageStore.mensagem" :class="alertClass"
        class="p-4 rounded-md shadow-xl fixed top-14 left-1/2 transform -translate-x-1/2 z-50 min-w-[300px]">
        <div class="flex justify-between items-center">
            <span>{{ messageStore.mensagem }}</span>
            <button @click="clearMessage" class="cursor-pointer ml-4">
                <!-- SVG para o ícone de fechar -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</template>
