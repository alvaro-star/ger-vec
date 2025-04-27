<script setup lang="ts">
import {
    Chart as ChartJS,
    BarElement,
    Tooltip,
    Legend,
    CategoryScale,
    LinearScale
} from 'chart.js'
import { Bar } from 'vue-chartjs'
import { computed } from 'vue'
import ChartDataLabels from 'chartjs-plugin-datalabels'
import defaultColors from './defaultColors'

const props = defineProps<{
    labels: string[],
    values: number[],
    colors?: string[],
    options?: object
}>()

ChartJS.register(BarElement, Tooltip, Legend, CategoryScale, LinearScale, ChartDataLabels)

const backgroundColors = computed(() =>
    props.colors && props.colors.length === props.values.length
        ? props.colors
        : defaultColors.slice(0, props.values.length)
)

const data = computed(() => ({
    labels: props.labels,
    datasets: [
        {
            label: '',
            backgroundColor: backgroundColors.value,
            data: props.values
        }
    ]
}))

const defaultOptions = {
    indexAxis: 'y',
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
    scales: {
        x: {
            beginAtZero: true
        }
    }
}


const chartOptions = computed(() => {
    return props.options ?? defaultOptions
})
</script>

<template>
    <div class="chart-container" style="position: relative; height: 300px;">
        <Bar :data="data" :options="chartOptions" />
    </div>
</template>
