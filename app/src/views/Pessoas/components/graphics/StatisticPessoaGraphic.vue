<script setup lang="ts">
import { computed, defineProps, ref } from 'vue';
import SectionComponent from '@/components/SectionComponent.vue';
import type IStatisticPessoa from '@/types/IStatisticPessoa';
import HorizontalBar from '@/plugins/chartjs/HorizontalBar.vue';
import CampoShow from '@/components/form-components/CampoShow.vue';
import SpinerAnimation from '@/components/animation/SpinerAnimation.vue';

const props = defineProps<{
    sexo: string[]
    loading: boolean;
    statistics: Record<string, IStatisticPessoa>;
}>();

const campoSelecionado = ref<'idade' | 'n_revisoes' | 'n_veiculos'>('idade');
const campoLabel = computed(() => {
    switch (campoSelecionado.value) {
        case 'idade': return 'Média de Idade';
        case 'n_veiculos': return 'Média de Veículos';
        case 'n_revisoes': return 'Média de Revisões';
    }
});

const sexoChave = computed(() => {
    const selected = props.sexo?.[0] || '';
    const firstChar = selected.charAt(0).toUpperCase();
    switch (firstChar) {
        case 'M': return 'M';
        case 'F': return 'F';
        case 'A': return 'Ambos';
        default: return 'Ambos';
    }
});
const temEstatisticas = computed(() => {
    const chaves = Object.keys(props.statistics).sort();
    return JSON.stringify(chaves) === JSON.stringify(['Ambos', 'F', 'M']);
});
</script>


<template>
    <SectionComponent titulo="Dados Estatistícos" v-if="temEstatisticas" class="container">
        <div v-if="loading" class="flex justify-center items-center h-60">
            <SpinerAnimation />
        </div>
        <div v-else class="w-full grid grid-cols-1 md:grid-cols-2 gap-4 px-4 py-10">
            <div>
                <p class="text-center w-full text-lg font-semibold mt-2">Qtd de Individuos no Sistema</p>
                <HorizontalBar :labels="['Masculino', 'Feminino']"
                    :values="[statistics['M'].n_elementos, statistics['F'].n_elementos]" />
                <p class="text-center w-full text-xs font-semibold mt-2">Quantidade de Individuos ({{
                    statistics['Ambos'].n_elementos }})</p>
            </div>
            <div class="w-full flex justify-center">
                <div class="space-y-2 max-w-[500px] w-full">
                    <h3 class="w-full text-center font-semibold text-lg">Média, mínimo e maximo</h3>
                    <div class="flex justify-center items-center space-x-4">
                        <label class="mr-4">
                            <input type="radio" value="idade" v-model="campoSelecionado" />
                            Idade
                        </label>
                        <label class="mr-4">
                            <input type="radio" value="n_veiculos" v-model="campoSelecionado" />
                            Nº Veículos
                        </label>
                        <label>
                            <input type="radio" value="n_revisoes" v-model="campoSelecionado" />
                            Nº Revisões
                        </label>
                    </div>
                    <div>
                        <div v-if="campoSelecionado === 'idade'" class="-space-y-7">
                            <CampoShow titulo="Geral" class="flex flex-row justify-between">
                                <p class="font-normal">
                                    Média: {{ statistics['Ambos'].media_idades }} (min: {{ statistics['Ambos'].min_idade
                                    }},
                                    max:
                                    {{ statistics['Ambos'].max_idade }}) anos
                                </p>
                            </CampoShow>
                            <CampoShow titulo="Homens" class="flex flex-row justify-between">
                                <p class="font-normal">
                                    Média: {{ statistics['M'].media_idades }} (min: {{ statistics['M'].min_idade }},
                                    max: {{
                                        statistics['M'].max_idade }}) anos
                                </p>
                            </CampoShow>
                            <CampoShow titulo="Mulheres" class="flex flex-row justify-between">
                                <p class="font-normal">
                                    Média: {{ statistics['F'].media_idades }} (min: {{ statistics['F'].min_idade }},
                                    max: {{
                                        statistics['F'].max_idade }}) anos
                                </p>
                            </CampoShow>
                        </div>

                        <div v-if="campoSelecionado === 'n_veiculos'" class="-space-y-7">
                            <CampoShow titulo="Geral" class="flex flex-row justify-between">
                                <p class="font-normal">
                                    Média: {{ statistics['Ambos'].media_veiculos }} (min: {{
                                        statistics['Ambos'].min_veiculos
                                    }},
                                    max: {{ statistics['Ambos'].max_veiculos }}) veículos
                                </p>
                            </CampoShow>
                            <CampoShow titulo="Homens" class="flex flex-row justify-between">
                                <p class="font-normal">
                                    Média{{ statistics['M'].media_veiculos }} (min: {{ statistics['M'].min_veiculos }},
                                    max:
                                    {{
                                        statistics['M'].max_veiculos }}) veículos
                                </p>
                            </CampoShow>
                            <CampoShow titulo="Mulheres" class="flex flex-row justify-between">
                                <p class="font-normal">
                                    Média {{ statistics['F'].media_veiculos }} (min: {{ statistics['F'].min_veiculos }},
                                    max:
                                    {{
                                        statistics['F'].max_veiculos }}) veículos
                                </p>
                            </CampoShow>
                        </div>

                        <div v-if="campoSelecionado === 'n_revisoes'" class="-space-y-7">
                            <CampoShow titulo="Geral" class="flex flex-row justify-between">
                                <p class="font-normal">
                                    Média {{ statistics['Ambos'].media_revisoes }} (min: {{
                                        statistics['Ambos'].min_revisoes
                                    }},
                                    max: {{ statistics['Ambos'].max_revisoes }}) revisões
                                </p>
                            </CampoShow>
                            <CampoShow titulo="Homens" class="flex flex-row justify-between">
                                <p class="font-normal">
                                    Média {{ statistics['M'].media_revisoes }} (min: {{ statistics['M'].min_revisoes }},
                                    max:
                                    {{
                                        statistics['M'].max_revisoes }}) revisões
                                </p>
                            </CampoShow>
                            <CampoShow titulo="Mulheres" class="flex flex-row justify-between">
                                <p class="font-normal">
                                    Média {{ statistics['F'].media_revisoes }} (min: {{ statistics['F'].min_revisoes }},
                                    max:
                                    {{
                                        statistics['F'].max_revisoes }}) revisões
                                </p>
                            </CampoShow>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </SectionComponent>
</template>
