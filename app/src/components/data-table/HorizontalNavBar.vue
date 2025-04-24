<template>
    <div class="container px-5 overflow-x-auto">
        <nav class="mx-auto flex space-x-4 bg-transparent border-b border-gray-300 bg-red-300">
            <button v-for="tab in tabs" :key="tab" @click="selectedTab = tab"
                class="whitespace-nowrap px-4 py-2 outline-none transition-colors duration-200" :class="selectedTab === tab
                    ? 'text-blue-600 border-b-2 border-blue-600 font-semibold'
                    : 'text-gray-500 hover:text-gray-700'">
                {{ tab }}
            </button>
        </nav>
    </div>
</template>

<script setup lang="ts">
import { defineProps, defineModel } from 'vue'

defineProps<{
    tabs: string[]
}>()

const selectedTab = defineModel<string>()
</script>