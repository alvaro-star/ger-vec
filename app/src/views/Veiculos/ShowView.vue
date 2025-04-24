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
        <HeaderModule class="mb-7">
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
                                <div class="-mx-3 mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
                                    <CampoShow class="" titulo="Marca" :valor="veiculo.marca" />
                                    <CampoShow class="" titulo="Modelo" :valor="veiculo.modelo" />
                                    <CampoShow class="" titulo="Placa" :valor="veiculo.placa" />
                                    <CampoShow class="" titulo="Proprietario">
                                        <router-link :to="{ name: 'pessoas.show', params: { id: veiculo.pessoa?.id } }"
                                            class="text-blue-500 hover:underline">
                                            {{ veiculo.pessoa?.nome }}
                                        </router-link>
                                    </CampoShow>
                                    <CampoShow class="" titulo="Renavam" :valor="veiculo.renavam" />
                                    <CampoShow class="" titulo="Ano" :valor="veiculo.ano" />
                                    <CampoShow class="" titulo="Cor" :valor="veiculo.cor" />
                                    <CampoShow class="" titulo="Tipo de Combustível"
                                        :valor="veiculo.tipo_combustivel" />
                                    <CampoShow class="" titulo="N Revisoes"
                                        :valor="veiculo.n_revisoes" />
                                </div>
                                <div class="-mx-3 mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    <CampoShow class="hidden md:block" titulo="" />
                                    <CampoShow titulo="Criado em" :valor="formatarData(veiculo.created_at)" />
                                    <CampoShow titulo="Atualizado em" :valor="formatarData(veiculo.updated_at)" />
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
