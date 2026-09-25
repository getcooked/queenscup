const CACHE_NAME = 'queens-cup-v4';
const APP_SHELL = [
  '/manifest.webmanifest',
  '/icons/queens-cup-logo.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(APP_SHELL)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => Promise.all(keys.filter(key => key.startsWith('queens-cup-') && key !== CACHE_NAME).map(key => caches.delete(key)))).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  const url = new URL(event.request.url);
  // Never persist account pages, API data, CSRF tokens or private orders.
  if (url.origin !== self.location.origin || url.search || !APP_SHELL.includes(url.pathname)) return;
  event.respondWith(
    fetch(event.request)
      .then(response => {
        if (response.ok && !/no-store|private/i.test(response.headers.get('Cache-Control') || '')) {
          const copy = response.clone();
          event.waitUntil(caches.open(CACHE_NAME).then(cache => cache.put(event.request, copy)));
        }
        return response;
      })
      .catch(() => caches.match(event.request).then(cached => cached || Response.error()))
  );
});
