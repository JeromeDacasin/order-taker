<template>
  <div class="login-container">
    <h2 class="title">Login</h2>
    <form @submit.prevent="handleLogin" class="login-form">
      <div class="form-group">
        <label>Email:</label>
        <input v-model="email" type="email" required />
      </div>
      <div class="form-group">
        <label>Password:</label>
        <input v-model="password" type="password" required />
      </div>
      <button type="submit" class="login-btn">Login</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { login } from '../apis/authApi';

const email = ref('jeromedacasin21@gmail.com');
const password = ref('12345');

const handleLogin = async () => {
    try {
        const response = await login(email.value, password.value);
        return response;
    } catch (error) {
        return error;
    }
}
</script>

<style scoped>
.login-container {
  max-width: 400px;
  margin: 2rem auto;
  padding: 2rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: #f9fafb;
}

.title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: #1f2937;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  font-size: 0.875rem;
  color: #4b5563;
  margin-bottom: 0.25rem;
}

.form-group input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 4px;
}

.login-btn {
  width: 100%;
  padding: 0.5rem;
  background: #3b82f6;
  color: white;
  font-weight: 600;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.login-btn:hover {
  background: #2563eb;
}
</style>
