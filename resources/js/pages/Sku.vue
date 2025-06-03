<template>
  <div class="top-right-alert">
    <v-alert v-if="errorMessage" type="error" dense outlined>
      {{ errorMessage }}
    </v-alert>
  </div>

  <v-container max-width="1500" class="pa-6">
    <v-row>
      <v-col>
        <h1>SKUs</h1>
      </v-col>
      <v-col class="d-flex justify-end">
        <v-btn color="primary" @click="openModal()">Create New</v-btn>
      </v-col>
    </v-row>

    <SkuTable
      :skus="skus"
      :loading="loading"
      @update:page="handlePageChange"
      @edit="handleEdit"
      @delete="handleDelete"
    />

    <v-dialog v-model="isModalOpen" max-width="600">
      <SkuForm
        :sku="selectedSku"
        @submit="handleSubmit"
        @close="isModalOpen = false"
        @error="handleError"
      />
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { fetchSkus, deleteSpecificSku } from '../apis/skuApi'
import SkuTable from '../components/sku/SkuTable.vue'
import SkuForm from '../components/sku/SkuForm.vue'

const skus = ref({ data: [], meta: {} })
const isModalOpen = ref(false)
const selectedSku = ref(null)
const loading = ref(false)
const error = ref(false)
const errorMessage = ref('')
const currentPage = ref(1)

const loadSkus = async (page = 1) => {
  try {
    loading.value = true
    const response = await fetchSkus(page)
    skus.value = response.data
  } catch (err) {
    showError('Failed to load SKUs')
  } finally {
    loading.value = false
  }
}

const handlePageChange = (page) => {
  currentPage.value = page
  loadSkus(page)
}

const handleError = (msg) => {
  errorMessage.value = msg
  setTimeout(() => (errorMessage.value = ''), 5000)
}

const openModal = (sku = null) => {
  selectedSku.value = sku
  isModalOpen.value = true
}

const handleSubmit = async () => {
  await loadSkus(currentPage.value)
  closeDialog()
}

const handleEdit = (sku) => {
  selectedSku.value = { ...sku }
  isModalOpen.value = true
}

const closeDialog = () => {
  isModalOpen.value = false
  selectedSku.value = null
}

const showError = (message) => {
  error.value = true
  errorMessage.value = message
  setTimeout(() => (error.value = false), 3000)
}

const handleDelete = async (id) => {
  try {
    loading.value = true
    await deleteSpecificSku(id)
    await loadSkus(currentPage.value)
  } catch (err) {
    showError('Failed to delete SKU')
  } finally {
    loading.value = false
  }
}

onMounted(() => loadSkus())
</script>

<style>
.top-right-alert {
  position: fixed;
  top: 10px;
  right: 10px !important;
  z-index: 9999;
  max-width: 300px;
  width: auto;
  min-width: 250px;
}
</style>
