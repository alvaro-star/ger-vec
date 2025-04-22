<script setup lang="ts">
import { RouterView } from 'vue-router';
import { initFlowbite } from 'flowbite';
import { onMounted } from 'vue';
import AlertMessage from './components/AlertMessage.vue';
onMounted(() => {
  initFlowbite();
});
</script>

<template>
  <AlertMessage />
  <RouterView />
</template>
