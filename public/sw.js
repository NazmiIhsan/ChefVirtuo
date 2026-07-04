const CACHE_NAME = 'chefvirtuo-v2';

const urlsToCache = [
    '/',
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    // Never cache auth pages
    if (
        url.pathname.startsWith('/auth') ||
        url.pathname === '/login'
    ) {
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(response => response || fetch(event.request))
    );
});