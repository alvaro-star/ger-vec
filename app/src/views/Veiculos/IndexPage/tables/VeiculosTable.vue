<script setup lang="ts">
import api from '@/plugins/api'
import { computed, onMounted, reactive, ref, watch } from 'vue'
import Table from '@/components/data-table/Table.vue'
import type IColumn from '@/types/IColumn'
import type IPageOutput from '@/types/IPageOutput'
import type IVeiculo from '@/types/IVeiculo'
import LoadingComponent from '@/components/LoadingComponent.vue'
import type { ISort } from '@/types/IFilter'


defineProps<{
    show: boolean;
}>()

const query = ref<string>('')
const currentPage = ref<number>(1)
const pageSize = ref<number>(10)
const totalRecords = ref<number>(0)
const sorters = reactive<ISort>({
    options: ['Proprietario'],
    value: ''
})

const columns = ref<IColumn[]>([
    { label: 'Proprietario', field: 'nome' },
    { label: 'Placa', field: 'placa' },
    { label: 'Marca', field: 'marca' },
    { label: 'Modelo', field: 'modelo' },
    { label: 'Renavam', field: 'renavam' },
    { label: 'Ano', field: 'ano' }
])

const rows = ref<any[]>([])

const paginatedRecords = computed(() => rows.value)

const fetchData = async () => {
    const params = {
        page: currentPage.value,
        size: pageSize.value,
        query: query.value,
        sort: sorters.value
    }

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
    }
}

watch(currentPage, fetchData)
watch(pageSize, fetchData)

const updateSort = async (value: string) => {
    currentPage.value = 1
    sorters.value = value
    await fetchData()
}

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

                <Table class="container" title="Lista de Veículos" :sorters="sorters" :columns="columns"
                    :rows="paginatedRecords" :currentPage="currentPage" :totalRecords="totalRecords"
                    :pageSize="pageSize" :pageSizeOptions="[5, 10, 15, 20, 25]" @search="handleSearch"
                    @page-changed="updatePage" @page-size-changed="updatePageSize" @update-sort="updateSort"
                    placeholder="Pesquise pela placa" />
            </div>


        </template>
        <template #fallback>
            <LoadingComponent />
        </template>
    </Suspense>
</template>