<script setup lang="ts">
import PrimaryButton from '@/components/form-components/buttons/PrimaryButton.vue'
import api from '@/plugins/api'
import { computed, onMounted, ref, watch } from 'vue'

import HeaderModule from '@/components/data-table/HeaderModule.vue'
import Table from '@/components/data-table/Table.vue'
import type IColumn from '@/types/IColumn'
import type IPageOutput from '@/types/IPageOutput'
import type IPessoa from '@/types/IPessoa'

const query = ref<string>('')
const currentPage = ref<number>(1)
const pageSize = ref<number>(10)
const totalRecords = ref<number>(0)

const columns = ref<IColumn[]>([
    { label: 'Nome', field: 'nome' },
    { label: 'Telefone', field: 'telefone' },
    { label: 'CPF', field: 'cpf' },
    { label: 'Sexo', field: 'sexo' },
    { label: 'Idade', field: 'idade' }
]);

const rows = ref<any[]>([])

const paginatedRecords = computed(() => rows.value)

const fetchData = async () => {
    const params = {
        page: currentPage.value,
        size: pageSize.value,
        query: query.value,
    }

    try {
        const response = await api.get<IPageOutput<IPessoa>>('/pessoas', { params })
        const data = response.data
        console.log(data);


        rows.value = data.content.map((item: any) => ({
            ...item,
            sexo: item.is_masculino ? 'Masculino' : 'Feminino',
            routeName: 'pessoas.show',
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
                <Table class="container" :columns="columns" :rows="paginatedRecords" :currentPage="currentPage"
                    :totalRecords="totalRecords" :pageSize="pageSize" :pageSizeOptions="[5, 10, 15, 20, 25]"
                    @search="handleSearch" @page-changed="updatePage" @page-size-changed="updatePageSize"
                    title="Lista de Pessoas" placeholder="Pesquise pelo nome da pessoa" />
            </template>
            <template #fallback>
                <div>Loading...</div>
            </template>
        </Suspense>
    </main>
</template>
