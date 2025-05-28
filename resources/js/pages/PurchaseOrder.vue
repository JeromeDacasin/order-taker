<template>
    <div class="top-right-alert">   
        <v-alert
        v-if="errorMessage"
        type="error"
        dense
        outlined
        >
        {{ errorMessage }}
        </v-alert>
    </div>
    <v-container max-width="1000">
        <v-row>
        <v-col>
            <h1>Orders</h1>
        </v-col>
        <v-col class="d-flex justify-end">
            <v-btn color="primary" @click="goToCreate()">Create New</v-btn>
        </v-col>
        </v-row>

        <OrderTable
            :orders="orders"
            :loading="loading"
            :error="error"
            @update:page="handlePageChange"
            @edit="handleEdit"
            @error="handleError"
            @delete="handleDelete"
        />
    </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { fetchPurchaseOrders, deleteSpecificPurchaseOrder } from '../apis/purchaseOrderApi'
import OrderTable from './../components/order/OrderTable.vue'

const orders = ref({ data: [], meta: {} })
const loading = ref(false)
const errorMessage = ref('')
const router = useRouter()

const loadOrders = async () => {
    try {
        loading.value = true
        const response = await fetchPurchaseOrders()
        orders.value = response.data
    } catch (err) {
        handleError('Failed to load orders')
    } finally {
        loading.value = false
    }
}

onMounted(() => loadOrders())

const goToCreate = () => {
    router.push('/orders/create')
}

const handleEdit = (order) => {
    router.push(`/orders/${order.id}/edit`)
}

const handleDelete = async (id) => {
    try {
        await deleteSpecificPurchaseOrder(id)
        await loadOrders()
    } catch (err) {
        handleError('Failed to delete order')
    }
}

const handleError = (msg) => {
    errorMessage.value = msg
    setTimeout(() => (errorMessage.value = ''), 5000)
}


</script>
