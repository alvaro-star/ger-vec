<script lang="ts" setup>
import HeaderModule from '@/components/data-table/HeaderModule.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import FormTemplate from '@/components/form-components/form/FormTemplate.vue';
import NumberInput from '@/components/form-components/NumberInput.vue';
import SelectInput from '@/components/form-components/SelectInput.vue';
import TextInput from '@/components/form-components/TextInput.vue';
import { cores, tipo_combustivel } from '@/data/options_selects';
import { validEmptyFieldsForm } from '@/helpers/functions/validFormData';
import patterns from '@/helpers/regexp/patterns';
import { isInteger, validInterval } from '@/helpers/validatorsFunctions';
import api from '@/plugins/api';
import { useAlertStore } from '@/stores/alertState';
import type IMarca from '@/types/IMarca';
import type IPageOutput from '@/types/IPageOutput';
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const alertStore = useAlertStore();
const router = useRouter();
const route = useRoute();

const pessoaId = route.params.id;
interface VeiculoFormData {
    marca: string;
    modelo: string;
    placa: string;
    renavam: string;
    ano: string;
    cor: string;
    tipo_combustivel: string;
}

const form = reactive<VeiculoFormData>({
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

function voltar() {
    router.back();
}

function validateForm(): boolean {
    const requiredFields = ['marca', 'modelo', 'placa', 'ano', 'cor', 'renavam', 'tipo_combustivel'];
    let formErrors: Record<string, string> = validEmptyFieldsForm(form, requiredFields);

    if (!patterns.placa.valid(form.placa))
        formErrors.placa = 'Insira uma placa válida';
    if (!isInteger(form.renavam))
        formErrors.renavam = 'O renavam deve conter apenas números';
    else if (form.renavam.length != 11)
        formErrors.renavam = 'O renavam deve conter 11 digitos';
    else if (!patterns.renavam.valid(form.renavam))
        formErrors.renavam = 'O renavam não é válido.';

    if (!isInteger(form.ano)) {
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
        const formData = {
            ...form,
            pessoa_id: pessoaId,
            marca_id: form.marca,
        }

        await api.post('/veiculos', formData);

        alertStore.setMessage('O veículo foi cadastrado com sucesso!', null);
        router.push({
            name: 'pessoas.show',
            params: { id: pessoaId },
            replace: true
        });
    } catch (erro: any) {
        if (erro.status === 422 && erro.response?.data?.errors) {

            Object.assign(errors.value, erro.response.data.errors);
        } else {
            alertStore.setMessage('Nao foi possivel cadastrar o veiculo', 'danger');
        }

    } finally {
        loading.value = false;
    }
}

function cancelarCadastro() {
    router.back();
}

onMounted(() => {
    fetchMarcas();
})

</script>

<template>
    <HeaderModule class="mb-7">
        <template #title>
            <h1 class="text-3xl font-bold">Cadastrar Veículo</h1>
        </template>
        <template #actions>
            <BackButton label="Voltar" @click="voltar" />
        </template>
    </HeaderModule>
    <main class="min-h-[calc(100vh-56px)] pb-10">

        <FormTemplate class="container" :create="true" header="Dados do Veículo" @submit.prevent="submitForm"
            :cancelar-processo="cancelarCadastro">
            <SelectInput class="px-3" label="Marca" v-model="form.marca" :message="errors.marca" required>
                <option v-for="marca in marcas" :value="marca.id" :key="marca.id">{{ marca.nome }}</option>
            </SelectInput>

            <TextInput class="px-3" label="Modelo" v-model="form.modelo" :message="errors.modelo"
                placeholder="Digite o modelo do veículo" show-max-size :max-size="40" uppercase required />

            <TextInput class="px-3" label="Placa" type="placa" v-model="form.placa" :message="errors.placa"
                placeholder="Digite a placa" show-max-size :max-size="7" uppercase required />

            <NumberInput class="px-3" type="integer" label="Renavam" v-model="form.renavam" :message="errors.renavam"
                placeholder="Digite o renavam" show-max-size :max-size="11" required not_format />

            <NumberInput type="integer" not_format class="px-3" label="Ano de Fabricação" v-model="form.ano"
                :message="errors.ano" :max-value="2025" placeholder="Digite o ano" required />

            <SelectInput class="px-3" label="Cor" v-model="form.cor" :options="cores" :message="errors.cor"
                placeholder="Selecione a cor" required />

            <SelectInput class="px-3" label="Tipo de Combustível" v-model="form.tipo_combustivel"
                :options="tipo_combustivel" :message="errors.tipo_combustivel"
                placeholder="Selecione o tipo de combustível" required />

        </FormTemplate>

    </main>
</template>