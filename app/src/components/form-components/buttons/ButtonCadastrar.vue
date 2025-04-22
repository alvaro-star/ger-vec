<script setup lang="ts">
defineProps<{
  type: 'button' | 'submit' | 'reset'
  label?: string
}>()
</script>

<template>
  <button :type="type" class="register-button">
    {{ label ?? 'Botao' }}
  </button>
</template>

<style scoped>
.register-button {
  padding: 10px 20px;
  background-color: #00A710;
  color: white;
  font-family: 'Poppins', sans-serif;
  font-weight: bold;
  font-size: 16px;
  border: none;
  border-radius: 3.2px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.register-button:hover {
  background-color: #016F0C;
}
</style>
