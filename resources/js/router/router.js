import { createRouter, createWebHistory } from 'vue-router';
import Login from './../pages/Login.vue';
import Customer from './../pages/Customer.vue';
import Sku from './../pages/Sku.vue';
import PurchaseOrder from './../pages/PurchaseOrder.vue';
import PurchaseOrderForm from '../pages/PurchaseOrderForm.vue';


const routes = [
    { path: '/login', component: Login },
    { path: '/customers', name:'Orders', component: Customer },
    { path: '/skus', component: Sku },
    { path: '/orders', component: PurchaseOrder },
    { path: '/orders/create', name: 'CreateProduct', component: PurchaseOrderForm },
    { path: '/orders/:id', component: PurchaseOrderForm, props: true },

];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;