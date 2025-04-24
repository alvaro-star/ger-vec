<script setup lang="ts">
import api from '@/plugins/api'
import { computed, onMounted, ref, watch } from 'vue'

import Table from '@/components/data-table/Table.vue'
import formatarData, { formatarClassicData } from '@/helpers/formatarData'
import type IColumn from '@/types/IColumn'
import type IPageOutput from '@/types/IPageOutput'
import type IPessoa from '@/types/IPessoa'

defineProps<{
    show: boolean
}>()
const query = ref<string>('')
const currentPage = ref<number>(1)
const pageSize = ref<number>(10)
const totalRecords = ref<number>(0)

const columns = ref<IColumn[]>([
    { label: 'Nome', field: 'nome' },
    { label: 'CPF', field: 'cpf' },
    { label: 'N Revisões', field: 'n_revisoes' },
    { label: 'Ultima Revisao', field: 'last_revisao' },
    { label: 'MTR (dias)', field: 'avg_tempo_revisoes' },
    { label: 'Seg. Revisão Prevista', field: 'next_revisao' },
])


const calcuateNextRevisao = (last_revisao: string, avg_tempo_revisoes: number): string => {
    avg_tempo_revisoes = avg_tempo_revisoes || 0
    if (!last_revisao || avg_tempo_revisoes == 0) return 'Sem previsão'

    const dias = Math.round(avg_tempo_revisoes)
    const data = new Date(last_revisao)

    data.setDate(data.getDate() + dias)

    return formatarData(data.toISOString())
}
const rows = ref<any[]>([])

const paginatedRecords = computed(() => rows.value)

const fetchData = async () => {
    const params = {
        page: currentPage.value,
        size: pageSize.value,
        query: query.value,
    }

    try {
        const response = await api.get<IPageOutput<IPessoa>>('/pessoas/statistics/revisoes/quantidade-media-tempo', { params })
        const data = response.data

        rows.value = data.content.map((item: any) => ({
            ...item,
            sexo: item.is_masculino ? 'Masculino' : 'Feminino',
            last_revisao: item.last_revisao ? formatarClassicData(item.last_revisao) : 'Sem revisão',
            next_revisao: calcuateNextRevisao(item.last_revisao, item.avg_tempo_revisoes),
            routeName: 'pessoas.show'
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
    <Suspense>
        <template #default>
            <div class="container" v-show="show">
                <p class="font-bold text-xs w-full ml-5">MTR - Media de Tempo entre duas revisões consecutivas</p>
                <Table class="" :columns="columns" :show-filters="0" :rows="paginatedRecords" :currentPage="currentPage"
                    :totalRecords="totalRecords" :pageSize="pageSize" :pageSizeOptions="[5, 10, 15, 20, 25]"
                    @search="handleSearch" @page-changed="updatePage" @page-size-changed="updatePageSize"
                    title="Lista de Clientes (Ordenados por N Revisões)" placeholder="Pesquise pelo nome da pessoa" />
            </div>
        </template>

        <template #fallback>
            <div>Loading...</div>
        </template>
    </Suspense>
</template>
