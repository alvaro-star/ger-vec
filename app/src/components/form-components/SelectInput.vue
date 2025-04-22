<script setup lang="ts">
defineProps < {
  label: string;
  options?: string[];
  placeholder?: string;
  message?: string;
} > ();

const model = defineModel < string > ({ required: true });
</script>

<template>
  <div>
    <label :class="{ 'text-red-500': message }" class="block tracking-wide text-gray-700 text-sm font-semibold mb-2">
      {{ label }}
      <span class="text-red-500">*</span>
    </label>

    <select v-model="model" :class="{ 'border-red-500': message }"
      class="block w-full bg-inputBg text-gray-700 border border-colorline rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
      <option value="" disabled>{{ placeholder || 'Selecione uma opção' }}</option>
      <option v-for="option in options || []" :key="option" :value="option">
        {{ option }}
      </option>
    </select>

    <p v-if="message" class="text-red-500 text-xs mt-1">{{ message }}</p>
  </div>
</template>
