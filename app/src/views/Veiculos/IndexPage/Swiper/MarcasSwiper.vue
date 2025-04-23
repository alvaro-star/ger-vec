<script setup lang="ts">
import api from '@/plugins/api'
import HorizontalBar from '@/plugins/chartjs/HorizontalBar.vue'
import { onMounted, ref } from 'vue'

type MarcaData = { label: string; total: number }

const marcasData = ref<MarcaData[]>([])
const topMarcas = ref<MarcaData[]>([])

const fetchMarcasData = async () => {
    try {
        const { data } = await api.get('/veiculos/statistics/marcas/n_veiculos')
        const totalGeral = data.reduce((acc: number, item: any) => acc + item.total, 0)

        const formatted = data.map((item: any) => {
            const percentual = ((item.total / totalGeral) * 100).toFixed(1)
            return {
                label: `${item.marca} (${percentual}%)`,
                total: item.total
            }
        })

        marcasData.value = formatted
        topMarcas.value = formatted.slice(0, 7)
    } catch (err) {
        console.error('Erro ao carregar marcas:', err)
    }
}

onMounted(() => {
    fetchMarcasData()
})
</script>


<template>
    <section class="py-10 h-[280px] overflow-y-auto">
        <div class="">
            <div class="">
                <h2 class="text-lg font-semibold mb-4 text-center">Qtd de veículo por marca</h2>
                <HorizontalBar class="h-64" :labels="topMarcas.map(e => e.label)"
                    :values="topMarcas.map(e => e.total)" />
            </div>

            <div class="mt-10">
                <h2 class="text-lg font-semibold mb-4 text-center">Lista completa</h2>
                <ul class="max-h-[400px] overflow-y-auto px-4">
                    <li v-for="(marca, index) in marcasData" :key="index" class="flex justify-between py-1 border-b">
                        <span>{{ marca.label }}</span>
                        <span class="font-semibold">{{ marca.total }}</span>
                    </li>
                </ul>
            </div>
        </div>

    </section>
</template>