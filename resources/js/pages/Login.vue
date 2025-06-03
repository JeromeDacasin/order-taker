<template>
    <v-container class="d-flex align-center justify-center fill-height">
        <v-card class="pa-6" width="400" elevation="6">
            <h2 class="text-center mb-4">Login</h2>
            <v-form @submit.prevent="handleLogin">
                <v-text-field
                    v-model="email"
                    label="Email"
                    type="email"
                    required
                    variant="outlined"
                ></v-text-field>

                <v-text-field
                    v-model="password"
                    label="Password"
                    type="password"
                    required
                    variant="outlined"
                ></v-text-field>

                <v-btn
                    type="submit"
                    color="primary"
                    block
                    class="mt-4"
                >
                    Login
                </v-btn>

            </v-form>

        </v-card>
    
    </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { login } from '../apis/authApi';
import { useRouter } from 'vue-router';

const email = ref('jeromedacasin21@gmail.com');
const password = ref('12345');
const router = useRouter()

const handleLogin = async () => {
    try {
        const response = await login(email.value, password.value);
        router.push({ name: 'Orders'})
        return response;
    } catch (error) {
        return error;
    }
}
</script>
