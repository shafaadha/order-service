<script setup>
import { computed } from "vue";
import Sidebar from "./components/Sidebar.vue";
import { getUser } from "./services/auth";
import { useRoute } from "vue-router";

const user = computed(() => getUser());
const route = useRoute();

const menu = [
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
  <router-view />
</template>
