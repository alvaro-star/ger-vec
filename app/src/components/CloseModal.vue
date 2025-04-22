<template>
    <div v-show="isVisible" class="fixed inset-0  flex justify-center items-center" @click.self="closeModal">
        <div class="bg-white p-6 rounded w-full max-w-[600px] shadow-2xl border border-gray-400">
            <div class="text-xl font-semibold">Confirmar Exclusão</div>
            <div class="mt-4 text-lg text-terciary">
                <slot>
                    Deseja realmente excluir este refgstro? Esta ação é irreversível.
                </slot>
            </div>
            <div class="mt-6 flex justify-end gap-4">
                <ButtonCancel label="Sim, Tenho certeza" @click="handleConfirmDelete" />
                <ButtonOption label="Não, Cancelar" @click="closeModal" />
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref, defineExpose } from 'vue';
import ButtonCancel from '@/components/form-components/buttons/ButtonCancel.vue';
import ButtonOption from '@/components/form-components/buttons/ButtonOption.vue';
import { defineProps } from 'vue';

const isVisible = ref(false);

const props = defineProps<{
    confirmDelete: () => void
}>();

const openModal = () => {
    isVisible.value = true;
};

const closeModal = () => {
    isVisible.value = false;
};

const handleConfirmDelete = () => {
    props.confirmDelete();
    closeModal();
};


defineExpose({
    openModal
});
</script>