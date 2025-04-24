<script lang="ts" setup>
import HeaderModule from '@/components/data-table/HeaderModule.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import ButtonCadastrar from '@/components/form-components/buttons/ButtonCadastrar.vue';
import ButtonCancel from '@/components/form-components/buttons/ButtonCancel.vue';
import SelectInput from '@/components/form-components/SelectInput.vue';
import TextInput from '@/components/form-components/TextInput.vue';
import { tipo_revisao } from '@/data/options_selects';
import api from '@/plugins/api';
import { useAlertStore } from '@/stores/alertState';
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const alertStore = useAlertStore();
const router = useRouter();
const route = useRoute();

const veiculoPlaca = route.query.placa;

interface RevisaoFormData {
    data: string;
    quilometragem: number | string;
    tipo: string;
    descricao: string;
    observacoes: string;
    valor_total: number | string;
    garantia_meses: number | string;
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

const errors = reactive<Record<string, string>>({});
const loading = ref(false);

function voltar() {
    router.back();
}

function validateForm(): boolean {
    Object.keys(errors).forEach((key) => delete errors[key]);

    const requiredFields = ['data', 'quilometragem', 'tipo', 'valor_total', 'garantia_meses', 'placa'];

    for (const field of requiredFields) {
        const value = (form as any)[field];

        if (value === '' || value === null || value === undefined) {
            errors[field] = 'Campo obrigatório';
        }

        if (field === 'quilometragem' || field === 'valor_total' || field === 'garantia_meses') {
            const numericValue = parseFloat(String(form[field]));
            if (isNaN(numericValue) || numericValue <= 0) {
                errors[field] = `Deve ser um valor numérico válido`;
            } else {
                form[field] = numericValue;
            }
        }
    }

    return Object.keys(errors).length === 0;
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

function cancelarCadastro() {
    router.back();
}
onMounted(() => {
    if (veiculoPlaca)
        form.placa = veiculoPlaca as string;
})
</script>

<template>
    <main class="h-[calc(100vh-56px)]">
        <HeaderModule class="mb-7">
            <template #title>
                <h1 class="text-3xl font-bold">Cadastrar Revisão</h1>
            </template>
            <template #actions>
                <BackButton label="Voltar" @click="voltar" />
            </template>
        </HeaderModule>

        <form @submit.prevent="submitForm" class="p-3 container">
            <section class="pb-3">
                <div class="mx-auto max-w-screen">
                    <div class="p-5 bg-white relative border border-gray-300 sm:rounded overflow-hidden">
                        <h2 class="text-xl font-semibold mb-4">Dados da Revisão</h2>
                        <div class="border-b border-colorline"></div>
                        <div class="-mx-3 mt-4 grid grid-cols-1 md:grid-cols-2  lg:grid-cols-3 gap-3">
                            <TextInput v-if="!veiculoPlaca" class="py-3" label="Placa"
                                v-model="form.placa" :message="errors.placa" placeholder="Digite a placa do veículo" />

                            <TextInput class="py-3" label="Data" v-model="form.data"
                                :message="errors.data" placeholder="Digite a data da revisão" type="date" />

                            <TextInput class="py-3" label="Quilometragem"
                                v-model="form.quilometragem" :message="errors.quilometragem"
                                placeholder="Digite a quilometragem" type="number" />
                            <SelectInput class="py-3" label="Tipo de Revisão" v-model="form.tipo"
                                :options="tipo_revisao" :message="errors.is_masculino"
                                placeholder="Selecione o tipo" />
                            <TextInput class="py-3" label="Descrição" v-model="form.descricao"
                                :message="errors.descricao" :required="0"
                                placeholder="Descrição detalhada da revisão" />
                            <TextInput class="py-3" label="Garantia (meses)"
                                v-model="form.garantia_meses" :message="errors.garantia_meses"
                                placeholder="Digite o número de meses de garantia" type="number" />

                            <TextInput class="py-3" label="Valor Total (R$)"
                                v-model="form.valor_total" :message="errors.valor_total"
                                placeholder="Digite o valor total" type="number" />

                            <TextInput class="py-3" label="Observações" v-model="form.observacoes"
                                :required="0" :message="errors.observacoes" placeholder="Observações adicionais" />
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
    </main>
</template>
