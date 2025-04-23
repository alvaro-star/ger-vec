<script setup lang="ts">
import type { IFilter } from '@/types/IFilter';
import { ref, defineProps, defineEmits, watch, onMounted } from 'vue';

const props = defineProps<{
  label: string;
  filtro: IFilter;
}>();

const emit = defineEmits<{
  (e: 'updateFilter', label: string, values: string[]): void;
}>();

const selectedValues = ref<string[]>([]);

watch(selectedValues, (newValues) => {
  emit('updateFilter', props.label, newValues);
});

const handleChange = (value: string, checked: boolean) => {
  if (props.filtro.type === 'checkbox') {
    if (checked) {
      selectedValues.value.push(value);
    } else {
      selectedValues.value = selectedValues.value.filter((v) => v !== value);
    }
  } else if (props.filtro.type === 'radio') {
    selectedValues.value = [value];
  }
};

const generateUniqueId = (label: string, value: string) => {
  return `${label}-element-${value}`;
};

onMounted(() => {
  selectedValues.value = props.filtro.value
})

</script>

<template>
  <section>
    <h6 class="mb-3 text-base font-medium text-gray-900">
      {{ props.label }}
    </h6>

    <ul class="space-y-2 text-sm" aria-labelledby="filterDropdownButton">
      <li v-for="option in filtro.options" :key="option" class="flex items-center">
        <input :id="generateUniqueId(props.label, option)" :type="filtro.type" :value="option"
          :checked="selectedValues.includes(option)"
          @change="e => handleChange(option, (e.target as HTMLInputElement).checked)"
          class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500" />

        <label :for="generateUniqueId(props.label, option)" class="ml-2 text-sm font-medium text-gray-900">
          {{ option }}
        </label>
      </li>
    </ul>
  </section>
</template>
