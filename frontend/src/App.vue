<script setup>
import { useRoute } from "vue-router";
const route = useRoute();
</script>

<template>
  <div class="flex min-h-screen flex-col bg-gray-100">
    <main class="flex-1 bg-gray-100">
      <router-view />
    </main>
  </div>
</template>
