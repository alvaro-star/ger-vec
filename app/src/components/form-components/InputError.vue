<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  message?: string | number | null | false;
}>();

const shouldShowMessage = computed(() => {
  return props.message !== undefined && props.message !== null && props.message !== false;
});
</script>

<template>
  <div>
    <p v-if="shouldShowMessage" class="text-red-500 text-sm mt-1">
      {{ props.message || 'Campo é obrigatório' }}
    </p>
  </div>
</template>
