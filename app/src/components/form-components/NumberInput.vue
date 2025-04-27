<script setup lang="ts">
import type { InputType } from '@/helpers/regexp/patterns';
import patterns, { formatarFistLetter } from '@/helpers/regexp/patterns';
import { computed, defineEmits, defineProps, ref, watch } from 'vue';
import InputError from './InputError.vue';

const props = withDefaults(defineProps < {
  label?: string;
  message?: string;
  placeholder?: string;
  required?: boolean;
  type?: InputType;
  modelValue: string;
  maxValue?: number;
  firstUppercase?: boolean;
  uppercase?: boolean;
  precicion?: number;
  minValue?: number;
  maxSize?: number;
  showMaxSize?: boolean;
} > (), {
  label: '',
  message: '',
  placeholder: '',
  required: false,
  type: 'text',
  maxSize: 10,
  firstUppercase: false,
  precicion: undefined,
  maxValue: undefined,
  minValue: undefined,
  uppercase: false,
  showMaxSize: false,
});


const emit = defineEmits < {
  (e: 'update:modelValue', value: string): void
}> ()

watch(() => props.modelValue, (newValue) => {
  value.value = newValue
})

const typeLocal = computed(() => {
  switch (props.type) {
    case 'date': return 'date'
    default: return 'text'
  }
})

const value = ref(props.modelValue)
const messageLocal = ref('')

const messageDefault = computed(() => {
  return messageLocal.value == '' ? props.message : messageLocal.value;
});

const validator = computed(() => {
  if (!props.type) return patterns.text
  if (props.type === 'date') return undefined
  return patterns[props.type]
})

watch(messageLocal, (newMessage) => {
  if (newMessage !== '') {
    setTimeout(() => {
      messageLocal.value = '';
    }, 2000);
  }
});

const updateValue = (event: Event) => {
  const target = event.target as HTMLInputElement;
  let valueTarget = target.value;


  if (props.firstUppercase) {
    const textFirstUppercase = formatarFistLetter(valueTarget)
    if (valueTarget !== textFirstUppercase) {
      value.value = textFirstUppercase
      valueTarget = textFirstUppercase
    }
  }
  if (props.uppercase) {
    const textUppercase = valueTarget.toUpperCase()
    if (valueTarget !== textUppercase) {
      value.value = textUppercase
      valueTarget = textUppercase
    }
  }


  if (validator.value && !validator.value.semiValid(valueTarget)) {
    value.value = props.modelValue;
    messageLocal.value = validator.value.message;
    return;
  }

  if (props.maxSize && valueTarget.length > props.maxSize) {
    value.value = props.modelValue;
    messageLocal.value = `No máximo são ${props.maxSize} caracteres.`;
    return;
  }


  if (props.type === 'integer' || props.type === 'float') {
    const numericValue = Number(valueTarget);

    if (!isNaN(numericValue)) {
      if (props.minValue !== undefined && numericValue < props.minValue) {
        value.value = props.modelValue;
        messageLocal.value = `O valor mínimo permitido é ${props.minValue}.`;
        return;
      }
      if (props.maxValue !== undefined && numericValue > props.maxValue) {
        value.value = props.modelValue;
        messageLocal.value = `O valor máximo permitido é ${props.maxValue}.`;
        return;
      }
    }
  }
  if (props.type === 'float' && props.precicion !== undefined) {
    const subPartes = valueTarget.split(".")
    if (subPartes.length > 1) {
      const decimalPart = subPartes[1];
      if (decimalPart.length > props.precicion) {
        value.value = props.modelValue;
        messageLocal.value = `São permitidas apenas ${props.precicion} casas decimais.`;
        return;
      }
    }
  }

  emit('update:modelValue', valueTarget);
}

</script>

<template>
  <div class="">
    <label v-if="label" :class="[messageDefault !== '' && 'text-red-500']"
      class="block tracking-wide text-gray-700 text-x font-semibold mb-2">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <div :class="['relative flex items-center transition duration-300 text-gray-700']">
      <input v-model="value" @input="updateValue" :type="typeLocal" :placeholder="placeholder" :class="[
        'appearance-none block w-full bg-inputBg  focus:shadow-none focus:ring-1 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white transition duration-300',
        messageDefault !== '' && 'border-red-600 focus:border-red-700 focus:ring-red-500  transition duration-300'
      ]" />
      <p v-if="showMaxSize" :class="['absolute right-0 mr-3', messageDefault != '' && 'text-red-700']">{{ maxSize -
        props.modelValue.length }}</p>
    </div>
    <InputError :message="messageDefault" />
  </div>
</template>