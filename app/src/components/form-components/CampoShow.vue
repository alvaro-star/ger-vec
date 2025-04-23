<template>
    <div class="px-3 py-3">
        <p class="block tracking-wide text-171717 text-x font-semibold mb-2">
            {{ titulo }}
        </p>
        <p class="text-terciary font-medium text-x">
            <slot>{{ valor }}</slot>
        </p>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    titulo: string
    valor?: any
}>()
</script>