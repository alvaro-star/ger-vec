<template>
  <div class="p-3 flex flex-row items-center justify-between">
    <div>
      <select class="w-14 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded py-1.5 px-2"
        v-model="localPageSize" @change="updatePageSize">
        <option v-for="size in pageSizeOptions" :key="size" :value="size">
          {{ size }}
        </option>
      </select>
      <span class="text-sm ml-2">
        Mostrando {{ startItem }}-{{ endItem }} de {{ totalRecords }} itens
      </span>
    </div>

    <ul class="inline-flex items-stretch -space-x-px">
      <li>
        <button @click="changePage(currentPage - 1)"
          :class="[currentPage === 1 ? 'bg-gray-100 border-gray-300' : 'bg-customBackgroundButtonDisabled']"
          :disabled="currentPage === 1"
          class="flex items-center justify-center h-full py-1.5 px-3 ml-0 rounded-l border">
          <span class="sr-only">Previous</span>
          <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg">
            <path :class="[currentPage === 1 ? 'fill-gray-300' : 'fill-white']"
              d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
              clip-rule="evenodd" />
          </svg>
        </button>
      </li>
      <li v-for="page in visiblePages" :key="page" :class="{ active: page === currentPage }">
        <a @click="changePage(page)" href="#"
          class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 border border-gray-300"
          :class="[page === currentPage ? 'bg-blue-100' : 'bg-white hover:bg-gray-100 hover:text-gray-700']">
          {{ page }}
        </a>
      </li>
      <li>
        <button @click="changePage(currentPage + 1)"
          :class="[currentPage === totalPages ? 'border-gray-300 bg-gray-100' : 'bg-customBackgroundButtonDisabled']"
          :disabled="currentPage === totalPages"
          class="flex items-center justify-center h-full py-1.5 px-3 leading-tight rounded-r border">
          <span class="sr-only">Next</span>
          <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg">
            <path :class="[currentPage === totalPages ? 'fill-gray-400' : 'fill-white']"
              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
              clip-rule="evenodd" />
          </svg>
        </button>
      </li>
    </ul>
  </div>
</template>

<style>
.bg-customBackgroundButtonDisabled {
  background-color: rgba(38, 38, 38, 1);
  border-color: rgba(38, 38, 38, 1);
}
</style>



<script setup lang="ts">
import { ref, computed, watch, defineProps, defineEmits } from 'vue';

const props = defineProps<{
  currentPage: number;
  totalRecords: number;
  pageSize: number;
  pageSizeOptions?: number[];
}>();

const emit = defineEmits<{
  (e: 'page-changed', page: number): void;
  (e: 'page-size-changed', size: number): void;
}>();

const localPageSize = ref<number>(props.pageSize);

watch(() => props.pageSize, (newSize) => {
  localPageSize.value = newSize;
});

const totalPages = computed(() => {
  return Math.ceil(props.totalRecords / localPageSize.value);
});

const startItem = computed(() => {
  return (props.currentPage - 1) * localPageSize.value + 1;
});

const endItem = computed(() => {
  return Math.min(props.currentPage * localPageSize.value, props.totalRecords);
});

const visiblePages = computed<(number | string)[]>(() => {
  const pages: (number | string)[] = [];
  for (let i = 1; i <= totalPages.value; i++) {
    if (i === 1 || i === totalPages.value || Math.abs(i - props.currentPage) <= 1) {
      pages.push(i);
    } else if (
      i === props.currentPage - 2 ||
      i === props.currentPage + 2
    ) {
      pages.push('...');
    }
  }
  return pages.filter((v, i, a) => a.indexOf(v) === i);
});

const changePage = (page: string | number) => {
  const pageNumber = typeof page === 'string' ? parseInt(page) : page;
  if (pageNumber >= 1 && pageNumber <= totalPages.value) {
    emit('page-changed', pageNumber);
  }
};

const updatePageSize = () => {
  emit('page-size-changed', localPageSize.value);
};
</script>