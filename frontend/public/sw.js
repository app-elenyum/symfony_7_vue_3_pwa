/* Service Worker for PWA - Diagnostic Version */

self.addEventListener('push', (event) => {
  console.log('[SW] Push Received.');

  let data = {
    title: 'Vionis.ru',
    body: 'Notification from Vionis',
    icon: '/icons/icon-192x192.png'
  };

  if (event.data) {
    try {
      data = event.data.json();
      console.log('[SW] Push Data Parsed:', data);
    } catch (e) {
      data.body = event.data.text();
      console.log('[SW] Push Data (Text):', data.body);
    }
  } else {
    console.log('[SW] Push event had no data.');
  }

  const options = {
    body: data.body,
    icon: data.icon,
    badge: data.icon,
    vibrate: [100, 50, 100],
    data: {
      url: data.url || '/'
    }
  };

  console.log('[SW] Attempting to showNotification:', data.title);

  event.waitUntil(
    self.registration.showNotification(data.title, options)
      .then(() => {
        console.log('[SW] showNotification resolved successfully.');
      })
      .catch((error) => {
        console.error('[SW] showNotification failed:', error);
      })
  );
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const urlToOpen = new URL(event.notification.data.url, self.location.origin).href;

  event.waitUntil(
    clients.matchAll({
      type: 'window',
      includeUncontrolled: true
    }).then((windowClients) => {
      for (let i = 0; i < windowClients.length; i++) {
        const client = windowClients[i];
        if (client.url === urlToOpen && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(urlToOpen);
      }
    })
  );
});
