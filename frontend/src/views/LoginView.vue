<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const error = ref(null)
const isLoading = ref(false)
const authStore = useAuthStore()
const router = useRouter()

const handleLogin = async () => {
  if (isLoading.value) return
  
  isLoading.value = true
  error.value = null
  
  try {
    await authStore.login(email.value, password.value)
    router.push({ name: 'home' })
  } catch (err) {
    error.value = 'Invalid email or password'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
      <div class="card shadow border-0">
        <div class="card-body p-4">
          <h2 class="text-center mb-4">Login</h2>
          <form @submit.prevent="handleLogin">
            <div class="mb-3">
              <label class="form-label">Email address</label>
              <input 
                v-model="email" 
                type="email" 
                class="form-control" 
                required 
                :disabled="isLoading"
                placeholder="email@example.com"
              />
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input 
                v-model="password" 
                type="password" 
                class="form-control" 
                required 
                :disabled="isLoading"
                placeholder="••••••••"
              />
            </div>
            <div v-if="error" class="alert alert-danger py-2">{{ error }}</div>
            <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center" :disabled="isLoading">
              <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
              {{ isLoading ? 'Logging in...' : 'Login' }}
            </button>
          </form>
          <div class="mt-3 text-center small">
            Don't have an account? <RouterLink to="/register">Register here</RouterLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
