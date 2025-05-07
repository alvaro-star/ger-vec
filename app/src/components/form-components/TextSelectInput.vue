<script setup lang="ts">
import InputError from './InputError.vue';

withDefaults(defineProps<{
    label?: string
    message?: string
    placeholder?: string
    options: string[]
    required?: boolean
    disabled?: boolean
}>(), {
    label: undefined,
    message: '',
    placeholder: "Digite um valor",
    required: false,
    disabled: false
})
const model = defineModel<string>({ required: true })
</script>
<template>
    <div class="">
        <label v-if="label" :class="[message !== '' && 'text-red-500']"
            class="block tracking-wide text-gray-700 text-x font-semibold mb-2">
            {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>
        <v-select :disabled="disabled" input-class="text-xl" :class="['style-chooser  base text-base',
            message !== '' ? 'error' : 'success'

        ]" :options="options" v-model="model" :placeholder="placeholder" :searchable="true" :clearable="false"
            label="nome">
            <template #no-options>
                <span class="text-gray-500">
                    Nenhum registro foi encontrado
                </span>
            </template>
        </v-select>


        <InputError :message="message" />
    </div>

</template>
<style>
@import "tailwindcss";

.style-chooser .vs__search::placeholder {
    @apply text-gray-700;
}

.style-chooser .vs__search {
    @apply text-gray-700 py-[4.9px];
}

.base .vs__dropdown-toggle {
    background-color: #F9FAFB;
    @apply px-1 transition border-gray-900 duration-300 rounded outline-none w-full text-gray-700 border;
}

.success .vs__dropdown-toggle {
    @apply focus-within:ring-1 focus-within:ring-blue-500 focus-within:border-blue-600;
}

.error .vs__dropdown-toggle {
    @apply focus-within:ring-1 focus-within:ring-red-500 focus-within:border-red-600;
}


.style-chooser .vs__dropdown-menu {
    @apply rounded;
}
</style>
