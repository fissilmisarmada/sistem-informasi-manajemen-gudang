// Web Push service worker — payload dari WebPushMessage::toArray() (title/body/icon/tag/data.url)
self.addEventListener('push', (event) => {
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = { title: 'Notifikasi Gudang', body: event.data ? event.data.text() : '' };
    }
    event.waitUntil(self.registration.showNotification(data.title || 'Notifikasi Gudang', {
        body: data.body || '',
        icon: data.icon || '/favicon.ico',
        tag: data.tag || undefined,
        data: { url: (data.data && data.data.url) || '/' },
    }));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = (event.notification.data && event.notification.data.url) || '/';
    event.waitUntil((async () => {
        const windows = await self.clients.matchAll({ type: 'window', includeUncontrolled: true });
        for (const w of windows) {
            if ('focus' in w) {
                await w.focus();
                if ('navigate' in w) await w.navigate(url);
                return;
            }
        }
        if (self.clients.openWindow) await self.clients.openWindow(url);
    })());
});
