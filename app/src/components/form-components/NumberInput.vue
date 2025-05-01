<script setup lang="ts">
import patterns from '@/helpers/regexp/patterns';
import { computed, defineEmits, defineProps } from 'vue';
import InputComponent from './InputComponent.vue';
import { formatarFloat, formatarInteger } from '@/helpers/formatters';
import extractNumbers from '@/helpers/functions/extractNumbers';

const props = withDefaults(defineProps<{
    label?: string;
    message?: string;
    placeholder?: string;
    required?: boolean;
    type?: 'integer' | 'float';
    modelValue: string;
    precision?: number
    not_format?: boolean
    maxSize?: number
    prefix?: string;
    maxValue?: number;
}>(), {
    label: '',
    message: '',
    placeholder: '',
    prefix: undefined,
    required: false,
    precision: 2,
    maxSize: undefined,
    type: 'integer',
    maxValue: undefined,
});

const model = defineModel<string>({ required: true, default: '' })

const validator = computed(() => patterns[props.type])

const formatter = (value: string) => {
    if (props.not_format) return extractNumbers(value)

    if (value == '0,0' || value == '') return ''
    let newFormat = value
    if (validator.value.format) {
        if (props.type == 'float') newFormat = validator.value.format(value, props.precision)
        else newFormat = validator.value.format(value)
    }
    return newFormat
}

const validTarget = (value: string) => {
    if (value == '') return ''

    if (!(validator.value?.format) && !validator.value.semiValid(value))
        return validator.value.message
    const numberFormated = value.replace(/\./g, "").replace(",", ".")
    const numericValue = Number(numberFormated);

    if (props.maxSize) {
        if (value.length > props.maxSize)
            return props.not_format ? "No maximo podem haver 11 digitos" : "No maximo podem haver 11 caracteres"
    }
    if (!isNaN(numericValue) && props.maxValue && numericValue > props.maxValue) {
        let maxValueFormatted = ''
        if (props.type == 'float')
            maxValueFormatted = formatarFloat(props.maxValue.toString().replace(".", ","), props.precision)
        else maxValueFormatted = formatarInteger(props.maxValue.toString())
        if (props.not_format) {
            maxValueFormatted = props.maxValue.toString()
        }
        return `O valor máximo permitido é ${props.prefix} ${maxValueFormatted}.`;
    }

    return ''
}

</script>

<template>
    <InputComponent v-model="model" :type="type" :label="label" :message="message" :placeholder="placeholder"
        :required="required" :show-max-size="maxSize != undefined" :valid-target="validTarget" :format-target="formatter"
        :prefix="prefix" restart-select-edit :max-size="maxSize" />
</template>