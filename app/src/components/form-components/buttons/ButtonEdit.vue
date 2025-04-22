<script setup lang="ts">
import { defineProps } from 'vue';
defineProps<{
  label?: string,
}>();
</script>

<template>
  <button class="edit-button bg-colorLaranja hover:bg-colorLaranjaHover">
    {{ label ?? 'Editar' }}
  </button>
</template>

<style scoped>
.edit-button {
  padding: 10px 20px;
  color: white;
  font-family: 'Poppins', sans-serif;
  font-weight: bold;
  font-size: 16px;
  border: none;
  border-radius: 3.2px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}
</style>
