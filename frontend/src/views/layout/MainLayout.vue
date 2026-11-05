<script setup lang="ts">
import Sidebar from '../../components/Sidebar.vue';
import { computed } from "vue";
import { getUser } from '../../services/auth.js';

const user = computed(() => getUser());

const menu = [
    {
        name: "Dashboard",
        to: "/",
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

</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <Sidebar :menu="menu" :user="user" />

        <main class="min-h-screen md:ml-64">
            <div class="max-w-7xl mx-auto p-5 md:p-6">
                <router-view />
            </div>
        </main>
    </div>
</template>