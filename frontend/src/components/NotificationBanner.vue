<script setup>
import { ref, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const isVisible = ref(false)
const isSubscribing = ref(false)
const permission = ref(Notification.permission)
const hasSubscription = ref(false)

const checkState = async () => {
  permission.value = Notification.permission
  
  if (!authStore.isAuthenticated) {
    isVisible.value = false
    return
  }

  // Check if actually subscribed in SW
  if ('serviceWorker' in navigator) {
    const registration = await navigator.serviceWorker.getRegistration()
    if (registration) {
      const subscription = await registration.pushManager.getSubscription()
      hasSubscription.value = !!subscription
    } else {
      hasSubscription.value = false
    }
  }

  // Show if permission not granted OR subscription missing
  isVisible.value = (permission.value !== 'granted' || !hasSubscription.value)
}

const handleEnable = async () => {
  isSubscribing.value = true
  try {
    const success = await authStore.subscribeToPush()
    if (success) {
      isVisible.value = false
    }
  } finally {
    isSubscribing.value = false
    await checkState()
  }
}

const closeBanner = () => {
  isVisible.value = false
}

onMounted(() => {
  checkState()
  // Interval check to keep it in sync if they change settings in another tab
  setInterval(checkState, 5000)
})

watch(() => authStore.isAuthenticated, (newVal) => {
  if (newVal) checkState()
  else isVisible.value = false
})
</script>

<template>
  <div v-if="isVisible && authStore.isAuthenticated" class="notification-banner shadow-sm p-2 d-flex align-items-center justify-content-center bg-warning text-dark border-bottom">
    <div class="container d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
        <i class="bi bi-bell-fill me-3 fs-5"></i>
        <span>
          <strong>Notifications are disabled!</strong> 
          Enable them to stay updated with important alerts from Vionis.ru
        </span>
      </div>
      <div class="d-flex gap-2">
        <button @click="handleEnable" class="btn btn-dark btn-sm px-3" :disabled="isSubscribing">
          <span v-if="isSubscribing" class="spinner-border spinner-border-sm me-1"></span>
          Enable Now
        </button>
        <button @click="closeBanner" class="btn btn-link text-dark p-0 ms-2" title="Dismiss">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.notification-banner {
  position: sticky;
  top: 56px; /* Height of the navbar */
  z-index: 1020;
  transition: all 0.3s ease;
}

.btn-link {
  text-decoration: none;
}

@media (max-width: 768px) {
  .notification-banner {
    font-size: 0.85rem;
  }
  .container {
    flex-direction: column;
    gap: 10px;
  }
}
</style>
