<script setup lang="ts">
import patterns from '@/helpers/regexp/patterns';
import { ref } from 'vue';
import Datepicker from 'vue3-datepicker';
import InputError from './InputError.vue';
withDefaults(defineProps<{
    label?: string
    message?: string
    placeholder?: string
    required?: boolean
}>(), {
    label: undefined,
    message: '',
    placeholder: "Digite um valor",
    required: false
})

const model = defineModel<Date>()

const inputRef = ref<HTMLInputElement | null>(null);
const updateValue = (event: InputEvent) => {
    const input = event.target as HTMLInputElement;
    const start = input.selectionStart ?? 0;
    const end = input.selectionEnd ?? 0;

    let predictedValue = input.value;

    if (event.inputType === 'insertText') {
        const char = event.data ?? '';
        predictedValue = input.value.slice(0, start) + char + input.value.slice(end);
    }

    else if (event.inputType === 'deleteContentBackward') {
        // Simula Backspace
        if (start === end && start > 0) {
            predictedValue = input.value.slice(0, start - 1) + input.value.slice(end);
        } else {
            predictedValue = input.value.slice(0, start) + input.value.slice(end);
        }
    }

    else if (event.inputType === 'deleteContentForward') {
        // Simula Delete
        if (start === end) {
            predictedValue = input.value.slice(0, start) + input.value.slice(end + 1);
        } else {
            predictedValue = input.value.slice(0, start) + input.value.slice(end);
        }
    }

    else if (event.inputType === 'insertFromPaste') {
        const pasted = event.data ?? '';
        predictedValue = input.value.slice(0, start) + pasted + input.value.slice(end);
    }


    if (!patterns.date.semiValid(predictedValue)) {
        event.preventDefault();
    }
};


</script>

<template>
    <div class="">
        <label v-if="label" :class="[message !== '' && 'text-red-500']"
            class="block tracking-wide text-gray-700 text-x font-semibold mb-2">
            {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>
        <Datepicker ref="inputRef" inputFormat="dd/MM/yyyy" :class="['py-[10px] transition border-gray-900 duration-300 rounded outline-none w-full bg-inputBg text-gray-700 border focus:ring-1 focus:ring-blue-500 focus:border-blue-600',
            message !== '' && 'focus:ring-1 focus:ring-red-500 focus:border-red-600'
        ]" @beforeinput="updateValue" :placeholder="placeholder" typeable v-model="model" format="dd/MM/yyyy" />
        <InputError :message="message" />
    </div>
</template>