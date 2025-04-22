<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

import HeaderModule from '@/components/data-table/HeaderModule.vue';

import CampoShow from '@/components/form-components/CampoShow.vue';
import formatarData, { formatarClassicData } from '@/helpers/formatarData';
import api from '@/plugins/api';
import type IRevisao from '@/types/IRevisao';
import ButtonEdit from '@/components/form-components/buttons/ButtonEdit.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';

const route = useRoute();
const router = useRouter();

const revisao = ref<IRevisao | null>(null);
const { id } = route.params;

const fetchRevisao = async () => {
    try {
        const response = await api.get(`/revisoes/${id}`);
        revisao.value = response.data;
    } catch (error) {
        console.error((error as Error).message);
    }
};

const voltar = () => {
    router.back();
};

const editarRevisao = () => {
    const id = route.params.id;
    router.push({ name: 'revisoes.edit', params: { id: id } });
};

onMounted(fetchRevisao);
</script>

<template class="mt-4">
    <main class="h-[calc(100vh-56px)]">
        <HeaderModule>
            <template #title>
                <h1 class="text-3xl font-bold">Detalhes da Revisão</h1>
            </template>

            <template #actions>
                <ButtonEdit label="Editar" @click="editarRevisao" />
                <BackButton label="Voltar" @click="voltar" />
            </template>
        </HeaderModule>

        <Suspense>
            <template #default>
                <div class="container">
                    <section v-if="revisao" class="pt-3 pb-1.5 pr-3 pl-3">
                        <div class="mx-auto max-w-screen">
                            <div class="p-5 bg-white relative border border-gray-300 sm:rounded overflow-hidden">
                                <h2 class="text-xl font-semibold mb-4">Dados da Revisão</h2>
                                <div class="border-b border-colorline"></div>
                                <div class="flex flex-wrap -mx-3 mt-4">
                                    <CampoShow class="md:w-1/3" titulo="Data"
                                        :valor="formatarClassicData(revisao.data)" />
                                    <CampoShow class="md:w-1/3" titulo="Quilometragem" :valor="revisao.quilometragem" />
                                    <CampoShow class="md:w-1/3" titulo="Descrição" :valor="revisao.descricao" />
                                    <CampoShow class="md:w-1/3" titulo="Valor" :valor="revisao.valor_total" />
                                    <CampoShow class="md:w-1/3" titulo="Placa do Veiculo">
                                        <router-link
                                            :to="{ name: 'veiculos.show', params: { id: revisao.veiculo?.id } }"
                                            class="text-blue-500 hover:underline">
                                            {{ revisao.veiculo?.placa }}
                                        </router-link>
                                    </CampoShow>
                                </div>
                                <div class="flex flex-wrap -mx-3 mt-4">
                                    <CampoShow class="md:w-1/3" titulo="Criado em"
                                        :valor="formatarData(revisao.created_at)" />
                                    <CampoShow class="md:w-1/3" titulo="Atualizado em"
                                        :valor="formatarData(revisao.updated_at)" />
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </template>
            <template #fallback>
                <div>Carregando...</div>
            </template>
        </Suspense>
    </main>
</template>
