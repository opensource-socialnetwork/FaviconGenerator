self.addEventListener('install', function(e) {
	e.waitUntil(
		caches.open('ossn').then(function(cache) {
			return cache.addAll([
				// array of cacheable pages ...
			]);
		})
	);
});

self.addEventListener('fetch', function(e) {
	if(e.request.url.includes('edit?section=siteappinstaller')) {
		e.respondWith(
			caches.match(e.request).then(function(response) {
				return response || fetch(e.request);
			})
		);
	}
});

self.addEventListener('push', event => {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }
	try {
		var notification = JSON.parse(event.data.text());
		var title = notification.title;
		var icon = notification.icon;
		var body = notification.body;
	} catch (e) {
		var title = 'Developer Test Push';
		var icon = '';
		var body = event.data.text();
	}
	event.waitUntil(
		self.registration.showNotification(title, {
			body: body,
			icon: icon,
		//	vibrate: [100, 50, 100],
		//	data: { ourKey: 1 },
			requireInteraction: true,
		//	actions: [
		//		{action: 'go', title: 'go to site'},
		//	]
		})
	);
});
