<script setup lang="ts">
import type { ISort } from '@/types/IFilter';
import { ref, defineProps, defineEmits, watch, onMounted } from 'vue';


const props = defineProps<{
    label: string;
    sort: ISort;
}>();

const emit = defineEmits<{
    (e: 'updateSort', value: string): void;
}>();

const selectedValue = ref<string>('');

// Emite valor sempre que mudar
watch(selectedValue, (newValue) => {
    emit('updateSort', newValue);
});

// Gera um id único por opção
const generateUniqueId = (label: string, value: string) => {
    return `${label}-sort-${value}`;
};

onMounted(() => {
    selectedValue.value = props.sort.value || '';
});
</script>

<template>
    <section>
        <h6 class="mb-3 text-base font-medium text-gray-900">
            {{ props.label }}
        </h6>

        <ul class="space-y-2 text-sm" aria-labelledby="sortDropdownButton">
            <!-- Opção "Nenhum" -->
            <li class="flex items-center">
                <input :id="generateUniqueId(props.label, 'none')" type="radio" value="" v-model="selectedValue"
                    class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500" />
                <label :for="generateUniqueId(props.label, 'none')" class="ml-2 text-sm font-medium text-gray-900">
                    Nenhum
                </label>
            </li>

            <!-- Demais opções -->
            <li v-for="option in props.sort.options" :key="option" class="flex items-center">
                <input :id="generateUniqueId(props.label, option)" type="radio" :value="option" v-model="selectedValue"
                    class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500" />
                <label :for="generateUniqueId(props.label, option)" class="ml-2 text-sm font-medium text-gray-900">
                    {{ option }}
                </label>
            </li>
        </ul>
    </section>
</template>
