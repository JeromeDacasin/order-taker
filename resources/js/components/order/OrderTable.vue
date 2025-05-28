<template>
  <div class="data-table-container">
    <div class="d-flex justify-space-between align-center mb-4">
      <h2>Order List</h2>
    </div>

    <v-data-table-server
      v-model:items-per-page="itemsPerPage"
      v-model:page="currentPage"
      :headers="headers"
      :items="orders.data"
      :items-length="orders.meta.total"
      :loading="loading"
      item-value="id"
      class="elevation-1"
      @update:options="loadItems"
    >
      <template #item.amount_due="{ item }">
        ₱{{ Number(item.amount_due).toLocaleString() }}
      </template>

      <template #item.actions="{ item }">
        <v-btn
          icon
          variant="text"
          color="primary"
          size="small"
          @click="$emit('edit', item)"
        >
          <v-icon>mdi-pencil</v-icon>
        </v-btn>
        <v-btn
          icon
          variant="text"
          color="error"
          size="small"
          @click="$emit('delete', item.id)"
          class="ml-2"
        >
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </template>

      <template #no-data>
        <div class="py-8 text-center text-grey">
          No orders found.
        </div>
      </template>
    </v-data-table-server>

    <div class="d-flex justify-center mt-4">
      <v-pagination
        v-model="currentPage"
        :length="Math.ceil(orders.meta.total / itemsPerPage)"
        :total-visible="7"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    orders: {
        type: Object,
        required: true,
        default: () => ({ data: [], meta: {} }),
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:page', 'update:items-per-page', 'update:sort', 'edit', 'delete']);

const currentPage = ref(1);
const itemsPerPage = ref(10);

const headers = [
  { title: 'Customer', key: 'customer', sortable: true },
  { title: 'Delivery Date', key: 'delivery_date', sortable: true },
  { title: 'Status', key: 'status', sortable: true },
  { title: 'Amount Due', key: 'amount_due', sortable: true },
  { title: 'Actions', key: 'actions', align: 'end', sortable: false },
];

const loadItems = ({ page, itemsPerPage, sortBy }) => {
  emit('update:page', page);
  emit('update:items-per-page', itemsPerPage);

  if (sortBy.length > 0) {
    const sortKey = sortBy[0].key;
    const sortOrder = sortBy[0].order;
    emit('update:sort', { sortKey, sortOrder });
  }
};

watch(() => props.orders.meta.current_page, (newPage) => {
  if (newPage !== currentPage.value) {
    currentPage.value = newPage;
  }
});

watch(() => props.orders.meta.per_page, (newPerPage) => {
  if (newPerPage !== itemsPerPage.value) {
    itemsPerPage.value = newPerPage;
  }
});
</script>

<style scoped>
.data-table-container {
  background: white;
  border-radius: 8px;
  padding: 20px;
}
</style>
