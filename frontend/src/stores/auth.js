import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    async login(email, password) {
      try {
        const response = await axios.post('/api/login_check', {
          username: email,
          password: password,
        })
        this.setAuth(response.data.token, { email })
        return true
      } catch (error) {
        console.error('Login failed', error)
        throw error
      }
    },
    async register(email, password) {
      try {
        const response = await axios.post('/api/register', {
          email,
          password,
        })
        this.setAuth(response.data.token, response.data.user)
        return true
      } catch (error) {
        console.error('Registration failed', error)
        throw error
      }
    },
    setAuth(token, user) {
      this.token = token
      this.user = user
      localStorage.setItem('token', token)
      localStorage.setItem('user', JSON.stringify(user))
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    },
    logout() {
      this.token = null
      this.user = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      delete axios.defaults.headers.common['Authorization']
    },
    init() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
      }
    },
    async subscribeToPush() {
      if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        console.warn('Push notifications are not supported in this browser.')
        return false
      }

      try {
        // Ensure Service Worker is registered and ready
        let registration = await navigator.serviceWorker.getRegistration();
        
        if (!registration) {
             console.log('Registering service worker manually...');
             registration = await navigator.serviceWorker.register('/sw.js');
        }
        
        await navigator.serviceWorker.ready;

        const permission = await Notification.requestPermission()
        if (permission !== 'granted') {
          console.warn('Notification permission denied.')
          return false
        }

        const subscription = await registration.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: this.urlBase64ToUint8Array('BH0z3KSrxZTS4QprrNYEYAVF7FYlI2ApkLy3M-VfFFUgdSBisJZfWRtfQjLhBhOqn-ycuuU40riJAryfspblGKg')
        })

        // CRITICAL: Call toJSON() to get the actual data for the server
        const subscriptionData = subscription.toJSON()

        const response = await axios.post('/api/push/subscribe', subscriptionData)
        console.log('Push subscription successful', response.data)
        return true
      } catch (error) {
        console.error('Push subscription failed', error)
        return false
      }
    },
    urlBase64ToUint8Array(base64String) {
      const padding = '='.repeat((4 - (base64String.length % 4)) % 4)
      const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/')
      const rawData = window.atob(base64)
      const outputArray = new Uint8Array(rawData.length)
      for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i)
      }
      return outputArray
    }
  },
})
