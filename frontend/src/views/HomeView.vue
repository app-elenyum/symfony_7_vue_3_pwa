<script setup>
// HomeView is now a clean landing page.
// Notification management is handled by the persistent NotificationBanner component.
</script>

<template>
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm border-0 bg-light p-5 text-center mb-4">
        <h1 class="display-4 mb-3">Welcome to Vionis.ru</h1>
        <p class="lead text-muted">Your personal cross-platform PWA service built with Symfony 7 and Vue 3.</p>
        <hr class="my-4">
        
        <div class="py-4">
          <div class="row g-4 text-start">
            <div class="col-md-4">
              <div class="p-3 bg-white rounded shadow-sm h-100">
                <h4 class="h5 border-bottom pb-2 mb-3 text-primary"><i class="bi bi-shield-lock me-2"></i> Secure</h4>
                <p class="small text-muted">Full JWT authentication system with secure http-only token handling logic.</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-3 bg-white rounded shadow-sm h-100">
                <h4 class="h5 border-bottom pb-2 mb-3 text-success"><i class="bi bi-phone-vibrate me-2"></i> PWA Ready</h4>
                <p class="small text-muted">Installable on iOS, Android, and Desktop with offline support and app-like experience.</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-3 bg-white rounded shadow-sm h-100">
                <h4 class="h5 border-bottom pb-2 mb-3 text-info"><i class="bi bi-chat-dots me-2"></i> Push Alerts</h4>
                <p class="small text-muted">Real-time web push notifications delivered across all your devices.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.card {
  border-radius: 1rem;
}
</style>
