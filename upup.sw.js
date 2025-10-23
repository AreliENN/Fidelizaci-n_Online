// upup.sw.js
const CACHE_NAME = "mi-app-cache-v2";
const BASE = "/Fidelizacion_Online"; // Carpeta de tu proyecto
const urlsToCache = [
  `${BASE}/index.html`,
  `${BASE}/offline.html`,
  `${BASE}/offline.js`,
  `${BASE}/upup.js`,
  `${BASE}/css/style.css`,
  `${BASE}/icons/icon-64x64.png`,
  `${BASE}/icons/icon-96x96.png`,
  `${BASE}/icons/icon-128x128.png`,
  `${BASE}/icons/icon-144x144.png`,
  `${BASE}/icons/icon-192x192.png`,
  `${BASE}/icons/icon-512x512.png`
];

// Instalación: cacheamos los recursos
self.addEventListener("install", event => {
  console.log("SW: Instalando...");
  event.waitUntil((async () => {
    const cache = await caches.open(CACHE_NAME);
    const failed = [];
    for (const url of urlsToCache) {
      try {
        const resp = await fetch(url, { cache: "no-store" });
        if (!resp || !resp.ok) throw new Error(`HTTP ${resp ? resp.status : 'no-response'}`);
        await cache.put(url, resp.clone());
        console.log("SW: Cached ->", url);
      } catch (err) {
        console.error("SW: No se pudo cachear:", url, err);
        failed.push({url, err: String(err)});
      }
    }
    if (failed.length) console.warn("SW: Archivos no cacheados:", failed);
  })());
});

// Activación: limpieza de cachés antiguas
self.addEventListener("activate", event => {
  console.log("SW: Activado");
  event.waitUntil(
    caches.keys().then(keys => Promise.all(
      keys.map(key => (key !== CACHE_NAME) ? caches.delete(key) : null)
    ))
  );
});

// Interceptar requests
self.addEventListener("fetch", event => {
  if (event.request.mode === 'navigate') {
    // Network-first para navegación, fallback a offline.html
    event.respondWith(
      fetch(event.request)
        .then(resp => resp)
        .catch(() => caches.match(`${BASE}/offline.html`))
    );
    return;
  }

  // Cache-first para otros recursos
  event.respondWith(
    caches.match(event.request).then(cached => {
      return cached || fetch(event.request)
        .then(networkResp => networkResp)
        .catch(() => caches.match(`${BASE}/offline.html`));
    })
  );
});

// Mensajes desde la página
self.addEventListener("message", event => {
  console.log("SW: Mensaje recibido:", event.data);
  if (event.source && event.data) {
    event.source.postMessage({status: "ok"});
  }
});
