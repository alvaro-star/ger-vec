<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import HeaderModule from '@/components/data-table/HeaderModule.vue'
import BackButton from '@/components/form-components/buttons/BackButton.vue'
import ButtonEdit from '@/components/form-components/buttons/ButtonEdit.vue'
import CampoShow from '@/components/form-components/CampoShow.vue'
import api from '@/plugins/api'
import type IMarca from '@/types/IMarca'
import ShowTemplate from '@/components/form-components/ShowTemplate.vue'
import VeiculosTable from './components/VeiculosTable.vue'
import { formatarLocalDateTime } from '@/helpers/formatters'

const route = useRoute()
const router = useRouter()

const marca = ref<IMarca | null>(null)
const { id } = route.params

const fetchMarca = async () => {
    try {
        const { data } = await api.get(`/marcas/${id}`)
        marca.value = data
    } catch (error) {
        console.error((error as Error).message)
    }
}

const voltar = () => {
    router.back()
}

const editarMarca = () => {
    router.push({ name: 'marcas.edit', params: { id: route.params.id } })
}

onMounted(fetchMarca)
</script>

<template class="mt-4">
    <HeaderModule class="mb-7">
        <template #title>
            <h1 class="text-3xl font-bold">Detalhes da Marca</h1>
        </template>

        <template #actions>
            <ButtonEdit label="Editar" @click="editarMarca" />
            <BackButton label="Voltar" @click="voltar" />
        </template>
    </HeaderModule>
    <main class="min-h-[calc(100vh-56px)] pb-10">
        <Suspense>
            <template #default>
                <div v-if="marca" class="container">
                    <ShowTemplate title="Dados da Marca">
                        <CampoShow class="" titulo="Nome" :valor="marca.nome" />
                        <CampoShow class="" titulo="Ano de Fundação" :valor="marca.ano_fundacao" />
                        <CampoShow class="" titulo="País de Origem" :valor="marca.pais" />
                        <template #dates>
                            <CampoShow class="" titulo="Criado em" :valor="formatarLocalDateTime(marca.created_at)" />
                            <CampoShow class="" titulo="Atualizado em"
                                :valor="formatarLocalDateTime(marca.updated_at)" />
                        </template>
                    </ShowTemplate>
                    <VeiculosTable :id="id" title="Veículos da Marca" />
                </div>
            </template>
            <template #fallback>
                <div>Carregando...</div>
            </template>
        </Suspense>
    </main>
</template>
