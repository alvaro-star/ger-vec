<script lang="ts" setup>
import HeaderModule from '@/components/data-table/HeaderModule.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import DateInput from '@/components/form-components/DateInput.vue';
import FormTemplate from '@/components/form-components/form/FormTemplate.vue';
import NumberInput from '@/components/form-components/NumberInput.vue';
import SelectInput from '@/components/form-components/SelectInput.vue';
import TextInput from '@/components/form-components/TextInput.vue';
import TextSelectInput from '@/components/form-components/TextSelectInput.vue';
import { tipo_revisao } from '@/data/options_selects';
import { formatarDateToStringDate } from '@/helpers/formatters';
import { validEmptyFieldsForm } from '@/helpers/functions/validFormData';
import patterns from '@/helpers/regexp/patterns';
import { isDateInFuture } from '@/helpers/validatorsFunctions';
import api from '@/plugins/api';
import { useAlertStore } from '@/stores/alertState';
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const alertStore = useAlertStore();

const router = useRouter();
const route = useRoute();
const placas = ref<string[]>([])

const veiculoPlaca = route.query.placa;

interface RevisaoFormData {
    data: Date | undefined;
    quilometragem: string;
    tipo: string;
    descricao: string;
    observacoes: string;
    valor_total: string;
    garantia_meses: string;
    placa: string;
}

const form = reactive<RevisaoFormData>({
    data: undefined,
    quilometragem: '',
    tipo: '',
    descricao: '',
    observacoes: '',
    valor_total: '',
    garantia_meses: '',
    placa: '',
});

const errors = ref<Record<string, string>>({});
const loading = ref(false);


function voltar() {
    router.back();
}

function validateForm(): boolean {
    const requiredFields = ['tipo', 'placa'];

    let formErrors: Record<string, string> = validEmptyFieldsForm(form, requiredFields);

    if (!patterns.placa.valid(form.placa))
        formErrors.placa = 'Insira uma placa válida';

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

function formatForm(form: any) {
    const formCopia = { ...form };

    formCopia.valor_total = formCopia.valor_total === '' ? '0' : formCopia.valor_total;
    formCopia.quilometragem = formCopia.quilometragem === '' ? '0' : formCopia.quilometragem;
    formCopia.garantia_meses = formCopia.garantia_meses === '' ? '0' : formCopia.garantia_meses;
    formCopia.data = formatarDateToStringDate(formCopia.data);

    for (const field of ['quilometragem', 'valor_total', 'garantia_meses']) {
        let numericValue = formCopia[field].replace(/\./g, '').replace(',', '.');
        formCopia[field] = numericValue.toString();
    }

    return formCopia;
}

async function submitForm() {
    if (!validateForm()) return;
    loading.value = true;
    try {
        const formData = formatForm(form)
        await api.post('/revisoes', formData);
        alertStore.setMessage('A revisão foi cadastrada com sucesso!', null);
        window.history.go(-1);
    } catch (erro: any) {
        if (erro.status === 422 && erro.response?.data?.errors) {
            Object.assign(errors.value, erro.response.data.errors);
        } else {
            alertStore.setMessage('Não foi possível cadastrar a revisão', 'danger');
        }
    } finally {
        loading.value = false;
    }
}

const cancelarCadastro = () => {
    router.back();
}

const fetchPlacas = async () => {
    try {
        const response = await api.get('/veiculos/placas');
        placas.value = response.data || []
    } catch (error) {
        console.error('Erro ao buscar placas:', error);
    }
};

onMounted(() => {
    fetchPlacas()
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
            <TextSelectInput v-model="form.placa" :disabled="(!!veiculoPlaca)" :options="placas" label="Placa"
                class="px-3 flex-shrink-0" placeholder="Digite a placa do carro" required />

            <DateInput class="w-full px-3" label="Data" placeholder="Digite a data da revisão" :message="errors.data"
                v-model="form.data" required />

            <NumberInput class="px-3" label="Quilometragem" prefix="Km" v-model="form.quilometragem"
                :message="errors.quilometragem" placeholder="Digite a quilometragem" type="integer" required
                :max-value="999999" />

            <SelectInput class="px-3" label="Tipo de Revisão" v-model="form.tipo" :options="tipo_revisao"
                :message="errors.tipo" placeholder="Selecione o tipo" required :show-max-size="true" />

            <TextInput class="px-3" label="Descrição" v-model="form.descricao" :message="errors.descricao"
                :required="false" placeholder="Descrição detalhada da revisão" :show-max-size="true" :max-size="200" />

            <NumberInput class="px-3" label="Garantia (meses)" v-model="form.garantia_meses"
                :message="errors.garantia_meses" placeholder="Digite o número de meses de garantia" type="integer"
                :precision="2" required :max-value="48" />

            <NumberInput class="px-3" label="Valor Total (R$)" v-model="form.valor_total" :message="errors.valor_total"
                placeholder="Digite o valor total" type="float" prefix="R$" :precision="2" required
                :max-value="9999999.99" :max-size="30" />

            <TextInput class="px-3" label="Observações" v-model="form.observacoes" :required="false"
                :message="errors.observacoes" placeholder="Observações adicionais" :show-max-size="true"
                :max-size="200" />
        </FormTemplate>
    </main>
</template>
