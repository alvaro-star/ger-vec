<script setup lang="ts">
import Table from '@/components/data-table/Table.vue'
import LoadingComponent from '@/components/LoadingComponent.vue'
import api from '@/plugins/api'
import type IColumn from '@/types/IColumn'
import type IPageOutput from '@/types/IPageOutput'
import type IVeiculo from '@/types/IVeiculo'
import { computed, onMounted, ref, watch } from 'vue'

defineProps<{
    show: boolean;
}>()

const query = ref<string>('')
const currentPage = ref<number>(1)
const pageSize = ref<number>(10)
const totalRecords = ref<number>(0)

const sort = ref<string>('proprietario')
const asc = ref<boolean>(true)

const columns = ref<IColumn[]>([
    { label: 'Proprietario', field: 'proprietario', sorteable: true },
    { label: 'Placa', field: 'placa', sorteable: true },
    { label: 'Marca', field: 'marca', sorteable: true },
    { label: 'Modelo', field: 'modelo', sorteable: true },
    { label: 'Renavam', field: 'renavam', sorteable: true },
    { label: 'Ano de Fabricação', field: 'ano', sorteable: true }
])

const rows = ref<any[]>([])
const loadingTable = ref<boolean>(false)

const paginatedRecords = computed(() => rows.value)

const fetchData = async () => {
    const params = {
        page: currentPage.value,
        size: pageSize.value,
        query: query.value,
        sort: sort.value,
        asc: asc.value
    }

    loadingTable.value = true
    try {
        const response = await api.get<IPageOutput<IVeiculo>>('/veiculos', { params })
        const data = response.data
        rows.value = data.content.map((item: any) => ({
            ...item,
            routeName: 'veiculos.show',
        }))
        totalRecords.value = data.nElementos

    } catch (error) {
        rows.value = []
        console.error('Erro ao buscar veículos:', error)
    } finally {
        loadingTable.value = false
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
    <Suspense>
        <template #default>
            <div v-show="show" class="w-full">
                <Table class="container" title="Lista de Veículos" :show-filters="0" :show-order-info="true"
                    v-model:sort="sort" v-model:asc="asc" :columns="columns" :rows="paginatedRecords"
                    :currentPage="currentPage" :totalRecords="totalRecords" :pageSize="pageSize" :loading="loadingTable"
                    :pageSizeOptions="[5, 10, 15, 20, 25]" @search="handleSearch" @page-changed="updatePage"
                    @page-size-changed="updatePageSize" placeholder="Pesquise pela placa, renavam, marca..." />
            </div>
        </template>
        <template #fallback>
            <LoadingComponent />
        </template>
    </Suspense>
</template>
