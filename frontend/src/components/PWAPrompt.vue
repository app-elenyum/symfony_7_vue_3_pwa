<script setup>
import { ref, onMounted } from 'vue'

const deferredPrompt = ref(null)
const showInstallPrompt = ref(false)

onMounted(() => {
  window.addEventListener('beforeinstallprompt', (e) => {
    // Prevent Chrome 67 and earlier from automatically showing the prompt
    e.preventDefault()
    // Stash the event so it can be triggered later.
    deferredPrompt.value = e
    // Update UI notify the user they can add to home screen
    showInstallPrompt.value = true
  })

  window.addEventListener('appinstalled', (evt) => {
    console.log('PWA was installed')
    showInstallPrompt.value = false
  })
})

const installPWA = async () => {
  if (!deferredPrompt.value) return
  
  // Show the prompt
  deferredPrompt.value.prompt()
  
  // Wait for the user to respond to the prompt
  const { outcome } = await deferredPrompt.value.userChoice
  console.log(`User response to the install prompt: ${outcome}`)
  
  // We've used the prompt, and can't use it again, discard it
  deferredPrompt.value = null
  showInstallPrompt.value = false
}
</script>

<template>
  <div v-if="showInstallPrompt" class="pwa-install-banner alert alert-info alert-dismissible fade show fixed-bottom m-3 shadow" role="alert">
    <strong>Install Vionis!</strong> Add this app to your home screen for a better experience.
    <button type="button" class="btn btn-primary btn-sm ms-3" @click="installPWA">Install</button>
    <button type="button" class="btn-close" @click="showInstallPrompt = false"></button>
  </div>
</template>

<style scoped>
.pwa-install-banner {
  z-index: 1050;
  max-width: 400px;
  right: auto;
}
</style>
