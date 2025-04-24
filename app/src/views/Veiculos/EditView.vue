<script lang="ts" setup>
import CloseModal from '@/components/CloseModal.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import ButtonCadastrar from '@/components/form-components/buttons/ButtonCadastrar.vue';
import ButtonCancel from '@/components/form-components/buttons/ButtonCancel.vue';
import SelectInput from '@/components/form-components/SelectInput.vue';
import TextInput from '@/components/form-components/TextInput.vue';
import TrashIcon from '@/components/icons/TrashIcon.vue';
import { cores, tipo_combustivel } from '@/data/options_selects';
import api from '@/plugins/api';
import { useAlertStore } from '@/stores/alertState';
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
    ano: number | string;
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

const errors = reactive<Record<string, string>>({});
const loading = ref(false);

const fetchVeiculo = async () => {
    try {
        const id = getIdByRoute();
        const response = await api.get<IVeiculo>(`/veiculos/${id}`);
        const data = response.data;

        form.marca = data.marca;
        form.modelo = data.modelo;
        form.ano = data.ano;
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
    Object.keys(errors).forEach((key) => delete errors[key]);

    const requiredFields = ['marca', 'modelo', 'ano', 'cor', 'tipo_combustivel', 'placa', 'renavam'];


    for (const field of requiredFields) {
        const value = (form as any)[field];
        if (value === '' || value === null || value === undefined) {
            errors[field] = 'Campo obrigatório';
        }

        if (field === 'ano') {
            const anoValue = parseInt(String(form.ano));
            if (isNaN(anoValue) || anoValue < 1886 || anoValue > new Date().getFullYear() + 1) {
                errors[field] = 'Ano inválido';
            } else {
                form.ano = anoValue;
            }
        }
    }

    return Object.keys(errors).length === 0;
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
            Object.assign(errors, erro.response.data.errors);
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
            const message = exception.response?.data?.message
            alertStore.setMessage(message, 'danger');
        } else {
            alertStore.setMessage('Não foi possível excluir o veículo.', 'danger');
        }
    };

}


onMounted(fetchVeiculo);
</script>

<template>
    <main class="h-[calc(100vh-56px)]">
        <HeaderModule class="mb-7">
            <template #title>
                <h1 class="text-3xl font-bold">Editar Veículo</h1>
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
                                <h2 class="text-xl font-semibold mb-4">Dados do Veículo</h2>
                                <div class="border-b border-colorline"></div>

                                <div class="-mx-3 mt-4 grid grid-cols-1 md:grid-cols-2  lg:grid-cols-3 gap-3">
                                    <!-- Marca -->
                                    <TextInput class="w-full md:w-1/3 px-3 py-3" label="Marca" v-model="form.marca"
                                        :message="errors.marca" placeholder="Digite a marca" />

                                    <!-- Modelo -->
                                    <TextInput class="w-full md:w-1/3 px-3 py-3" label="Modelo" v-model="form.modelo"
                                        :message="errors.modelo" placeholder="Digite o modelo" />

                                    <!-- Placa -->
                                    <TextInput class="w-full md:w-1/3 px-3 py-3" label="Placa" v-model="form.placa"
                                        :message="errors.placa" placeholder="Digite a placa" />

                                    <!-- Renavam -->
                                    <TextInput class="w-full md:w-1/3 px-3 py-3" label="Renavam" v-model="form.renavam"
                                        :message="errors.renavam" placeholder="Digite o renavam" />

                                    <!-- Ano -->
                                    <TextInput class="w-full md:w-1/3 px-3 py-3" label="Ano" type="number"
                                        v-model="form.ano" :message="errors.ano" placeholder="Digite o ano" />

                                    <!-- Cor como select -->
                                    <SelectInput class="w-full md:w-1/3 px-3 py-3" label="Cor" v-model="form.cor"
                                        :options="cores" :message="errors.cor" placeholder="Selecione a cor" />

                                    <!-- Tipo de combustível como select -->
                                    <SelectInput class="w-full md:w-1/3 px-3 py-3" label="Tipo de Combustível"
                                        v-model="form.tipo_combustivel" :options="tipo_combustivel"
                                        :message="errors.tipo_combustivel"
                                        placeholder="Selecione o tipo de combustível" />

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
                                <ButtonCancel label="Cancelar" type="button" @click="cancelarEdicao" />
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
            Deseja realmente excluir este veículo? Esta ação é irreversível.
        </CloseModal>
    </main>
</template>
