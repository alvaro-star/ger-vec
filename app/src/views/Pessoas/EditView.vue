<script lang="ts" setup>
import CloseModal from '@/components/CloseModal.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import ButtonCadastrar from '@/components/form-components/buttons/ButtonCadastrar.vue';
import ButtonCancel from '@/components/form-components/buttons/ButtonCancel.vue';
import SelectInput from '@/components/form-components/SelectInput.vue';
import TextInput from '@/components/form-components/TextInput.vue';
import TrashIcon from '@/components/icons/TrashIcon.vue';
import api from '@/plugins/api';
import type IPessoa from '@/types/IPessoa';
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

interface FormData {
    nome: string;
    telefone: string;
    cpf: string;
    sexo: string;
    idade: number | string;
}

const router = useRouter();
const route = useRoute();

const getIdByRoute = () => {
    const id = route.query.id || route.params.id;
    return id;
}

const form = reactive<FormData>({
    nome: '',
    telefone: '',
    cpf: '',
    sexo: '',
    idade: ''
});

const errors = reactive<Record<string, string>>({});
const loading = ref(false);
const error = ref<string | null>(null);

const fetchPessoa = async () => {
    try {
        const id = getIdByRoute();
        const response = await api.get<IPessoa>(`/pessoas/${id}`);
        form.nome = response.data.nome;
        form.telefone = response.data.telefone;
        form.cpf = response.data.cpf;
        form.sexo = response.data.is_masculino ? 'Masculino' : 'Feminino';
        form.idade = response.data.idade;
    } catch (error) {
        console.error((error as Error).message);
    }
}

function voltar() {
    router.back();
}

function validateForm(): boolean {
    Object.keys(errors).forEach((key) => delete errors[key]);

    const requiredFields = ['nome', 'telefone', 'cpf', 'sexo', 'idade'];

    for (const field of requiredFields) {
        const value = (form as any)[field];

        if (value === '' || value === null || value === undefined) {
            errors[field] = 'Campo obrigatório';
        }

        if (field === 'idade') {
            const idadeValue = parseInt(String(form.idade));
            if (isNaN(idadeValue) || idadeValue < 0) {
                errors[field] = 'Deve ser uma idade válida';
            } else {
                form.idade = idadeValue;
            }
        }
    }

    if (form.sexo !== 'Masculino' && form.sexo !== 'Feminino') {
        errors['sexo'] = 'Sexo é obrigatório';
    }

    return Object.keys(errors).length === 0;
}

async function submitForm() {
    if (!validateForm()) return;

    loading.value = true;
    error.value = null;

    try {
        const id = getIdByRoute() as string;
        if (!id) return;
        const response = await api.put(`/pessoas/${id}`, {
            ...form,
            sexo: form.sexo === 'Masculino' ? 'M' : 'F'
        });

        router.back();

    } catch (erro: any) {
        if (erro.status === 422 && erro.response?.data?.errors) {
            Object.assign(errors, erro.response.data.errors);
        }
        error.value = 'Erro ao realizar o cadastro.';
    } finally {
        loading.value = false;
    }
}

function cancelarEdicao() {
    router.back();
}

const deleteModal = ref<InstanceType<typeof CloseModal> | null>(null);
const openDeleteModal = () => {
    if (deleteModal.value)
        deleteModal.value?.openModal();

}

const confirmDelete = async () => {
    try {
        const id = getIdByRoute();
        if (id) {
            await api.delete(`/pessoas/${id}`);
            router.push({
                name: 'pessoas.index',
                query: {
                    showToast: 'true',
                    toastMessage: 'Pessoa deletada com sucesso!'
                },
                replace: true
            });
        }
    } catch (exception) {
        console.log(exception);
        error.value = 'Erro ao excluir a pessoa.';
    }
}

onMounted(() => {
    fetchPessoa();
})
</script>

<template class="mt-4">
    <main class="h-[calc(100vh-56px)]">
        <HeaderModule>
            <template #title>
                <h1 class="text-3xl font-bold">Editar Pessoa</h1>
            </template>
            <template #actions>
                <BackButton label="Voltar" @click="voltar" />
            </template>
        </HeaderModule>

        <Suspense>
            <template #default>
                <form @submit.prevent="submitForm" class="p-3 container">
                    <section class="pb-3">
                        <div class="mx-auto max-w-screen">
                            <div class="p-5 bg-white relative border border-gray-300 sm:rounded overflow-hidden">
                                <h2 class="text-xl font-semibold mb-4">Dados da Pessoa</h2>
                                <div class="border-b border-colorline"></div>

                                <div class="flex flex-wrap -mx-3 mt-4">
                                    <TextInput class="w-full md:w-1/3 px-3 py-3" label="Nome" v-model="form.nome"
                                        :message="errors.nome" placeholder="Digite o nome da pessoa" />

                                    <TextInput class="w-full md:w-1/3 px-3 py-3" label="Telefone"
                                        v-model="form.telefone" :message="errors.telefone"
                                        placeholder="Digite o telefone" />

                                    <TextInput class="w-full md:w-1/3 px-3 py-3" label="CPF" v-model="form.cpf"
                                        :message="errors.cpf" placeholder="Digite o CPF" />

                                    <SelectInput class="w-full md:w-1/3 px-3 py-3" label="Sexo" v-model="form.sexo"
                                        :options="['Masculino', 'Feminino']" :message="errors.sexo"
                                        placeholder="Selecione o sexo" />

                                    <TextInput class="w-full md:w-1/3 px-3 py-3" label="Idade" v-model="form.idade"
                                        :message="errors.idade" placeholder="Digite a idade" type="number" />
                                </div>
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="flex justify-between p-5 bg-white relative border border-gray-300 sm:rounded gap-4">

                            <button type="button" @click="openDeleteModal" class="focus:outline-none">
                                <TrashIcon />
                            </button>

                            <div class="flex gap-4">
                                <ButtonCancel label="Cancelar" @click="cancelarEdicao" />
                                <ButtonCadastrar type="submit" label="Salvar Alterações" />
                            </div>
                        </div>
                    </section>
                </form>
            </template>
            <template #fallback>
                <div>Carregando...</div>
            </template>
        </Suspense>
        <CloseModal ref="deleteModal" :confirm-delete="confirmDelete">
            Deseja realmente excluir esta pessoa? Esta ação é irreversível.
        </CloseModal>
    </main>
</template>
