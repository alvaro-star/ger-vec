<script setup lang="ts">
import { shallowRef } from 'vue';
import { RouterView } from 'vue-router';
import Header from './components/Header.vue';
import SideBar from './components/SideBar.vue';

const isSidebarOpen = shallowRef(false);
const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};
</script>

<template>
    <div :class="{ 'sidebar-open': isSidebarOpen, 'sidebar-closed': !isSidebarOpen }">
        <SideBar :isSidebarOpen="isSidebarOpen" @toggle-sidebar="toggleSidebar"
            class="fixed top-0 left-0 h-full z-10" />

        <!-- Main Content -->
        <main :class="{ 'expanded-main': isSidebarOpen, 'collapsed-main': !isSidebarOpen }" class=" bg-mainBackground">
            <RouterView />
        </main>
    </div>

</template>


<style scoped>
.bg-mainBackground {
    background-color: #F9FBFE;
}


.sidebar-open .expanded-main {
    margin-left: 16rem;
}

.sidebar-closed .collapsed-main {
    margin-left: 5rem;
}
</style>