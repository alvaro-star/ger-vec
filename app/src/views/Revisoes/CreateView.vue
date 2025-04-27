<script lang="ts" setup>
import HeaderModule from '@/components/data-table/HeaderModule.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import FormTemplate from '@/components/form-components/form/FormTemplate.vue';
import SelectInput from '@/components/form-components/SelectInput.vue';
import TextInput from '@/components/form-components/TextInput.vue';
import { tipo_revisao } from '@/data/options_selects';
import { validEmptyFieldsForm } from '@/helpers/functions/validFormData';
import patterns, { isDateInFuture } from '@/helpers/regexp/patterns';
import api from '@/plugins/api';
import { useAlertStore } from '@/stores/alertState';
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const alertStore = useAlertStore();

const router = useRouter();
const route = useRoute();
const getIdByRoute = () => route.query.id || route.params.id;

const veiculoPlaca = route.query.placa;

interface RevisaoFormData {
    data: string;
    quilometragem: string;
    tipo: string;
    descricao: string;
    observacoes: string;
    valor_total: string;
    garantia_meses: string;
    placa: string;
}

const form = reactive<RevisaoFormData>({
    data: '',
    quilometragem: '0',
    tipo: '',
    descricao: '',
    observacoes: '',
    valor_total: '0',
    garantia_meses: '0',
    placa: '',
});

const errors = ref<Record<string, string>>({});
const loading = ref(false);

function voltar() {
    router.back();
}

function validateForm(): boolean {
    const requiredFields = ['data', 'quilometragem', 'tipo', 'valor_total', 'garantia_meses', 'placa'];

    let formErrors: Record<string, string> = validEmptyFieldsForm(form, requiredFields);

    if (!patterns.placa.valid(form.placa))
        formErrors.placa = 'Insira uma placa válida';

    for (const field of ["quilometragem", 'valor_total', 'garantia_meses']) {
        const value = (form as any)[field]
        if (!patterns.float.valid(value)) {
            formErrors[field] = 'Insira um valor válido';
        }
    }
    if (isDateInFuture(form.data))
        formErrors['data'] = 'A data ainda não ocorreu';

    errors.value = formErrors;
    return Object.keys(formErrors).length === 0;
}

async function submitForm() {
    if (!validateForm()) return;
    loading.value = true;
    try {
        await api.post('/revisoes', form);
        alertStore.setMessage('A revisão foi cadastrada com sucesso!', null);
        window.history.go(-1);
    } catch (erro: any) {
        if (erro.status === 422 && erro.response?.data?.errors) {
            Object.assign(errors, erro.response.data.errors);
        }
        alertStore.setMessage('Não foi possível cadastrar a revisão', 'danger');
    } finally {
        loading.value = false;
    }
}

const cancelarCadastro = () => {
    router.back();
}

onMounted(() => {
    if (veiculoPlaca) form.placa = veiculoPlaca as string;
})
</script>

<template>
    <HeaderModule class="mb-7">
        <template #title>
            <h1 class="text-3xl font-bold">Cadastrar Revisão</h1>
        </template>
        <template #actions>
            <BackButton label="Voltar" @click="voltar" />
        </template>
    </HeaderModule>
    <main class="min-h-[calc(100vh-56px)] pb-10">
        <FormTemplate class="container" :create="true" header="Dados da Revisão" @submit.prevent="submitForm"
            :cancelar-processo="cancelarCadastro">
            <TextInput type="placa" v-if="!veiculoPlaca" class="px-3" label="Placa" v-model="form.placa"
                :message="errors.placa" uppercase placeholder="Digite a placa do veículo" :required="true"
                :show-max-size="true" :max-size="7" />

            <TextInput class="px-3" label="Data" v-model="form.data" :message="errors.data"
                placeholder="Digite a data da revisão" type="date" :required="true" />

            <TextInput class="px-3" label="Quilometragem" prefix="Km" v-model="form.quilometragem"
                :message="errors.quilometragem" placeholder="Digite a quilometragem" type="float" :precision="2"
                :required="true" :max-value="999999.99" />

            <SelectInput class="px-3" label="Tipo de Revisão" v-model="form.tipo" :options="tipo_revisao"
                :message="errors.tipo" placeholder="Selecione o tipo" :required="true" :show-max-size="true" />

            <TextInput class="px-3" label="Descrição" v-model="form.descricao" :message="errors.descricao"
                :required="false" placeholder="Descrição detalhada da revisão" :show-max-size="true" :max-size="200" />

            <TextInput class="px-3" label="Garantia (meses)" v-model="form.garantia_meses"
                :message="errors.garantia_meses" placeholder="Digite o número de meses de garantia" type="float"
                :precision="2" :required="true" :max-value="48" />

            <TextInput class="px-3" label="Valor Total (R$)" v-model="form.valor_total" :message="errors.valor_total"
                placeholder="Digite o valor total" type="float" prefix="R$" :precision="2" :required="true"
                :max-value="9999999.99" :max-size="30" />

            <TextInput class="px-3" label="Observações" v-model="form.observacoes" :required="false"
                :message="errors.observacoes" placeholder="Observações adicionais" :show-max-size="true"
                :max-size="200" />
        </FormTemplate>
    </main>
</template>
