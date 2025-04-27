<script setup lang="ts">
defineProps<{
  label: string;
  options?: string[];
  placeholder?: string;
  message?: string;
}>();

const model = defineModel<string>({ required: true });
</script>

<template>
  <div>
    <label :class="{ 'text-red-500': message }" class="block tracking-wide text-gray-700 text-x font-semibold mb-2">
      {{ label }} <span class="text-red-500">*</span>
    </label>

    <select v-model="model" :class="[
      'appearance-none block w-full bg-inputBg  focus:shadow-none focus:ring-1 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white transition duration-300',
      message && 'border-red-600 focus:border-red-700 focus:ring-red-500 transition duration-300'
    ]">
      <option value="" disabled>{{ placeholder || 'Selecione uma opção' }}</option>
      <slot>
        <option v-for="option in options || []" :key="option" :value="option">
          {{ option }}
        </option>
      </slot>
    </select>

    <p v-if="message" class="text-red-500 text-xs mt-1">{{ message }}</p>
  </div>
</template>
