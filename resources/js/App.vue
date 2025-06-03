<template>
  <component :is="isLoginPage ? 'router-view' : Sidebar" />
</template>

<script setup>
import { useRoute } from 'vue-router';
import { computed } from 'vue';
import Sidebar from './components/layouts/Sidebar.vue'
const route = useRoute();

const isLoginPage = computed(() => route.path === '/login');
</script>

<style>
html, body, #app {
  height: 100%;
  margin: 0;
}
</style>
