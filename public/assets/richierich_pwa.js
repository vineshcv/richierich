/* Richie Rich Boutique — install prompt (Android one-tap + iOS guide) */
(function () {
  var DISMISS_KEY = "richierich-pwa-dismiss";
  var DISMISS_DAYS = 7;
  var deferredPrompt = null;
  var banner = null;

  function isStandalone() {
    return (
      window.matchMedia("(display-mode: standalone)").matches ||
      window.navigator.standalone === true ||
      document.referrer.indexOf("android-app://") === 0
    );
  }

  function isDismissed() {
    try {
      var raw = localStorage.getItem(DISMISS_KEY);
      if (!raw) return false;
      var until = parseInt(raw, 10);
      if (!until || Date.now() > until) {
        localStorage.removeItem(DISMISS_KEY);
        return false;
      }
      return true;
    } catch (e) {
      return false;
    }
  }

  function dismiss() {
    try {
      localStorage.setItem(
        DISMISS_KEY,
        String(Date.now() + DISMISS_DAYS * 24 * 60 * 60 * 1000)
      );
    } catch (e) {}
    hideBanner();
  }

  function isIos() {
    var ua = window.navigator.userAgent || "";
    var iOS = /iPad|iPhone|iPod/.test(ua);
    var iPadOS = ua.indexOf("Mac") !== -1 && "ontouchend" in document;
    return iOS || iPadOS;
  }

  function isIosSafari() {
    var ua = window.navigator.userAgent || "";
    var isWebkit = /WebKit/i.test(ua);
    var isOther = /CriOS|FxiOS|EdgiOS|OPiOS|Chrome|Firefox|Edg/i.test(ua);
    return isWebkit && !isOther;
  }

  function hideBanner() {
    if (!banner) return;
    banner.classList.remove("is-visible");
    setTimeout(function () {
      if (banner && banner.parentNode) banner.parentNode.removeChild(banner);
      banner = null;
      document.documentElement.classList.remove("dress-pwa-open");
    }, 280);
  }

  function ensureBanner() {
    if (banner) return banner;
    banner = document.createElement("div");
    banner.id = "dress-pwa-banner";
    banner.setAttribute("role", "dialog");
    banner.setAttribute("aria-label", "Install Richie Rich");
    var iconSrc = (document.querySelector('link[rel="apple-touch-icon"]') || {}).href
      || "assets/dress_icon-192.png";
    banner.innerHTML =
      '<div class="dress-pwa-inner">' +
      '<img class="dress-pwa-icon" src="' + iconSrc + '" alt="" width="48" height="48" />' +
      '<div class="dress-pwa-copy">' +
      "<strong>Install Richie Rich</strong>" +
      '<span class="dress-pwa-sub"></span>' +
      "</div>" +
      '<div class="dress-pwa-actions">' +
      '<button type="button" class="dress-pwa-install">Install</button>' +
      '<button type="button" class="dress-pwa-close" aria-label="Dismiss">×</button>' +
      "</div>" +
      "</div>" +
      '<div class="dress-pwa-ios-steps" hidden></div>';
    document.body.appendChild(banner);
    document.documentElement.classList.add("dress-pwa-open");

    banner.querySelector(".dress-pwa-close").addEventListener("click", dismiss);
    return banner;
  }

  function showAndroidBanner() {
    var el = ensureBanner();
    el.querySelector(".dress-pwa-sub").textContent =
      "Add to your home screen for faster access.";
    el.querySelector(".dress-pwa-ios-steps").hidden = true;
    var btn = el.querySelector(".dress-pwa-install");
    btn.textContent = "Install";
    btn.onclick = function () {
      if (!deferredPrompt) return;
      deferredPrompt.prompt();
      deferredPrompt.userChoice.finally(function () {
        deferredPrompt = null;
        hideBanner();
      });
    };
    requestAnimationFrame(function () {
      el.classList.add("is-visible");
    });
  }

  function iosStepsHtml() {
    if (isIosSafari()) {
      return (
        "<ol>" +
        "<li>Tap the <strong>Share</strong> button at the bottom</li>" +
        "<li>Scroll and tap <strong>Add to Home Screen</strong></li>" +
        "<li>Tap <strong>Add</strong></li>" +
        "</ol>"
      );
    }
    return (
      "<ol>" +
      "<li>Tap the <strong>Share</strong> / menu icon</li>" +
      "<li>Choose <strong>Add to Home Screen</strong> if listed</li>" +
      "<li>Or open this page in <strong>Safari</strong>, then Share → Add to Home Screen</li>" +
      "</ol>"
    );
  }

  function showIosGuide() {
    if (isStandalone() || isDismissed()) return;
    var el = ensureBanner();
    el.querySelector(".dress-pwa-sub").textContent =
      "Add to Home Screen to use it like an app.";
    var steps = el.querySelector(".dress-pwa-ios-steps");
    steps.hidden = false;
    steps.innerHTML = iosStepsHtml();
    var btn = el.querySelector(".dress-pwa-install");
    btn.textContent = "How to";
    btn.onclick = function () {
      el.classList.toggle("is-expanded");
      btn.textContent = el.classList.contains("is-expanded") ? "Got it" : "How to";
      if (!el.classList.contains("is-expanded")) dismiss();
    };
    requestAnimationFrame(function () {
      el.classList.add("is-visible");
    });
  }

  function scheduleIosGuide() {
    setTimeout(showIosGuide, 1600);
  }

  if (isStandalone() || isDismissed()) return;

  window.addEventListener("beforeinstallprompt", function (e) {
    e.preventDefault();
    deferredPrompt = e;
    showAndroidBanner();
  });

  window.addEventListener("appinstalled", function () {
    deferredPrompt = null;
    hideBanner();
    try {
      localStorage.setItem(DISMISS_KEY, String(Date.now() + 365 * 24 * 60 * 60 * 1000));
    } catch (e) {}
  });

  // iOS never fires beforeinstallprompt (Safari or Chrome) — show a how-to banner
  if (isIos()) {
    if (document.readyState === "complete") scheduleIosGuide();
    else window.addEventListener("load", scheduleIosGuide);
  }
})();
