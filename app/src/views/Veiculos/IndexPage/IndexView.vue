<script setup lang="ts">
import HeaderModule from '@/components/data-table/HeaderModule.vue'
import HorizontalNavBar from '@/components/data-table/HorizontalNavBar.vue'
import SectionComponent from '@/components/SectionComponent.vue'
import HorizontalBar from '@/plugins/chartjs/HorizontalBar.vue'
import Pie from '@/plugins/chartjs/Pie.vue'
import { ref } from 'vue'
import MarcasTable from './tables/MarcasTable.vue'
import VeiculosTable from './tables/VeiculosTable.vue'
import selectTop5 from '@/helpers/functions/selectTop5'

const marcasGeral = ref<any[]>([])
const marcasGroupFeminino = ref<any[]>([])
const marcasGroupMasculino = ref<any[]>([])

const abaAtual = ref<string>('Todos os Veículos')
const opcoesAba = ['Todos os Veículos', 'Marcas']
const selecionado = ref<string>('todos')

const totaisPorGrupo = ref<Record<'Geral' | 'Feminino' | 'Masculino', number>>({
    Geral: 0,
    Feminino: 0,
    Masculino: 0
})

function calcularTotal(lista: any[]): number {
    return lista.reduce((soma, item) => soma + item.total, 0)
}



const updateData = (marcasGeralData: any[], groupMasculinos: any[], groupFemininos: any[]) => {
    marcasGeral.value = selectTop5(marcasGeralData, 'marca', 'total')
    totaisPorGrupo.value.Geral = calcularTotal(marcasGeralData)
    totaisPorGrupo.value.Feminino = calcularTotal(groupFemininos)
    totaisPorGrupo.value.Masculino = calcularTotal(groupMasculinos)

    marcasGroupFeminino.value = selectTop5(groupFemininos, 'marca', 'total')
    marcasGroupMasculino.value = selectTop5(groupMasculinos, 'marca', 'total')
}

</script>

<template>
    <HeaderModule class="mb-7">
        <template #title>
            <h1 class="text-3xl font-bold">Veículos</h1>
        </template>
    </HeaderModule>
    <main class="min-min-h-[calc(100vh)] pb-10">
        <SectionComponent titulo="Dados Estatistícos" class="container">
            <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4 px-4 py-5">
                <div class="flex flex-col items-center justify-center">
                    <div class="text-xl text-center font-semibold pt-2  max-w-96">
                        <p v-if="selecionado === 'todos'">Qtd de Veículos agrupado por marca</p>
                        <p v-if="selecionado === 'feminino'">Qtd de Veículos por marca das Proprietáritarias Femininas
                        </p>
                        <p v-if="selecionado === 'masculino'">Qtd de Veículos por marca dos Proprietáritarios Masculinos
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

        <VeiculosTable :show="abaAtual === 'Todos os Veículos'" />
        <MarcasTable @update-data="updateData" :show="abaAtual === 'Marcas'" />
    </main>
</template>
