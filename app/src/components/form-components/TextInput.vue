<script setup lang="ts">
import { defineModel } from 'vue';
import InputError from './InputError.vue';

defineProps<{
  label: string;
  message?: string;
  placeholder?: string;
  required?: 0 | 1
  type?: string;
  step?: string;
}>();

const model = defineModel<string | number>({ required: true });
</script>

<template>
  <div>
    <label :class="{ 'text-red-500': message }" class="block tracking-wide text-gray-700 text-x font-semibold mb-2">
      {{ label }} <span v-if="required === undefined || required === 1" class="text-red-500">*</span>
    </label>
    <input v-model="model" :type="type ?? 'text'" :step="step ?? 1" :class="{ 'border-red-500': message }" class="appearance-none block w-full bg-inputBg  text-gray-700 border
                          border-colorline rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white
                          focus:border-gray-500" :placeholder="placeholder" />
    <InputError :message="message" />
  </div>
</template>
