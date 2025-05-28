<template>
  <v-container max-width="9100">
    <div class="top-right-alert">
        <v-alert
            v-if="errorMessage"
            type="error"
            dense
            outlined
        >
            {{ errorMessage }}
        </v-alert>

        <v-alert
            v-if="successMessage"
            type="success"
            dense
            outlined
        >
            {{ successMessage }}
        </v-alert>
    </div>
    <v-form ref="form" class="mt-4">
      <v-row>
        <v-col cols="4">
          <v-autocomplete
            v-model="selectedCustomer"
            :items="customers"
            item-title="full_name"
            item-value="id"
            return-object
            label="Customer"
            clearable
            hide-no-data
            no-filter
            variant="outlined"
            :loading="loading"
            v-model:search="customerSearch"
            :disabled="isEditMode"
          />
        </v-col>

        <v-col cols="4">
          <v-text-field
            v-model="order.delivery_date"
            label="Delivery Date"
            type="date"
            :min="minDeliveryDate"
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

      <v-btn class="my-2" color="secondary" @click="openItemModal">Add Item</v-btn>

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
            <td>{{ getSkuName(item.sku_id) }}</td>
            <td class="text-end">{{ item.quantity }}</td>
            <td class="text-end">{{ item.price?.toLocaleString() }}</td>
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

      <v-btn color="primary" :loading="loadingSubmit" :disabled="loadingSubmit" @click="submitForm">
        Submit
      </v-btn>
    </v-form>

    <v-dialog v-model="isItemModalOpen" max-width="500">
      <v-card>
        <v-card-title>{{ editingIndex === null ? 'Add Item' : 'Edit Item' }}</v-card-title>
        <v-card-text>
          <v-autocomplete
            v-model="itemForm.sku_id"
            :items="skuList"
            item-title="name"
            item-value="id"
            label="SKU"
            dense
            variant="outlined"
            return-object
            @update:modelValue="onSkuSelect"
            :disabled="editingIndex !== null"
          />

          <v-text-field
            v-model.number="itemForm.quantity"
            label="Quantity"
            type="number"
            variant="outlined"
            dense
            @input="calculateTotal"
          />

          <v-text-field
            :value="itemForm.price"
            type="number"
            variant="outlined"
            dense
            readonly
          />
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
    import { ref, computed, watch, onMounted } from 'vue'
    import { fetchCustomers } from '../apis/customerApi'
    import { fetchSkus } from '../apis/skuApi'
    import { useRouter } from 'vue-router'
    import { createPurchaseOrder, fetchSpecificPurchaseOrder, updateSpecificPurchaseOrder } from '../apis/purchaseOrderApi'

    const props = defineProps({
        id: {
            type: String,
            required: false
        }
    })

    const isEditMode = computed(() => !!props.id)
    const errorMessage = ref('')
    const successMessage = ref('')
    const router = useRouter()
    const selectedCustomer = ref(null)
    const loadingSubmit = ref(false)
    const customerSearch = ref('')
    const customers = ref([])
    const loading = ref(false)
    let timer = null

    const order = ref({
    delivery_date: '',
    status: 'New',
    items: []
    })

    const statuses = ['New', 'Completed', 'Cancelled']
    const isItemModalOpen = ref(false)
    const skuList = ref([])
    const itemForm = ref({ sku_id: null, quantity: 1, price: 0 })
    const editingIndex = ref(null)

    const minDeliveryDate = computed(() => {
        const today = new Date()
        today.setDate(today.getDate() + 1)
        return today.toISOString().split('T')[0]
    })

    const totalAmount = computed(() =>
        order.value.items.reduce((sum, item) => sum + (item.price || 0), 0)
    )

    const openItemModal = () => {
        editingIndex.value = null
        itemForm.value = { sku_id: null, quantity: 1, price: 0 }
        isItemModalOpen.value = true
    }

    const editItem = (index) => {
        editingIndex.value = index
        itemForm.value = { ...order.value.items[index] }
        isItemModalOpen.value = true
    }

    const saveItem = () => {
        const skuId = itemForm.value.sku_id
        if (!skuId) return

        if (editingIndex.value !== null) {
            order.value.items[editingIndex.value] = { ...itemForm.value }
        } else {
            const existingIndex = order.value.items.findIndex(item => item.sku_id === skuId)
            if (existingIndex !== -1) {
            order.value.items[existingIndex].quantity += itemForm.value.quantity
            order.value.items[existingIndex].price += itemForm.value.price
            } else {
            order.value.items.push({ ...itemForm.value })
            }
        }

        isItemModalOpen.value = false
    }

    const fetchSkusData = async () => {
        try {
            const { data } = await fetchSkus()
            skuList.value = data.data
        } catch (e) {
            console.error('Failed to fetch SKUs:', e)
        }
    }

    const getSkuName = (skuId) => {
        return skuList.value.find(sku => sku.id === skuId)?.name || 'Unknown SKU'
    }

    const onSkuSelect = (sku) => {
        if (sku && sku.unit_price != null) {
            itemForm.value.sku_id = sku.id
            calculateTotal()
        }
    }

    const calculateTotal = () => {
        const sku = skuList.value.find(s => s.id === itemForm.value.sku_id)
        const unit_price = sku?.unit_price || 0
        itemForm.value.price = unit_price * itemForm.value.quantity
    }

    watch(customerSearch, (query) => {
        if (!query) {
            customers.value = []
            return
        }

        loading.value = true
        clearTimeout(timer)

        timer = setTimeout(async () => {
            try {
                const { data } = await fetchCustomers(query)
                customers.value = data.data
            } catch (e) {
                console.error('Search API error:', e)
            } finally {
                loading.value = false
            }
        }, 300)
        })

    onMounted(async () => {
        await fetchSkusData()

        if (isEditMode.value) {
            try {
            const { data } = await fetchSpecificPurchaseOrder(props.id)
            selectedCustomer.value = {
                id: data.data.customer_id,
                full_name: data.data.customer
            }
            order.value.delivery_date = data.data.delivery_date?.split(' ')[0] || ''
            order.value.status = data.data.status || 'New'
            order.value.items = (data.data.items || []).map(item => ({
                sku_id: item.id,
                quantity: item.quantity,
                price: item.price
            }))
            } catch (err) {
            console.error('Failed to load order:', err)
            }
        }
    })

    const submitForm = async () => {
        if (!selectedCustomer.value || !order.value.items.length) return

        loadingSubmit.value = true
        errorMessage.value = ''
        successMessage.value = ''

        const payload = {
            customer_id: selectedCustomer.value.id,
            delivery_date: order.value.delivery_date,
            status: order.value.status,
            amount_due: totalAmount.value,
            items: order.value.items.map(item => ({
                sku_id: item.sku_id,
                quantity: item.quantity,
                price: item.price
            }))
        }

        try {
            let response

            if (isEditMode.value) {
                response = await updateSpecificPurchaseOrder(props.id, payload)
                console.log('Order updated successfully:', response)
            } else {
                response = await createPurchaseOrder(payload)
                console.log('Order created successfully:', response)
            }

            successMessage.value = isEditMode.value
                ? 'Order updated successfully!'
                : 'Order created successfully!'

            setTimeout(() => {
                router.push('/orders') 
            }, 3000)

        } catch (error) {
            console.error('Failed to submit order:', error)
            errorMessage.value = error.message || 'Failed to submit order'
        } finally {
            loadingSubmit.value = false
        }
    }
</script>
