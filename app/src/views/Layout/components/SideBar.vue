<script setup lang="ts">
import { RouterLink } from 'vue-router';
import ApplicationLogo from './ApplicationLogo.vue';
import PeapleIcon from './icons/PeapleIcon.vue';
import RevistaIcon from './icons/RevistaIcon.vue';
import HomeIcon from './icons/HomeIcon.vue';
import CarIcon from './icons/CarIcon.vue';
import MarcasIcon from './icons/MarcasIcon.vue';
defineProps<{
    isSidebarOpen: boolean;
}>();



const routes = [
    { icon: HomeIcon, name: 'home', title: 'Início', path: '/' },
    { icon: PeapleIcon, name: 'pessoas.index', title: 'Clientes', path: '/pessoa' },
    { icon: CarIcon, name: 'veiculos.index', title: 'Veículo', path: '/veiculo' },
    { icon: RevistaIcon, name: 'revisoes.index', title: 'Revisão', path: '/revisao' },
    { icon: MarcasIcon, name: 'marcas.index', title: 'Marcas', path: '/marca' },
];
const emit = defineEmits(['toggleSidebar']);

const toggleSidebar = () => {
    emit('toggleSidebar');
}
</script>

<template>
    <!-- Sidebar -->
    <aside :class="[
        'fixed top-0 left-0 z-40 h-screen bg-customBackground transition-all duration-300',
        isSidebarOpen ? 'w-64' : 'w-20',
    ]" aria-label="Sidebar" id="default-sidebar">
        <div class="h-full flex flex-col">
            <div :class="['flex items-center px-4 py-4', !isSidebarOpen ? 'justify-center' : 'justify-between']">
                <!-- Logo -->
                <RouterLink to="/" class="flex items-center">
                    <!-- Texto completo quando a sidebar está aberta -->
                    <ApplicationLogo v-if="isSidebarOpen" class="text-white" />
                </RouterLink>

                <!-- Ícone de hambúrguer para abrir/fechar a Sidebar -->
                <button @click="toggleSidebar" class="text-white focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="20" viewBox="0 0 28 20" fill="none">
                        <g clip-path="url(#clip0_223_70)">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M1.33327 0C1.05037 0 0.77906 0.117063 0.579021 0.325437C0.378982 0.533811 0.266602 0.816426 0.266602 1.11111C0.266602 1.4058 0.378982 1.68841 0.579021 1.89679C0.77906 2.10516 1.05037 2.22222 1.33327 2.22222H26.9333C27.2162 2.22222 27.4875 2.10516 27.6875 1.89679C27.8876 1.68841 27.9999 1.4058 27.9999 1.11111C27.9999 0.816426 27.8876 0.533811 27.6875 0.325437C27.4875 0.117063 27.2162 0 26.9333 0H1.33327ZM0.266602 10C0.266602 9.70532 0.378982 9.4227 0.579021 9.21433C0.77906 9.00595 1.05037 8.88889 1.33327 8.88889H26.9333C27.2162 8.88889 27.4875 9.00595 27.6875 9.21433C27.8876 9.4227 27.9999 9.70532 27.9999 10C27.9999 10.2947 27.8876 10.5773 27.6875 10.7857C27.4875 10.994 27.2162 11.1111 26.9333 11.1111H1.33327C1.05037 11.1111 0.77906 10.994 0.579021 10.7857C0.378982 10.5773 0.266602 10.2947 0.266602 10ZM0.266602 18.8889C0.266602 18.5942 0.378982 18.3116 0.579021 18.1032C0.77906 17.8948 1.05037 17.7778 1.33327 17.7778H26.9333C27.2162 17.7778 27.4875 17.8948 27.6875 18.1032C27.8876 18.3116 27.9999 18.5942 27.9999 18.8889C27.9999 19.1836 27.8876 19.4662 27.6875 19.6746C27.4875 19.8829 27.2162 20 26.9333 20H1.33327C1.05037 20 0.77906 19.8829 0.579021 19.6746C0.378982 19.4662 0.266602 19.1836 0.266602 18.8889Z"
                                fill="white" />
                        </g>
                        <defs>
                            <clipPath id="clip0_223_70">
                                <rect width="27.7333" height="20" fill="white" transform="translate(0.266602)" />
                            </clipPath>
                        </defs>
                    </svg>
                </button>
            </div>

            <!-- Linha de separação -->
            <div class="border-b border-gray-600 ml-4 mr-4"></div>

            <!-- Conteúdo da Sidebar -->
            <div class="flex-1 px-3 py-4 overflow-y-auto">
                <ul class="space-y-2 font-medium">
                    <!-- Itens do menu -->
                    <li v-for="route in routes" :key="route.name">
                        <router-link :to="{ name: route.name }"
                            :class="['flex items-center p-2 text-white font-normal rounded-sm hover:bg-customHover group', !isSidebarOpen ? 'justify-center' : '']">
                            <component v-if="route.icon" :is="route.icon" />
                            <span class="ml-3" v-if="isSidebarOpen">{{ route.title }}</span>
                        </router-link>
                    </li>
                </ul>
            </div>
        </div>
    </aside>
</template>



<style scoped>
.bg-customBackground {
    background-color: #171717;
}

.hover\:bg-customHover:hover {
    background-color: #C81E1E;
}

ul>li>.router-link-exact-active {
    background-color: #C81E1E;
}
</style>
