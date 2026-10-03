/* Richie Rich PWA service worker — dress demo parity */
var CACHE_VERSION = "richierich-v8";
var SHELL_CACHE = CACHE_VERSION + "-shell";
var RUNTIME_CACHE = CACHE_VERSION + "-runtime";
var OFFLINE_FALLBACK = "./";

var PRECACHE_URLS = [
  "./",
  "./shop",
  "./cart",
  "./contact",
  "./combos",
  "./season",
  "./bulk",
  "./manifest.webmanifest",
  "./assets/dress_styles.css",
  "./assets/dress_logo.png",
  "./assets/dress_mark.png",
  "./assets/dress_icon-192.png",
  "./assets/dress_icon-512.png",
  "./assets/dress_apple-touch-icon.png",
  "./assets/dress_main.js",
  "./assets/dress_cart.js",
  "./assets/dress_cart-page.js",
  "./assets/dress_loader.js",
  "./assets/dress_pwa.js",
  "./assets/azores_share.js",
];

self.addEventListener("install", function (event) {
  event.waitUntil(
    caches.open(SHELL_CACHE).then(function (cache) {
      return Promise.all(
        PRECACHE_URLS.map(function (url) {
          return cache.add(url).catch(function () {});
        })
      );
    }).then(function () {
      return self.skipWaiting();
    })
  );
});

self.addEventListener("activate", function (event) {
  event.waitUntil(
    caches.keys().then(function (keys) {
      return Promise.all(
        keys.map(function (key) {
          if (key.indexOf("richierich-") === 0 && key.indexOf(CACHE_VERSION) !== 0) {
            return caches.delete(key);
          }
        })
      );
    }).then(function () {
      return self.clients.claim();
    })
  );
});

self.addEventListener("fetch", function (event) {
  var request = event.request;
  if (request.method !== "GET") return;

  var url = new URL(request.url);
  if (url.origin !== self.location.origin) return;

  if (url.pathname.indexOf("/admin") !== -1 || url.pathname.indexOf("/api") !== -1) {
    return;
  }

  if (request.mode === "navigate") {
    event.respondWith(
      fetch(request)
        .then(function (response) {
          var copy = response.clone();
          caches.open(RUNTIME_CACHE).then(function (cache) {
            cache.put(request, copy);
          });
          return response;
        })
        .catch(function () {
          return caches.match(request).then(function (cached) {
            return cached || caches.match(OFFLINE_FALLBACK);
          });
        })
    );
    return;
  }

  if (url.pathname.indexOf("/assets/") !== -1 || url.pathname.indexOf("/storage/") !== -1) {
    event.respondWith(
      caches.match(request).then(function (cached) {
        if (cached) return cached;
        return fetch(request).then(function (response) {
          var copy = response.clone();
          caches.open(RUNTIME_CACHE).then(function (cache) {
            cache.put(request, copy);
          });
          return response;
        });
      })
    );
  }
});
