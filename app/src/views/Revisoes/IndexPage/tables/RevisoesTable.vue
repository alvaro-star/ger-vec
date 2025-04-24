<script setup lang="ts">
import api from '@/plugins/api'
import { onMounted, ref, watch } from 'vue'
import Header from '@/components/data-table/Header.vue'
import SearchIcon from '@/components/data-table/icons/SearchIcon.vue'
import Pagination from '@/components/data-table/Pagination.vue'
import Row from '@/components/data-table/Row.vue'
import TextInput from '@/components/form-components/TextInput.vue'
import { converterDataParaISO, formatarClassicData, formatoDataValido } from '@/helpers/formatarData'
import { useAlertStore } from '@/stores/alertState'
import type IColumn from '@/types/IColumn'
import type IPageOutput from '@/types/IPageOutput'
import type IRevisao from '@/types/IRevisao'

defineProps<{
    show: boolean
}>()

const alertStore = useAlertStore()
const dataInicio = ref<string>('')
const dataFim = ref<string>('')
const currentPage = ref<number>(1)
const pageSize = ref<number>(10)
const totalRecords = ref<number>(0)

const columns = ref<IColumn[]>([
    { label: 'Data', field: 'data' },
    { label: 'Tipo', field: 'tipo' },
    { label: 'Quilometragem', field: 'quilometragem' },
    { label: 'Descrição', field: 'descricao' },
    { label: 'Valor Total', field: 'valor_total' },
])
const rows = ref<any[]>([])

const fetchData = async () => {
    let dataIniDate = null
    let dataFimDate = null
    if (dataInicio.value !== '') {
        if (!formatoDataValido(dataInicio.value)) {
            alertStore.setMessage('As datas devem estar no formato dd/mm/aaaa.', 'danger')
            return
        }
        dataIniDate = new Date(converterDataParaISO(dataInicio.value))
    }
    if (dataFim.value !== '') {
        if (!formatoDataValido(dataFim.value)) {
            alertStore.setMessage('As datas devem estar no formato dd/mm/aaaa.', 'danger')
            return
        }
        dataFimDate = new Date(converterDataParaISO(dataInicio.value))
    }

    if (dataIniDate && dataFimDate) {
        if (dataIniDate > dataFimDate) {
            alertStore.setMessage('A data de início deve ser anterior à data de fim.', 'danger')
            return
        }
    }

    const params = {
        page: currentPage.value,
        size: pageSize.value,
        data_start: dataInicio.value,
        data_end: dataFim.value
    }

    try {
        const response = await api.get<IPageOutput<IRevisao>>('/revisoes', { params })
        const data = response.data

        rows.value = data.content.map((item: any) => ({
            ...item,
            quilometragem: item.quilometragem + 'km',
            data: formatarClassicData(item.data),
            valor_total: "R$" + item.valor_total,
            veiculoPlaca: item.veiculo?.placa ?? 'Desconhecido',
            routeName: 'revisoes.show',
        }))
        totalRecords.value = data.nElementos
    } catch (error) {
        rows.value = []
        console.error('Erro ao buscar revisões:', error)
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

onMounted(() => {
    fetchData()
})
</script>

<template>
    <Suspense>
        <template #default>
            <section v-show="show" class="container px-3">
                <div class="mx-auto max-w-screen ">
                    <div class="bg-white relative border border-gray-300 sm:rounded overflow-hidden">
                        <div
                            class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                            <h2 class="text-2xl">Lista de Revisões</h2>
                            <div class="flex gap-4 items-center">
                                <p class="text-xl">Intervalo</p>
                                <TextInput class="w-40" v-model="dataInicio" placeholder="Inicio" />
                                <TextInput class="w-40" v-model="dataFim" placeholder="Fim" />
                                <button class="flex items-center px-4 bg-customBackground h-11 rounded"
                                    @click="fetchData">
                                    <SearchIcon />
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <Header :columns="columns" :show-actions="true" />
                                <tbody>
                                    <Row v-for="(row, index) in rows" :key="index" :columns="columns" :row="row" />
                                </tbody>
                            </table>
                        </div>
                        <Pagination :currentPage="currentPage" :totalRecords="totalRecords" :pageSize="pageSize"
                            :pageSizeOptions="[5, 10, 15, 20, 25]" @page-changed="updatePage"
                            @page-size-changed="updatePageSize" />
                    </div>
                </div>
            </section>
        </template>
        <template #fallback>
            <div>Carregando revisões...</div>
        </template>
    </Suspense>

</template>
