<script setup lang="ts">
import { defineEmits, defineProps, ref } from "vue";

import type IColumn from "@/types/IColumn";
import Header from "./Header.vue";
import Pagination from './Pagination.vue';
import Row from './Row.vue';
import TabelaAcoes from './TableActions.vue';
import type { IFilters } from "@/types/IFilter";


defineProps<{
  title?: string,
  placeholder?: string,
  columns: IColumn[],
  rows: any[],
  showActions?: 0 | 1,
  showFilters?: number,
  filters?: IFilters,
  currentPage: number,
  totalRecords: number,
  pageSize: number,
  pageSizeOptions: number[]
}>();

const emit = defineEmits(['search', 'page-changed', 'page-size-changed', 'update-filter']);

const emitSearch = (query: string) => {
  emit('search', query);
};

const emitUpdateFilter = (label: string, values: string[]) => {
  emit('update-filter', label, values);
}

const handlePageChange = (page: number) => {
  emit('page-changed', page);
};

const handlePageSizeChange = (size: number) => {
  emit('page-size-changed', size);
};

</script>

<template>
  <section class="px-3">
    <div class="mx-auto max-w-screen">
      <div class="bg-white relative border border-gray-300 sm:rounded overflow-hidden">
        <TabelaAcoes :title="title ?? 'Título Padrão'" :placeholder="placeholder ?? 'Digite sua pesquisa aqui'"
          @search="emitSearch" :filters="filters" @update-filter="emitUpdateFilter" :show-filters="showFilters" />
        <div class="overflow-x-auto">

          <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <Header :columns="columns" :showActions="showActions === undefined || showActions == 1" />

            <tbody>
              <Row v-for="(row, index) in rows" :key="index" :columns="columns" :row="row" />
            </tbody>
          </table>
        </div>
        <Pagination :currentPage="currentPage" :totalRecords="totalRecords" :pageSize="pageSize"
          :pageSizeOptions="pageSizeOptions" @page-changed="handlePageChange"
          @page-size-changed="handlePageSizeChange" />
      </div>
    </div>
  </section>
</template>
