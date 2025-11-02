self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open('volti-cache-v1').then((cache) => {
      return cache.addAll([
        '/',
        '/volti-home/',
        '/volti-home/index.html',
        '/volti-home/assets/css/style.css',
        '/volti-home/assets/js/script.js',
        '/volti-home/assets/images/hero-banner.png',
        // Add all your essential files here
      ]);
    })
  );
});

self.addEventListener('fetch', (e) => {
  e.respondWith(
    caches.match(e.request).then((response) => {
      return response || fetch(e.request);
    })
  );
});
