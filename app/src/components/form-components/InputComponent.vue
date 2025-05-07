<script setup lang="ts">
import type { InputType } from '@/helpers/regexp/patterns';
import { computed, defineEmits, defineProps, nextTick, ref, watch } from 'vue';
import InputError from './InputError.vue';

const props = withDefaults(defineProps<{
    label?: string;
    message?: string;
    placeholder?: string;
    required?: boolean;
    type: InputType;
    modelValue: string;
    prefix?: string;
    maxValue?: number;
    showMaxSize?: boolean
    maxSize?: number
    restartSelectEdit?: boolean
    validTarget: (arg: string) => string
    formatTarget?: (arg: string) => string
    disabled?: boolean
}>(), {
    label: '',
    message: '',
    placeholder: '',
    prefix: undefined,
    required: false,
    precision: undefined,
    type: 'text',
    maxValue: undefined,
    showMaxSize: false,
    restartSelectEdit: false,
    maxSize: 20,
    disabled: false
});


const messageLocal = ref('')

const messageDefault = computed(() => {
    return messageLocal.value == '' ? props.message : messageLocal.value;
});

const inputRef = ref<HTMLInputElement | null>(null);

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void
}>()

watch(() => props.modelValue, (newValue) => {
    value.value = newValue
})


const value = ref(props.modelValue)

const updateValue = (event: Event) => {
    const target = event.target as HTMLInputElement;
    let valueTarget = target.value;

    const selectionStart = inputRef.value?.selectionStart

    if (props.formatTarget) {
        let newValueTarget = props.formatTarget(valueTarget)
        value.value = newValueTarget
        valueTarget = newValueTarget
    }

    const message = props.validTarget(valueTarget) || ''

    if (message == '') emit('update:modelValue', valueTarget);
    else {
        value.value = props.modelValue
        messageLocal.value = message
        setTimeout(() => {
            messageLocal.value = ''
        }, 2000)
    }

    nextTick(() => {
        if (props.restartSelectEdit && selectionStart) inputRef.value?.setSelectionRange(selectionStart, selectionStart)
    })
}

const focusWithin = ref(false);
</script>

<template>
    <div class="">
        <label v-if="label" :class="[messageDefault !== '' && 'text-red-500']"
            class="block tracking-wide text-gray-700 text-x font-semibold mb-2">
            {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>
        <div :class="[' flex items-center transition duration-300 rounded w-full bg-inputBg text-gray-700',
            focusWithin ? (messageDefault !== '' ? 'ring-1 ring-red-500 border-red-600' : 'border ring-1 ring-blue-500 border-blue-600')
                : (messageDefault !== '' ? 'border border-red-600' : 'border border-gray-900')
        ]">

            <p v-show="prefix" :class="['pl-4', messageDefault !== '' && 'text-red-700']">
                {{ prefix }}
            </p>

            <input ref="inputRef" :type="type" v-model="value" @input="updateValue" @focus="focusWithin = true"
                @blur="focusWithin = false" :disabled="disabled" :placeholder="placeholder" :class="['appearance-none block w-full bg-transparent outline-none border-none ring-0 py-3 leading-tight',
                    prefix ? 'pl-2' : 'pl-4',
                    showMaxSize ? 'pr-1' : 'pr-4'
                ]" />

            <p v-show="showMaxSize" :class="['pr-4', messageDefault !== '' && 'text-red-700']">
                {{ maxSize - props.modelValue.length }}
            </p>
        </div>

        <InputError :message="messageDefault" />
    </div>
</template>