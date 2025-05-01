<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import HeaderModule from '@/components/data-table/HeaderModule.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import ButtonEdit from '@/components/form-components/buttons/ButtonEdit.vue';
import CampoShow from '@/components/form-components/CampoShow.vue';
import api from '@/plugins/api';
import type IPessoa from '@/types/IPessoa';
import TableVeiculos from './components/TableVeiculos.vue';
import ShowTemplate from '@/components/form-components/ShowTemplate.vue';
import { formatarCelular, formatarCPF, formatarLocalDateTime } from '@/helpers/formatters';


const route = useRoute();
const router = useRouter();

const pessoa = ref<IPessoa | null>(null);
const { id } = route.params;
const fetchPessoa = async () => {
    try {
        const { data } = await api.get(`/pessoas/${id}`);
        data.cpf = formatarCPF(data.cpf);
        data.celular = formatarCelular(data.celular);
        pessoa.value = data;
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
    <main class="min-h-[calc(100vh-56px)] pb-10">

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
                    <ShowTemplate title="Dados da Pessoa" v-if="pessoa">
                        <CampoShow class="" titulo="Nome" :valor="pessoa.nome" />
                        <CampoShow class="" titulo="Celular" :valor="pessoa.celular" />
                        <CampoShow class="" titulo="CPF" :valor="pessoa.cpf" />
                        <CampoShow class="" titulo="Sexo" :valor="pessoa.is_masculino ? 'Masculino' : 'Feminino'" />
                        <CampoShow class="" titulo="Idade" :valor="pessoa.idade + ' anos'" />
                        <template #dates>
                            <CampoShow class="" titulo="Criado em" :valor="formatarLocalDateTime(pessoa.created_at)" />
                            <CampoShow class="" titulo="Atualizado em"
                                :valor="formatarLocalDateTime(pessoa.updated_at)" />
                        </template>
                    </ShowTemplate>
                    <TableVeiculos title="Veículos da pessoa" :id="route.params.id" />
                </div>
            </template>
            <template #fallback>
                <div>Carregando...</div>
            </template>
        </Suspense>
    </main>
</template>
