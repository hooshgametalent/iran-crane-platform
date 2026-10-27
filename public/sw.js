const CACHE = 'souri-public-shell-v2';
const SHELL = ['/offline.html', '/manifest.webmanifest', '/icons/app-icon.svg', '/icons/app-icon-maskable.svg'];
const PUBLIC_DOCUMENTS = new Set(['/', '/catalog', '/articles', '/case-studies']);

self.addEventListener('install', (event) => {
  event.waitUntil(caches.open(CACHE).then((cache) => cache.addAll(SHELL)));
});

self.addEventListener('activate', (event) => {
  event.waitUntil(caches.keys().then((keys) => Promise.all(keys.filter((key) => key.startsWith('souri-public-shell-') && key !== CACHE).map((key) => caches.delete(key)))));
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;
  const url = new URL(event.request.url);
  if (url.origin !== self.location.origin || event.request.mode !== 'navigate' || !PUBLIC_DOCUMENTS.has(url.pathname)) return;

  event.respondWith(fetch(event.request).catch(() => caches.match('/offline.html')));
});
