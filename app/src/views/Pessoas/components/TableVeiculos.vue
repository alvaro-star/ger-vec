<script setup lang="ts">

import api from '@/plugins/api'
import { computed, onMounted, ref, watch } from 'vue'

import Table from '@/components/data-table/Table.vue'
import type IColumn from '@/types/IColumn'
import type IPageOutput from '@/types/IPageOutput'
import type IVeiculo from '@/types/IVeiculo'
import PrimaryButton from '@/components/form-components/buttons/PrimaryButton.vue'

const props = defineProps<{
    id: string[] | string
    title: string
}>()


const query = ref<string>('')
const currentPage = ref<number>(1)
const pageSize = ref<number>(10)
const totalRecords = ref<number>(0)

const columns = ref<IColumn[]>([
    { label: 'Placa', field: 'placa' },
    { label: 'Marca', field: 'marca' },
    { label: 'Modelo', field: 'modelo' },
    { label: 'Renavam', field: 'renavam' },
    { label: 'Ano', field: 'ano' }
]);


const rows = ref<any[]>([])

const paginatedRecords = computed(() => rows.value)

const fetchData = async () => {
    const params = {
        page: currentPage.value,
        size: pageSize.value,
        query: query.value,
    }

    try {
        const response = await api.get<IPageOutput<IVeiculo>>(`/pessoas/${props.id}/veiculos`, { params })
        const data = response.data

        rows.value = data.content.map((item: any) => ({
            ...item,
            routeName: 'veiculos.show',
        }))
        totalRecords.value = data.nElementos
    } catch (error) {
        rows.value = []
        console.error('Erro ao buscar dados:', error)
    }
}

watch(currentPage, fetchData)
watch(pageSize, fetchData)


const updatePage = (page: number) => {
    currentPage.value = page
}

const updatePageSize = (size: number) => {
    pageSize.value = size
    currentPage.value = 1
}

const handleSearch = async (newQuery: string) => {
    query.value = newQuery
    currentPage.value = 1
    await fetchData()
}

onMounted(() => {
    fetchData()
})
</script>

<template>
    <main class="w-full">
        <Suspense>
            <template #default>
                <Table class="container" :show-filters="0" :title="title" :columns="columns" :rows="paginatedRecords"
                    :currentPage="currentPage" :totalRecords="totalRecords" :pageSize="pageSize"
                    :pageSizeOptions="[5, 10, 15, 20, 25]" @search="handleSearch" @page-changed="updatePage"
                    @page-size-changed="updatePageSize" placeholder="Pesquise pelo numero da placa" />
            </template>
            <template #fallback>
                <div>Loading...</div>
            </template>
        </Suspense>
        <div class="flex justify-center py-7">
            <RouterLink :to="{ name: 'veiculos.create' }">
                <PrimaryButton label="Cadastrar Veículo" />
            </RouterLink>
        </div>
    </main>
</template>
