<script setup lang="ts">
import TrashIcon from '@/components/icons/TrashIcon.vue';
import ButtonCadastrar from '../buttons/ButtonCadastrar.vue';
import ButtonCancel from '../buttons/ButtonCancel.vue';

defineProps<{
    header: string;
    create: boolean
    openDeleteModal?: () => void
    cancelarProcesso: () => void
}>()

</script>

<template>
    <form class="p-3">
        <section class="pb-3">
            <div class="mx-auto max-w-screen">
                <div class="p-5 bg-white relative border border-gray-300 sm:rounded overflow-hidden">
                    <h2 class="text-xl font-semibold mb-4">{{ header }}</h2>
                    <div class="border-b border-colorline"></div>
                    <div class="-mx-3 mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <slot>
                            <div>Sem Inputs</div>
                        </slot>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div v-if="create" class="mx-auto max-w-screen">
                <div class="flex justify-end p-5 bg-white border border-gray-300 sm:rounded gap-4">
                    <ButtonCancel label="Cancelar" @click="cancelarProcesso" />
                    <ButtonCadastrar type="submit" label="Confirmar Cadastro" />
            </div>
            </div>
            <div v-else class="flex justify-between p-5 bg-white relative border border-gray-300 sm:rounded gap-4">
                <button type="button" @click="openDeleteModal" class="cursor-pointer focus:outline-none">
                    <TrashIcon />
                </button>

                <div class="flex gap-4">
                    <ButtonCancel label="Cancelar" type="button" @click="cancelarProcesso" />
                    <ButtonCadastrar type="submit" label="Salvar Alterações" />
                </div>
            </div>
        </section>
    </form>
</template>
