<script setup>
import { RouterLink, RouterView, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import PWAPrompt from '@/components/PWAPrompt.vue'
import NotificationBanner from '@/components/NotificationBanner.vue'

const authStore = useAuthStore()
const router = useRouter()

const logout = () => {
  authStore.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
      <RouterLink class="navbar-brand" to="/">
        <i class="bi bi-rocket-takeoff-fill me-2"></i>Vionis.ru
      </RouterLink>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <RouterLink class="nav-link" active-class="active" to="/">Home</RouterLink>
          </li>
          <li class="nav-item">
            <RouterLink class="nav-link" active-class="active" to="/about">About</RouterLink>
          </li>
          <template v-if="!authStore.isAuthenticated">
            <li class="nav-item">
              <RouterLink class="nav-link" active-class="active" to="/login">Login</RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" active-class="active" to="/register">Register</RouterLink>
            </li>
          </template>
          <template v-else>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                {{ authStore.user?.email }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><button class="dropdown-item" @click="logout">Logout</button></li>
              </ul>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Persistent Notification Banner -->
  <NotificationBanner />

  <main class="container py-4">
    <RouterView />
  </main>

  <footer class="footer mt-auto py-3 bg-light border-top">
    <div class="container text-center">
      <span class="text-muted">© 2026 Vionis.ru - Built with Symfony 7 & Vue 3</span>
    </div>
  </footer>

  <!-- PWA Install Prompt -->
  <PWAPrompt />
</template>

<style lang="scss">
body {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

#app {
  flex: 1;
  display: flex;
  flex-direction: column;
}

main {
  flex-shrink: 0;
}

.footer {
  margin-top: auto;
}
</style>
