<script setup lang="ts">
import api from '@/plugins/api'
import { computed, onMounted, ref, watch } from 'vue'
import Table from '@/components/data-table/Table.vue'
import type IColumn from '@/types/IColumn'
import type IPageOutput from '@/types/IPageOutput'
import type IPessoa from '@/types/IPessoa'
import { formatarCPF, formatarLocalDate, formatarLocalDateTime } from '@/helpers/formatters'

defineProps<{
    show: boolean
}>()

const query = ref<string>('')
const currentPage = ref<number>(1)
const pageSize = ref<number>(10)
const totalRecords = ref<number>(0)
const loading = ref<boolean>(false)

const sort = ref<string>('n_revisoes')
const asc = ref<boolean>(false)

const columns = ref<IColumn[]>([
    { label: 'Nome', field: 'nome', sorteable: true },
    { label: 'CPF', field: 'cpf', class: 'whitespace-nowrap', sorteable: true },
    { label: 'N Revisões', field: 'n_revisoes', sorteable: true },
    { label: 'Ultima Revisão', field: 'last_revisao' },
    { label: 'MTR (dias)', field: 'avg_tempo_revisoes' },
    { label: 'Seg. Revisão Prevista', field: 'next_revisao' },
])

const calcuateNextRevisao = (last_revisao: string, avg_tempo_revisoes: number): string => {
    avg_tempo_revisoes = avg_tempo_revisoes || 0
    if (!last_revisao || avg_tempo_revisoes == 0) return 'Sem previsão'

    const dias = Math.round(avg_tempo_revisoes)
    const data = new Date(last_revisao)
    data.setDate(data.getDate() + dias)

    return formatarLocalDateTime(data.toISOString())
}

const rows = ref<any[]>([])
const paginatedRecords = computed(() => rows.value)

const fetchData = async () => {
    loading.value = true
    const params = {
        page: currentPage.value,
        size: pageSize.value,
        query: query.value,
        sort: sort.value,
        asc: asc.value
    }

    try {
        const response = await api.get<IPageOutput<IPessoa>>('/pessoas/statistics/revisoes/quantidade-media-tempo', { params })
        const data = response.data

        rows.value = data.content.map((item: any) => ({
            ...item,
            sexo: item.is_masculino ? 'Masculino' : 'Feminino',
            cpf: formatarCPF(item.cpf),
            last_revisao: item.last_revisao ? formatarLocalDate(item.last_revisao) : 'Sem revisão',
            next_revisao: calcuateNextRevisao(item.last_revisao, item.avg_tempo_revisoes),
            routeName: 'pessoas.show'
        }))
        totalRecords.value = data.nElementos
    } catch (error) {
        rows.value = []
        console.error('Erro ao buscar dados:', error)
    } finally {
        loading.value = false
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
    <Suspense>
        <template #default>
            <div class="container" v-show="show">
                <p class="font-bold text-xs ml-5">MTR - Média de Tempo entre duas revisões consecutivas</p>
                <Table :loading="loading" :columns="columns" :show-filters="0" :rows="paginatedRecords" show-order-info
                    v-model:asc="asc" v-model:sort="sort" :currentPage="currentPage" :totalRecords="totalRecords"
                    :pageSize="pageSize" :pageSizeOptions="[5, 10, 15, 20, 25]" title="Lista de Clientes"
                    placeholder="Pesquise pelo nome ou cpf da pessoa" @search="handleSearch" @page-changed="updatePage"
                    @page-size-changed="updatePageSize" @update-sort="updateSort" />
            </div>
        </template>

        <template #fallback>
            <div>Loading...</div>
        </template>
    </Suspense>
</template>
