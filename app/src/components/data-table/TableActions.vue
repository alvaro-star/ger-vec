<script setup lang="ts">
import type { IFilters } from '@/types/IFilter';
import { defineEmits, defineProps, ref } from 'vue';
import ItemFiltro from './filter/ItemFiltro.vue';
import SearchIcon from './icons/SearchIcon.vue';

defineProps<{
  title: string;
  placeholder: string;
  filters?: IFilters;
  showFilters?: boolean;
  showSearch: boolean;
}>();

const isDropdownVisible = ref(false);
const toggleDropdown = () => {
  isDropdownVisible.value = !isDropdownVisible.value;
};

const emit = defineEmits(['search', 'update-filter', 'update-sort']);

const searchQuery = ref<string>('');

const emitFilters = (label: string, values: string[]) => {
  emit('update-filter', label, values);
};

const emitSearch = () => {
  emit('search', searchQuery.value);
};

</script>

<template>
  <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
    <h2 class="text-2xl">{{ title }}</h2>
    <div
      class="flex flex-col justify-center text-center md:flex-row items-center md:space-y-0 md:space-x-4 px-2 py-0.5">

      <!-- Dropdown de filtros -->
      <div v-if="showFilters"
        class="w-full md:w-auto flex items-center justify-center mb-3 md:mb-0 md:justify-end relative">
        <button id="filterDropdownButton" @click="toggleDropdown"
          class=" min-w-[60px] md:w-auto flex items-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded border border-gray-300 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 whitespace-nowrap"
          type="button">
          <div class="flex items-center space-x-2">
            <span>Filtrar por</span>
            <svg class="w-5 h-5" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path
                d="M13.9971 21C13.7822 21 13.5732 20.9317 13.4013 20.8054L9.40702 17.8865C9.28368 17.7959 9.18357 17.6783 9.11463 17.5432C9.04568 17.4081 9.00978 17.2592 9.00978 17.1081V13.5373L3.38984 6.10378C3.17102 5.81419 3.03856 5.47075 3.00722 5.11175C2.97589 4.75274 3.04691 4.39226 3.21237 4.07047C3.37783 3.74867 3.63123 3.47821 3.94434 3.28921C4.25745 3.1002 4.61798 3.00008 4.98575 3H19.0143C19.382 3.00008 19.7425 3.1002 20.0557 3.28921C20.3688 3.47821 20.6222 3.74867 20.7876 4.07047C20.9531 4.39226 21.0241 4.75274 20.9928 5.11175C20.9614 5.47075 20.829 5.81419 20.6102 6.10378L14.9902 13.5373V20.027C14.9902 20.2851 14.8856 20.5326 14.6993 20.715C14.5131 20.8975 14.2605 21 13.9971 21ZM10.996 16.6216L13.004 18.0879V13.2162C13.004 13.0057 13.0737 12.8008 13.2026 12.6324L19.0143 4.94595H4.98575L10.8013 12.6324C10.9303 12.8008 11 13.0057 11 13.2162L10.996 16.6216Z"
                fill="#262626" />
            </svg>
          </div>
        </button>
        <div v-if="isDropdownVisible"
          class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 z-10 w-48 p-3 bg-white rounded border border-colorline">
          <h6 v-if="filters === undefined" class="text-base font-medium text-gray-900">
            Em breve
          </h6>

          <template v-if="filters !== undefined">
            <ItemFiltro v-for="[label, filtro] in Object.entries(filters)" :key="label" :filtro="filtro" :label="label"
              @updateFilter="emitFilters" />
          </template>
        </div>
      </div>

      <div>
        <slot/>
      </div>

      <div v-if="showSearch" class="w-full">
        <form class="flex items-center" @submit.prevent="emitSearch">
          <div class="relative w-full">
            <button
              class="cursor-pointer absolute inset-y-0 right-0 flex items-center px-4 bg-customBackgroundButtonSearch rounded"
              @click="emitSearch">
              <SearchIcon />
            </button>
            <input type="text" id="simple-search"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded focus:ring-primary-500 focus:border-primary-500 block min-w-[350px] pl-2 p-2.5"
              :placeholder="placeholder" v-model="searchQuery" />
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style>
.bg-customBackgroundButtonSearch {
  background-color: rgba(38, 38, 38, 1);
}
</style>
