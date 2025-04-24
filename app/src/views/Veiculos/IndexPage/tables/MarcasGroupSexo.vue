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
    { label: 'N Veiculos', field: 'total' }
])

const masculinos = ref<any[]>([])
const femininos = ref<any[]>([])

const emit = defineEmits(['update-data'])

const fetchData = async () => {
    try {
        const response = await api.get('/veiculos/statistics/sexo-marca/n_veiculos')
        const data = response.data

        masculinos.value = data
            .filter((item: any) => item.is_masculino)
            .map((item: any) => ({ ...item }))

        femininos.value = data
            .filter((item: any) => !item.is_masculino)
            .map((item: any) => ({ ...item }))
        emit('update-data', masculinos.value, femininos.value)
    } catch (error) {
        masculinos.value = []
        femininos.value = []
        console.error('Erro ao buscar estatísticas de veículos por sexo e marca:', error)
    }
}

onMounted(() => {
    fetchData()
})
</script>

<template>
    <Suspense>
        <template #default>
            <div v-show="show" class="w-full">
                <section class="px-3 max-w-7xl mx-auto">
                    <div class="mx-auto max-w-screen">
                        <div class="bg-white relative border border-gray-300 sm:rounded overflow-hidden p-4">
                            <TableActions placeholder="" :show-search="false" title="Veículos por Sexo e Marca"
                                :show-filters="false" />
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Tabela Masculino -->
                                <div>
                                    <h2 class="text-lg font-semibold mb-2 text-gray-700">Proprietários Masculinos</h2>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
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
                                    <h2 class="text-lg font-semibold mb-2 text-gray-700">Proprietários Femininos</h2>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
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
