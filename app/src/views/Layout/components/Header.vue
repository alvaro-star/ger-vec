<script setup lang="ts">
import IconHamburguer from '@/components/icons/IconHamburguer.vue';
import type IRouterLink from '@/types/IRouterLink';
import { ref, shallowRef } from 'vue';

const showMenu = ref(false)

defineProps<{
    routes: IRouterLink[]
}>()


</script>

<template>
    <header class="bg-blue-600 text-white p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">Meu Site</h1>
        <button class="md:hidden p-2" @click="showMenu = !showMenu" aria-label="Abrir menu">
            <IconHamburguer />
        </button>

        <nav class="hidden md:flex gap-4">
            <RouterLink v-for="route in routes" :key="route.name" :to="route.path" class="hover:underline">
                {{ route.title }}
            </RouterLink>
        </nav>
    </header>

    <div v-if="showMenu" class="md:hidden bg-blue-500 text-white p-4">
        <nav class="flex flex-col gap-2">
            <RouterLink v-for="route in routes" :key="route.name" :to="route.path" class="hover:underline"
                @click="showMenu = false">
                {{ route.title }}
            </RouterLink>
        </nav>
    </div>
</template>