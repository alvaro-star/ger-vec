<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import HeaderModule from '@/components/data-table/HeaderModule.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import ButtonEdit from '@/components/form-components/buttons/ButtonEdit.vue';
import CampoShow from '@/components/form-components/CampoShow.vue';
import ShowTemplate from '@/components/form-components/ShowTemplate.vue';
import api from '@/plugins/api';
import type IRevisao from '@/types/IRevisao';
import { formatarFloat, formatarInteger, formatarLocalDate, formatarLocalDateTime } from '@/helpers/formatters';

const route = useRoute();
const router = useRouter();

const revisao = ref<IRevisao | null>(null);
const { id } = route.params;

const fetchRevisao = async () => {
    try {
        const response = await api.get(`/revisoes/${id}`);
        response.data.quilometragem = formatarInteger(response.data.quilometragem.replace(".", ","))
        response.data.quilometragem = formatarFloat(response.data.quilometragem.replace(".", ","), 2)
        response.data.valor_total = formatarFloat(response.data.valor_total.replace(".", ","), 2)
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
    <HeaderModule class="mb-7">
        <template #title>
            <h1 class="text-3xl font-bold">Detalhes da Revisão</h1>
        </template>

        <template #actions>
            <ButtonEdit label="Editar" @click="editarRevisao" />
            <BackButton label="Voltar" @click="voltar" />
        </template>
    </HeaderModule>
    <main class="min-h-[calc(100vh-56px)] pb-10">
        <ShowTemplate v-if="revisao" class="mt-4 container" title="Dados da Revisão">
            <CampoShow titulo="Placa do Veiculo">
                <router-link :to="{ name: 'veiculos.show', params: { id: revisao.veiculo?.id } }"
                    class="text-blue-500 hover:underline">
                    {{ revisao.veiculo?.placa }}
                </router-link>
            </CampoShow>
            <CampoShow titulo="Tipo de Revisão" :valor="revisao.tipo" />
            <CampoShow titulo="Descrição" :valor="revisao.descricao" />
            <CampoShow titulo="Quilometragem" :valor="revisao.quilometragem + ' km'" />
            <CampoShow titulo="Garantia" :valor="revisao.garantia_meses + ' meses'" />
            <CampoShow titulo="Valor">
                R$ {{ revisao.valor_total }}
            </CampoShow>
            <CampoShow titulo="Data" :valor="formatarLocalDate(revisao.data)" />
            <CampoShow titulo="Observacoes" :valor="revisao.observacoes" />
            <template #dates>
                <CampoShow titulo="Criado em" :valor="formatarLocalDateTime(revisao.created_at)" />
                <CampoShow titulo="Atualizado em" :valor="formatarLocalDateTime(revisao.updated_at)" />
            </template>
        </ShowTemplate>
    </main>
</template>
