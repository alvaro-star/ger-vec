<script lang="ts" setup>
import CloseModal from '@/components/CloseModal.vue';
import HeaderModule from '@/components/data-table/HeaderModule.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import DateInput from '@/components/form-components/DateInput.vue';
import FormTemplate from '@/components/form-components/form/FormTemplate.vue';
import NumberInput from '@/components/form-components/NumberInput.vue';
import SelectInput from '@/components/form-components/SelectInput.vue';
import TextInput from '@/components/form-components/TextInput.vue';
import { tipo_revisao } from '@/data/options_selects';
import { createDateByString, formatarDateToStringDate, formatarFloat, formatarInteger } from '@/helpers/formatters';
import { validEmptyFieldsForm } from '@/helpers/functions/validFormData';
import patterns from '@/helpers/regexp/patterns';
import { isDateInFuture } from '@/helpers/validatorsFunctions';
import api from '@/plugins/api';
import { useAlertStore } from '@/stores/alertState';
import type IRevisao from '@/types/IRevisao';
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const alertStore = useAlertStore();
const router = useRouter();
const route = useRoute();
const placaVeiculo = ref<string | null>(null)

const getIdByRoute = () => route.query.id || route.params.id;

interface FormData {
    data: Date | undefined;
    quilometragem: string;
    tipo: string;
    descricao: string;
    observacoes: string;
    valor_total: string;
    garantia_meses: string;
}

const form = reactive<FormData>({
    data: undefined,
    quilometragem: '',
    tipo: '',
    descricao: '',
    observacoes: '',
    valor_total: '',
    garantia_meses: ''
});

const errors = ref<Record<string, string>>({});
const loading = ref(false);

function voltar() {
    router.back();
}
const fetchRevisao = async () => {
    try {
        const id = getIdByRoute();
        const { data } = await api.get<IRevisao>(`/revisoes/${id}`);
        Object.assign(form, data);
        form.garantia_meses = formatarInteger(form.garantia_meses.replace(".", ","))
        form.quilometragem = formatarFloat(form.quilometragem.replace(".", ","), 2)
        form.valor_total = formatarFloat(form.valor_total.replace(".", ","), 2)
        form.observacoes = data.observacoes || '';
        form.descricao = data.descricao || '';
        form.data = createDateByString(data.data)
        if (data.veiculo)
            placaVeiculo.value = data.veiculo.placa
    } catch (error) {
        console.error((error as Error).message);
    }
};

function validateForm(): boolean {
    const requiredFields = ['quilometragem', 'tipo', 'valor_total', 'garantia_meses'];

    const formErrors: Record<string, string> = validEmptyFieldsForm(form, requiredFields);

    for (const field of ["quilometragem", 'valor_total', 'garantia_meses']) {
        const value = (form as any)[field]
        if (!patterns.float.valid(value))
            formErrors[field] = 'Insira um valor válido';

    }


    if (!form.data)
        formErrors['data'] = 'O valor não deve estar em branco';
    else if (isDateInFuture(form.data))
        formErrors['data'] = 'A data ainda não ocorreu';

    errors.value = formErrors;

    return Object.keys(formErrors).length === 0;
}

async function submitForm() {
    if (!validateForm()) return;
    loading.value = true;

    try {
        const id = getIdByRoute();

        if (form.valor_total == '') form.valor_total = '0'
        if (form.quilometragem == '') form.quilometragem = '0'
        if (form.garantia_meses == '') form.garantia_meses = '0'
        for (const field of ["quilometragem", 'valor_total', 'garantia_meses']) {
            let numericValue = (form as any)[field].replace(/\./g, "").replace(",", ".");
            (form as any)[field] = numericValue.toString();
        }
        if (!id) return;
        await api.put(`/revisoes/${id}`, form);
        alertStore.setMessage('Revisão editada com sucesso', null);
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

const cancelarEdicao = () => router.back();

const deleteModal = ref<InstanceType<typeof CloseModal> | null>(null);
const openDeleteModal = () => deleteModal.value?.openModal();

const confirmDelete = async () => {
    try {
        const id = getIdByRoute();
        if (id) {
            await api.delete(`/revisoes/${id}`);
            alertStore.setMessage('Revisão deletada com sucesso', null);
            window.history.go(-2);
        }
    } catch (erro: any) {
        if (erro.status === 422 && erro.response?.data?.errors) {
            Object.assign(errors.value, erro.response.data.errors);
        } else {
            alertStore.setMessage('Não foi possível cadastrar a revisão', 'danger');
        }
    }
};

onMounted(fetchRevisao);
</script>

<template>
    <HeaderModule class="mb-7">
        <template #title>
            <h1 class="text-3xl font-bold">Editar Revisão</h1>
        </template>
        <template #actions>
            <BackButton label="Voltar" @click="voltar" />
        </template>
    </HeaderModule>
    <main class="min-h-[calc(100vh-56px)] pb-10">
        <FormTemplate class="container" :create="false" :open-delete-modal="openDeleteModal" header="Dados da Revisão"
            @submit.prevent="submitForm" :cancelar-processo="cancelarEdicao">
            <TextInput type="placa" v-if="placaVeiculo" disabled class="px-3" label="Placa" v-model="placaVeiculo"
                :message="errors.placa" uppercase placeholder="Placa do veículo" required :show-max-size="true"
                :max-size="7" />
            <DateInput class="w-full px-3" label="Data" placeholder="Digite a data da revisão" :message="errors.data"
                v-model="form.data" required />

            <NumberInput class="px-3" label="Quilometragem" prefix="Km" v-model="form.quilometragem"
                :message="errors.quilometragem" placeholder="Digite a quilometragem" type="integer" :required="true"
                :max-value="999999" />

            <SelectInput class="px-3" label="Tipo de Revisão" v-model="form.tipo" :options="tipo_revisao"
                :message="errors.tipo" placeholder="Selecione o tipo" :required="true" :show-max-size="true" />

            <TextInput class="px-3" label="Descrição" v-model="form.descricao" :message="errors.descricao"
                :required="false" placeholder="Descrição detalhada da revisão" :show-max-size="true" :max-size="200" />

            <NumberInput class="px-3" label="Garantia (meses)" v-model="form.garantia_meses"
                :message="errors.garantia_meses" placeholder="Digite o número de meses de garantia" type="integer"
                :precision="2" :required="true" :max-value="48" />

            <NumberInput class="px-3" label="Valor Total (R$)" v-model="form.valor_total" :message="errors.valor_total"
                placeholder="Digite o valor total" type="float" prefix="R$" :precision="2" :required="true"
                :max-value="9999999.99" :max-size="30" />

            <TextInput class="px-3" label="Observações" v-model="form.observacoes" :required="false"
                :message="errors.observacoes" placeholder="Observações adicionais" :show-max-size="true"
                :max-size="200" />
        </FormTemplate>
        <CloseModal ref="deleteModal" :confirm-delete="confirmDelete">
            Deseja realmente excluir esta revisão? Esta ação é irreversível.
        </CloseModal>
    </main>
</template>
