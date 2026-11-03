<script setup>
import { useRoute } from "vue-router";
const route = useRoute();
</script>

<template>
  <div class="min-h-screen w-full bg-gray-100">
    <main class="min-h-screen w-full bg-gray-100">
      <router-view />
    </main>
  </div>
</template>
