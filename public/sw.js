self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('mi-cache-v1').then((cache) => {
            return cache.addAll([
                '/',
                "/js/admin.js",
                "/js/main.js",
                "/js/vendor.js",
                "/css/dashboard.css",
                "/css/main.css",
                '/images/icons/icon-192x192.png'
            ]);
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});
