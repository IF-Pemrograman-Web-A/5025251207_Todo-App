
self.addEventListener("install", function(event) {
    self.skipWaiting();
});

self.addEventListener("activate", function(event) {
    event.waitUntil(self.clients.claim());
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