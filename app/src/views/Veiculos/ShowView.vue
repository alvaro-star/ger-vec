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

onMounted(fetchVeiculo);
</script>

<template class="mt-4">
    <main class="h-[calc(100vh-56px)]">
        <HeaderModule class="mb-4">
            <template #title>
                <h1 class="text-3xl font-bold">Detalhes do Veículo</h1>
            </template>

            <template #actions>
                <ButtonEdit label="Editar" @click="editarVeiculo" />
                <BackButton label="Voltar" @click="voltar" />
            </template>
        </HeaderModule>

        <Suspense>
            <template #default>
                <div class="container">
                    <section v-if="veiculo" class="pt-3 pb-1.5 pr-3 pl-3">
                        <div class="mx-auto max-w-screen">
                            <div class="p-5 bg-white relative border border-gray-300 sm:rounded overflow-hidden">
                                <h2 class="text-xl font-semibold mb-4">Dados do Veículo</h2>
                                <div class="border-b border-colorline"></div>
                                <div class="flex flex-wrap -mx-3 mt-4">
                                    <CampoShow class="md:w-1/3" titulo="Marca" :valor="veiculo.marca" />
                                    <CampoShow class="md:w-1/3" titulo="Modelo" :valor="veiculo.modelo" />
                                    <CampoShow class="md:w-1/3" titulo="Placa" :valor="veiculo.placa" />
                                    <CampoShow class="md:w-1/3" titulo="Proprietario">
                                        <router-link :to="{ name: 'pessoas.show', params: { id: veiculo.pessoa?.id } }"
                                            class="text-blue-500 hover:underline">
                                            {{ veiculo.pessoa?.nome }}
                                        </router-link>
                                    </CampoShow>
                                    <CampoShow class="md:w-1/3" titulo="Renavam" :valor="veiculo.renavam" />
                                    <CampoShow class="md:w-1/3" titulo="Ano" :valor="veiculo.ano" />
                                    <CampoShow class="md:w-1/3" titulo="Cor" :valor="veiculo.cor" />
                                    <CampoShow class="md:w-1/3" titulo="Tipo de Combustível"
                                        :valor="veiculo.tipo_combustivel" />
                                </div>
                                <div class="flex flex-wrap -mx-3 mt-4">
                                    <CampoShow class="md:w-1/3" titulo="Criado em"
                                        :valor="formatarData(veiculo.created_at)" />
                                    <CampoShow class="md:w-1/3" titulo="Atualizado em"
                                        :valor="formatarData(veiculo.updated_at)" />
                                </div>
                            </div>
                        </div>
                    </section>

                    <TableRevisoes v-if="veiculo" title="Revisões do veículo" :id="route.params.id"
                        :placa="veiculo?.placa" />
                </div>
            </template>
            <template #fallback>
                <div>Carregando...</div>
            </template>
        </Suspense>
    </main>
</template>
