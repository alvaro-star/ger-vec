<script setup lang="ts">
defineProps<{
    titulo: string
}>()
</script>

<template>
    <section class="px-3 sm:rounded w-full">
        <div
            class="bg-white relative border border-gray-300 sm:rounded-t flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
            <h2 class="text-2xl">{{ titulo }}</h2>
        </div>
        <div class="bg-white border-x border-b border-gray-300 rounded-b">
            <slot>Conteudo</slot>
        </div>
    </section>
</template>
