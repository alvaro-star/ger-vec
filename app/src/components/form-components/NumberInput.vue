<script setup>
const props = defineProps({
  label: {
    type: String,
    required: true,
  },
  placeholder: {
    type: String,
  },
  message: {
    type: String,
  },
  step: {
    type: [String, Number],
    default: '1',
  },
});

const model = defineModel({ required: true });

function updateModel(event) {
  const value = event.target.value;
  // Garante que o valor 0 é aceito, enquanto strings vazias retornam ''
  model.value = value === '' ? '' : parseFloat(value);
}
</script>

<template>
  <div>
    <label
        :class="{ 'text-red-500': message }"
        class="block tracking-wide text-gray-700 dark:text-gray-200 text-sm font-semibold mb-2"
    >
      {{ label }}
      <span class="text-red-500">*</span>
    </label>
    <input
        :step="step"
        :placeholder="placeholder"
        type="number"
        v-model="model"
        @input="updateModel"
        :class="{ 'border-red-500': message }"
        class="appearance-none block w-full bg-inputBg dark:bg-gray-700 text-gray-700 dark:text-gray-200 border
             border-colorline dark:border-gray-600 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white
             focus:border-gray-500"
    />
    <p v-if="message" class="text-red-500 text-xs mt-1">{{ message }}</p>
  </div>
</template>
