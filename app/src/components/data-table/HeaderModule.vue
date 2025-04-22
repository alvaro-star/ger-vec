<script setup lang="ts">
defineProps<{
  moduleName?: string
}>()
</script>

<template>
  <div class="max-w-screen py-5 bg-white border-b border-gray-300">
    <div class="container flex justify-between items-center ">
      <div class="pl-4">
        <slot name="title">
          <h1 class="text-xl font-semibold text-bg-customBackground ">{{ moduleName }}</h1>
        </slot>
      </div>
      <div class="flex gap-3 pr-7">
        <slot name="actions" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.bg-customBackground {
  background-color: #171717;
}

.hover\:bg-customHover:hover {
  background-color: #C81E1E;
}
</style>
