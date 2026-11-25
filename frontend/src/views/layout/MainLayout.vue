<script setup lang="ts">
import Sidebar from '../../components/Sidebar.vue';
import { computed, ref } from "vue";
import { getUser } from '../../services/auth.js';
import { faBars } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useRoute } from 'vue-router';
import { initials } from '../../utils/format.js';

const user = computed(() => getUser());
const isSidebarOpen = ref(false)
const route = useRoute();

const menu = [
    {
        name: "Dashboard",
        to: "/dashboard",
    },
    {
        name: "Products",
        to: "/products",
    },
    {
        name: "Customers",
        to: "/customers",
    },
    {
        name: "Orders",
        to: "/orders",
    },
];

const pageTitle = computed(() => {
    const currentMenu = menu.find(item => item.to === route.path);

    return currentMenu?.name || "Dashboard";
});

</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <Sidebar :menu="menu" :user="user" :is-open="isSidebarOpen" @close="isSidebarOpen = false" />

        <main class="min-h-screen md:ml-64">
            <div class="sticky top-0 z-30 flex items-center gap-3 
                        bg-white p-4 shadow-sm md:hidden justify-between">
                <div class="flex flex-row gap-4 ">
                    <button @click="isSidebarOpen = true">
                        <FontAwesomeIcon :icon="faBars" />
                    </button>

                    <span class="font-semibold">{{ pageTitle }}</span>
                </div>
                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    {{ initials(user.name) }}
                </div>
            </div>
            <div class="max-w-7xl mx-auto p-5 md:p-6">
                <router-view />
            </div>
        </main>
    </div>
</template>