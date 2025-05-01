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
    { label: 'N Revisões', field: 'total' }
])



const rows = ref<any[]>([])


const emit = defineEmits(['update-data'])
const fetchData = async () => {
    try {
        const response = await api.get('/marcas/statistics/n_revisoes')
        rows.value = response.data
        emit('update-data', response.data)
    } catch (error) {
        rows.value = []
        console.error('Erro ao buscar estatísticas de veículos por marca:', error)
    }
}


onMounted(() => {
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
                            <TableActions placeholder="" :show-search="false" title="Quantidade de Revisões por Marca"
                                :show-filters="false">
                            </TableActions>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500 ">
                                    <Header :columns="columns" :showActions="false" />
                                    <tbody>
                                        <Row v-for="(row, index) in rows" :key="index" :columns="columns" :row="row" />
                                    </tbody>
                                </table>
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
