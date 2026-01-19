// service-worker.js
// Very small service worker to support showNotification and click handling.
// NOTE: Browsers require HTTPS for service workers on mobile; localhost is allowed for development demos.

self.addEventListener('install', event => {
    // Activate immediately for faster development.
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    // Claim clients so notifications work without reload.
    event.waitUntil(self.clients.claim());
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    // Focus or open the dashboard when user taps notification.
    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientList => {
            for (const client of clientList) {
                if (client.url.includes('/WorkReminder/dashboard.php') && 'focus' in client) {
                    return client.focus();
                }
            }
            if (self.clients.openWindow) {
                return self.clients.openWindow('/WorkReminder/dashboard.php');
            }
        })
    );
});

