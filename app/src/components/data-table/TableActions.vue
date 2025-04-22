<script setup lang="ts">
import { ref, defineProps, defineEmits, computed } from 'vue';
import SearchIcon from './icons/SearchIcon.vue';

defineProps<{
  title: string;
  placeholder: string;
}>();


const emit = defineEmits<{
  (e: 'search', query: string): void;
}>();

const searchQuery = ref<string>('');

function emitSearch() {
  emit('search', searchQuery.value);
}
</script>


<template>
  <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
    <h2 class="text-2xl">{{ title }}</h2>
    <div class="flex flex-col md:flex-row items-center md:space-y-0 md:space-x-4 px-2 py-0.5">
      <div class="w-full">
        <form class="flex items-center" @submit.prevent="emitSearch">
          <div class="relative w-full">
            <button class="absolute inset-y-0 right-0 flex items-center px-4 bg-customBackgroundButtonSearch rounded"
              @click="emitSearch">
              <SearchIcon />
            </button>
            <input type="text" id="simple-search"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded focus:ring-primary-500 focus:border-primary-500 block min-w-[350px] pl-2 p-2.5"
              :placeholder="placeholder" v-model="searchQuery" required />
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
