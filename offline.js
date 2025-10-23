console.log("Iniciando UpUp...");

UpUp.start({
  'cache-version': 'v2',
  'content-url': '/offline.html', // página offline estática
  'assets': [
    '/index.html',
    '/offline.html',
    '/offline.js',
    '/upup.js',
    '/upup.sw.js',
    '/css/style.css',
    '/icons/icon-64x64.png',
    '/icons/icon-96x96.png',
    '/icons/icon-128x128.png',
    '/icons/icon-144x144.png',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png'
  ],
  'service-worker-url': '/upup.sw.js' // ruta del SW
});

// Registrar el Service Worker (separado del UpUp.start)
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('upup.sw.js', { scope: './' })
    .then(reg => console.log('Service Worker registrado:', reg))
    .catch(err => console.warn('Error al registrar SW:', err));
}
