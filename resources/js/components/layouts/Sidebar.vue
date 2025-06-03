<template>
  <v-app>
    <v-navigation-drawer app permanent>
      <v-list dense nav>
        <v-list-item v-for="item in navItems" :key="item.title" :to="item.to" router>
          <v-list-item-title>{{ item.title }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<script setup>
const navItems = [
  { title: 'Customers', to: '/customers' },
  { title: 'Skus', to: '/skus' },
  { title: 'Orders', to: '/orders' },
];
</script>
