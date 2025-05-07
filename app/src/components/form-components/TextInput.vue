<script setup lang="ts">
import { formatarFistLetter } from '@/helpers/formatters';
import type { InputType } from '@/helpers/regexp/patterns';
import patterns from '@/helpers/regexp/patterns';
import { computed, defineEmits, defineProps } from 'vue';
import InputComponent from './InputComponent.vue';

const props = withDefaults(defineProps<{
  label?: string;
  message?: string;
  placeholder?: string;
  required?: boolean;
  type?: Exclude<InputType, 'integer' | 'float'>;
  firstUppercase?: boolean;
  uppercase?: boolean;
  maxSize?: number;
  disabled?: boolean
}>(), {
  label: '',
  message: '',
  placeholder: '',
  required: false,
  type: 'text',
  maxSize: undefined,
  firstUppercase: false,
  uppercase: false,
  disabled: false
});


const model = defineModel<string>({ required: true, default: '' })

const validator = computed(() => {
  if (!props.type) return patterns.text
  if (props.type === 'date') return undefined
  return patterns[props.type]
})

const formatter = (value: string) => {
  if (props.firstUppercase) return formatarFistLetter(value)
  if (props.uppercase) return value.toUpperCase()
  if (validator.value?.format) return validator.value.format(value)
  return value
}

const validTarget = (value: string) => {
  if (!(validator.value?.format) && validator.value && !validator.value.semiValid(value))
    return validator.value.message;
  if (props.maxSize && value.length > props.maxSize)
    return `No máximo são ${props.maxSize} caracteres.`;
  return ''
}

</script>

<template>
  <InputComponent v-model="model" :type="type" :label="label" :message="message" :placeholder="placeholder"
    :required="required" :max-size="maxSize" :show-max-size="maxSize !== undefined" :valid-target="validTarget"
    :format-target="formatter" :disabled="disabled" />
</template>