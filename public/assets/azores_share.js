(function () {
  function cfg() {
    return window.AZORES_SHARE || {};
  }

  function siteName() {
    return cfg().siteName || "Azores Interactive";
  }

  function siteBase() {
    var path = window.location.pathname;
    var dir = path.endsWith("/") ? path : path.replace(/[^/]*$/, "");
    return window.location.origin + dir;
  }

  function absoluteUrl(path) {
    if (!path) return siteBase();
    if (/^https?:\/\//i.test(path)) return path;
    return siteBase() + String(path).replace(/^\//, "");
  }

  function productPageName() {
    return cfg().productPage || "index.html";
  }

  function productUrl(productId) {
    var c = cfg();
    if (typeof c.productUrl === "function") {
      var custom = c.productUrl(productId);
      if (custom) return custom;
    }
    if (window.productDetailUrl && window.isShopProduct && isShopProduct(productId)) {
      return window.productDetailUrl(productId);
    }
    return absoluteUrl(productPageName() + "?id=" + encodeURIComponent(productId));
  }

  function getProduct(productId) {
    var c = cfg();
    if (typeof c.getProduct === "function") return c.getProduct(productId);
    if (window.getProductById) return getProductById(productId);
    if (window.getCarById) return getCarById(productId);
    if (window.getCctvProduct) return getCctvProduct(productId);
    if (window.getSolarProduct) return getSolarProduct(productId);
    if (window.getInverterProduct) return getInverterProduct(productId);
    return null;
  }

  function productList() {
    var c = cfg();
    if (typeof c.products === "function") return c.products() || [];
    if (Array.isArray(c.products)) return c.products;
    return (
      window.CCTV_PRODUCTS ||
      window.SOLAR_PRODUCTS ||
      window.INVERTER_PRODUCTS ||
      (window.FLEET && FLEET.cars) ||
      (window.STORE && STORE.products) ||
      []
    );
  }

  function productImage(product) {
    if (!product) return "";
    return product.image || (product.images && product.images[0]) || "";
  }

  function defaultOgImage() {
    var c = cfg();
    if (c.defaultImage) return absoluteUrl(c.defaultImage);
    var list = productList();
    if (list.length && productImage(list[0])) return absoluteUrl(productImage(list[0]));
    return absoluteUrl("azoresLogo.png");
  }

  function shareText(product) {
    var c = cfg();
    if (typeof c.shareText === "function") return c.shareText(product);
    if (!product) return document.title;
    if (product.pricePerDay != null && typeof formatINR === "function") {
      return product.name + " — " + formatINR(product.pricePerDay) + "/day";
    }
    if (product.price != null && typeof formatINR === "function") {
      return product.name + " — " + formatINR(product.price);
    }
    if (product.brand) return product.name + " — " + product.brand;
    return product.name;
  }

  function setMeta(attr, key, value) {
    if (!value) return;
    var selector =
      attr === "property"
        ? 'meta[property="' + key + '"]'
        : 'meta[name="' + key + '"]';
    var el = document.querySelector(selector);
    if (!el) {
      el = document.createElement("meta");
      el.setAttribute(attr, key);
      document.head.appendChild(el);
    }
    el.setAttribute("content", value);
  }

  function applyDefaultOg() {
    var title =
      (document.querySelector('meta[property="og:title"]') &&
        document.querySelector('meta[property="og:title"]').getAttribute("content")) ||
      document.title ||
      siteName();

    var descEl = document.querySelector('meta[name="description"]');
    var desc = descEl
      ? descEl.getAttribute("content")
      : siteName() + " — enquire on WhatsApp.";

    var image = defaultOgImage();
    var url = window.location.href.split("#")[0];

    setMeta("property", "og:type", "website");
    setMeta("property", "og:site_name", siteName());
    setMeta("property", "og:title", title);
    setMeta("property", "og:description", desc);
    setMeta("property", "og:image", image);
    setMeta("property", "og:image:width", "800");
    setMeta("property", "og:image:height", "800");
    setMeta("property", "og:url", url);
    setMeta("name", "twitter:card", "summary_large_image");
    setMeta("name", "twitter:title", title);
    setMeta("name", "twitter:description", desc);
    setMeta("name", "twitter:image", image);

    document
      .querySelectorAll('meta[property="og:image"], meta[name="twitter:image"]')
      .forEach(function (el) {
        var c = el.getAttribute("content") || "";
        if (c && !/^https?:\/\//i.test(c)) {
          el.setAttribute("content", absoluteUrl(c));
        }
      });
  }

  function setProductOg(product) {
    if (!product) return;
    var title = product.name + " | " + siteName();
    var desc =
      product.description ||
      product.short ||
      shareText(product) + " | " + siteName();
    var image = absoluteUrl(productImage(product) || "azoresLogo.png");
    var url = productUrl(product.id);

    document.title = title;
    setMeta("name", "description", desc);
    setMeta("property", "og:type", "product");
    setMeta("property", "og:site_name", siteName());
    setMeta("property", "og:title", title);
    setMeta("property", "og:description", desc);
    setMeta("property", "og:image", image);
    setMeta("property", "og:url", url);
    setMeta("name", "twitter:card", "summary_large_image");
    setMeta("name", "twitter:title", title);
    setMeta("name", "twitter:description", desc);
    setMeta("name", "twitter:image", image);
  }

  function canNativeShare() {
    return typeof navigator !== "undefined" && typeof navigator.share === "function";
  }

  function toast(msg) {
    if (window.Cart && Cart.showToast) {
      Cart.showToast(msg);
      return;
    }
    if (window.showToast) {
      showToast(msg);
      return;
    }
    var el = document.getElementById("azores-toast");
    if (!el) {
      el = document.createElement("div");
      el.id = "azores-toast";
      el.setAttribute(
        "style",
        "position:fixed;left:50%;bottom:1.5rem;transform:translateX(-50%);z-index:100000;" +
          "background:#111;color:#fff;padding:.7rem 1.1rem;border-radius:999px;font:500 .85rem/1.2 system-ui,sans-serif;" +
          "opacity:0;transition:opacity .25s ease;pointer-events:none;box-shadow:0 10px 30px rgba(0,0,0,.25)"
      );
      document.body.appendChild(el);
    }
    el.textContent = msg;
    el.style.opacity = "1";
    clearTimeout(el._t);
    el._t = setTimeout(function () {
      el.style.opacity = "0";
    }, 1800);
  }

  function copyText(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      return navigator.clipboard.writeText(text);
    }
    return new Promise(function (resolve, reject) {
      var input = document.createElement("textarea");
      input.value = text;
      input.setAttribute("readonly", "");
      input.style.position = "fixed";
      input.style.left = "-9999px";
      document.body.appendChild(input);
      input.select();
      try {
        document.execCommand("copy");
        resolve();
      } catch (err) {
        reject(err);
      } finally {
        document.body.removeChild(input);
      }
    });
  }

  function shareProduct(productId) {
    var product = getProduct(productId);
    var url = product ? productUrl(product.id) : window.location.href;
    var title = product ? product.name + " | " + siteName() : document.title;
    var text = product ? shareText(product) : title;

    if (product) setProductOg(product);

    function fallbackCopy() {
      copyText(url)
        .then(function () {
          toast("Link copied");
        })
        .catch(function () {
          // Last resort: still show the link
          try {
            window.prompt("Copy this link:", url);
          } catch (err) {
            /* ignore */
          }
          toast("Copy link: " + url);
        });
    }

    if (!canNativeShare()) {
      fallbackCopy();
      return;
    }

    function sharePlain() {
      return navigator.share({ title: title, text: text, url: url }).catch(function () {
        fallbackCopy();
      });
    }

    if (product && productImage(product) && navigator.canShare) {
      fetch(absoluteUrl(productImage(product)))
        .then(function (res) {
          return res.blob();
        })
        .then(function (blob) {
          var ext = (blob.type || "").indexOf("png") !== -1 ? "png" : "jpg";
          var file = new File([blob], (product.id || "product") + "." + ext, {
            type: blob.type || "image/jpeg",
          });
          var withFiles = { title: title, text: text, url: url, files: [file] };
          if (navigator.canShare(withFiles)) {
            return navigator.share(withFiles).catch(function () {
              return sharePlain();
            });
          }
          return sharePlain();
        })
        .catch(function () {
          sharePlain();
        });
      return;
    }

    sharePlain();
  }

  function shareCurrentPage() {
    var params = new URLSearchParams(window.location.search);
    var id = params.get("id");
    if (id) {
      shareProduct(id);
      return;
    }
    var url = window.location.href.split("#")[0];
    var title = document.title;
    if (canNativeShare()) {
      navigator.share({ title: title, url: url }).catch(function () {});
      return;
    }
    copyText(url).then(function () {
      toast("Link copied");
    });
  }

  function bindShareClicks() {
    document.addEventListener("click", function (e) {
      var btn = e.target.closest("[data-share]");
      if (!btn) return;
      e.preventDefault();
      e.stopPropagation();
      var id = btn.getAttribute("data-share");
      if (id) shareProduct(id);
      else shareCurrentPage();
    });
  }

  function boot() {
    // On product/detail pages, set OG from the product image immediately
    var params = new URLSearchParams(window.location.search);
    var id = params.get("id");
    var product = id ? getProduct(id) : null;
    if (product) setProductOg(product);
    else applyDefaultOg();
    bindShareClicks();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }

  window.Share = {
    shareProduct: shareProduct,
    shareCurrentPage: shareCurrentPage,
    setProductOg: setProductOg,
    productUrl: productUrl,
    absoluteUrl: absoluteUrl,
    shareSvg: function (cls) {
      cls = cls || "w-4 h-4";
      return (
        '<svg class="' +
        cls +
        '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">' +
        '<path stroke-linecap="round" stroke-linejoin="round" d="M4 12v7a1 1 0 001 1h14a1 1 0 001-1v-7M16 6l-4-4-4 4M12 2v13"/>' +
        "</svg>"
      );
    },
  };
})();
