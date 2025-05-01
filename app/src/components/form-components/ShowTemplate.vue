<script setup lang="ts">
import CampoShow from '@/components/form-components/CampoShow.vue';
defineProps<{
    title: string;
}>()
</script>
<template>
    <section class="w-full pt-3 pb-1.5 pr-3 pl-3">
        <div class="">
            <div class="p-5 bg-white relative border border-gray-300 sm:rounded overflow-hidden">
                <h2 class="text-xl font-semibold mb-4">{{ title }}</h2>
                <div class="border-b border-colorline"></div>
                <div class=" mt-4 grid sm:grid-cols-2 md:grid-cols-3 gap-3">
                    <slot></slot>
                </div>
                <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
                    <CampoShow class="hidden md:block" titulo="" />
                    <slot name="dates"></slot>
                </div>
            </div>
        </div>
    </section>
</template>