<script lang="ts" setup>
import HeaderModule from '@/components/data-table/HeaderModule.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import ButtonCadastrar from '@/components/form-components/buttons/ButtonCadastrar.vue';
import ButtonCancel from '@/components/form-components/buttons/ButtonCancel.vue';
import SelectInput from '@/components/form-components/SelectInput.vue';
import TextInput from '@/components/form-components/TextInput.vue';
import api from '@/plugins/api';
import { useAlertStore } from '@/stores/alertState';
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';

const alertStore = useAlertStore()

interface FormData {
    nome: string;
    telefone: string;
    cpf: string;
    sexo: string;
    idade: number | string;
}

const router = useRouter();

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

    return Object.keys(errors).length === 0;
}

async function submitForm() {
    if (!validateForm()) return;

    loading.value = true;
    error.value = null;

    try {
        const response = await api.post('/pessoas', {
            ...form,
            sexo: form.sexo === 'Masculino' ? 'M' : 'F'
        });

        const pessoaId = response.data.id;

        alertStore.setMessage('O cliente foi cadastrado com sucesso!', null);
        router.push({
            name: 'pessoas.show',
            params: {
                id: pessoaId
            },
            replace: true
        });

    } catch (erro: any) {
        if (erro.status === 422 && erro.response?.data?.errors) {
            Object.assign(errors, erro.response.data.errors);
        }
        error.value = 'Erro ao realizar o cadastro.';
    } finally {
        loading.value = false;
    }
}

function cancelarCadastro() {
    router.back();
}
</script>


<template class="mt-4">
    <main class="h-[calc(100vh-56px)]">
        <HeaderModule classs="mb-4">>
            <template #title>
                <h1 class="text-3xl font-bold">Cadastrar Pessoa</h1>
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

                                <div class="-mx-3 mt-4 grid grid-cols-1 md:grid-cols-2  lg:grid-cols-3 gap-3">
                                    <TextInput class="w-full px-3" label="Nome" v-model="form.nome"
                                        :message="errors.nome" placeholder="Digite o nome do cliente" />

                                    <TextInput class="w-full px-3" label="Telefone" v-model="form.telefone"
                                        :message="errors.telefone" placeholder="Digite o telefone" />

                                    <TextInput class="w-full px-3" label="CPF" v-model="form.cpf" :message="errors.cpf"
                                        placeholder="Digite o CPF" />

                                    <SelectInput class="w-full px-3" label="Sexo" v-model="form.sexo"
                                        :options="['Masculino', 'Feminino']" :message="errors.is_masculino"
                                        placeholder="Selecione o sexo" />

                                    <TextInput class="w-full px-3" label="Idade" v-model="form.idade"
                                        :message="errors.idade" placeholder="Digite a idade" type="number" />
                                </div>
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="mx-auto max-w-screen">
                            <div class="flex justify-end p-5 bg-white border border-gray-300 sm:rounded gap-4">
                                <ButtonCancel label="Cancelar" @click="cancelarCadastro" />
                                <ButtonCadastrar type="submit" label="Confirmar Cadastro" />
                            </div>
                        </div>
                    </section>
                </form>
            </template>
            <template #fallback>
                <div>Carregando...</div>
            </template>
        </Suspense>
    </main>
</template>
