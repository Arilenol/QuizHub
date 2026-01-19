const PREFIX = "V1";
const BASE = location.protocol + "//" + location.host;
const CACHED_FILES = [
    `${BASE}/offline/index.html`,
    `${BASE}/offline/flashcard.html`,
    `${BASE}/offline/quiz.html`,
    `${BASE}/offline/offline.css`,
    `${BASE}/offline/offline.js`,
    `${BASE}/offline/logo.svg`,
    `${BASE}/offline/icon.png`,
    "https://cdnjs.cloudflare.com/ajax/libs/localforage/1.10.0/localforage.js"
]

self.addEventListener("install", (e) => {
    self.skipWaiting();
    e.waitUntil(
        (async () => {
            const cache = await caches.open(PREFIX);
            await Promise.all(
                [...CACHED_FILES].map((path) => {
                    return cache.add(path);
                })
            )
        })()
    );
});

self.addEventListener("activate", (e) => {
    e.waitUntil(
        (async () => {
            const keys = await caches.keys();
            await Promise.all(
                keys.map((key) => {
                    if(!key.includes(PREFIX)){
                        return caches.delete(key);
                    }
                })
            )
        })()
    )
    clients.claim();
    if (self.registration.navigationPreload) {
        e.waitUntil(self.registration.navigationPreload.enable());
    }
});

self.addEventListener("fetch", (e) => {
    if (e.request.mode === "navigate") {
        e.respondWith(
            (async () => {
                try {
                    const preloadResponse = await e.preloadResponse;
                    if (preloadResponse) {
                        return preloadResponse;
                    }

                    return await fetch(e.request);

                } catch (error) {
                    const cache = await caches.open(PREFIX);
                    const page = e.request.url.split("/")[3].split("?")[0];
                    if(page == "flashcard.html"){
                        return await cache.match("/offline/flashcard.html")
                    }
                    else if(page == "quiz.html"){
                        return await cache.match("/offline/quiz.html")
                    }
                    return await cache.match("/offline/index.html");
                }
            })()
        );
    }
    else if(CACHED_FILES.includes(e.request.url)){
        e.respondWith(caches.match(e.request));
    }
});