<script setup lang="ts">
import SectionComponent from '@/components/SectionComponent.vue';
import type { Swiper as SwiperClass } from 'swiper';
import 'swiper/css';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { ref } from 'vue';
import BarBySexo from './BarBySexo.vue';
import MarcasSwiper from './MarcasSwiper.vue';


const swiperInstance = ref<SwiperClass | null>(null)

const onSwiper = (swiper: SwiperClass) => {
    swiperInstance.value = swiper
}

const nextSlide = () => {
    swiperInstance.value?.slideNext()
}

const prevSlide = () => {
    swiperInstance.value?.slidePrev()
}
</script>


<template>
    <SectionComponent titulo="Dados Estatisticos" class="container">
        <Swiper :slides-per-view="1" :loop="true" @swiper="onSwiper" class="mx-auto">
            <SwiperSlide>
                <BarBySexo class="h-[400px]" />
            </SwiperSlide>
            <SwiperSlide>
                <MarcasSwiper class="h-[400px]" />
            </SwiperSlide>
        </Swiper>
    </SectionComponent>
</template>
