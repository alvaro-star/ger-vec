<script setup lang="ts">
import { useRouter } from 'vue-router';
import { defineProps } from 'vue';
import type IColumn from '@/types/IColumn';
import GoToDetailsIcon from './icons/GoToDetailsIcon.vue';

const router = useRouter();

const props = defineProps<{
  row: Record<string, any>;
  columns: IColumn[];
}>();


function goToDetails() {
  const id = props.row.id;
  const routeName = props.row.routeName;

  if (!id || !routeName) {
    console.error('ID ou nome da rota não foram fornecidos.');
    return;
  }

  console.log(routeName, id);
  
  router.push({ name: routeName, params: { id:id } });
}
</script>

<template class="flex justify-center">
  <tr class="border-b hover:bg-gray-50">
    <td v-for="(column, index) in columns" :key="index" class="px-4 py-4 border-r border-gray-300">
      {{ row[column.field] }}
    </td>
    <td v-if="row.routeName" class="border-t border-gray-300">
      <button @click="goToDetails" class="h-full w-full px-3 py-3 flex items-center justify-center" type="button">
        <GoToDetailsIcon />
      </button>
    </td>
  </tr>
</template>