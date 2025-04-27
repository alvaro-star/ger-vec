<script setup lang="ts">
import SectionComponent from '@/components/SectionComponent.vue';
import type { Swiper as SwiperClass } from 'swiper';
import 'swiper/css';
import { ref } from 'vue';
import BarBySexo from './BarBySexo.vue';


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
    <SectionComponent titulo="Dados Estatistícos" class="container">
        <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-4 px-4 py-10">
            <BarBySexo />
        </div>
    </SectionComponent>
</template>
