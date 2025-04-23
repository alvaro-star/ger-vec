<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

import HeaderModule from '@/components/data-table/HeaderModule.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import ButtonEdit from '@/components/form-components/buttons/ButtonEdit.vue';
import CampoShow from '@/components/form-components/CampoShow.vue';
import formatarData from '@/helpers/formatarData';
import api from '@/plugins/api';
import type IPessoa from '@/types/IPessoa';
import TableVeiculos from './components/TableVeiculos.vue';


const route = useRoute();
const router = useRouter();

const pessoa = ref<IPessoa | null>(null);
const { id } = route.params;
const fetchPessoa = async () => {
    try {
        const response = await api.get(`/pessoas/${id}`);
        pessoa.value = response.data;
    } catch (error) {
        console.error((error as Error).message);
    }
};

const voltar = () => {
    router.back();
};

const editarPessoa = () => {
    const id = route.params.id;
    router.push({ name: 'pessoas.edit', params: { id: id } });
};

onMounted(fetchPessoa);
</script>
<template class="mt-4">
    <main class="h-[calc(100vh-56px)]">

        <HeaderModule class="mb-7">
            <template #title>
                <h1 class="text-3xl font-bold">Detalhes do Cliente</h1>
            </template>

            <template #actions>
                <ButtonEdit label="Editar" @click="editarPessoa" />
                <BackButton label="Voltar" @click="voltar" />
            </template>
        </HeaderModule>

        <Suspense>
            <template #default>
                <div class="container">
                    <section v-if="pessoa" class="pt-3 pb-1.5 pr-3 pl-3">
                        <div class="mx-auto max-w-screen">
                            <div class="p-5 bg-white relative border border-gray-300 sm:rounded overflow-hidden">
                                <h2 class="text-xl font-semibold mb-4">Dados do Cliente</h2>
                                <div class="border-b border-colorline"></div>
                                <div class="-mx-3 mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
                                    <CampoShow class="" titulo="Nome" :valor="pessoa.nome" />
                                    <CampoShow class="" titulo="Telefone" :valor="pessoa.telefone" />
                                    <CampoShow class="" titulo="CPF" :valor="pessoa.cpf" />
                                    <CampoShow class="" titulo="Sexo"
                                        :valor="pessoa.is_masculino ? 'Masculino' : 'Feminino'" />
                                    <CampoShow class="" titulo="Idade" :valor="pessoa.idade" />

                                </div>
                                <div class="-mx-3 mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
                                    <CampoShow class="hidden md:block" titulo="" />
                                    <CampoShow class="" titulo="Criado em" :valor="formatarData(pessoa.created_at)" />
                                    <CampoShow class="" titulo="Atualizado em"
                                        :valor="formatarData(pessoa.updated_at)" />
                                </div>
                            </div>
                        </div>
                    </section>

                    <TableVeiculos title="Veiculos da pessoa" :id="route.params.id" />
                </div>
            </template>
            <template #fallback>
                <div>Carregando...</div>
            </template>
        </Suspense>
    </main>
</template>
