<script setup lang="ts">
import PrimaryButton from '@/components/form-components/buttons/PrimaryButton.vue'
import api from '@/plugins/api'
import { computed, onMounted, reactive, ref, watch } from 'vue'
import HeaderModule from '@/components/data-table/HeaderModule.vue'
import Table from '@/components/data-table/Table.vue'
import type IColumn from '@/types/IColumn'
import type { IFilters } from '@/types/IFilter'
import type IPageOutput from '@/types/IPageOutput'
import type IPessoa from '@/types/IPessoa'
import type IStatisticPessoa from '@/types/IStatisticPessoa'
import StatisticPessoaGraphic from './components/graphics/StatisticPessoaGraphic.vue'
import { formatarCelular, formatarCPF } from '@/helpers/formatters'

const query = ref<string>('')
const currentPage = ref<number>(1)
const pageSize = ref<number>(10)
const totalRecords = ref<number>(0)
const loadingTable = ref<boolean>(false)
const loadingStatistics = ref<boolean>(false)
const sort = ref<string>('nome')
const asc = ref(true)

type IStatistics = Record<string, IStatisticPessoa>
const statistics = ref<IStatistics>({})

const columns = ref<IColumn[]>([
    { label: 'Nome', field: 'nome', sorteable: true },
    { label: 'Celular', field: 'celular', sorteable: true },
    { label: 'CPF', field: 'cpf', sorteable: true },
    { label: 'Sexo', field: 'sexo' },
    { label: 'Idade', field: 'idade', sorteable: true }
])

const filters = reactive<IFilters>({
    Sexo: {
        options: ['Ambos', 'Masculino', 'Feminino'],
        type: 'radio',
        value: ['Ambos']
    }
})

const rows = ref<any[]>([])

const paginatedRecords = computed(() => rows.value)


const getSexoFiltro = () => {
    const selected = filters.Sexo?.value?.[0] || ''
    const firstChar = selected.charAt(0).toUpperCase()
    switch (firstChar) {
        case 'M': return 'M'
        case 'F': return 'F'
        default: return ''
    }
}

const fetchData = async () => {
    loadingTable.value = true
    const sexo = getSexoFiltro()

    const params = {
        page: currentPage.value,
        size: pageSize.value,
        query: query.value,
        sort: sort.value,
        asc: asc.value,
        sexo
    }

    try {
        const response = await api.get<IPageOutput<IPessoa>>('/pessoas', { params })
        const data = response.data

        rows.value = data.content.map((item: any) => ({
            ...item,
            sexo: item.is_masculino ? 'Masculino' : 'Feminino',
            idade: item.idade + ' anos',
            cpf: formatarCPF(item.cpf),
            celular: formatarCelular(item.celular),
            routeName: 'pessoas.show'
        }))
        totalRecords.value = data.nElementos
    } catch (error) {
        rows.value = []
        console.error('Erro ao buscar dados:', error)
    } finally {
        loadingTable.value = false
    }
}

const fetchStatistics = async () => {
    loadingStatistics.value = true
    try {
        const response = await api.get<IStatistics>('/pessoas/statistics')
        statistics.value = response.data
    } catch (error) {
        console.error('Erro ao buscar dados:', error)
    } finally {
        loadingStatistics.value = false
    }
}


// Watchers para atualizar os dados quando as variáveis mudarem
watch(currentPage, fetchData)
watch(pageSize, fetchData)
watch(asc, fetchData)
watch(sort, fetchData)
watch(() => filters.Sexo.value, fetchData)

const updatePage = (page: number) => {
    currentPage.value = page
}

// Atualiza o tamanho da página
const updatePageSize = (size: number) => {
    pageSize.value = size
    currentPage.value = 1
}

const updateSort = (field: string) => {
    if (sort.value == field) {
        asc.value = !asc.value
    } else {
        sort.value = field
        asc.value = true
    }
    fetchData()
}

// Atualiza a busca
const handleSearch = async (newQuery: string) => {
    query.value = newQuery
    currentPage.value = 1
    await fetchData()
}

// Atualiza os filtros quando um filho emite
const updateFilter = (label: string, value: string | string[]) => {
    if (filters[label])
        filters[label].value = Array.isArray(value) ? value : [value]
}

onMounted(() => {
    fetchStatistics()
    fetchData()
})
</script>

<template>
    <HeaderModule class="mb-7">
        <template #title>
            <h1 class="text-3xl font-bold">Pessoas</h1>
        </template>

        <template #actions>
            <router-link to="/pessoa/create">
                <PrimaryButton label="Cadastrar Pessoa" />
            </router-link>
        </template>
    </HeaderModule>
    <main class="min-h-[calc(100vh-56px)] pb-10">

        <StatisticPessoaGraphic :loading="loadingStatistics" class="mb-7" :sexo="filters['Sexo'].value"
            :statistics="statistics" />

        <Suspense>
            <template #default>
                <Table v-model:asc="asc" v-model:sort="sort" show-order-info :loading="loadingTable" class="container"
                    :columns="columns" :rows="paginatedRecords" :currentPage="currentPage" :filters="filters"
                    :totalRecords="totalRecords" :pageSize="pageSize" @update-filter="updateFilter"
                    :pageSizeOptions="[5, 10, 15, 20, 25]" @search="handleSearch" @page-changed="updatePage"
                    @page-size-changed="updatePageSize" title="Lista de Pessoas"
                    placeholder="Pesquise pelo nome, cpf ou celular da pessoa" />
            </template>

            <template #fallback>
                <div>Loading...</div>
            </template>
        </Suspense>
    </main>
</template>
