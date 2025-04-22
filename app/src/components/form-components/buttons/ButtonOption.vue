<script setup lang="ts">
defineProps<{
  label?: string;
}>()

</script>

<template>
  <button class="ButtonOption bg-white">
    {{ label ?? 'Cancelar' }}
  </button>
</template>


<style scoped>
.ButtonOption {
  padding: 10px 20px;
  color: #171717;
  font-family: 'Poppins', sans-serif;
  font-weight: bold;
  font-size: 16px;
  border: solid 1.5px #B9B9B9;
  border-radius: 3.2px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}
</style>
