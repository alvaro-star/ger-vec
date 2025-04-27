<script setup lang="ts">
import { defineProps } from 'vue';
import ButtonCancel from '../buttons/ButtonCancel.vue';
import ButtonCadastrar from '../buttons/ButtonCadastrar.vue';
import TrashIcon from '@/components/icons/TrashIcon.vue';


const props = defineProps<{
    openDeleteModal: () => void
    cancelarEdicao: () => void
}>();
</script>

<template>
    <section>
        <div class="flex justify-between p-5 bg-white relative border border-gray-300 sm:rounded gap-4">
            <button type="button" @click="props.openDeleteModal" class="focus:outline-none">
                <TrashIcon />
            </button>

            <!-- Botões de ação: Cancelar e Salvar -->
            <div class="flex gap-4">
                <ButtonCancel label="Cancelar" type="button" @click="props.cancelarEdicao" />
                <ButtonCadastrar type="submit" label="Salvar Alterações" />
            </div>
        </div>
    </section>
</template>
