self.addEventListener('install', event => {
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  });+9

  self.addEventListener('push', event => {
  const data = event.data ? event.data.json() : {};
  console.log('Push recibido:', data);

  const options = {
    body: data.body || 'Notificación sin contenido',
    icon: './icons/icon.png',
    badge: './icons/icon.png'
  };

  event.waitUntil(
    self.registration.showNotification(data.title || 'Notificación', options)
  );
});

