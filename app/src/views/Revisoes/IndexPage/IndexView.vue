<script setup lang="ts">
import HeaderModule from '@/components/data-table/HeaderModule.vue'
import HorizontalNavBar from '@/components/data-table/HorizontalNavBar.vue'
import PrimaryButton from '@/components/form-components/buttons/PrimaryButton.vue'
import { ref } from 'vue'
import MarcaTable from './tables/MarcaTable.vue'
import NRevisoesByPessoaTable from './tables/NRevisoesByPessoaTable.vue'
import RevisoesTable from './tables/RevisoesTable.vue'
import Pie from '@/plugins/chartjs/Pie.vue'
import SectionComponent from '@/components/SectionComponent.vue'

const abaAtual = ref<string>('Todas as Revisoes')
const opcoesAba = ['Todas as Revisoes', 'Marcas (N Revisoes)', 'Pessoas (Agrupados por N Revisões)']

const marcasGeral = ref<any[]>([])

function escolherTop5(items: any[]) {
    if (items.length <= 5) return items
    const principais = items.slice(0, 5)
    const outros = items.slice(5)
    const totalOutros = outros.reduce((soma, item) => soma + parseInt(item.total), 0)


    return [...principais, { marca: 'Outros', total: totalOutros }]
}

const updateMarcasGeral = (marcasGeralData: any[]) => {
    marcasGeral.value = escolherTop5(marcasGeralData)
    console.log(marcasGeral.value);
}

</script>

<template>
    <main class="h-[calc(100vh-56px)]">
        <HeaderModule class="mb-7">
            <template #title>
                <h1 class="text-3xl font-bold">Revisões</h1>
            </template>
            <template #actions>
                <router-link :to="{ name: 'revisoes.create' }">
                    <PrimaryButton label="Cadastrar Revisão" />
                </router-link>
            </template>
        </HeaderModule>

        <SectionComponent titulo="Dados Estatisticos" class="container">
            <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4 px-4 py-5">
                <div class="flex flex-col items-center justify-center">
                    <div class="text-xl text-center font-semibold pt-2  max-w-96">
                        <p>Qtd de Revisoes por Marca</p>
                    </div>

                    <Pie class="w-80" :labels="marcasGeral.map(e => e.marca)" :values="marcasGeral.map(e => e.total)" />
                </div>

            </div>
        </SectionComponent>

        <HorizontalNavBar v-model="abaAtual" :tabs="opcoesAba" class="my-6" />
        <RevisoesTable :show="abaAtual === 'Todas as Revisoes'" />
        <MarcaTable @update-data="updateMarcasGeral" :show="abaAtual === 'Marcas (N Revisoes)'" />
        <NRevisoesByPessoaTable :show="abaAtual === 'Pessoas (Agrupados por N Revisões)'" />
    </main>
</template>
