<script setup lang="ts">
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Pie } from 'vue-chartjs'
import { computed } from 'vue'
import defaultColors from './defaultColors';
import ChartDataLabels from 'chartjs-plugin-datalabels'

const props = defineProps<{
  title?: string
  subtitle?: string
  labels: string[]
  values: number[]
  colors?: string[]
  options?: object
}>()


const backgroundColors = computed(() =>
  props.colors && props.colors.length === props.values.length
    ? props.colors
    : defaultColors.slice(0, props.values.length)
)
const data = computed(() => ({
  labels: props.labels,
  datasets: [
    {
      backgroundColor: backgroundColors.value,
      data: props.values,
    },
  ],
}))

const defaultOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    datalabels: {
      display: true,
      anchor: 'center',
      align: 'center',
      color: 'white',
      font: {
        weight: 'bold',
        size: 12
      },
      formatter: (value: number) => value.toLocaleString()
    }
  },
}

const chartOptions = computed(() => {
  return props.options ?? defaultOptions
})


ChartJS.register(ArcElement, Tooltip, Legend, ChartDataLabels)
</script>

<template>
  <div class="flex items-center flex-col">
    <header v-if="title" class="text-xl mb-5 font-semibold">{{ title }}</header>
    <div class="chart-container">
      <Pie :data="data" :options="chartOptions" />
    </div>
    <footer v-if="subtitle" class="font-semibold text-sm">
      {{ subtitle }}
    </footer>
  </div>
</template>
