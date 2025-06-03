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
   
    <v-container max-width="1500" class="pa-6">
        <v-row justify="space-between" align="center" class="mb-4"> 
            <v-col cols="auto">
                <h1>Customers</h1>
            </v-col>
            <v-col class="d-flex justify-end" cols="auto">
                <v-btn color="primary" @click="openModal()">Create New</v-btn>
            </v-col>
        </v-row>

        <CustomerTable
            :customers="customers"
            :loading="loading"
            :error="error"
            @update:page="handlePageChange"
            @edit="handleEdit"
            @error="handleError"
            @delete="handleDelete"
        />
    
        <v-dialog v-model="isModalOpen" max-width="600">
            <CustomerForm
                :customer="selectedCustomer"
                @submit="handleSubmit"
                @close="isModalOpen = false"
                @error="handleError"
            />
        </v-dialog>
        
    </v-container>
</template>

<script setup>
    import { ref, onMounted } from 'vue';
    import { deleteSpecificCustomer, fetchCustomers } from '../apis/customerApi';
    import CustomerTable from '../components/customer/CustomerTable.vue';
    import CustomerForm from '../components/customer/CustomerForm.vue';

    const customers = ref({ data: [], meta: {} });
    const isModalOpen = ref(false);
    const selectedCustomer = ref(null);
    const loading = ref(false);
    const error = ref(false);   
    const errorMessage = ref('');
    const currentPage = ref(1);

    const loadCustomers = async (page = 1) => {
        try {
            loading.value = true;
            const response = await fetchCustomers();
            customers.value = response.data;
        } catch (err) {
            showError('Failed to load customers');
        } finally {
            loading.value = false;
        }
    };

    const handlePageChange = (page) => {
        currentPage.value = page;
        loadCustomers(page);
    };

    const handleError = (msg) => {
        errorMessage.value = msg

        setTimeout(() => (errorMessage.value = ''), 5000)
    }
    const openModal = (customer = null) => {
        selectedCustomer.value = customer;
        isModalOpen.value = true;
    };

    const handleSubmit = async () => {
        await loadCustomers(currentPage.value);
        closeDialog();
    };

    function handleEdit(customer) {
        console.log(customer)
        selectedCustomer.value = { ...customer }
        isModalOpen.value = true
    }
    const closeDialog = () => {
        isModalOpen.value = false;
        selectedCustomer.value = null;
    };

    const showError = (message) => {
        error.value = true;
        errorMessage.value = message;
        setTimeout(() => error.value = false, 3000);
    };

    const handleDelete = async (id) => {
         try {
            loading.value = true;
            const response = await deleteSpecificCustomer(id);
            await loadCustomers(currentPage.value);
            return response;
        } catch (err) {
            showError('Failed to delete customers');
        } finally {
            loading.value = false;
        }
    }

    onMounted(() => loadCustomers());
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