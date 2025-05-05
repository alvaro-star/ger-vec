<script setup lang="ts">
import selectTop5 from '@/helpers/functions/selectTop5';
import api from '@/plugins/api';
import Pie from '@/plugins/chartjs/Pie.vue';
import { Swiper, SwiperSlide } from 'swiper/vue';

import 'swiper/css';
import 'swiper/css/scrollbar';
import 'swiper/css/pagination';
import { Navigation, Pagination, Scrollbar, A11y } from 'swiper/modules';

import { onMounted, ref } from 'vue';

const paises = ref<any[]>([]);
const nVeiculos = ref<any[]>([]);
const nRevisoes = ref<any[]>([]);

const fetchAll = () => {
    api.get('/marcas/statistics/group_pais')
        .then(({ data }) => {
            const top5 = selectTop5(data, 'pais', 'total');
            paises.value = [...top5];
        });
    api.get('/marcas/statistics/n_veiculos')
        .then(({ data }) => {
            const top5 = selectTop5(data, 'marca', 'total');
            nVeiculos.value = [...top5];
        });
    api.get('/marcas/statistics/n_revisoes')
        .then(({ data }) => {
            (data as any[]).forEach((e: any) => e.total = parseInt(e.total));
            const top5 = selectTop5(data, 'marca', 'total');
            nRevisoes.value = [...top5];
        });
};

onMounted(() => {
    fetchAll();
});
</script>

<template>
    <div class="w-full">
        <div class="flex flex-col md:flex-row">
            <div class="w-full md:w-1/2 p-8 flex items-center justify-center">
                <Pie title="Qnt de Marcas por país" class="w-80" :labels="paises.map(e => e.pais)"
                    :values="paises.map(e => e.total)" />
            </div>
            <Swiper class="w-full md:w-1/2" :loop="true" pagination :modules="[Navigation, Pagination]">
                <SwiperSlide>
                    <div class="p-8 flex items-center justify-center">
                        <Pie title="Qnt de Veículos por Marca" class="w-80" :labels="nVeiculos.map(e => e.marca)"
                            :values="nVeiculos.map(e => e.total)" />
                    </div>
                </SwiperSlide>
                <SwiperSlide>
                    <div class="p-8 flex items-center justify-center">
                        <Pie title="Qnt de Revisões por Marca" class="w-80" :labels="nRevisoes.map(e => e.marca)"
                            :values="nRevisoes.map(e => e.total)" />
                    </div>
                </SwiperSlide>
            </Swiper>
        </div>
    </div>
</template>
