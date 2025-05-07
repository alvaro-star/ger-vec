<script setup lang="ts">
import SpinerAnimation from '@/components/animation/SpinerAnimation.vue'
import Header from '@/components/data-table/Header.vue'
import SearchIcon from '@/components/data-table/icons/SearchIcon.vue'
import Pagination from '@/components/data-table/Pagination.vue'
import Row from '@/components/data-table/Row.vue'
import TableActions from '@/components/data-table/TableActions.vue'
import PrimaryButton from '@/components/form-components/buttons/PrimaryButton.vue'
import DateInput from '@/components/form-components/DateInput.vue'
import { formatarDateToStringDate, formatarFistLetter, formatarFloat, formatarInteger, formatarLocalDate } from '@/helpers/formatters'
import api from '@/plugins/api'
import type IColumn from '@/types/IColumn'
import type IPageOutput from '@/types/IPageOutput'
import type IRevisao from '@/types/IRevisao'
import { computed, onMounted, ref, watch } from 'vue'

const props = defineProps<{
    id: string[] | string
    placa: string
    title: string
}>()
const dataInicio = ref<Date>()
const dataFim = ref<Date>()

const currentPage = ref<number>(1)
const pageSize = ref<number>(10)
const totalRecords = ref<number>(0)
const loading = ref<boolean>(false)

const sort = ref<string>('data')
const asc = ref<boolean>(false)

const columns = ref<IColumn[]>([
    { label: 'Data', field: 'data', sorteable: true },
    { label: 'Tipo', field: 'tipo' },
    { label: 'Quilometragem', field: 'quilometragem', class: 'whitespace-nowrap', sorteable: true },
    { label: 'Descrição', field: 'descricao', },
    { label: 'Valor Total', field: 'valor_total', class: 'whitespace-nowrap', sorteable: true },
])

const rows = ref<any[]>([])
const errors = ref<Record<string, string>>({})

const paginatedRecords = computed(() => rows.value)
const fetchData = async () => {

    errors.value = {}

    if (dataInicio.value && dataFim.value && dataInicio.value > dataFim.value) {
        errors.value = {
            dataFim: 'Valor inválido'
        }
        return
    }

    const params = {
        veiculo_id: props.id,
        page: currentPage.value,
        size: pageSize.value,
        data_start: formatarDateToStringDate(dataInicio.value),
        data_end: formatarDateToStringDate(dataFim.value),
        sort: sort.value,
        asc: asc.value
    }

    loading.value = true

    try {
        const response = await api.get<IPageOutput<IRevisao>>(`/revisoes`, { params })
        const data = response.data

        rows.value = data.content.map((item: IRevisao) => ({
            ...item,
            data: formatarLocalDate(item.data),
            quilometragem: formatarInteger(item.quilometragem.toString().replace(".", ",")) + ' km',
            valor_total: "R$ " + formatarFloat(item.valor_total.toString().replace(".", ","), 2),
            garantia_meses: `${item.garantia_meses} meses`,
            routeName: 'revisoes.show',
        }))

        totalRecords.value = data.nElementos
    } catch (error) {
        rows.value = []
        console.error('Erro ao buscar revisões:', error)
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
const updateSort = (field: string) => {
    if (sort.value === field) {
        asc.value = !asc.value
    } else {
        sort.value = field
        asc.value = true
    }
}
onMounted(fetchData)
</script>

<template>
    <main class="w-full">
        <section class="container px-3">
            <div class="mx-auto max-w-screen">
                <div class="bg-white relative border border-gray-300 sm:rounded overflow-hidden">
                    <TableActions :show-search="false" placeholder="" title="Lista de Revisões">
                        <div class="flex flex-col md:flex-row gap-2 items-center">
                            <p class="text-xl">Intervalo</p>
                            <DateInput class="w-40 flex-shrink-0" v-model="dataInicio" placeholder="Início" />
                            <DateInput class="w-40 flex-shrink-0" v-model="dataFim" placeholder="Fim"
                                :message="errors.dataFim" />
                            <div class="w-full flex justify-center">
                                <button
                                    class="flex cursor-pointer items-center px-4 bg-customBackground h-11 rounded text-white font-semibold"
                                    @click="fetchData">
                                    <SearchIcon />
                                    <p class="md:hidden">Buscar</p>
                                </button>
                            </div>
                        </div>
                    </TableActions>

                    <span class="text-sm text-gray-600 px-4">
                        Ordenado por
                        <span class="font-semibold">{{ formatarFistLetter(sort) || 'não definido' }}</span>
                        <span>({{ asc ? 'Crescente' : 'Decrescente' }})</span>
                    </span>
                    <div v-if="loading"
                        class="w-full text-sm text-left text-gray-300 grid place-content-center border-y h-96 border-gray-300">
                        <SpinerAnimation />
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="min-w-[800px] w-full text-sm text-left text-gray-500">
                            <Header :columns="columns" showActions @sort="updateSort" />
                            <tbody>
                                <Row v-for="(row, index) in rows" :key="index" :columns="columns" :row="row" />
                            </tbody>
                        </table>

                        <div v-if="!rows.length"
                            class="min-w-[800px] w-full text-base text-center py-4 text-gray-500 border-y">
                            Não encontramos nenhum registro.
                        </div>
                    </div>

                    <Pagination v-show="!loading" :currentPage="currentPage" :totalRecords="totalRecords"
                        :pageSize="pageSize" :pageSizeOptions="[5, 10, 15, 20, 25]" @page-changed="updatePage"
                        @page-size-changed="updatePageSize" />
                </div>
            </div>
        </section>


        <div class="flex justify-center py-7">
            <RouterLink :to="{ name: 'revisoes.create', query: { placa: props.placa } }">
                <PrimaryButton label="Cadastrar Revisão" />
            </RouterLink>
        </div>
    </main>
</template>
