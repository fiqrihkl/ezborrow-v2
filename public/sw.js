const cacheName = "ezborrow-v2";
const staticAssets = [
    "/",
    "/css/app.css",
    "/js/app.js",
    "assets/img/favicon.png",
];

self.addEventListener("install", async (event) => {
    const cache = await caches.open(cacheName);
    await cache.addAll(staticAssets);
});

self.addEventListener("fetch", (event) => {
    const req = event.request;
    event.respondWith(cacheFirst(req));
});

async function cacheFirst(req) {
    const cache = await caches.open(cacheName);
    const cachedResponse = await cache.match(req);
    return cachedResponse || fetch(req);
}
