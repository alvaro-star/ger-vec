<script setup lang="ts">
import SelectInput from '@/components/form-components/SelectInput.vue';
import TextInput from '@/components/form-components/TextInput.vue';
import type IFormData from '../types/IFormData';
defineProps<{
    errors: Record<string, string>;
}>()

const form = defineModel<IFormData>({ required: true });
</script>
<template>
    <TextInput class="w-full px-3" label="Nome" first-uppercase v-model="form.nome" :max-size="50" required
        show-max-size :message="errors.nome" placeholder="Digite o nome do cliente" />

    <TextInput class="w-full px-3" type="phone" label="Celular" v-model="form.celular" :message="errors.celular"
        placeholder="Digite o celular" :max-size="15" show-max-size required />

    <TextInput class="w-full px-3" label="CPF" type="cpf" v-model="form.cpf" :message="errors.cpf"
        placeholder="Digite o CPF" required :max-size="14" show-max-size />

    <SelectInput class="w-full px-3" label="Sexo" v-model="form.sexo" :options="['Masculino', 'Feminino']"
        :message="errors.sexo" placeholder="Selecione o sexo" />

    <TextInput required class="w-full px-3" label="Idade" v-model="form.idade" :message="errors.idade"
        placeholder="Digite a idade" type="integer" :max-value="100" />
</template>