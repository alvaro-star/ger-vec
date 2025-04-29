<script setup lang="ts">
import PrimaryButton from '@/components/form-components/buttons/PrimaryButton.vue'
import api from '@/plugins/api'
import { computed, onMounted, reactive, ref, watch } from 'vue'
import HeaderModule from '@/components/data-table/HeaderModule.vue'
import Table from '@/components/data-table/Table.vue'
import type IColumn from '@/types/IColumn'
import type { IFilters } from '@/types/IFilter'
import type IPageOutput from '@/types/IPageOutput'
import type IMarca from '@/types/IMarca'
import SectionComponent from '@/components/SectionComponent.vue'
import Pie from '@/plugins/chartjs/Pie.vue'
import StatisticsMarcaComponente from './components/StatisticsMarcaComponente.vue'

const query = ref<string>('')
const currentPage = ref<number>(1)
const pageSize = ref<number>(10)
const totalRecords = ref<number>(0)
const loadingTable = ref<boolean>(false)

const sort = ref<string>('nome')
const asc = ref<boolean>(true)

const filters = reactive<IFilters>({})

const columns = ref<IColumn[]>([
    { label: 'Nome', field: 'nome', sorteable: true },
    { label: 'País de Origem', field: 'pais', sorteable: true },
    { label: 'Ano de Fundação', field: 'ano_fundacao', sorteable: true }
])

const rows = ref<any[]>([])
const paginatedRecords = computed(() => rows.value)

const fetchData = async () => {
    loadingTable.value = true

    const params = {
        page: currentPage.value,
        size: pageSize.value,
        query: query.value,
        sort: sort.value,
        asc: asc.value
    }

    try {
        const response = await api.get<IPageOutput<IMarca>>('/marcas', { params })
        const data = response.data

        rows.value = data.content.map((item: any) => ({
            ...item,
            routeName: 'marcas.show'
        }))
        totalRecords.value = data.nElementos
    } catch (error) {
        rows.value = []
        console.error('Erro ao buscar marcas:', error)
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

const updateSort = (field: string) => {
    if (sort.value === field) {
        asc.value = !asc.value
    } else {
        sort.value = field
        asc.value = true
    }
}

onMounted(() => {
    fetchData()
})
</script>

<template>
    <HeaderModule class="mb-7">
        <template #title>
            <h1 class="text-3xl font-bold">Marcas</h1>
        </template>

        <template #actions>
            <router-link to="/marca/create">
                <PrimaryButton label="Cadastrar Marca" />
            </router-link>
        </template>
    </HeaderModule>

    <main class="min-h-[calc(100vh-56px)] pb-10">
        <SectionComponent titulo="Dados Estatisticos" class="mb-10 container">
            <StatisticsMarcaComponente />
        </SectionComponent>
        <Suspense>
            <template #default>
                <Table :show-filters="0" :loading="loadingTable" class="container" :columns="columns"
                    show-order-info
                    :rows="paginatedRecords" :currentPage="currentPage" :filters="filters" :totalRecords="totalRecords"
                    :pageSize="pageSize" :pageSizeOptions="[5, 10, 15, 20, 25]" v-model:sort="sort" v-model:asc="asc"
                    @search="handleSearch" @page-changed="updatePage" @page-size-changed="updatePageSize"
                    @update-sort="updateSort" title="Lista de Marcas"
                    placeholder="Pesquise pelo nome ou país da marca" />
            </template>

            <template #fallback>
                <div>Loading...</div>
            </template>
        </Suspense>
    </main>
</template>
