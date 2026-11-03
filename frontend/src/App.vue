<script setup>
import { computed } from "vue";
import Sidebar from "./components/Sidebar.vue";
import { getUser } from "./services/auth";

const user = computed(() => getUser());

const menu = [
  {
    name: "Dashboard",
    to: "/admin/dashboard",
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
    to: "/admin/orders",
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
