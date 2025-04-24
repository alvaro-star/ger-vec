<script setup lang="ts">
import api from '@/plugins/api';
import HorizontalBar from '@/plugins/chartjs/HorizontalBar.vue';
import { onMounted, ref } from 'vue';

const nVeiculosBySexo = ref<{ label: string, total: number }[]>([]);

const fetchCountBySex = async () => {
    try {
        const { data } = await api.get('/pessoas/statistics/sexo/n_veiculos')
        let dataFormat = data.map((item: any) => ({
            label: item.is_masculino ? 'Masculino' : 'Feminino',
            total: item.total
        }))
        nVeiculosBySexo.value = dataFormat
    } catch (error) {
        console.error('Erro ao buscar dados:', error)
    }
}
onMounted(() => {
    fetchCountBySex();
})
</script>

<template>
    <div class="">
        <h2 class="text-xl font-semibold">
            Qtd de veiculos por sexo
        </h2>
        <div class="">
            <HorizontalBar :labels="nVeiculosBySexo.map(e => (e.label))" :values="nVeiculosBySexo.map(e => e.total)" />
        </div>
    </div>
</template>