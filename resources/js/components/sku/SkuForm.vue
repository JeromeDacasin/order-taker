<template>
  <v-card elevation="1" rounded="lg">
    <v-card-title class="d-flex align-center justify-space-between py-2">
      {{ sku?.id ? 'Edit SKU' : 'Add SKU' }}
      <v-spacer></v-spacer>
      <v-btn icon @click="close" :disabled="loading">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </v-card-title>

    <v-card-text>
      <v-form @submit.prevent="submitForm" ref="formRef">
        <v-row>
          <v-col cols="12" sm="6">
            <v-text-field v-model="form.name" label="Name" :rules="nameRules" variant="outlined" dense required />
          </v-col>

          <v-col cols="12" sm="6">
            <v-text-field v-model="form.code" label="Code" :rules="codeRules" variant="outlined" dense required />
          </v-col>

          <v-col cols="12" sm="6">
            <v-text-field v-model="form.unit_price" label="Unit Price" type="number" variant="outlined" dense required />
          </v-col>

          <v-col cols="12" sm="6">
            <v-switch v-model="form.is_active" label="Is Active" />
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>

    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="primary" variant="flat" @click="submitForm" :loading="loading">
        <template v-slot:loader>
          <v-progress-circular indeterminate size="24" />
        </template>
        Save
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { createSku, updateSpecificSku } from '@/apis/skuApi'

const props = defineProps({
  sku: Object,
  isOpen: Boolean,
  loading: Boolean
})
const emit = defineEmits(['close', 'submit', 'error'])

const form = reactive({
  name: '',
  code: '',
  unit_price: null,
  is_active: true
})

const formRef = ref(null)

const nameRules = [v => !!v || 'Name is required']
const codeRules = [v => !!v || 'Code is required']

const resetForm = () => {
    form.name = ''
    form.code = ''
    form.unit_price = null
    form.is_active = true
}

watch(() => props.sku, (val) => {
    if (val) Object.assign(form, val)
    else resetForm()
}, { immediate: true })

const close = () => {
    resetForm()
    emit('close')
}

const submitForm = async () => {
    const { valid } = await formRef.value.validate()
    if (!valid) return

    try {
        if (props.sku?.id) await updateSpecificSku(props.sku.id, form)
        else await createSku(form)
        emit('submit')
        close()
    } catch (err) {
        const msg = err.response?.data?.message || 'An unexpected error occurred.'
        emit('error', msg)
    }
}
</script>
