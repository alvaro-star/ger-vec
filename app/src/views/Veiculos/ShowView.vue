<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import HeaderModule from '@/components/data-table/HeaderModule.vue';
import CampoShow from '@/components/form-components/CampoShow.vue';
import formatarData from '@/helpers/formatarData';
import api from '@/plugins/api';
import type IVeiculo from '@/types/IVeiculo';
import TableRevisoes from './components/TableRevisoes.vue';
import ButtonEdit from '@/components/form-components/buttons/ButtonEdit.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import ShowTemplate from '@/components/form-components/ShowTemplate.vue';

const route = useRoute();
const router = useRouter();

const veiculo = ref<IVeiculo | null>(null);
const { id } = route.params;

const fetchVeiculo = async () => {
    try {
        const response = await api.get(`/veiculos/${id}`);
        veiculo.value = response.data;
    } catch (error) {
        console.error((error as Error).message);
    }
};

const voltar = () => {
    router.back();
};

const editarVeiculo = () => {
    const id = route.params.id;
    router.push({ name: 'veiculos.edit', params: { id: id } });
};

onMounted(() => {
    fetchVeiculo()
});
</script>

<template class="mt-4">
    <HeaderModule class="mb-7">
        <template #title>
            <h1 class="text-3xl font-bold">Detalhes do Veículo</h1>
        </template>

        <template #actions>
            <ButtonEdit label="Editar" @click="editarVeiculo" />
            <BackButton label="Voltar" @click="voltar" />
        </template>
    </HeaderModule>
    <main class="min-h-[calc(100vh-56px)] pb-10">
        <div v-if="veiculo != undefined" class="container">
            <ShowTemplate title="Dados do Veiculo" class="mt-4">
                <CampoShow class="" titulo="Marca" :valor="veiculo.marca?.nome" />
                <CampoShow class="" titulo="Modelo" :valor="veiculo.modelo" />
                <CampoShow class="" titulo="Placa" :valor="veiculo.placa" />
                <CampoShow class="" titulo="Proprietario">
                    <router-link :to="{ name: 'pessoas.show', params: { id: veiculo.pessoa?.id } }"
                        class="text-blue-500 hover:underline">
                        {{ veiculo.pessoa?.nome }}
                    </router-link>
                </CampoShow>
                <CampoShow class="" titulo="Renavam" :valor="veiculo.renavam" />
                <CampoShow class="" titulo="Ano de Fabricação" :valor="veiculo.ano" />
                <CampoShow class="" titulo="Cor" :valor="veiculo.cor" />
                <CampoShow class="" titulo="Tipo de Combustível" :valor="veiculo.tipo_combustivel" />
                <CampoShow class="" titulo="N Revisões" :valor="veiculo.n_revisoes" />
                <template #dates>
                    <CampoShow titulo="Criado em" :valor="formatarData(veiculo.created_at)" />
                    <CampoShow titulo="Atualizado em" :valor="formatarData(veiculo.updated_at)" />
                </template>
            </ShowTemplate>
            
            <TableRevisoes title="Revisões do veículo" :id="route.params.id" :placa="veiculo?.placa" />
        </div>

    </main>
</template>
