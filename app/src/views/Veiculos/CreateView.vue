<script lang="ts" setup>
import HeaderModule from '@/components/data-table/HeaderModule.vue';
import BackButton from '@/components/form-components/buttons/BackButton.vue';
import ButtonCadastrar from '@/components/form-components/buttons/ButtonCadastrar.vue';
import ButtonCancel from '@/components/form-components/buttons/ButtonCancel.vue';
import SelectInput from '@/components/form-components/SelectInput.vue';
import TextInput from '@/components/form-components/TextInput.vue';
import { cores, tipo_combustivel } from '@/data/options_selects';
import api from '@/plugins/api';
import { useAlertStore } from '@/stores/alertState';
import { reactive, ref } from 'vue';
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
    ano: number | string;
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

const errors = reactive<Record<string, string>>({});
const loading = ref(false);

function voltar() {
    router.back();
}

function validateForm(): boolean {
    Object.keys(errors).forEach((key) => delete errors[key]);

    const requiredFields = ['marca', 'modelo', 'placa', 'ano', 'cor', 'renavam', 'tipo_combustivel'];

    for (const field of requiredFields) {
        const value = (form as any)[field];

        if (value === '' || value === null || value === undefined) {
            errors[field] = 'Campo obrigatório';
        }

        if (field === 'ano') {
            const anoValue = parseInt(String(form.ano));
            if (isNaN(anoValue) || anoValue < 1900) {
                errors[field] = 'Deve ser um ano válido';
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
        const response = await api.post('/veiculos', {
            ...form,
            pessoa_id: pessoaId
        });

        alertStore.setMessage('O veículo foi cadastrado com sucesso!', null);
        router.push({
            name: 'pessoas.show',
            params: { id: pessoaId },
            replace: true
        });
    } catch (erro: any) {
        if (erro.status === 422 && erro.response?.data?.errors) {
            Object.assign(errors, erro.response.data.errors);
        }
        alertStore.setMessage('Nao foi possivel cadastrar o veiculo', 'danger');
    } finally {
        loading.value = false;
    }
}

function cancelarCadastro() {
    router.back();
}
</script>

<template>
    <main class="h-[calc(100vh-56px)]">
        <HeaderModule class="mb-4">
            <template #title>
                <h1 class="text-3xl font-bold">Cadastrar Veículo</h1>
            </template>
            <template #actions>
                <BackButton label="Voltar" @click="voltar" />
            </template>
        </HeaderModule>

        <form @submit.prevent="submitForm" class="p-3 container">
            <section class="pb-3">
                <div class="mx-auto max-w-screen">
                    <div class="p-5 bg-white relative border border-gray-300 sm:rounded overflow-hidden">
                        <h2 class="text-xl font-semibold mb-4">Dados do Veículo</h2>
                        <div class="border-b border-colorline"></div>

                        <div class="flex flex-wrap -mx-3 mt-4">
                            <TextInput class="w-full md:w-1/3 px-3 py-3" label="Marca" v-model="form.marca"
                                :message="errors.marca" placeholder="Digite a marca do veículo" />

                            <TextInput class="w-full md:w-1/3 px-3 py-3" label="Modelo" v-model="form.modelo"
                                :message="errors.modelo" placeholder="Digite o modelo do veículo" />

                            <TextInput class="w-full md:w-1/3 px-3 py-3" label="Placa" v-model="form.placa"
                                :message="errors.placa" placeholder="Digite a placa" />

                            <TextInput class="w-full md:w-1/3 px-3 py-3" label="Renavam" v-model="form.renavam"
                                :message="errors.renavam" placeholder="Digite o renavam" />

                            <TextInput class="w-full md:w-1/3 px-3 py-3" label="Ano" v-model="form.ano"
                                :message="errors.ano" placeholder="Digite o ano" type="number" />
                            <SelectInput class="w-full md:w-1/3 px-3 py-3" label="Cor" v-model="form.cor"
                                :options="cores" :message="errors.cor" placeholder="Selecione a cor" />
                            <SelectInput class="w-full md:w-1/3 px-3 py-3" label="Tipo de Combustível"
                                v-model="form.tipo_combustivel" :options="tipo_combustivel"
                                :message="errors.tipo_combustivel" placeholder="Selecione o tipo de combustível" />
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