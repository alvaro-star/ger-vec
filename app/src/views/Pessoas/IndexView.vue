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
import Pie from '../../plugins/chartjs/Pie.vue'
import type IStatisticPessoa from '@/types/IStatisticPessoa'

const query = ref<string>('')
const currentPage = ref<number>(1)
const pageSize = ref<number>(10)
const totalRecords = ref<number>(0)
type IStatistics = Record<string, IStatisticPessoa>
const statistics = ref<IStatistics>({})

const columns = ref<IColumn[]>([
    { label: 'Nome', field: 'nome' },
    { label: 'Telefone', field: 'telefone' },
    { label: 'CPF', field: 'cpf' },
    { label: 'Sexo', field: 'sexo' },
    { label: 'Idade', field: 'idade' }
])

// Torna os filtros reativos com reactive
const filters = reactive<IFilters>({
    Sexo: {
        options: ['Ambos', 'Masculino', 'Feminino'],
        type: 'radio',
        value: ['Ambos']
    }
})

const rows = ref<any[]>([])

const paginatedRecords = computed(() => rows.value)

// Função para obter o valor do filtro de sexo
const getSexoFiltro = () => {
    const selected = filters.Sexo?.value?.[0] || ''
    const firstChar = selected.charAt(0).toUpperCase()
    switch (firstChar) {
        case 'M': return 'M'
        case 'F': return 'F'
        default: return ''
    }
}

// Função para buscar os dados com base no filtro
const fetchData = async () => {
    const sexo = getSexoFiltro()

    const params = {
        page: currentPage.value,
        size: pageSize.value,
        query: query.value,
        sexo
    }

    try {
        const response = await api.get<IPageOutput<IPessoa>>('/pessoas', { params })
        const data = response.data

        rows.value = data.content.map((item: any) => ({
            ...item,
            sexo: item.is_masculino ? 'Masculino' : 'Feminino',
            routeName: 'pessoas.show'
        }))
        totalRecords.value = data.nElementos
    } catch (error) {
        rows.value = []
        console.error('Erro ao buscar dados:', error)
    }
}


const fetchStatistics = async () => {
    try {
        const response = await api.get<IStatistics>('/pessoas/statistics')
        statistics.value = response.data
    } catch (error) {
        console.error('Erro ao buscar dados:', error)
    }
}


// Watchers para atualizar os dados quando as variáveis mudarem
watch(currentPage, fetchData)
watch(pageSize, fetchData)
watch(() => filters.Sexo.value, fetchData)

const updatePage = (page: number) => {
    currentPage.value = page
}

// Atualiza o tamanho da página
const updatePageSize = (size: number) => {
    pageSize.value = size
    currentPage.value = 1
}

// Atualiza a busca
const handleSearch = async (newQuery: string) => {
    query.value = newQuery
    currentPage.value = 1
    await fetchData()
}

// Atualiza os filtros quando um filho emite
const updateFilter = (label: string, value: string | string[]) => {
    if (filters[label]) {
        filters[label].value = Array.isArray(value) ? value : [value]
    }
}

onMounted(() => {
    fetchStatistics()
    fetchData()
})
</script>

<template>
    <main class="h-[calc(100vh-56px)]">
        <HeaderModule class="mb-4">
            <template #title>
                <h1 class="text-3xl font-bold">Pessoas</h1>
            </template>

            <template #actions>
                <router-link to="/pessoa/create">
                    <PrimaryButton label="Cadastrar Pessoa" />
                </router-link>
            </template>
        </HeaderModule>

        <Suspense>
            <template #default>
                <h2>
                    Dados Estatisticos
                </h2>
                <div class="grid grid-cols-3">
                    <Pie :labels="['A', 'B', 'C']" :values="[10, 20, 30]" />
                </div>
            </template>
        </Suspense>
        <Suspense>
            <template #default>
                <Table class="container" :columns="columns" :rows="paginatedRecords" :currentPage="currentPage"
                    :filters="filters" :totalRecords="totalRecords" :pageSize="pageSize" @update-filter="updateFilter"
                    :pageSizeOptions="[5, 10, 15, 20, 25]" @search="handleSearch" @page-changed="updatePage"
                    @page-size-changed="updatePageSize" title="Lista de Pessoas"
                    placeholder="Pesquise pelo nome da pessoa" />
            </template>

            <template #fallback>
                <div>Loading...</div>
            </template>
        </Suspense>
    </main>
</template>
