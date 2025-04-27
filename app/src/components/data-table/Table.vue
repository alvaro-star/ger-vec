<script setup lang="ts">
import { defineEmits, defineProps, withDefaults } from "vue";

import type IColumn from "@/types/IColumn";
import Header from "./Header.vue";
import Pagination from './Pagination.vue';
import Row from './Row.vue';
import TabelaAcoes from './TableActions.vue';
import type { IFilters, ISort } from "@/types/IFilter";
import SpinerAnimation from "../animation/SpinerAnimation.vue";
import { formatarFistLetter } from "@/helpers/regexp/patterns";

// Aqui usando withDefaults para organizar tudo
const props = withDefaults(defineProps<{
  loading: boolean,
  title?: string,
  placeholder?: string,
  columns: IColumn[],
  rows: any[],
  showActions?: 0 | 1,
  sort?: string,
  asc?: boolean,
  showOrderInfo?: boolean,
  sorters?: ISort;
  showSearch?: number,
  showFilters?: number,
  filters?: IFilters,
  currentPage: number,
  totalRecords: number,
  pageSize: number,
  pageSizeOptions: number[]
}>(), {
  sort: '',
  asc: false,
  showOrderInfo: false
})

const emit = defineEmits([
  'search',
  'page-changed',
  'page-size-changed',
  'update-filter',
  'update:sort',
  'update:asc'
]);

const emitSearch = (query: string) => {
  emit('search', query);
};

const emitUpdateFilter = (label: string, values: string[]) => {
  emit('update-filter', label, values);
};

const emitUpdateSort = (value: string) => {
  if (value != props.sort) {
    updateSort(value)
    updateAsc(true)
  } else {
    updateAsc(!props.asc)
  }
};

const handlePageChange = (page: number) => {
  emit('page-changed', page);
};

const handlePageSizeChange = (size: number) => {
  emit('page-size-changed', size);
};

const updateSort = (newSort: string) => {
  emit('update:sort', newSort);
};

const updateAsc = (newAsc: boolean) => {
  emit('update:asc', newAsc);
};
</script>

<template>
  <section class="px-3">
    <div class="mx-auto max-w-screen">
      <div class="bg-white relative border border-gray-300 sm:rounded overflow-hidden">
        <TabelaAcoes :show-search="showSearch == undefined || showSearch === 1" :title="title ?? 'Título Padrão'"
          :placeholder="placeholder ?? 'Digite sua pesquisa aqui'" @search="emitSearch" :filters="filters"
          @update-filter="emitUpdateFilter" :show-filters="showFilters == undefined || showFilters === 1" />

        <span v-if="showOrderInfo" class="text-sm text-gray-600 px-4">
          Ordenado por <span class="font-semibold">{{ formatarFistLetter(props.sort) || 'não definido' }}</span>
          <span v-if="props.sort">({{ props.asc ? 'Crescente' : 'Decrescente' }})</span>
        </span>

        <div v-if="loading"
          class="w-full text-sm text-left text-gray-300 grid place-content-center border-y h-96 border-gray-300">
          <SpinerAnimation />
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-[800px] w-full text-sm text-left text-gray-500">
            <Header @sort="emitUpdateSort" :columns="columns"
              :showActions="showActions === undefined || showActions == 1" />
            <tbody>
              <Row v-for="(row, index) in rows" :key="index" :columns="columns" :row="row" />
            </tbody>
          </table>

          <div v-if="!rows.length" class="[800px] w-full text-base text-center py-4 text-gray-500 border-y">
            Não encontramos nenhum registro.
          </div>
        </div>

        <Pagination v-show="!loading" :currentPage="currentPage" :totalRecords="totalRecords" :pageSize="pageSize"
          :pageSizeOptions="pageSizeOptions" @page-changed="handlePageChange"
          @page-size-changed="handlePageSizeChange" />
      </div>
    </div>
  </section>
</template>
