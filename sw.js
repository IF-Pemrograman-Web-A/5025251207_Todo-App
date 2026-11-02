const CACHE_NAME = 'todo-app-v1';
const ASSETS_TO_CACHE = [
    './',
    './index.php', './cantik.css', './interaction.js', './manifest.json','./icon-192.png','./E05.png'
];
self.addEventListener("install", function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            console.log('[SW] Precaching App Shell');
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
    self.skipWaiting();
});

self.addEventListener("activate", function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cache) {
                    if (cache !== CACHE_NAME) {
                        console.log('[SW] Clearing Old Cache:', cache);
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
    event.waitUntil(self.clients.claim());
});

self.addEventListener("fetch", function(event) {
    if (event.request.method !== 'GET') return;

    event.respondWith(
        caches.match(event.request).then(function(cachedResponse) {
            if (cachedResponse) {
                // Update cache di background
                fetch(event.request).then(function(networkResponse) {
                    if (networkResponse && networkResponse.status === 200) {
                        caches.open(CACHE_NAME).then(function(cache) {
                            cache.put(event.request, networkResponse);
                        });
                    }
                }).catch(function() {});
                return cachedResponse;
            }

            return fetch(event.request).catch(function() {
                // Fallback halaman utama jika offline total
                return caches.match('./hateemel.html');
            });
        })
    );
});

self.addEventListener("message", function(event) {
    if (event.data && event.data.type === "SCHEDULE_NOTIFICATION") {
        const title = event.data.title;
        const body = event.data.body;
        const delay = event.data.delay;

        setTimeout(function() {
            self.registration.showNotification(title, {
                body: body,
                icon: "https://via.placeholder.com/128"
            });
        }, delay);
    }
});

self.addEventListener("notificationclick", function(event) {
    event.notification.close();
    event.waitUntil(
        self.clients.matchAll({ type: "window", includeUncontrolled: true }).then(function(list) {
            if (list.length > 0) return list[0].focus();
            return self.clients.openWindow("./hateemel.html");
        })
    );
});