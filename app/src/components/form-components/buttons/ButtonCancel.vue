<script setup lang="ts">
defineProps<{
  label?: string;
}>();
</script>

<template>
  <button class="cancel-button bg-colorRed hover:bg-colorRedHover">
    {{ label || "Cancelar" }}
  </button>
</template>

<style scoped>
.cancel-button {
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
