<script lang="ts" setup>
import CloseModal from '@/components/CloseModal.vue';
import HeaderModule from '@/components/data-table/HeaderModule.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import FormTemplate from '@/components/form-components/form/FormTemplate.vue';
import SelectInput from '@/components/form-components/SelectInput.vue';
import TextInput from '@/components/form-components/TextInput.vue';
import { cores, tipo_combustivel } from '@/data/options_selects';
import { validEmptyFieldsForm } from '@/helpers/functions/validFormData';
import patterns, { isInteger, validInterval } from '@/helpers/regexp/patterns';
import api from '@/plugins/api';
import { useAlertStore } from '@/stores/alertState';
import type IMarca from '@/types/IMarca';
import type IPageOutput from '@/types/IPageOutput';
import type IVeiculo from '@/types/IVeiculo';
import type { AxiosError } from 'axios';
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const alertStore = useAlertStore();

interface FormData {
    marca: string;
    modelo: string;
    placa: string;
    renavam: string;
    ano: string;
    cor: string;
    tipo_combustivel: string;
}

const router = useRouter();
const route = useRoute();

const getIdByRoute = () => route.query.id || route.params.id;

const form = reactive<FormData>({
    marca: '',
    modelo: '',
    placa: '',
    renavam: '',
    ano: '',
    cor: '',
    tipo_combustivel: ''
});

const errors = ref<Record<string, string>>({});
const loading = ref(false);
const marcas = ref<IMarca[]>([]);

const fetchVeiculo = async () => {
    try {
        const id = getIdByRoute();
        const response = await api.get<IVeiculo>(`/veiculos/${id}`);
        const data = response.data;

        form.marca = data.marca?.id.toString() || '';
        form.modelo = data.modelo;
        form.ano = data.ano.toString();
        form.cor = data.cor;
        form.placa = data.placa;
        form.renavam = data.renavam;
        form.tipo_combustivel = data.tipo_combustivel;
    } catch (error) {
        console.error((error as Error).message);
    }
};

function voltar() {
    router.back();
}

function validateForm(): boolean {
    const requiredFields = ['marca', 'modelo', 'placa', 'ano', 'cor', 'renavam', 'tipo_combustivel'];
    let formErrors: Record<string, string> = validEmptyFieldsForm(form, requiredFields);

    if (!patterns.placa.valid(form.placa))
        formErrors.placa = 'Insira uma placa válida';
    if (!patterns.integer.valid(form.renavam))
        formErrors.renavam = 'O renavam deve conter apenas números';
    else if (form.renavam.length != 11)
        formErrors.renavam = 'O renavam deve conter 11 digitos';

    if (!patterns.integer.valid(form.ano)) {
        formErrors.ano = 'O ano deve conter apenas números';
    } else if (!validInterval(form.ano, 1880, 2025)) {
        formErrors.ano = 'O ano deve ser entre 1880 e 2025';
    }
    errors.value = formErrors;
    return Object.keys(formErrors).length === 0;
}

const fetchMarcas = async () => {
    try {
        const response = await api.get<IPageOutput<IMarca>>('/marcas', { params: { page: 0, size: 100 } });
        marcas.value = response.data.content;
    } catch (error) {
        alertStore.setMessage('Não foi possível carregar as marcas', 'danger');
    }
}

async function submitForm() {
    if (!validateForm()) return;
    loading.value = true;

    try {
        const id = getIdByRoute();
        if (!id) return;

        await api.put(`/veiculos/${id}`, form);
        alertStore.setMessage('Veículo editado com sucesso', null);
        router.back();
    } catch (erro: any) {
        if (erro.status === 422 && erro.response?.data?.errors) {
            Object.assign(errors.value, erro.response.data.errors);
        }
        alertStore.setMessage('Erro ao salvar alterações.', 'danger');
    } finally {
        loading.value = false;
    }
}

function cancelarEdicao() {
    router.back();
}

const deleteModal = ref<InstanceType<typeof CloseModal> | null>(null);
const openDeleteModal = () => {
    deleteModal.value?.openModal();
};

const confirmDelete = async () => {
    try {
        const id = getIdByRoute();
        if (id) {
            await api.delete(`/veiculos/${id}`);
            alertStore.setMessage('Veículo deletado com sucesso', null);
            window.history.go(-2);
        }
    } catch (exception: AxiosError | any) {
        if (exception.response?.status === 400 && exception.response?.data?.message) {
            const message = exception.response?.data?.message;
            alertStore.setMessage(message, 'danger');
        } else {
            alertStore.setMessage('Não foi possível excluir o veículo.', 'danger');
        }
    }
};

onMounted(() => {
    fetchVeiculo()
    fetchMarcas()
});
</script>

<template>
    <HeaderModule class="mb-7">
        <template #title>
            <h1 class="text-3xl font-bold">Editar Veículo</h1>
        </template>
        <template #actions>
            <BackButton label="Voltar" @click="voltar" />
        </template>
    </HeaderModule>
    <main class="min-h-[calc(100vh-56px)] pb-10">

        <FormTemplate class="container" header="Dados do Veículo" :create="false" @submit.prevent="submitForm"
            :cancelar-processo="cancelarEdicao" :open-delete-modal="openDeleteModal">
            <SelectInput class="px-3" label="Marca" v-model="form.marca" :message="errors.marca" required>
                <option v-for="marca in marcas" :value="marca.id" :key="marca.id">
                    {{ marca.nome }}
                </option>
            </SelectInput>

            <TextInput class="px-3" label="Modelo" v-model="form.modelo" :message="errors.modelo"
                placeholder="Digite o modelo do veículo" show-max-size :max-size="40" uppercase required />

            <TextInput class="px-3" label="Placa" type="placa" v-model="form.placa" :message="errors.placa"
                placeholder="Digite a placa" show-max-size :max-size="7" uppercase required />

            <TextInput class="px-3" type="integer" label="Renavam" v-model="form.renavam" :message="errors.renavam"
                placeholder="Digite o renavam" show-max-size :max-size="11" required />

            <TextInput type="integer" class="px-3" label="Ano de Fabricação" v-model="form.ano" :message="errors.ano"
                :max-value="2025" placeholder="Digite o ano" show-max-size :max-size="4" required />

            <SelectInput class="px-3" label="Cor" v-model="form.cor" :options="cores" :message="errors.cor"
                placeholder="Selecione a cor" required />

            <SelectInput class="px-3" label="Tipo de Combustível" v-model="form.tipo_combustivel"
                :options="tipo_combustivel" :message="errors.tipo_combustivel"
                placeholder="Selecione o tipo de combustível" required />
        </FormTemplate>

        <CloseModal ref="deleteModal" :confirm-delete="confirmDelete">
            Deseja realmente excluir este veículo? Esta ação é irreversível.
        </CloseModal>
    </main>
</template>
