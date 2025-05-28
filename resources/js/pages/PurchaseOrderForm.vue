<template>
  <v-container max-width="900">
    <h1>Order Taking</h1>

    <v-form ref="form" class="mt-4">
      <v-row>
        <v-col cols="4">
           <v-autocomplete
                v-model="selectedCustomer"
                :items="customers"
                v-model:search="customerSearch"
                :loading="loading"
                item-text="full_name"
                item-value="id"
                label="Customer"
                clearable
                hide-no-data
                no-filter
            />
        </v-col>

        <v-col cols="4">
          <v-text-field
            v-model="order.delivery_date"
            label="Delivery Date"
            type="date"
            variant="outlined"
            dense
            required
          />
        </v-col>

        <v-col cols="4">
          <v-select
            v-model="order.status"
            :items="statuses"
            label="Status"
            variant="outlined"
            dense
          />
        </v-col>
      </v-row>

      <v-btn class="my-2" color="secondary" @click="openItemModal()">Add Item</v-btn>

      <!-- Items Table -->
      <v-table class="mb-4" dense>
        <thead>
          <tr>
            <th>SKU</th>
            <th class="text-end">Quantity</th>
            <th class="text-end">Price</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in order.items" :key="index">
            <td>{{ item.sku }}</td>
            <td class="text-end">{{ item.quantity }}</td>
            <td class="text-end">{{ item.price.toLocaleString() }}</td>
            <td>
              <v-btn size="x-small" @click="editItem(index)">Edit</v-btn>
            </td>
          </tr>
        </tbody>
      </v-table>

      <v-row justify="end">
        <v-col cols="4">
          <v-text-field
            label="Total Amount"
            :model-value="totalAmount.toLocaleString()"
            readonly
            outlined
            dense
          />
        </v-col>
      </v-row>

      <v-btn color="primary" @click="submitForm">Submit</v-btn>
    </v-form>

    <!-- Item Modal -->
    <v-dialog v-model="isItemModalOpen" max-width="500">
      <v-card>
        <v-card-title>{{ editingIndex === null ? 'Add Item' : 'Edit Item' }}</v-card-title>
        <v-card-text>
          <v-text-field v-model="itemForm.sku" label="SKU" dense />
          <v-text-field v-model.number="itemForm.quantity" label="Quantity" type="number" dense />
          <v-text-field v-model.number="itemForm.price" label="Price" type="number" dense />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text @click="isItemModalOpen = false">Cancel</v-btn>
          <v-btn text color="primary" @click="saveItem">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { fetchCustomers } from '../apis/customerApi';
// import { fetchCustomersBySearch } from '@/apis/customerApi' // Example API

const selectedCustomer = ref(null);  
const customerSearch = ref('');     
const customers = ref([]);          
const loading = ref(false);         
let timer = null;                  

const order = ref({
    customer: null,
    delivery_date: '',
    status: 'New',
    items: []
})


const statuses = ['New', 'Processing', 'Delivered']

const isItemModalOpen = ref(false)
const itemForm = ref({ sku: '', quantity: 1, price: 0 })
const editingIndex = ref(null)

const totalAmount = computed(() =>
  order.value.items.reduce((sum, item) => sum + (item.quantity * item.price), 0)
)

const openItemModal = () => {
  editingIndex.value = null
  itemForm.value = { sku: '', quantity: 1, price: 0 }
  isItemModalOpen.value = true
}

const editItem = (index) => {
  editingIndex.value = index
  itemForm.value = { ...order.value.items[index] }
  isItemModalOpen.value = true
}

const saveItem = () => {
  if (editingIndex.value !== null) {
    order.value.items[editingIndex.value] = { ...itemForm.value }
  } else {
    order.value.items.push({ ...itemForm.value })
  }
  isItemModalOpen.value = false
}

watch(customerSearch, (query) => {
    console.log(query)
    if (!query) {
        customers.value = [];
        return;
    }

    loading.value = true;
    clearTimeout(timer);

    timer = setTimeout(async () => {
        try {
            const { data } = await fetchCustomers(query); 
            customers.value = data; 
        } catch (e) {
            console.error('Search API error:', e);
        } finally {
        loading.value = false;
        }
    }, 300);
});

const submitForm = () => {
  console.log('Submitting order:', order.value)
  // Send to API...
}
</script>
