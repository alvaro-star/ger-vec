<script setup lang="ts">
import HeaderModule from '@/components/data-table/HeaderModule.vue'
import HorizontalNavBar from '@/components/data-table/HorizontalNavBar.vue'
import SectionComponent from '@/components/SectionComponent.vue'
import HorizontalBar from '@/plugins/chartjs/HorizontalBar.vue'
import Pie from '@/plugins/chartjs/Pie.vue'
import { ref } from 'vue'
import MarcasGroupSexo from './tables/MarcasGroupSexo.vue'
import MarcasTable from './tables/MarcasTable.vue'
import VeiculosTable from './tables/VeiculosTable.vue'



const marcasGeral = ref<any[]>([])
const marcasGroupFeminino = ref<any[]>([])
const marcasGroupMasculino = ref<any[]>([])

const abaAtual = ref<string>('Todos os Veiculos')
const opcoesAba = ['Todos os Veiculos', 'Marcas', 'Marcas (Agrupados por sexo)']

const selecionado = ref<string>('todos')


const totaisPorGrupo = ref<Record<'Geral' | 'Feminino' | 'Masculino', number>>({
    Geral: 0,
    Feminino: 0,
    Masculino: 0
})

function calcularTotal(lista: any[]): number {
    return lista.reduce((soma, item) => soma + item.total, 0)
}

function escolherTop5(items: any[]) {
    if (items.length <= 5) return items
    const principais = items.slice(0, 5)
    const outros = items.slice(5)
    const totalOutros = outros.reduce((soma, item) => soma + item.total, 0)
    return [...principais, { marca: 'Outros', total: totalOutros }]
}

const updateMarcasGeral = (marcasGeralData: any[]) => {
    marcasGeral.value = escolherTop5(marcasGeralData)
    totaisPorGrupo.value.Geral = calcularTotal(marcasGeralData)
}

const updateMarcasSexo = (groupMasculinos: any[], groupFemininos: any[]) => {
    totaisPorGrupo.value.Feminino = calcularTotal(groupFemininos)
    totaisPorGrupo.value.Masculino = calcularTotal(groupMasculinos)

    marcasGroupFeminino.value = escolherTop5(groupFemininos)
    marcasGroupMasculino.value = escolherTop5(groupMasculinos)
}
</script>

<template>
    <main class="h-[calc(100vh)]">
        <HeaderModule class="mb-7">
            <template #title>
                <h1 class="text-3xl font-bold">Veículos</h1>
            </template>
        </HeaderModule>

        <SectionComponent titulo="Dados Estatisticos" class="container">
            <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4 px-4 py-5">
                <div class="flex flex-col items-center justify-center">
                    <div class="text-xl text-center font-semibold pt-2  max-w-96">
                        <p v-if="selecionado === 'todos'">Qtd de Veiculos agrupado por marca</p>
                        <p v-if="selecionado === 'feminino'">Qtd de Veiculos por marca das Proprietaritarias Femininas
                        </p>
                        <p v-if="selecionado === 'masculino'">Qtd de Veiculos por marca dos Proprietaritarios Masculinos
                        </p>
                    </div>
                    <div class="flex space-x-5 mb-6 w-full justify-center">
                        <label>
                            <input type="radio" v-model="selecionado" value="todos" />
                            Todos
                        </label>
                        <label>
                            <input type="radio" v-model="selecionado" value="feminino" />
                            Feminino
                        </label>
                        <label>
                            <input type="radio" v-model="selecionado" value="masculino" />
                            Masculino
                        </label>
                    </div>

                    <Pie class="w-80" v-if="selecionado === 'todos'" :labels="marcasGeral.map(e => e.marca)"
                        :values="marcasGeral.map(e => e.total)" />

                    <Pie class="w-80" v-if="selecionado === 'feminino'" :labels="marcasGroupFeminino.map(e => e.marca)"
                        :values="marcasGroupFeminino.map(e => e.total)" />

                    <Pie class="w-80" v-if="selecionado === 'masculino'"
                        :labels="marcasGroupMasculino.map(e => e.marca)"
                        :values="marcasGroupMasculino.map(e => e.total)" />
                </div>
                <div class="w-full flex justify-center items-center">
                    <div class="flex flex-col items-center space-y-2 w-full max-w-[500px]">
                        <h3 class="text-lg font-medium">Soma total de veículos por grupo</h3>
                        <HorizontalBar class="w-full h-72" :labels="['Geral', 'Masculino', 'Feminino']"
                            :values="[totaisPorGrupo.Geral, totaisPorGrupo.Masculino, totaisPorGrupo.Feminino]" />
                    </div>
                </div>
            </div>
        </SectionComponent>

        <HorizontalNavBar v-model="abaAtual" :tabs="opcoesAba" class="my-6" />

        <VeiculosTable :show="abaAtual === 'Todos os Veiculos'" />
        <MarcasTable @update-data="updateMarcasGeral" :show="abaAtual === 'Marcas'" />
        <MarcasGroupSexo @update-data="updateMarcasSexo" :show="abaAtual === 'Marcas (Agrupados por sexo)'" />
    </main>
</template>
