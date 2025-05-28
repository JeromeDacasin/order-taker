<template>
    <v-card elevation="1" rounded="lg">
      <v-card-title class="d-flex align-center justify-space-between py-2">
        {{ customer?.id ? 'Edit Customer' : 'Add Customer' }}
        <v-spacer></v-spacer>
        <v-btn icon @click="close" :disabled="loading">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-card-text>
        <v-form @submit.prevent="submitForm" ref="formRef">
          <v-row >
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.first_name"
                label="First Name"
                :rules="nameRules"
                variant="outlined"
                dense
                required
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.last_name"
                label="Last Name"
                :rules="nameRules"
                variant="outlined"
                dense
                required
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.mobile_number"
                label="Mobile Number"
                :rules="mobileRules"
                variant="outlined"
                dense
                required
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.city"
                label="City"
                :rules="cityRules"
                variant="outlined"
                dense
                required
              />
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          variant="flat"
          @click="submitForm"
          :loading="loading"
        >
          <template v-slot:loader>
            <v-progress-circular indeterminate size="24"/>
          </template>
          Save
        </v-btn>
      </v-card-actions>
    </v-card>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { createCustomer, updateSpecificCustomer } from '../../apis/customerApi';

const props = defineProps({
  customer: Object,
  isOpen: Boolean,
  loading: Boolean
});

console.log('Customer prop:', props.customer);
const emit = defineEmits(['close', 'submit', 'error']);

const form = reactive({
    first_name: '',
    last_name: '',
    mobile_number: '',
    city: '',
});

const resetForm = () => {
    form.first_name = '';
    form.last_name = '';
    form.mobile_number = '';
    form.city = '';
};


const errorMessage = ref('');
const formRef = ref(null);
const nameRules = [
    v => !!v || 'Name is required',
    v => (v && v.length <= 50) || 'Name must be less than 50 characters'
];
const mobileRules = [
    v => !!v || 'Mobile number is required',
    v => /^[0-9]{10}$/.test(v) || 'Invalid mobile number'
];
const cityRules = [
  v => !!v || 'City is required',
  v => (v && v.length <= 50) || 'City must be less than 50 characters'
];

watch(() => props.customer, (newVal) => {
  if (newVal) {
    Object.assign(form, newVal)
  } else {
    resetForm()
  }
}, { immediate: true })

const close = () => {
    resetForm();
    emit('close');
};


const submitForm = async () => {
    const { valid } = await formRef.value.validate();
    if (!valid) return;

    errorMessage.value = ''; 

    try {
        if (props.customer?.id) {
             await updateSpecificCustomer(props.customer.id, form);
        } else {
            await createCustomer(form);
        }
        emit('submit');
        close(); 

    } catch (error) {
       
        if (error.response && error.response.data && error.response.data.message) {
            errorMessage.value = error.response.data.message;
        } else {
            errorMessage.value = 'An unexpected error occurred.';
        }
        emit('error', errorMessage);
    }
};
</script>