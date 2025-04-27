<script lang="ts" setup>
import HeaderModule from '@/components/data-table/HeaderModule.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import FormTemplate from '@/components/form-components/form/FormTemplate.vue';
import extractNumbers from '@/helpers/functions/extractNumbers';
import patterns, { formatarCelular, formatarCPF, isInteger, validInterval } from '@/helpers/regexp/patterns';
import api from '@/plugins/api';
import { useAlertStore } from '@/stores/alertState';
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import GroupInputForm from './components/GroupInputForm.vue';
import type IFormData from './types/IFormData';
import { validEmptyFieldsForm } from '@/helpers/functions/validFormData';
import type IPessoa from '@/types/IPessoa';
import CloseModal from '@/components/CloseModal.vue';

const alertStore = useAlertStore()
const router = useRouter();
const route = useRoute();

const getIdByRoute = () => route.query.id || route.params.id;

const form = reactive<IFormData>({
    nome: '',
    celular: '',
    cpf: '',
    sexo: '',
    idade: ''
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
        form.idade = response.data.idade.toString();
    } catch (error) {
        console.error((error as Error).message);
    }
}

function voltar() {
    router.back();
}

function validateForm(): boolean {
    const requiredFields = ['nome', 'celular', 'cpf', 'sexo', 'idade'];
    let formErrors: Record<string, string> = validEmptyFieldsForm(form, requiredFields);

    for (const field of requiredFields) {
        if (field === 'idade') {
            if (!isInteger(form.idade))
                formErrors[field] = 'Deve ser um número inteiro';
            if (isInteger(form.idade) && !validInterval(form.idade, 18, 100))
                formErrors[field] = 'Deve ter no mínimo 18 anos';
        }
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
        console.log(erro);
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
    <main class="min-h-[calc(100vh-56px)] pb-10">
        <HeaderModule classs="mb-4">
            <template #title>
                <h1 class="text-3xl font-bold">{{ !getIdByRoute() ? 'Cadastrar' : 'Editar' }} Pessoa</h1>
            </template>
            <template #actions>
                <BackButton label="Voltar" @click="voltar" />
            </template>
        </HeaderModule>
        <Suspense>
            <template #default>
                <FormTemplate :open-delete-modal="openDeleteModal" :create="!getIdByRoute()" header="Dados do Cliente"
                    @submit.prevent="submitForm" class="container" :cancelarProcesso="cancelarProcesso">
                    <GroupInputForm v-model="form" :errors="errors" />
                </FormTemplate>
            </template>
            <template #fallback>
                <div>Carregando...</div>
            </template>
        </Suspense>
        <CloseModal v-if="!getIdByRoute()" ref="deleteModal" :confirm-delete="confirmDelete">
            Deseja realmente excluir esta cliente? Esta ação é irreversível.
        </CloseModal>
    </main>
</template>
