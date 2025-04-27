<script setup lang="ts">
import Header from '@/components/data-table/Header.vue'
import Row from '@/components/data-table/Row.vue'
import TableActions from '@/components/data-table/TableActions.vue'
import LoadingComponent from '@/components/LoadingComponent.vue'
import api from '@/plugins/api'
import type IColumn from '@/types/IColumn'
import { onMounted, ref } from 'vue'

defineProps<{ show: boolean }>()

const columns = ref<IColumn[]>([
    { label: 'Marca', field: 'marca' },
    { label: 'N Veículos', field: 'total' }
])

const geral = ref<any[]>([])
const masculinos = ref<any[]>([])
const femininos = ref<any[]>([])


const groupBySexo = ref(false)

const emit = defineEmits(['update-data'])

const fetchDataGroupBySexo = async () => {
    try {
        const response = await api.get('/veiculos/statistics/marcas/n_veiculos')
        geral.value = response.data
        emit('update-data', response.data)
    } catch (error) {
        geral.value = []
        console.error('Erro ao buscar estatísticas de veículos por marca:', error)
    }
}


const fetchData = async () => {
    try {
        const response = await api.get('/veiculos/statistics/sexo-marca/n_veiculos')
        const data = response.data

        const responseGeral = await api.get('/veiculos/statistics/marcas/n_veiculos')
        geral.value = responseGeral.data

        masculinos.value = data
            .filter((item: any) => item.is_masculino)
            .map((item: any) => ({ ...item }))

        femininos.value = data
            .filter((item: any) => !item.is_masculino)
            .map((item: any) => ({ ...item }))
        emit('update-data', geral.value, masculinos.value, femininos.value)
    } catch (error) {
        geral.value = []
        masculinos.value = []
        femininos.value = []
        console.error('Erro ao buscar estatísticas de veículos por sexo e marca:', error)
    }
}

onMounted(() => {
    fetchDataGroupBySexo()
    fetchData()
})
</script>

<template>
    <Suspense>
        <template #default>
            <div v-show="show" class="w-full max-w-7xl mx-auto">
                <section class="px-3">
                    <div class="mx-auto max-w-screen">
                        <div class="bg-white relative border border-gray-300 sm:rounded overflow-hidden">
                            <TableActions placeholder="" :show-search="false" title="Quantidade de Veículos por Marca"
                                :show-filters="false">
                                <!-- Checkbox com Label bonita -->
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" v-model="groupBySexo"
                                        class="border-gray-300 text-blue-600 focus:ring-blue-500 rounded" />
                                    <label for="groupBySexo" class="text-sm font-medium text-gray-700">
                                        Agrupar por sexo
                                    </label>
                                </div>
                            </TableActions>
                            <div v-show="!groupBySexo" class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <Header :columns="columns" :showActions="false" />
                                    <tbody>
                                        <Row v-for="(row, index) in geral" :key="index" :columns="columns" :row="row" />
                                    </tbody>
                                </table>
                            </div>
                            <div v-show="groupBySexo" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Tabela Masculino -->
                                <div>
                                    <h2 class="text-lg font-semibold mb-2 text-gray-700 ml-5">Proprietários Masculinos</h2>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm text-left text-gray-500 ">
                                            <Header :columns="columns" :showActions="false" />
                                            <tbody>
                                                <Row v-for="(row, index) in masculinos" :key="`m-${index}`"
                                                    :columns="columns" :row="row" />
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Tabela Feminino -->
                                <div>
                                    <h2 class="text-lg font-semibold mb-2 text-gray-700 ml-5">Proprietários Femininos</h2>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm text-left text-gray-500 ">
                                            <Header :columns="columns" :showActions="false" />
                                            <tbody>
                                                <Row v-for="(row, index) in femininos" :key="`f-${index}`"
                                                    :columns="columns" :row="row" />
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </template>

        <template #fallback>
            <LoadingComponent />
        </template>
    </Suspense>
</template>
