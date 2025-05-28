<template>
  <div class="data-table-container">
    <!-- <div class="d-flex justify-space-between align-center mb-4">
    </div> -->

    <v-data-table-server
      v-model:items-per-page="itemsPerPage"
      v-model:page="currentPage"
      :headers="headers"
      :items="skus.data"
      :items-length="skus.meta.total"
      :loading="loading"
      item-value="id"
      class="elevation-1"
      @update:options="loadItems"
    >
      <template #item.is_active="{ item }">
        <v-chip :color="item.is_active ? 'success' : 'error'" size="small">
          {{ item.is_active ? 'Active' : 'Inactive' }}
        </v-chip>
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
          No SKUs found
        </div>
      </template>
    </v-data-table-server>

    <div class="d-flex justify-center mt-4">
      <v-pagination
        v-model="currentPage"
        :length="Math.ceil(skus.meta.total / itemsPerPage)"
        :total-visible="7"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  skus: {
    type: Object,
    required: true,
    default: () => ({ data: [], meta: {} })
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:page', 'update:items-per-page', 'update:sort', 'edit', 'delete'])

const currentPage = ref(1)
const itemsPerPage = ref(10)

const headers = [
    { title: 'Name', key: 'name', sortable: true },
    { title: 'Code', key: 'code', sortable: true },
    { title: 'Unit Price', key: 'unit_price', sortable: true },
    { title: 'Status', key: 'is_active', align: 'center', sortable: false },
    { title: 'Actions', key: 'actions', align: 'end', sortable: false }
]

const loadItems = ({ page, sortBy }) => {
    emit('update:page', page)

    if (sortBy.length > 0) {
        const sortKey = sortBy[0].key
        const sortOrder = sortBy[0].order
        emit('update:sort', { sortKey, sortOrder })
    }
}

watch(() => props.skus.meta.current_page, (newPage) => {
    if (newPage !== currentPage.value) {
        currentPage.value = newPage
    }
})

watch(() => props.skus.meta.per_page, (newPerPage) => {
    if (newPerPage !== itemsPerPage.value) {
        itemsPerPage.value = newPerPage
    }
})
</script>

<style scoped>
    .data-table-container {
        background: white;
        border-radius: 8px;
        padding: 20px;
    }
</style>
