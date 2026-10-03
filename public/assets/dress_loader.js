(function () {
  var isHome = document.documentElement.getAttribute("data-loader") === "1";
  var LOADER_MIN_MS = 1500;
  var STYLE_ID = "dress-boot-style";
  var ROOT_ID = "dress-boot";

  function injectStyles() {
    if (document.getElementById(STYLE_ID)) return;
    var style = document.createElement("style");
    style.id = STYLE_ID;
    style.textContent =
      "html.dress-loading,html.dress-loading body{overflow:hidden!important;}" +
      "#" + ROOT_ID + "{" +
      "position:fixed;inset:0;z-index:99999;display:grid;place-items:center;" +
      "background:#ffffff;transition:opacity .5s ease,visibility .5s ease;" +
      "}" +
      "#" + ROOT_ID + ".is-done{opacity:0;visibility:hidden;pointer-events:none;}" +
      "#" + ROOT_ID + " .dress-boot-inner{" +
      "display:flex;flex-direction:column;align-items:center;" +
      "}" +
      "#" + ROOT_ID + " .dress-boot-logo{" +
      "width:72px;height:72px;display:block;object-fit:contain;" +
      "animation:dress-boot-in .7s ease both,dress-boot-pulse 1.4s ease .7s infinite;" +
      "}" +
      "@keyframes dress-boot-in{from{opacity:0;transform:scale(.92)}to{opacity:1;transform:scale(1)}}" +
      "@keyframes dress-boot-pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.04)}}" +
      "@media (prefers-reduced-motion:reduce){" +
      "#" + ROOT_ID + " .dress-boot-logo{animation:none}" +
      "}";
    (document.head || document.documentElement).appendChild(style);
  }

  function ensureLoader() {
    var existing = document.getElementById(ROOT_ID);
    if (existing) return existing;

    var legacy = document.getElementById("page-loader");
    if (legacy) legacy.style.display = "none";

    var loader = document.createElement("div");
    loader.id = ROOT_ID;
    loader.setAttribute("aria-hidden", "true");
    var mark = (window.DRESS_CONFIG && window.DRESS_CONFIG.mark) || "assets/dress_mark.webp";
    loader.innerHTML =
      '<div class="dress-boot-inner">' +
      '<img class="dress-boot-logo" src="' + mark + '" alt="" />' +
      "</div>";
    (document.body || document.documentElement).appendChild(loader);
    return loader;
  }

  function hideLoader() {
    var loader = document.getElementById(ROOT_ID);
    if (!loader) {
      document.documentElement.classList.remove("dress-loading", "azores-loading", "is-loading");
      return;
    }
    loader.classList.add("is-done");
    setTimeout(function () {
      if (loader.parentNode) loader.parentNode.removeChild(loader);
      document.documentElement.classList.remove("dress-loading", "azores-loading", "is-loading");
      var style = document.getElementById(STYLE_ID);
      if (style && style.parentNode) style.parentNode.removeChild(style);
      var legacy = document.getElementById("page-loader");
      if (legacy && legacy.parentNode) legacy.parentNode.removeChild(legacy);
    }, 520);
  }

  if ("serviceWorker" in navigator) {
    navigator.serviceWorker.getRegistrations().then(function (registrations) {
      registrations.forEach(function (registration) {
        registration.unregister();
      });
    });
  }

  if (!isHome) {
    return;
  }

  document.documentElement.classList.add("dress-loading", "is-loading");
  injectStyles();

  function mount() {
    ensureLoader();
  }

  if (document.body) mount();
  else document.addEventListener("DOMContentLoaded", mount);

  var started = Date.now();
  var finished = false;
  function finish() {
    if (finished) return;
    finished = true;
    var wait = Math.max(0, LOADER_MIN_MS - (Date.now() - started));
    setTimeout(hideLoader, wait);
  }

  if (document.readyState === "complete") finish();
  else {
    window.addEventListener("load", finish);
    setTimeout(finish, 4200);
  }
})();
