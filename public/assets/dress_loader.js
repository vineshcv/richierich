(function () {
  var LOADER_MIN_MS = 550;
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
      "display:flex;flex-direction:column;align-items:center;gap:1.2rem;" +
      "animation:dress-boot-rise .85s cubic-bezier(.22,1,.36,1) both;" +
      "}" +
      "#" + ROOT_ID + " .dress-boot-logo{" +
      "width:min(240px,62vw);height:auto;display:block;" +
      "animation:dress-boot-pulse 1.7s ease-in-out infinite;" +
      "filter:drop-shadow(0 6px 18px rgba(139,30,45,.18));" +
      "}" +
      "#" + ROOT_ID + " .dress-boot-ring{" +
      "width:42px;height:42px;border-radius:999px;" +
      "border:2px solid rgba(139,30,45,.25);" +
      "border-top-color:#8b1e2d;" +
      "animation:dress-boot-spin .85s linear infinite;" +
      "}" +
      "#" + ROOT_ID + " .dress-boot-text{" +
      "margin:0;font-family:Arial,Helvetica,sans-serif;" +
      "font-size:.68rem;letter-spacing:.32em;text-transform:uppercase;" +
      "color:#8b1e2d;font-weight:600;" +
      "animation:dress-boot-fade 1.4s ease-in-out infinite;" +
      "}" +
      "@keyframes dress-boot-rise{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}" +
      "@keyframes dress-boot-pulse{0%,100%{transform:scale(1);opacity:.92}50%{transform:scale(1.04);opacity:1}}" +
      "@keyframes dress-boot-spin{to{transform:rotate(360deg)}}" +
      "@keyframes dress-boot-fade{0%,100%{opacity:.4}50%{opacity:.95}}" +
      "@media (prefers-reduced-motion:reduce){" +
      "#" + ROOT_ID + " .dress-boot-logo,#" + ROOT_ID + " .dress-boot-ring,#" + ROOT_ID + " .dress-boot-text{animation:none!important}" +
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
    loader.innerHTML =
      '<div class="dress-boot-inner">' +
      '<img class="dress-boot-logo" src="' + ((window.DRESS_CONFIG && window.DRESS_CONFIG.logo) || "assets/dress_logo.png") + '" alt="Richie Rich Boutique" />' +
      '<div class="dress-boot-ring" aria-hidden="true"></div>' +
      '<p class="dress-boot-text">Loading</p>' +
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
    setTimeout(finish, 2200);
  }

  if ("serviceWorker" in navigator) {
    window.addEventListener("load", function () {
      navigator.serviceWorker.register((window.DRESS_CONFIG && window.DRESS_CONFIG.sw) || "./sw.js").catch(function () {
        /* ignore registration errors (e.g. file://) */
      });
    });
  }
})();
