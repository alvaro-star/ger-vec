<script setup lang="ts">
import InputText from '@/components/form-components/InputText.vue';
import TextInput from '@/components/form-components/TextInput.vue';
import { ref } from 'vue';


const value = ref('AAA')

</script>

<template>
    <div>
        Fora do componente{{ value }}
        <TextInput type="cpf" v-model="value" :uppercase="true" label="Texte" :min-value="0" :show-max-size="true"/>



        <input v-model="value" />
        <InputText  type="placa" v-model="value" :uppercase="true" label="Texte" :min-value="0" :show-max-size="true"/>
        <InputText  type="placa" v-model="value" :uppercase="true" label="Texte" :min-value="0" :show-max-size="true"/>
    </div>
</template>