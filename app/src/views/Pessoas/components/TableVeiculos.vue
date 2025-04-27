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
const sort = ref<string>('placa')
const asc = ref<boolean>(true)

const columns = ref<IColumn[]>([
    { label: 'Placa', field: 'placa', sorteable: true },
    { label: 'Marca', field: 'marca', sorteable: true },
    { label: 'Modelo', field: 'modelo', sorteable: true },
    { label: 'Renavam', field: 'renavam', sorteable: true },
    { label: 'Ano de Fabrição', field: 'ano', sorteable: true }
]);


const rows = ref<any[]>([])

const paginatedRecords = computed(() => rows.value)

const loading = ref<boolean>(false)

const fetchData = async () => {
    loading.value = true;
    const params = {
        pessoa_id: props.id,
        asc: asc.value,
        sort: sort.value,
        page: currentPage.value,
        size: pageSize.value,
        query: query.value,
    }

    try {
        const response = await api.get<IPageOutput<IVeiculo>>(`/veiculos`, { params })
        const data = response.data

        rows.value = data.content.map((item: any) => ({
            ...item,
            routeName: 'veiculos.show',
        }))
        totalRecords.value = data.nElementos
    } catch (error) {
        rows.value = []
        console.error('Erro ao buscar dados:', error)
    } finally {
        loading.value = false;
    }
}
watch(currentPage, fetchData)
watch(pageSize, fetchData)
watch(sort, fetchData)
watch(asc, fetchData)

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
                <Table :loading="loading" show-order-info v-model:sort="sort" v-model:asc="asc" class="container"
                    :show-filters="0" :title="title" :columns="columns" :rows="paginatedRecords"
                    :currentPage="currentPage" :totalRecords="totalRecords" :pageSize="pageSize"
                    :pageSizeOptions="[5, 10, 15, 20, 25]" @search="handleSearch" @page-changed="updatePage"
                    @page-size-changed="updatePageSize" placeholder="Pesquise pelo placa, marca, renavam..." />
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
