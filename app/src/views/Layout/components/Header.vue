<template>
    <!-- Header -->
    <header
        class="bg-white h-auto pt-4 pb-4 pr-2 flex items-center justify-end mx-auto max-w-screen border-b border-gray-300 z-20">
        <button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar"
            aria-controls="default-sidebar" type="button"
            class="inline-flex items-center mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path clip-rule="evenodd" fill-rule="evenodd"
                    d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                </path>
            </svg>
        </button>

        <div class="flex items-center space-x-2 sm:mr-10">
            <button class="bg-customHover text-2xl text-white rounded h-10 w-10 flex items-center justify-center">
                A
            </button>
        </div>
    </header>
</template>

<style scoped>
.bg-mainBackground {
    background-color: #F9FBFE;
}

.bg-customBackground {
    background-color: #171717;
}

.hover\:bg-customHover:hover {
    background-color: #C81E1E;
}
</style>



<script setup>
</script>