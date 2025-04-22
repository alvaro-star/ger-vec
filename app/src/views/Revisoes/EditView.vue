<script lang="ts" setup>
import CloseModal from '@/components/CloseModal.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import ButtonCadastrar from '@/components/form-components/buttons/ButtonCadastrar.vue';
import ButtonCancel from '@/components/form-components/buttons/ButtonCancel.vue';
import SelectInput from '@/components/form-components/SelectInput.vue';
import TextInput from '@/components/form-components/TextInput.vue';
import TrashIcon from '@/components/icons/TrashIcon.vue';
import { tipo_revisao } from '@/data/options_selects';
import api from '@/plugins/api';
import { useAlertStore } from '@/stores/alertState';
import type IRevisao from '@/types/IRevisao';
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const alertStore = useAlertStore();
const router = useRouter();
const route = useRoute();

const getIdByRoute = () => route.query.id || route.params.id;

interface FormData {
    data: string;
    quilometragem: string;
    tipo: string;
    descricao: string;
    observacoes: string;
    valor_total: string;
    garantia_meses: string;
}

const form = reactive<FormData>({
    data: '',
    quilometragem: '',
    tipo: '',
    descricao: '',
    observacoes: '',
    valor_total: '',
    garantia_meses: ''
});

const errors = reactive<Record<string, string>>({});
const loading = ref(false);

const fetchRevisao = async () => {
    try {
        const id = getIdByRoute();
        const { data } = await api.get<IRevisao>(`/revisoes/${id}`);
        Object.assign(form, data);
    } catch (error) {
        console.error((error as Error).message);
    }
};

function validateForm(): boolean {
    Object.keys(errors).forEach((key) => delete errors[key]);
    const requiredFields = ['data', 'quilometragem', 'tipo', 'descricao', 'valor_total', 'garantia_meses'];

    for (const field of requiredFields) {
        const value = (form as any)[field];
        if (!value && value !== 0) {
            errors[field] = 'Campo obrigatório';
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
        await api.put(`/revisoes/${id}`, form);
        alertStore.setMessage('Revisão editada com sucesso', null);
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
    } catch (exception) {
        console.log(exception);
        alertStore.setMessage('Não foi possível excluir a revisão.', 'danger');
    }
};

onMounted(fetchRevisao);
</script>

<template>
    <main class="h-[calc(100vh-56px)]">
        <HeaderModule class="mb-4">
            <template #title>
                <h1 class="text-3xl font-bold">Editar Revisão</h1>
            </template>
            <template #actions>
                <BackButton label="Voltar" @click="cancelarEdicao" />
            </template>
        </HeaderModule>

        <form @submit.prevent="submitForm" class="p-3 container">
            <section class="pb-3">
                <div class="p-5 bg-white border border-gray-300 sm:rounded">
                    <h2 class="text-xl font-semibold mb-4">Dados da Revisão</h2>
                    <div class="flex flex-wrap -mx-3 mt-4">
                        <TextInput class="w-full md:w-1/3 px-3 py-3" label="Data" v-model="form.data"
                            :message="errors.data" placeholder="AAAA-MM-DD" type="date" />

                        <TextInput class="w-full md:w-1/3 px-3 py-3" label="Quilometragem" v-model="form.quilometragem"
                            :message="errors.quilometragem" placeholder="Digite a quilometragem" type="number" />

                        <SelectInput class="w-full md:w-1/3 px-3 py-3" label="Tipo" v-model="form.tipo"
                            :options="tipo_revisao" :message="errors.tipo" placeholder="Selecione o tipo" />

                        <TextInput class="w-full md:w-1/3 px-3 py-3" label="Garantia (meses)"
                            v-model="form.garantia_meses" :message="errors.garantia_meses"
                            placeholder="Meses de garantia" type="number" />

                        <TextInput class="w-full md:w-1/3 px-3 py-3" label="Valor Total" v-model="form.valor_total"
                            :message="errors.valor_total" placeholder="Digite o valor total" type="number" />

                        <TextInput class="w-full md:w-1/3 px-3 py-3" label="Descrição" v-model="form.descricao"
                            :message="errors.descricao" placeholder="Informe a descrição da revisão" />

                        <TextInput class="w-full md:w-1/3 px-3 py-3" label="Observações" v-model="form.observacoes"
                            :message="errors.observacoes" placeholder="Observações adicionais" />
                    </div>
                </div>
            </section>

            <section class="flex justify-between p-5 bg-white border border-gray-300 sm:rounded gap-4">
                <button type="button" @click="openDeleteModal" class="focus:outline-none">
                    <TrashIcon />
                </button>
                <div class="flex gap-4">
                    <ButtonCancel label="Cancelar" type="button" @click="cancelarEdicao" />
                    <ButtonCadastrar type="submit" label="Salvar Alterações" />
                </div>
            </section>
        </form>

        <CloseModal ref="deleteModal" :confirm-delete="confirmDelete">
            Deseja realmente excluir esta revisão? Esta ação é irreversível.
        </CloseModal>
    </main>
</template>
