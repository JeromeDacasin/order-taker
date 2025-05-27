<template>
  <div>
    <h1>Laravel + Vue Router</h1>
    <router-link to="/home">Go to Home</router-link>
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'App',
};
</script>