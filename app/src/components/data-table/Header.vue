<script setup lang="ts">
import { defineEmits, defineProps } from 'vue';
import type IColumn from '@/types/IColumn';

defineProps<{
  columns: IColumn[],
  showActions?: boolean
}>();

const emit = defineEmits<{
  (e: 'sort', field: string): void
}>();

function handleClick(column: IColumn) {
  if (column.sorteable) {
    emit('sort', column.field);
  }
}
</script>

<template>
  <thead class="text-sm text-gray-700 bg-gray-50 border-t border-b border-gray-300">
    <tr>
      <th v-for="(column, index) in columns" :key="index" scope="col" class="px-4 py-4"
        :class="{ 'hover:bg-gray-100 cursor-pointer': column.sorteable }" @click="handleClick(column)">
        {{ column.label }}
      </th>
      <th v-if="showActions" scope="col" class="px-4 py-3">
        <span class="sr-only">Actions</span>
      </th>
    </tr>
  </thead>
</template>
