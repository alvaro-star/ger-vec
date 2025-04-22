<script setup lang="ts">
import Header from '@/components/data-table/Header.vue'
import Pagination from '@/components/data-table/Pagination.vue'
import Row from '@/components/data-table/Row.vue'
import PrimaryButton from '@/components/form-components/buttons/PrimaryButton.vue'
import { formatarClassicData } from '@/helpers/formatarData'
import api from '@/plugins/api'
import type IColumn from '@/types/IColumn'
import type IPageOutput from '@/types/IPageOutput'
import type IRevisao from '@/types/IRevisao'
import { onMounted, ref, watch } from 'vue'

const props = defineProps<{
    id: string[] | string
    placa: string
    title: string
}>()

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
    const params = {
        page: currentPage.value,
        size: pageSize.value
    }

    try {
        const response = await api.get<IPageOutput<IRevisao>>(`/veiculos/${props.id}/revisoes`, { params })
        const data = response.data

        rows.value = data.content.map((item: IRevisao) => ({
            ...item,
            data: formatarClassicData(item.data),
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

onMounted(fetchData)
</script>

<template>
    <main class="w-full">
        <Suspense>
            <template #default>
                <section class="conteiner px-3">
                    <div class="mx-auto max-w-screen ">
                        <div class="bg-white relative border border-gray-300 sm:rounded overflow-hidden">
                            <div
                                class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                                <h2 class="text-2xl">{{ title }}</h2>
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

        <div class="flex justify-center py-7">
            <RouterLink :to="{ name: 'revisoes.create', query: { placa: props.placa } }">
                <PrimaryButton label="Cadastrar Revisão" />
            </RouterLink>

        </div>
    </main>
</template>
