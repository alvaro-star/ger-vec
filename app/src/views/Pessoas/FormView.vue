<script lang="ts" setup>
import CloseModal from '@/components/CloseModal.vue';
import HeaderModule from '@/components/data-table/HeaderModule.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import FormTemplate from '@/components/form-components/form/FormTemplate.vue';
import SelectInput from '@/components/form-components/SelectInput.vue';
import TextInput from '@/components/form-components/TextInput.vue';
import { formatarCelular, formatarCPF } from '@/helpers/formatters';
import extractNumbers from '@/helpers/functions/extractNumbers';
import { validEmptyFieldsForm } from '@/helpers/functions/validFormData';
import patterns from '@/helpers/regexp/patterns';
import { calculateIdade, isDate } from '@/helpers/validatorsFunctions';
import api from '@/plugins/api';
import { useAlertStore } from '@/stores/alertState';
import type IPessoa from '@/types/IPessoa';
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import type IFormData from './types/IFormData';

const alertStore = useAlertStore()
const router = useRouter();
const route = useRoute();

const getIdByRoute = () => route.query.id || route.params.id;

const form = reactive<IFormData>({
    nome: '',
    celular: '',
    cpf: '',
    sexo: '',
    nascimento: ''
});

const errors = ref<Record<string, string>>({});
const loading = ref(false);

const fetchPessoa = async () => {
    const id = getIdByRoute();
    if (!id) return;
    try {
        const response = await api.get<IPessoa>(`/pessoas/${id}`);
        form.nome = response.data.nome;
        form.celular = formatarCelular(response.data.celular);
        form.cpf = formatarCPF(response.data.cpf);
        form.sexo = response.data.is_masculino ? 'Masculino' : 'Feminino';
        form.nascimento = response.data.nascimento.toString();
    } catch (error) {
        console.error((error as Error).message);
    }
}

function voltar() {
    router.back();
}

function validateForm(): boolean {
    const requiredFields = ['nome', 'celular', 'cpf', 'sexo', 'nascimento'];
    let formErrors: Record<string, string> = validEmptyFieldsForm(form, requiredFields);


    if (!isDate(form.nascimento))
        formErrors['nascimento'] = 'Insira uma data válido';
    else {
        const idade = calculateIdade(form.nascimento)
        console.log("A idade do garoto", idade);

        if (idade < 18)
            formErrors['nascimento'] = 'Deve ter no mínimo 18 anos';
        if (idade > 100)
            formErrors['nascimento'] = 'Deve ter no maximo 100 anos';
    }




    if (!patterns.cpf.valid(form.cpf))
        formErrors.cpf = 'Insira um CPF válido';
    if (!patterns.phone.valid(form.celular))
        formErrors.celular = 'Insira um número de celular válido';
    errors.value = formErrors;
    return Object.keys(formErrors).length === 0;
}

const createPessoa = async (formData: any) => {
    try {
        const response = await api.post('/pessoas', formData);
        const pessoaId = response.data.id;

        alertStore.setMessage('O cliente foi cadastrado com sucesso!', null);
        router.push({
            name: 'pessoas.show', params: { id: pessoaId }, replace: true
        });

    } catch (erro: any) {
        throw erro;
    }
}

const updatePessoa = async (id: string, formData: any) => {
    try {
        await api.put(`/pessoas/${id}`, formData);
        alertStore.setMessage('Pessoa editada com sucesso', null)
        router.back();
    } catch (erro: any) {
        throw erro;
    }
}

const submitForm = async () => {
    if (!validateForm()) return;
    loading.value = true;
    try {
        const id = getIdByRoute() as string;
        const formData = {
            ...form,
            cpf: extractNumbers(form.cpf),
            celular: extractNumbers(form.celular),
            sexo: form.sexo === 'Masculino' ? 'M' : 'F'
        }
        if (id) {
            await updatePessoa(id, formData);
        } else {
            await createPessoa(formData);
        }
    } catch (erro: any) {
        if (erro.status === 422 && erro.response?.data?.errors) {
            Object.assign(errors.value, erro.response.data.errors);
        }
    } finally {
        loading.value = false;
    }
}

const cancelarProcesso = () => router.back();

const deleteModal = ref<InstanceType<typeof CloseModal> | null>(null);
const openDeleteModal = () => deleteModal.value?.openModal()

const confirmDelete = async () => {
    try {
        const id = getIdByRoute();
        if (!id) return

        await api.delete(`/pessoas/${id}`);
        alertStore.setMessage('Pessoa deletada com sucesso', null)
        router.push({
            name: 'pessoas.index',
            replace: true
        });

    } catch (exception: any) {
        if (exception.response?.status === 400 && exception.response?.data?.message) {
            const message = exception.response?.data?.message
            alertStore.setMessage(message, 'danger');
        } else {
            alertStore.setMessage('Não foi possível excluir o veículo.', 'danger');
        }
    };
}


onMounted(() => {
    if (!getIdByRoute()) return;
    fetchPessoa();
})

</script>


<template class="mt-4">
    <HeaderModule classs="mb-4">
        <template #title>
            <h1 class="text-3xl font-bold">{{ !getIdByRoute() ? 'Cadastrar' : 'Editar' }} Pessoa</h1>
        </template>
        <template #actions>
            <BackButton label="Voltar" @click="voltar" />
        </template>
    </HeaderModule>
    <main class="min-h-[calc(100vh-56px)] pb-10">
        <Suspense>
            <template #default>
                <FormTemplate :open-delete-modal="openDeleteModal" :create="!getIdByRoute()" header="Dados do Cliente"
                    @submit.prevent="submitForm" class="container" :cancelarProcesso="cancelarProcesso">
                    <TextInput type="letter_only" class="w-full px-3" label="Nome" first-uppercase v-model="form.nome"
                        :max-size="50" required show-max-size :message="errors.nome"
                        placeholder="Digite o nome do cliente" />

                    <TextInput class="w-full px-3" type="phone" label="Celular" v-model="form.celular"
                        :message="errors.celular" placeholder="Digite o celular" :max-size="15" show-max-size
                        required />

                    <TextInput class="w-full px-3" label="CPF" type="cpf" v-model="form.cpf" :message="errors.cpf"
                        placeholder="Digite o CPF" required :max-size="14" show-max-size />

                    <SelectInput class="w-full px-3" label="Sexo" v-model="form.sexo"
                        :options="['Masculino', 'Feminino']" :message="errors.sexo" placeholder="Selecione o sexo" />

                    <TextInput required class="w-full px-3" label="Data Nascimento" v-model="form.nascimento"
                        :message="errors.nascimento" not_format placeholder="Digite o seu ano de nascimento"
                        type="date" />
                </FormTemplate>
            </template>
            <template #fallback>
                <div>Carregando...</div>
            </template>
        </Suspense>
        <CloseModal ref="deleteModal" :confirm-delete="confirmDelete">
            Deseja realmente excluir esta cliente? Esta ação é irreversível.
        </CloseModal>
    </main>
</template>
