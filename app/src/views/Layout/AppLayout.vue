<script setup lang="ts">
import { RouterView } from 'vue-router';
import Footer from './components/Footer.vue';
import Header from './components/Header.vue';
import { shallowRef } from 'vue';

const routes = shallowRef([
    { name: 'home', title: 'Home', path: '/' },
    { name: 'veiculo.index', title: 'Veículo', path: '/veiculo' },
    { name: 'revisao.index', title: 'Revisão', path: '/revisao' },
    { name: 'pessoa.index', title: 'Pessoa', path: '/pessoa' }
])
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <Header :routes="routes" />
        <main class="w-full">
            <RouterView />
        </main>
    
    </div>
</template>
