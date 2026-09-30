/* Richie Rich Boutique */

const WHATSAPP_NUMBER = (window.DRESS_CONFIG && window.DRESS_CONFIG.whatsapp) || "919447836797";

function dressRoute(key) {
  var routes = (window.DRESS_CONFIG && window.DRESS_CONFIG.routes) || {};
  var defaults = {
    home: "dressindex.html",
    shop: "dressproducts.html",
    cart: "dresscart.html",
    contact: "dresscontact.html",
    combos: "dresscombos.html",
    season: "dressseason.html",
    bulk: "dressbulk.html",
  };
  return routes[key] || defaults[key] || "#";
}

function productDetailUrl(id) {
  var routes = (window.DRESS_CONFIG && window.DRESS_CONFIG.routes) || {};
  if (routes.product) {
    return String(routes.product).replace("__ID__", encodeURIComponent(id));
  }
  return "/product/" + encodeURIComponent(id);
}

function isShopProduct(id) {
  return (window.DRESS_PRODUCTS || []).some(function (p) {
    return p.id === id;
  });
}

function productGallery(p) {
  var g = Array.isArray(p.gallery) ? p.gallery.slice() : [];
  if (p.image && g.indexOf(p.image) === -1) g.unshift(p.image);
  var seen = {};
  return g.filter(function (src) {
    if (!src || seen[src]) return false;
    seen[src] = true;
    return true;
  });
}

window.productDetailUrl = productDetailUrl;
window.isShopProduct = isShopProduct;

const WA_ICON =
  '<svg class="ico-wa" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20.52 3.48A11.78 11.78 0 0012.04 0C5.5 0 .2 5.3.2 11.82c0 2.08.55 4.11 1.6 5.9L0 24l6.45-1.69a11.8 11.8 0 005.58 1.42h.01c6.54 0 11.84-5.3 11.84-11.82 0-3.16-1.23-6.13-3.36-8.43zM12.05 21.5h-.01a9.7 9.7 0 01-4.94-1.35l-.35-.21-3.82 1 1.02-3.72-.23-.38a9.7 9.7 0 01-1.5-5.18c0-5.36 4.36-9.72 9.73-9.72a9.66 9.66 0 016.88 2.85 9.66 9.66 0 012.85 6.88c0 5.36-4.37 9.73-9.73 9.73zm5.33-7.28c-.29-.15-1.72-.85-1.99-.95-.27-.1-.46-.15-.66.15-.2.29-.76.95-.93 1.14-.17.2-.34.22-.63.07-.29-.15-1.22-.45-2.33-1.43-.86-.77-1.44-1.72-1.61-2.01-.17-.29-.02-.45.13-.6.13-.13.29-.34.43-.51.15-.17.2-.29.29-.49.1-.2.05-.37-.02-.52-.07-.15-.66-1.59-.9-2.18-.24-.57-.48-.49-.66-.5h-.56c-.2 0-.52.07-.79.37-.27.29-1.04 1.02-1.04 2.48s1.07 2.87 1.22 3.07c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.72-.7 1.96-1.38.24-.68.24-1.26.17-1.38-.07-.11-.26-.18-.55-.33z"/></svg>';

const SHARE_ICON =
  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 12v7a1 1 0 001 1h14a1 1 0 001-1v-7M16 6l-4-4-4 4M12 2v13"/></svg>';

const DETAIL_ICON =
  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';

const CART_ICON =
  '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>';

function cartActionBtn(id, large) {
  if (large) {
    return (
      '<button type="button" class="icon-action icon-action-lg icon-action-cart" data-add-cart="' +
      id +
      '" aria-label="Add to cart" title="Add to cart">' +
      CART_ICON +
      '<span class="cart-plus-badge" aria-hidden="true">+</span></button>'
    );
  }
  return (
    '<button type="button" class="shop-icon-action shop-icon-cart" data-add-cart="' +
    id +
    '" aria-label="Add to cart" title="Add to cart">' +
    CART_ICON +
    '<span class="cart-plus-badge" aria-hidden="true">+</span></button>'
  );
}

window.AZORES_SHARE = {
  siteName: (window.DRESS_CONFIG && window.DRESS_CONFIG.siteName) || "Richie Rich Boutique",
  productPage: dressRoute("shop"),
  defaultImage: (window.DRESS_CONFIG && window.DRESS_CONFIG.defaultImage) || "assets/dress_prod-red-saree.png",
  getProduct: function (id) {
    return window.getDressProduct ? getDressProduct(id) : null;
  },
  products: function () {
    return window.DRESS_PRODUCTS || [];
  },
  shareText: function (p) {
    return p.name + (p.price ? " — " + p.price : "");
  },
  productUrl: function (id) {
    if (isShopProduct(id)) return productDetailUrl(id);
    return null;
  },
};

var pendingAction = null;

function buildWaUrl(message) {
  return (
    "https://wa.me/" +
    WHATSAPP_NUMBER +
    "?text=" +
    encodeURIComponent(message || "Hi Richie Rich Boutique, I want to enquire about a dress.")
  );
}

function openWhatsApp(message) {
  window.open(buildWaUrl(message), "_blank", "noopener,noreferrer");
}

function buildProductEnquiry(name, price, extra) {
  var lines = [
    "Hi Richie Rich Boutique,",
    "",
    "I'm interested in:",
    "",
    "🛒 " + (name || "your dresses") + (price ? " — " + price : ""),
    "",
    "Could you please confirm:",
    "• Availability",
    "• Current price / size options",
    "",
    "Thank you.",
  ];
  if (extra) {
    lines.push("");
    lines.push("📝 Note");
    lines.push(extra);
  }
  return lines.join("\n");
}

/** WhatsApp enquiry — opens chat directly (no payment). */
function enquireWhatsApp(opts) {
  opts = opts || {};
  var name = opts.label || "";
  var price = "";
  var pid = opts.productId || "";
  if (pid && window.getDressProduct) {
    var p = getDressProduct(pid);
    if (p) {
      name = p.name || name;
      price = p.price || "";
    }
  }
  var msg = buildProductEnquiry(name, price, opts.baseMessage && opts.baseMessage !== name ? opts.baseMessage : "");
  openWhatsApp(msg);
}

function wireDirectWhatsAppLinks() {
  document.querySelectorAll("[data-wa-direct]").forEach(function (el) {
    el.setAttribute("href", buildWaUrl(el.getAttribute("data-wa-direct") || ""));
    el.setAttribute("target", "_blank");
    el.setAttribute("rel", "noopener noreferrer");
  });
}

function wireNav() {
  var toggle = document.querySelector(".nav-toggle");
  var menu = document.querySelector(".nav-menu");
  if (!toggle || !menu) return;

  var ICONS = {
    home: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9.5z"/></svg>',
    products: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h10"/></svg>',
    combos: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7l8-4 8 4-8 4-8-4z"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 12l8 4 8-4M4 17l8 4 8-4"/></svg>',
    cart: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
    menu: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M5 7h14M5 12h14M5 17h14"/></svg>',
  };

  function pageKey() {
    var fromBody = document.body && document.body.getAttribute("data-dress-page");
    if (fromBody) return fromBody;
    var file = (location.pathname.split("/").pop() || "").replace(/\.html$/i, "").toLowerCase();
    if (!file || file === "dressindex" || file === "public") return "home";
    if (file === "dressproducts" || file === "dressdetail" || file === "shop") return "products";
    if (file === "dresscart" || file === "cart") return "cart";
    if (file === "dresscontact" || file === "contact") return "contact";
    return "more";
  }

  var navbar = document.querySelector(".navbar");
  var mobileMq = window.matchMedia("(max-width: 980px)");

  function placeMenuForViewport() {
    if (mobileMq.matches) {
      if (menu.parentElement !== document.body) document.body.appendChild(menu);
    } else if (navbar && menu.parentElement !== navbar) {
      navbar.appendChild(menu);
    }
  }

  placeMenuForViewport();
  if (mobileMq.addEventListener) mobileMq.addEventListener("change", placeMenuForViewport);
  else if (mobileMq.addListener) mobileMq.addListener(placeMenuForViewport);

  var backdrop = document.querySelector(".nav-menu-backdrop");
  if (!backdrop) {
    backdrop = document.createElement("div");
    backdrop.className = "nav-menu-backdrop";
    backdrop.setAttribute("aria-hidden", "true");
    document.body.appendChild(backdrop);
  }

  var existingDock = document.querySelector(".dress-bottom-nav");
  if (existingDock) existingDock.remove();

  var dock = document.createElement("nav");
  dock.className = "dress-bottom-nav";
  dock.setAttribute("aria-label", "Mobile");
  dock.innerHTML =
    '<a class="dress-bottom-item" data-tab="home" href="' + dressRoute("home") + '">' +
    ICONS.home +
    "<span>Home</span></a>" +
    '<a class="dress-bottom-item" data-tab="products" href="' + dressRoute("shop") + '">' +
    ICONS.products +
    "<span>Shop</span></a>" +
    '<a class="dress-bottom-item" data-tab="contact" href="' + dressRoute("contact") + '">' +
    ICONS.combos +
    "<span>Contact</span></a>" +
    '<a class="dress-bottom-item dress-bottom-cart" data-tab="cart" href="' + dressRoute("cart") + '">' +
    ICONS.cart +
    '<span>Cart</span><em data-cart-count hidden>0</em></a>' +
    '<button type="button" class="dress-bottom-menu" aria-label="Open menu" aria-expanded="false">' +
    ICONS.menu +
    "</button>";
  document.body.appendChild(dock);

  var menuBtn = dock.querySelector(".dress-bottom-menu");
  var active = pageKey();
  dock.querySelectorAll("[data-tab]").forEach(function (el) {
    if (el.getAttribute("data-tab") === active) el.classList.add("is-active");
  });
  if (active === "more") menuBtn.classList.add("is-active");

  function setOpen(open) {
    placeMenuForViewport();
    menu.classList.toggle("open", open);
    backdrop.classList.toggle("open", open);
    toggle.setAttribute("aria-expanded", open ? "true" : "false");
    menuBtn.setAttribute("aria-expanded", open ? "true" : "false");
    menuBtn.setAttribute("aria-label", open ? "Close menu" : "Open menu");
    document.documentElement.classList.toggle("dress-nav-open", open);
  }

  function toggleMenu(e) {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }
    setOpen(!menu.classList.contains("open"));
  }

  menuBtn.addEventListener("click", toggleMenu);
  toggle.addEventListener("click", toggleMenu);

  backdrop.addEventListener("click", function () {
    setOpen(false);
  });

  menu.querySelectorAll("a").forEach(function (link) {
    link.addEventListener("click", function () {
      setOpen(false);
    });
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") setOpen(false);
  });

  if (window.Cart && typeof window.Cart.updateCartBadge === "function") {
    window.Cart.updateCartBadge();
  }
}

function collectFormMessage(form, title) {
  var lines = [title || "Hi Richie Rich Boutique,"];
  new FormData(form).forEach(function (value, key) {
    if (String(value).trim()) lines.push(key + ": " + value);
  });
  return lines.join("\n");
}

function wireFormsToWhatsApp() {
  document.querySelectorAll("form[data-wa-form]").forEach(function (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var title = form.getAttribute("data-wa-form") || "Hi Richie Rich Boutique,";
      openWhatsApp(collectFormMessage(form, title));
    });
  });
}

function ensureActionModal() {
  if (document.getElementById("dress-action-modal")) return;
  var el = document.createElement("div");
  el.id = "dress-action-modal";
  el.className = "dress-modal";
  el.hidden = true;
  el.innerHTML =
    '<div class="dress-modal-backdrop" data-dress-close></div>' +
    '<div class="dress-modal-card" role="dialog" aria-modal="true" aria-labelledby="dress-modal-title">' +
    '<button type="button" class="dress-modal-x" data-dress-close aria-label="Close">×</button>' +
    '<p class="dress-modal-eyebrow">How should we help?</p>' +
    '<h3 id="dress-modal-title">Enquire on WhatsApp</h3>' +
    '<p class="dress-modal-sub" id="dress-modal-sub">Home delivery, Pack &amp; Pickup, or a quick enquiry.</p>' +
    '<div class="dress-action-grid" id="dress-step-choice">' +
    '<button type="button" class="dress-action-card" data-dress-choice="delivery">' +
    "<strong>Home delivery</strong><span>We’ll take your location for delivery.</span></button>" +
    '<button type="button" class="dress-action-card" data-dress-choice="pack">' +
    "<strong>Pack &amp; Pickup</strong><span>Choose a pickup time at the shop.</span></button>" +
    '<button type="button" class="dress-action-card" data-dress-choice="enquiry">' +
    "<strong>Enquiry</strong><span>Ask price, size or stock details.</span></button>" +
    "</div>" +
    '<div class="dress-step" id="dress-step-delivery" hidden>' +
    "<h4>Home delivery</h4>" +
    "<p>Share your location so we can confirm delivery.</p>" +
    '<button type="button" class="btn btn-outline" id="dress-use-location">Use my location</button>' +
    '<p class="dress-loc-status" id="dress-loc-status">Location not added yet.</p>' +
    '<label class="dress-field-label" for="dress-address">Address / landmark</label>' +
    '<textarea id="dress-address" rows="3" placeholder="House / street / area in Trivandrum"></textarea>' +
    '<label class="dress-field-label" for="dress-note-delivery">Note (optional)</label>' +
    '<textarea id="dress-note-delivery" rows="2" placeholder="e.g. Please deliver after 6 PM"></textarea>' +
    '<div class="dress-step-actions">' +
    '<button type="button" class="btn btn-ghost" data-dress-back>Back</button>' +
    '<button type="button" class="btn btn-primary" id="dress-continue-delivery">Continue</button>' +
    "</div></div>" +
    '<div class="dress-step" id="dress-step-pack" hidden>' +
    "<h4>Pack &amp; Pickup</h4>" +
    "<p>Tell us when you’ll pick up the packed order.</p>" +
    '<label class="dress-field-label" for="dress-pickup-date">Pickup date</label>' +
    '<input id="dress-pickup-date" type="date" />' +
    '<label class="dress-field-label" for="dress-pickup-time">Pickup time</label>' +
    '<input id="dress-pickup-time" type="time" />' +
    '<label class="dress-field-label" for="dress-note-pack">Note (optional)</label>' +
    '<textarea id="dress-note-pack" rows="2" placeholder="e.g. Please keep the order ready by pickup time"></textarea>' +
    '<div class="dress-step-actions">' +
    '<button type="button" class="btn btn-ghost" data-dress-back>Back</button>' +
    '<button type="button" class="btn btn-primary" id="dress-continue-pack">Continue</button>' +
    "</div></div>" +
    "</div>";
  document.body.appendChild(el);

  el.addEventListener("click", function (e) {
    if (e.target.closest("[data-dress-close]")) closeActionPopup();
    if (e.target.closest("[data-dress-back]")) showModalStep("choice");
    var choice = e.target.closest("[data-dress-choice]");
    if (choice) onActionChoice(choice.getAttribute("data-dress-choice"));
  });

  document.getElementById("dress-use-location").addEventListener("click", captureLocation);
  document.getElementById("dress-continue-delivery").addEventListener("click", function () {
    finishAction("delivery");
  });
  document.getElementById("dress-continue-pack").addEventListener("click", function () {
    finishAction("pack");
  });
}

function showModalStep(step) {
  document.getElementById("dress-step-choice").hidden = step !== "choice";
  document.getElementById("dress-step-delivery").hidden = step !== "delivery";
  document.getElementById("dress-step-pack").hidden = step !== "pack";
}

function openActionPopup(action) {
  ensureActionModal();
  pendingAction = Object.assign(
    {
      locationText: "",
      mapsUrl: "",
      address: "",
      pickupDate: "",
      pickupTime: "",
      note: "",
      mode: "buy",
      productId: "",
      label: "",
      baseMessage: "",
    },
    action
  );
  document.getElementById("dress-modal-sub").textContent = action.label
    ? action.label + " — pick home delivery, Pack & Pickup, or enquiry."
    : "Home delivery, Pack & Pickup, or a quick enquiry.";
  document.getElementById("dress-address").value = "";
  document.getElementById("dress-note-delivery").value = "";
  document.getElementById("dress-note-pack").value = "";
  document.getElementById("dress-loc-status").textContent = "Location not added yet.";
  var dateEl = document.getElementById("dress-pickup-date");
  var timeEl = document.getElementById("dress-pickup-time");
  var today = new Date();
  dateEl.min = today.toISOString().slice(0, 10);
  dateEl.value = today.toISOString().slice(0, 10);
  timeEl.value = "17:00";
  showModalStep("choice");
  var modal = document.getElementById("dress-action-modal");
  modal.hidden = false;
  document.documentElement.classList.add("dress-modal-open");
}

function closeActionPopup() {
  var modal = document.getElementById("dress-action-modal");
  if (modal) modal.hidden = true;
  document.documentElement.classList.remove("dress-modal-open");
  pendingAction = null;
}

function onActionChoice(choice) {
  if (choice === "delivery") showModalStep("delivery");
  else if (choice === "pack") showModalStep("pack");
  else finishAction("enquiry");
}

function captureLocation() {
  var status = document.getElementById("dress-loc-status");
  if (!navigator.geolocation) {
    status.textContent = "Location not supported on this device. Please type your address.";
    return;
  }
  status.textContent = "Getting your location…";
  navigator.geolocation.getCurrentPosition(
    function (pos) {
      var lat = pos.coords.latitude.toFixed(6);
      var lng = pos.coords.longitude.toFixed(6);
      pendingAction.locationText = lat + ", " + lng;
      pendingAction.mapsUrl = "https://maps.google.com/?q=" + lat + "," + lng;
      status.textContent = "Location added ✓ " + pendingAction.locationText;
    },
    function () {
      status.textContent = "Could not get location. Please type your address below.";
    },
    { enableHighAccuracy: true, timeout: 12000 }
  );
}

function formatDisplayDate(iso) {
  if (!iso) return "";
  var parts = String(iso).split("-");
  if (parts.length !== 3) return iso;
  var months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];
  var day = parseInt(parts[2], 10);
  var monthIdx = parseInt(parts[1], 10) - 1;
  var year = parts[0];
  var dd = day < 10 ? "0" + day : String(day);
  return dd + " " + (months[monthIdx] || parts[1]) + " " + year;
}

function formatDisplayTime(hhmm) {
  if (!hhmm) return "";
  var bits = String(hhmm).split(":");
  var h = parseInt(bits[0], 10);
  var m = bits[1] || "00";
  if (isNaN(h)) return hhmm;
  var ampm = h >= 12 ? "PM" : "AM";
  var h12 = h % 12;
  if (h12 === 0) h12 = 12;
  return h12 + ":" + m + " " + ampm;
}

function getPendingProducts() {
  if (pendingAction && pendingAction.mode === "cart" && window.Cart && Cart.getCart) {
    return Cart.getCart().map(function (item) {
      return { name: item.name, qty: item.qty || 1 };
    });
  }
  if (pendingAction && pendingAction.productId && window.getDressProduct) {
    var p = getDressProduct(pendingAction.productId);
    if (p) return [{ name: p.name, qty: 1 }];
  }
  if (pendingAction && pendingAction.label) {
    var skip = /^(enquiry|general enquiry|cart order|bulk \/ form enquiry)$/i.test(
      String(pendingAction.label).trim()
    );
    if (!skip) return [{ name: pendingAction.label, qty: 1 }];
  }
  return [];
}

function formatProductLines(products) {
  if (!products.length) return ["1. (Please advise products / quantities)"];
  return products.map(function (item, idx) {
    return idx + 1 + ". " + item.name + " × " + (item.qty || 1);
  });
}

function buildDeliveryMessage(products, addressBlock, note) {
  var lines = [
    "🛒 NEW ORDER – HOME DELIVERY",
    "",
    "📦 Products",
  ];
  lines = lines.concat(formatProductLines(products));
  lines.push("");
  lines.push("📍 Delivery Address");
  lines.push(addressBlock || "Please confirm address");
  lines.push("");
  lines.push("📝 Note");
  lines.push(note || "—");
  lines.push("");
  lines.push("💰 Please confirm:");
  lines.push("• Product availability");
  lines.push("• Total amount");
  lines.push("• Delivery charge");
  lines.push("• Expected delivery time");
  return lines.join("\n");
}

function buildPickupMessage(products, pickupDate, pickupTime, note) {
  var timeLabel = formatDisplayTime(pickupTime);
  var lines = [
    "📦 NEW ORDER – PICKUP",
    "",
    "🛒 Products",
  ];
  lines = lines.concat(formatProductLines(products));
  lines.push("");
  lines.push("📅 Pickup Date");
  lines.push(formatDisplayDate(pickupDate));
  lines.push("");
  lines.push("⏰ Pickup Time");
  lines.push(timeLabel);
  lines.push("");
  lines.push("📝 Note");
  lines.push(note || "Please keep the order ready by " + timeLabel + ".");
  lines.push("");
  lines.push("Please confirm when the order is ready.");
  return lines.join("\n");
}

function buildEnquiryMessage(productName, extraNote) {
  var lines = [
    "💬 PRODUCT ENQUIRY",
    "",
    "I'm interested in:",
    "",
    "🛒 " + (productName || "Richie Rich Boutique products"),
    "",
    "Could you please confirm:",
    "• Availability",
    "• Current price",
    "",
    "Thank you.",
  ];
  if (extraNote) {
    lines.push("");
    lines.push("📝 Note");
    lines.push(extraNote);
  }
  return lines.join("\n");
}

function finishAction(kind) {
  if (!pendingAction) return;

  if (kind === "delivery") {
    pendingAction.address = (document.getElementById("dress-address").value || "").trim();
    pendingAction.note = (document.getElementById("dress-note-delivery").value || "").trim();
    if (!pendingAction.locationText && !pendingAction.address) {
      document.getElementById("dress-loc-status").textContent =
        "Please use location or enter an address.";
      return;
    }
  }

  if (kind === "pack") {
    pendingAction.pickupDate = document.getElementById("dress-pickup-date").value;
    pendingAction.pickupTime = document.getElementById("dress-pickup-time").value;
    pendingAction.note = (document.getElementById("dress-note-pack").value || "").trim();
    if (!pendingAction.pickupDate || !pendingAction.pickupTime) {
      alert("Please enter pickup date and time.");
      return;
    }
  }

  var products = getPendingProducts();
  var message = "";

  if (kind === "delivery") {
    var addressParts = [];
    if (pendingAction.mapsUrl) addressParts.push(pendingAction.mapsUrl);
    else if (pendingAction.locationText) addressParts.push(pendingAction.locationText);
    if (pendingAction.address) addressParts.push(pendingAction.address);
    message = buildDeliveryMessage(products, addressParts.join("\n"), pendingAction.note);
  } else if (kind === "pack") {
    message = buildPickupMessage(
      products,
      pendingAction.pickupDate,
      pendingAction.pickupTime,
      pendingAction.note
    );
  } else {
    var productName =
      products.length === 1
        ? products[0].name
        : products.length > 1
          ? products.map(function (p) {
              return p.name + " × " + p.qty;
            }).join(", ")
          : pendingAction.label || "";
    var extra = "";
    if (pendingAction.baseMessage && pendingAction.mode !== "cart") {
      var base = String(pendingAction.baseMessage).trim();
      if (base && !/^hi richie rich/i.test(base) && base !== productName) {
        extra = base;
      }
    }
    message = buildEnquiryMessage(productName, extra);
  }

  var noteForPay = "";
  if (kind === "delivery") {
    noteForPay = [pendingAction.mapsUrl || pendingAction.locationText || "", pendingAction.address || "", pendingAction.note || ""]
      .filter(Boolean)
      .join(" | ");
  } else if (kind === "pack") {
    noteForPay =
      "Pickup " +
      pendingAction.pickupDate +
      " " +
      pendingAction.pickupTime +
      (pendingAction.note ? " | " + pendingAction.note : "");
  } else {
    noteForPay = message;
  }

  var wasCartOrder = pendingAction && pendingAction.mode === "cart";
  closeActionPopup();
  openWhatsApp(message);
  if (wasCartOrder && window.Cart && typeof Cart.clearCart === "function") {
    Cart.clearCart();
  }
}

function wireActionInterceptors() {
  document.addEventListener(
    "click",
    function (e) {
      var waBtn = e.target.closest("[data-wa]");
      if (waBtn && !waBtn.hasAttribute("data-wa-direct")) {
        e.preventDefault();
        e.stopPropagation();
        enquireWhatsApp({
          productId: waBtn.getAttribute("data-product") || "",
          label: waBtn.getAttribute("data-label") || "Enquiry",
          baseMessage: waBtn.getAttribute("data-wa") || "",
        });
      }
    },
    true
  );
}

function productCardHtml(p) {
  var pid = encodeURIComponent(p.id);
  var url = productDetailUrl(p.id);
  var name = p.name.replace(/"/g, "&quot;");
  return (
    '<article class="shop-card">' +
    '<div class="shop-card-media-wrap">' +
    '<a class="shop-card-media" href="' +
    url +
    '" data-product-detail="' +
    pid +
    '" aria-label="View ' +
    name +
    '">' +
    '<img src="' +
    p.image +
    '" alt="' +
    name +
    '" loading="lazy" /></a>' +
    '<button type="button" class="share-icon-btn" data-share="' +
    p.id +
    '" aria-label="Share" title="Share">' +
    SHARE_ICON +
    "</button>" +
    (p.price ? '<div class="shop-price">' + p.price + "</div>" : "") +
    "</div>" +
    '<div class="shop-card-body">' +
    '<h3><a class="shop-title-btn" href="' +
    url +
    '" data-product-detail="' +
    pid +
    '">' +
    p.name +
    "</a></h3>" +
    (p.tag ? '<p class="shop-tag">' + p.tag + "</p>" : "") +
    "<p>" +
    p.short +
    "</p>" +
    '<div class="shop-card-actions">' +
    '<a class="shop-icon-action" href="' +
    url +
    '" data-product-detail="' +
    pid +
    '" aria-label="View" title="View">' +
    DETAIL_ICON +
    "</a>" +
    '<a class="shop-icon-action shop-icon-wa" data-wa="' +
    p.wa.replace(/"/g, "&quot;") +
    '" data-product="' +
    p.id +
    '" data-label="' +
    name +
    '" href="#" aria-label="WhatsApp" title="WhatsApp enquire">' +
    WA_ICON +
    "</a>" +
    cartActionBtn(p.id) +
    "</div></div></article>"
  );
}

function categoryCardHtml(cat) {
  return (
    '<a class="category-card" href="#home-cat-' +
    encodeURIComponent(cat.id) +
    '">' +
    '<span class="category-card-media"><img src="' +
    cat.image +
    '" alt="" loading="lazy" /></span>' +
    "<span><strong>" +
    cat.name +
    "</strong><span>" +
    cat.desc +
    "</span></span></a>"
  );
}

function categoryRailItemHtml(cat) {
  return (
    '<a class="cat-rail-item" href="' + dressRoute("shop") + '?cat=' +
    encodeURIComponent(cat.id) +
    '">' +
    '<span class="cat-rail-ico"><img src="' +
    cat.image +
    '" alt="" loading="lazy" /></span>' +
    "<span>" +
    cat.name +
    "</span></a>"
  );
}

function initCategoryCarousel() {
  var rail = document.getElementById("category-grid");
  var prev = document.getElementById("category-prev");
  var next = document.getElementById("category-next");
  if (!rail) return;

  function step() {
    var card = rail.querySelector(".category-card");
    if (!card) return Math.max(rail.clientWidth * 0.7, 160);
    var styles = window.getComputedStyle(rail);
    var gap = parseFloat(styles.columnGap || styles.gap) || 0;
    return card.getBoundingClientRect().width + gap;
  }

  function updateButtons() {
    if (!prev || !next) return;
    var max = rail.scrollWidth - rail.clientWidth - 2;
    var canScroll = max > 2;
    prev.hidden = !canScroll;
    next.hidden = !canScroll;
    if (!canScroll) return;
    prev.disabled = rail.scrollLeft <= 2;
    next.disabled = rail.scrollLeft >= max;
    prev.style.opacity = prev.disabled ? "0.35" : "1";
    next.style.opacity = next.disabled ? "0.35" : "1";
  }

  if (prev) {
    prev.addEventListener("click", function () {
      rail.scrollBy({ left: -step(), behavior: "smooth" });
    });
  }
  if (next) {
    next.addEventListener("click", function () {
      rail.scrollBy({ left: step(), behavior: "smooth" });
    });
  }

  rail.addEventListener("scroll", updateButtons, { passive: true });
  window.addEventListener("resize", updateButtons);
  updateButtons();
}

function initBannerRail() {
  var rail = document.getElementById("banner-rail");
  var dots = document.getElementById("banner-dots");
  if (!rail || !dots) return;
  var cards = rail.querySelectorAll(".banner-card");
  if (!cards.length) return;

  dots.innerHTML = Array.prototype.map
    .call(cards, function (_, i) {
      return '<button type="button" class="banner-dot' + (i === 0 ? " is-active" : "") + '" data-banner-dot="' + i + '" aria-label="Slide ' + (i + 1) + '"></button>';
    })
    .join("");

  function activeIndex() {
    var center = rail.scrollLeft + rail.clientWidth / 2;
    var best = 0;
    var bestDist = Infinity;
    cards.forEach(function (card, i) {
      var mid = card.offsetLeft + card.offsetWidth / 2;
      var d = Math.abs(mid - center);
      if (d < bestDist) {
        bestDist = d;
        best = i;
      }
    });
    return best;
  }

  function setDots(i) {
    dots.querySelectorAll(".banner-dot").forEach(function (dot, idx) {
      dot.classList.toggle("is-active", idx === i);
    });
  }

  function goTo(i) {
    var card = cards[i];
    if (!card) return;
    var left = card.offsetLeft - cards[0].offsetLeft;
    rail.scrollTo({ left: left, behavior: "smooth" });
    setDots(i);
  }

  rail.addEventListener(
    "scroll",
    function () {
      setDots(activeIndex());
    },
    { passive: true }
  );

  dots.addEventListener("click", function (e) {
    var btn = e.target.closest("[data-banner-dot]");
    if (!btn) return;
    goTo(parseInt(btn.getAttribute("data-banner-dot"), 10) || 0);
  });

  var timer = setInterval(function () {
    if (document.hidden) return;
    var next = (activeIndex() + 1) % cards.length;
    goTo(next);
  }, 4200);

  rail.addEventListener(
    "pointerdown",
    function () {
      clearInterval(timer);
    },
    { once: true }
  );
}

function packageCardHtml(c, detailFallback) {
  var pid = encodeURIComponent(c.id);
  var items = (c.includes || [])
    .map(function (i) {
      return "<li>" + i + "</li>";
    })
    .join("");
  var priceHtml =
    '<div class="shop-price">' +
    c.price +
    (c.priceNote ? " <small>" + c.priceNote + "</small>" : "") +
    "</div>";
  return (
    '<article class="combo-card">' +
    '<div class="combo-card-main">' +
    '<div class="shop-card-media-wrap">' +
    '<button type="button" class="shop-card-media" data-product-detail="' +
    pid +
    '" aria-label="View ' +
    c.name.replace(/"/g, "&quot;") +
    '">' +
    '<img src="' +
    c.image +
    '" alt="' +
    c.name.replace(/"/g, "&quot;") +
    '" loading="lazy" /></button>' +
    '<button type="button" class="share-icon-btn" data-share="' +
    c.id +
    '" aria-label="Share" title="Share">' +
    SHARE_ICON +
    "</button>" +
    priceHtml +
    "</div>" +
    '<div class="combo-card-body">' +
    '<div class="combo-title-row">' +
    '<h3><button type="button" class="shop-title-btn" data-product-detail="' +
    pid +
    '">' +
    c.name +
    "</button></h3>" +
    '<button type="button" class="combo-expand-btn" data-combo-toggle aria-expanded="false" aria-label="Show included items" title="Includes">' +
    '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.5 9.5L12 15l5.5-5.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
    "</button></div>" +
    (c.tag ? '<p class="shop-tag">' + c.tag + "</p>" : "") +
    "<p>" +
    (c.blurb || c.short || "") +
    "</p>" +
    '<div class="shop-card-actions">' +
    '<button type="button" class="shop-icon-action" data-product-detail="' +
    pid +
    '" aria-label="View" title="View">' +
    DETAIL_ICON +
    "</button>" +
    '<a class="shop-icon-action shop-icon-wa" data-wa="' +
    c.wa.replace(/"/g, "&quot;") +
    '" data-product="' +
    c.id +
    '" data-label="' +
    c.name.replace(/"/g, "&quot;") +
    '" href="#" aria-label="WhatsApp" title="WhatsApp enquire">' +
    WA_ICON +
    "</a>" +
    cartActionBtn(c.id) +
    "</div></div></div>" +
    '<div class="combo-includes" hidden>' +
    '<p class="combo-includes-label">Includes</p>' +
    '<ul class="combo-list">' +
    items +
    "</ul></div></article>"
  );
}

function wireComboIncludesToggle() {
  document.addEventListener("click", function (e) {
    var btn = e.target.closest("[data-combo-toggle]");
    if (!btn) return;
    e.preventDefault();
    var card = btn.closest(".combo-card");
    if (!card) return;
    var panel = card.querySelector(".combo-includes");
    if (!panel) return;
    var open = panel.hasAttribute("hidden");
    if (open) {
      panel.removeAttribute("hidden");
      btn.setAttribute("aria-expanded", "true");
      btn.setAttribute("aria-label", "Hide included items");
      card.classList.add("is-open");
    } else {
      panel.setAttribute("hidden", "");
      btn.setAttribute("aria-expanded", "false");
      btn.setAttribute("aria-label", "Show included items");
      card.classList.remove("is-open");
    }
  });
}

function renderHome() {
  var cats = document.getElementById("category-grid");
  if (cats && window.DRESS_CATEGORIES) {
    cats.innerHTML = DRESS_CATEGORIES.map(categoryCardHtml).join("");
    initCategoryCarousel();
  }
  var combos = document.getElementById("combo-grid");
  if (combos && window.DRESS_COMBOS) {
    combos.innerHTML = DRESS_COMBOS.map(function (c) {
      return packageCardHtml(c);
    }).join("");
  }
  var season = document.getElementById("season-grid");
  if (season && window.DRESS_SEASON_OFFERS) {
    season.innerHTML = DRESS_SEASON_OFFERS.map(function (c) {
      return packageCardHtml(c);
    }).join("");
  }

  var host = document.getElementById("home-category-sections");
  if (host && window.DRESS_CATEGORIES && window.DRESS_PRODUCTS) {
    var productPage =
      (window.AZORES_SHARE && AZORES_SHARE.productPage) || dressRoute("shop");
    host.innerHTML = DRESS_CATEGORIES.map(function (cat, index) {
      var items = DRESS_PRODUCTS.filter(function (p) {
        return p.category === cat.id;
      });
      if (!items.length) return "";
      var sectionClass = "section" + (index % 2 === 0 ? " alt" : "");
      return (
        '<section class="' +
        sectionClass +
        '" id="home-cat-' +
        cat.id +
        '">' +
        '<div class="container">' +
        '<div class="section-head section-head-compact">' +
        "<div>" +
        '<p class="eyebrow">' +
        (cat.desc || "Shop") +
        "</p>" +
        "<h2>" +
        cat.name +
        "</h2>" +
        "</div>" +
        '<a class="btn btn-outline" href="' +
        productPage +
        "?cat=" +
        encodeURIComponent(cat.id) +
        '">View all</a>' +
        "</div>" +
        '<div class="shop-grid">' +
        items.slice(0, 8).map(productCardHtml).join("") +
        "</div>" +
        "</div>" +
        "</section>"
      );
    }).join("");
  }

  initBannerRail();
}

function renderCombosPage() {
  var grid = document.getElementById("combos-page-grid");
  if (!grid || !window.DRESS_COMBOS) return;
  grid.innerHTML = DRESS_COMBOS.map(function (c) {
    return packageCardHtml(c);
  }).join("");
}

function renderSeasonPage() {
  var grid = document.getElementById("season-page-grid");
  if (!grid || !window.DRESS_SEASON_OFFERS) return;
  grid.innerHTML = DRESS_SEASON_OFFERS.map(function (c) {
    return packageCardHtml(c);
  }).join("");
}

function renderCatalog() {
  var grid = document.getElementById("catalog-grid");
  var filters = document.getElementById("catalog-filters");
  if (!grid || !window.DRESS_PRODUCTS) return;

  var params = new URLSearchParams(window.location.search);
  var active = params.get("cat") || "all";
  var query = (params.get("q") || "").trim().toLowerCase();

  if (filters && window.DRESS_CATEGORIES) {
    var chips =
      '<button type="button" class="filter-chip' +
      (active === "all" ? " is-active" : "") +
      '" data-cat="all">All</button>' +
      DRESS_CATEGORIES.map(function (c) {
        return (
          '<button type="button" class="filter-chip' +
          (active === c.id ? " is-active" : "") +
          '" data-cat="' +
          c.id +
          '">' +
          c.name +
          "</button>"
        );
      }).join("");
    filters.innerHTML = chips;
    filters.querySelectorAll("[data-cat]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var cat = btn.getAttribute("data-cat");
        var url = new URL(window.location.href);
        if (cat === "all") url.searchParams.delete("cat");
        else url.searchParams.set("cat", cat);
        window.location.href = url.toString();
      });
    });
  }

  var list =
    active === "all"
      ? DRESS_PRODUCTS.filter(function (p) {
          return p.category !== "season";
        })
      : DRESS_PRODUCTS.filter(function (p) {
          return p.category === active;
        });
  if (query) {
    list = list.filter(function (p) {
      var hay = [p.name, p.short, p.brand, p.category].join(" ").toLowerCase();
      return hay.indexOf(query) !== -1;
    });
  }
  grid.innerHTML = list.length
    ? list.map(productCardHtml).join("")
    : '<p class="cart-empty">No products found. Try another search.</p>';

  var title = document.getElementById("catalog-title");
  if (title) {
    if (query) title.textContent = 'Results for "' + params.get("q") + '"';
    else {
      var cat = active !== "all" ? getDressCategory(active) : null;
      title.textContent = cat ? cat.name : "All products";
    }
  }
}

function ensureProductSheet() {
  if (document.getElementById("dress-product-sheet")) return;
  var el = document.createElement("div");
  el.id = "dress-product-sheet";
  el.className = "dress-product-sheet";
  el.hidden = true;
  el.innerHTML =
    '<button type="button" class="dress-product-sheet-backdrop" data-sheet-close aria-label="Close"></button>' +
    '<div class="dress-product-sheet-panel" role="dialog" aria-modal="true" aria-labelledby="dress-sheet-title">' +
    '<div class="dress-product-sheet-handle" aria-hidden="true"><span></span></div>' +
    '<button type="button" class="dress-product-sheet-x" data-sheet-close aria-label="Close">×</button>' +
    '<div class="dress-product-sheet-body" id="dress-product-sheet-body"></div>' +
    "</div>";
  document.body.appendChild(el);

  el.addEventListener("click", function (e) {
    if (e.target.closest("[data-sheet-close]")) {
      closeProductSheet();
      return;
    }
    var thumb = e.target.closest(".dress-sheet-thumbs [data-src]");
    if (thumb) {
      var main = document.getElementById("dress-sheet-main-img");
      if (main) main.src = thumb.getAttribute("data-src");
      el.querySelectorAll(".dress-sheet-thumbs button").forEach(function (b) {
        b.classList.toggle("is-active", b === thumb);
      });
    }
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && !el.hidden) closeProductSheet();
  });
}

function closeProductSheet() {
  var sheet = document.getElementById("dress-product-sheet");
  if (sheet) sheet.hidden = true;
  document.documentElement.classList.remove("dress-sheet-open");
  if (window.history && window.history.replaceState) {
    var url = new URL(window.location.href);
    if (url.searchParams.has("id")) {
      url.searchParams.delete("id");
      window.history.replaceState({}, "", url.pathname + url.search + url.hash);
    }
  }
}

function openProductSheet(id) {
  if (!id || !window.getDressProduct) return;
  if (isShopProduct(id)) {
    window.location.href = productDetailUrl(id);
    return;
  }
  var product = getDressProduct(id);
  if (!product) return;

  ensureProductSheet();
  var body = document.getElementById("dress-product-sheet-body");
  if (!body) return;

  var bullets = (product.bullets || product.includes || [])
    .map(function (b) {
      return "<li>" + b + "</li>";
    })
    .join("");
  var specs = (product.specs || [])
    .map(function (row) {
      return "<li><span>" + row[0] + "</span><span>" + row[1] + "</span></li>";
    })
    .join("");

  var gallery = productGallery(product);
  var thumbs =
    gallery.length > 1
      ? '<div class="dress-sheet-thumbs">' +
        gallery
          .map(function (src, i) {
            return (
              '<button type="button"' +
              (i === 0 ? ' class="is-active"' : "") +
              ' data-src="' +
              String(src).replace(/"/g, "&quot;") +
              '"><img src="' +
              String(src).replace(/"/g, "&quot;") +
              '" alt="" /></button>'
            );
          })
          .join("") +
        "</div>"
      : "";

  body.innerHTML =
    '<div class="dress-sheet-layout">' +
    '<div class="dress-sheet-top">' +
    '<div class="dress-sheet-media">' +
    '<div class="dress-sheet-main"><img id="dress-sheet-main-img" src="' +
    (gallery[0] || product.image) +
    '" alt="' +
    product.name.replace(/"/g, "&quot;") +
    '" /></div>' +
    thumbs +
    "</div>" +
    '<div class="dress-sheet-basics">' +
    (product.brand ? '<p class="dress-sheet-brand">' + product.brand + "</p>" : "") +
    '<h2 id="dress-sheet-title">' +
    product.name +
    "</h2>" +
    (product.tag ? '<p class="shop-tag">' + product.tag + "</p>" : "") +
    (product.price ? '<div class="shop-price">' + product.price + "</div>" : "") +
    "</div></div>" +
    '<div class="dress-sheet-more">' +
    '<p class="dress-sheet-lead">' +
    (product.description || product.blurb || product.short || "") +
    "</p>" +
    (bullets ? '<ul class="detail-bullets">' + bullets + "</ul>" : "") +
    (specs ? '<ul class="spec-table">' + specs + "</ul>" : "") +
    "</div>" +
    '<div class="dress-sheet-actions">' +
    '<a class="icon-action icon-action-lg icon-action-wa" data-wa="' +
    (product.wa || "").replace(/"/g, "&quot;") +
    '" data-product="' +
    product.id +
    '" data-label="' +
    product.name.replace(/"/g, "&quot;") +
    '" href="#" aria-label="WhatsApp" title="WhatsApp enquire">' +
    WA_ICON +
    "</a>" +
    cartActionBtn(product.id, true) +
    '<button type="button" class="icon-action icon-action-lg" data-share="' +
    product.id +
    '" aria-label="Share" title="Share">' +
    SHARE_ICON +
    "</button>" +
    '<a class="btn btn-outline dress-sheet-bulk" href="' + dressRoute("contact") + '">Contact</a>' +
    "</div></div>";

  var sheet = document.getElementById("dress-product-sheet");
  sheet.hidden = false;
  document.documentElement.classList.add("dress-sheet-open");

  if (window.history && window.history.replaceState) {
    var url = new URL(window.location.href);
    url.searchParams.set("id", product.id);
    window.history.replaceState({}, "", url.pathname + "?" + url.searchParams.toString() + url.hash);
  }

  if (window.Share && Share.setProductOg) Share.setProductOg(product);
}

function wireProductDetailSheet() {
  ensureProductSheet();
  document.addEventListener("click", function (e) {
    var btn = e.target.closest("[data-product-detail]");
    if (!btn) return;
    var id = btn.getAttribute("data-product-detail");
    if (isShopProduct(id)) {
      if (btn.tagName === "A" && btn.getAttribute("href")) return;
      e.preventDefault();
      window.location.href = productDetailUrl(id);
      return;
    }
    e.preventDefault();
    openProductSheet(id);
  });
}

function openProductFromQuery() {
  var id = new URLSearchParams(window.location.search).get("id");
  if (!id) return;
  var file = (location.pathname.split("/").pop() || "").replace(/\.html$/i, "");
  if (file === "dressdetail") {
    location.replace(isShopProduct(id) ? productDetailUrl(id) : dressRoute("shop") + "?id=" + encodeURIComponent(id));
    return;
  }
  if (isShopProduct(id)) {
    window.location.replace(productDetailUrl(id));
    return;
  }
  openProductSheet(id);
}

function ensureZoomLightbox() {
  if (document.getElementById("product-zoom-lightbox")) return;
  var el = document.createElement("div");
  el.id = "product-zoom-lightbox";
  el.className = "product-zoom-lightbox";
  el.hidden = true;
  el.innerHTML =
    '<div class="product-zoom-toolbar">' +
    '<button type="button" data-zoom-close aria-label="Close">×</button>' +
    "</div>" +
    '<div class="product-zoom-stage" id="product-zoom-stage"><img id="product-zoom-img" alt="" /></div>' +
    '<button type="button" class="product-zoom-nav prev" data-zoom-prev aria-label="Previous">‹</button>' +
    '<button type="button" class="product-zoom-nav next" data-zoom-next aria-label="Next">›</button>' +
    '<div class="product-zoom-dots" id="product-zoom-dots"></div>';
  document.body.appendChild(el);
}

function openZoomLightbox(gallery, startIndex) {
  ensureZoomLightbox();
  var box = document.getElementById("product-zoom-lightbox");
  var img = document.getElementById("product-zoom-img");
  var stage = document.getElementById("product-zoom-stage");
  var dots = document.getElementById("product-zoom-dots");
  if (!box || !img || !stage) return;

  var index = startIndex || 0;
  var scale = 1;
  var panX = 0;
  var panY = 0;
  var dragging = false;
  var lastX = 0;
  var lastY = 0;

  function applyTransform() {
    img.style.transform = "translate(" + panX + "px," + panY + "px) scale(" + scale + ")";
  }

  function show(i) {
    index = (i + gallery.length) % gallery.length;
    img.src = gallery[index];
    scale = 1;
    panX = 0;
    panY = 0;
    applyTransform();
    if (dots) {
      dots.querySelectorAll("button").forEach(function (b, k) {
        b.classList.toggle("is-active", k === index);
      });
    }
    var prev = box.querySelector("[data-zoom-prev]");
    var next = box.querySelector("[data-zoom-next]");
    if (prev) prev.hidden = gallery.length < 2;
    if (next) next.hidden = gallery.length < 2;
  }

  if (dots) {
    dots.innerHTML = gallery
      .map(function (src, i) {
        return (
          '<button type="button"' +
          (i === index ? ' class="is-active"' : "") +
          ' data-zoom-i="' +
          i +
          '"><img src="' +
          String(src).replace(/"/g, "&quot;") +
          '" alt="" /></button>'
        );
      })
      .join("");
  }

  box.hidden = false;
  document.documentElement.classList.add("product-zoom-open");
  show(index);

  function close() {
    box.hidden = true;
    document.documentElement.classList.remove("product-zoom-open");
    box.onclick = null;
    stage.onpointerdown = null;
    stage.onwheel = null;
    document.removeEventListener("keydown", onKey);
  }

  function onKey(e) {
    if (e.key === "Escape") close();
    if (e.key === "ArrowLeft") show(index - 1);
    if (e.key === "ArrowRight") show(index + 1);
  }

  box.onclick = function (e) {
    if (e.target.closest("[data-zoom-close]")) {
      close();
      return;
    }
    if (e.target.closest("[data-zoom-prev]")) {
      show(index - 1);
      return;
    }
    if (e.target.closest("[data-zoom-next]")) {
      show(index + 1);
      return;
    }
    var dot = e.target.closest("[data-zoom-i]");
    if (dot) show(parseInt(dot.getAttribute("data-zoom-i"), 10) || 0);
  };

  stage.onpointerdown = function (e) {
    dragging = true;
    lastX = e.clientX;
    lastY = e.clientY;
    stage.classList.add("is-dragging");
    stage.setPointerCapture(e.pointerId);
  };
  stage.onpointermove = function (e) {
    if (!dragging) return;
    panX += e.clientX - lastX;
    panY += e.clientY - lastY;
    lastX = e.clientX;
    lastY = e.clientY;
    applyTransform();
  };
  stage.onpointerup = function () {
    dragging = false;
    stage.classList.remove("is-dragging");
  };
  stage.onwheel = function (e) {
    e.preventDefault();
    scale = Math.min(4, Math.max(1, scale + (e.deltaY < 0 ? 0.2 : -0.2)));
    if (scale === 1) {
      panX = 0;
      panY = 0;
    }
    applyTransform();
  };
  stage.ondblclick = function () {
    if (scale > 1) {
      scale = 1;
      panX = 0;
      panY = 0;
    } else {
      scale = 2.2;
    }
    applyTransform();
  };

  document.addEventListener("keydown", onKey);
}

function bindDetailZoom(root, gallery) {
  var zoom = root.querySelector(".detail-zoom");
  var img = root.querySelector("#detail-main-img");
  if (!zoom || !img) return;
  var finePointer = window.matchMedia && window.matchMedia("(hover: hover) and (pointer: fine)").matches;

  if (finePointer) {
    zoom.addEventListener("mousemove", function (e) {
      var r = zoom.getBoundingClientRect();
      var x = ((e.clientX - r.left) / r.width) * 100;
      var y = ((e.clientY - r.top) / r.height) * 100;
      img.style.transformOrigin = x + "% " + y + "%";
      zoom.classList.add("is-hovering");
    });
    zoom.addEventListener("mouseleave", function () {
      zoom.classList.remove("is-hovering");
      img.style.transformOrigin = "center center";
    });
  }

  zoom.addEventListener("click", function () {
    var current = gallery.indexOf(img.getAttribute("src"));
    openZoomLightbox(gallery, current < 0 ? 0 : current);
  });
}

function renderRelatedProducts(product) {
  var grid = document.getElementById("related-grid");
  if (!grid || !product) return;
  var list = (window.DRESS_PRODUCTS || [])
    .filter(function (p) {
      return p.id !== product.id && p.category === product.category;
    })
    .slice(0, 8);
  if (!list.length) {
    list = (window.DRESS_PRODUCTS || [])
      .filter(function (p) {
        return p.id !== product.id;
      })
      .slice(0, 4);
  }
  grid.innerHTML = list.length
    ? list.map(productCardHtml).join("")
    : '<p class="muted">More products will show here.</p>';
}

function renderProductDetailPage(id) {
  var root = document.getElementById("detail-root");
  if (!root || !window.getDressProduct) return;
  id = id || root.getAttribute("data-product-id");
  var product = getDressProduct(id);
  if (!product) {
    root.innerHTML = '<p class="cart-empty">This product is no longer available.</p>';
    return;
  }

  var gallery = productGallery(product);
  var name = product.name.replace(/"/g, "&quot;");
  var thumbs =
    gallery.length > 1
      ? '<div class="detail-thumbs">' +
        gallery
          .map(function (src, i) {
            return (
              '<button type="button" class="detail-thumb' +
              (i === 0 ? " is-active" : "") +
              '" data-src="' +
              String(src).replace(/"/g, "&quot;") +
              '"><img src="' +
              String(src).replace(/"/g, "&quot;") +
              '" alt="" /></button>'
            );
          })
          .join("") +
        "</div>"
      : "";
  var bullets = (product.bullets || [])
    .map(function (b) {
      return "<li>" + b + "</li>";
    })
    .join("");
  var specs = (product.specs || [])
    .map(function (row) {
      return "<li><span>" + row[0] + "</span><span>" + row[1] + "</span></li>";
    })
    .join("");
  var colors = (product.colors || [])
    .map(function (c) {
      return '<span class="shop-tag">' + String(c).replace(/</g, "&lt;") + "</span>";
    })
    .join("");
  var sizes = (product.sizes || [])
    .map(function (s) {
      return '<span class="shop-tag">' + String(s).replace(/</g, "&lt;") + "</span>";
    })
    .join("");

  root.innerHTML =
    '<article class="detail-layout">' +
    '<div class="detail-gallery">' +
    '<div class="detail-main"><button type="button" class="detail-zoom" aria-label="Zoom image">' +
    '<img id="detail-main-img" src="' +
    (gallery[0] || product.image) +
    '" alt="' +
    name +
    '" /><span class="detail-zoom-hint">Zoom</span></button></div>' +
    thumbs +
    "</div>" +
    '<div class="detail-copy">' +
    (product.brand ? '<p class="dress-sheet-brand">' + product.brand + "</p>" : "") +
    "<h1>" +
    product.name +
    "</h1>" +
    (product.tag ? '<p class="shop-tag">' + product.tag + "</p>" : "") +
    (product.price ? '<div class="detail-price">' + product.price + "</div>" : "") +
    '<div class="detail-lead">' +
    (product.description || product.short || "") +
    "</div>" +
    (colors ? '<p class="detail-care"><strong>Colors</strong><br>' + colors + "</p>" : "") +
    (sizes ? '<p class="detail-care"><strong>Sizes</strong><br>' + sizes + "</p>" : "") +
    (bullets ? '<ul class="detail-bullets">' + bullets + "</ul>" : "") +
    (specs ? '<ul class="spec-table">' + specs + "</ul>" : "") +
    (product.care ? '<p class="detail-care"><strong>Care</strong><br>' + product.care + "</p>" : "") +
    '<div class="detail-actions">' +
    '<a class="icon-action icon-action-lg icon-action-wa" data-wa="' +
    (product.wa || "").replace(/"/g, "&quot;") +
    '" data-product="' +
    product.id +
    '" data-label="' +
    name +
    '" href="#" aria-label="WhatsApp" title="WhatsApp enquire">' +
    WA_ICON +
    "</a>" +
    cartActionBtn(product.id, true) +
    '<button type="button" class="icon-action icon-action-lg" data-share="' +
    product.id +
    '" aria-label="Share" title="Share">' +
    SHARE_ICON +
    "</button>" +
    '<a class="btn btn-outline" href="' +
    dressRoute("contact") +
    '">Contact</a>' +
    "</div></div></article>";

  root.querySelectorAll(".detail-thumb").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var main = document.getElementById("detail-main-img");
      if (main) main.src = btn.getAttribute("data-src");
      root.querySelectorAll(".detail-thumb").forEach(function (b) {
        b.classList.toggle("is-active", b === btn);
      });
    });
  });

  bindDetailZoom(root, gallery);
  renderRelatedProducts(product);
  if (window.Share && Share.setProductOg) Share.setProductOg(product);
}

function renderDetailPage() {
  var page = document.body && document.body.getAttribute("data-dress-page");
  if (page === "detail") {
    var root = document.getElementById("detail-root");
    renderProductDetailPage(root && root.getAttribute("data-product-id"));
    return;
  }
  openProductFromQuery();
}

function syncMartHeaderOffset() {
  var header = document.querySelector(".site-header");
  if (!header) return;
  document.documentElement.style.setProperty(
    "--dress-header-h",
    header.offsetHeight + "px"
  );
}

function initDressShop() {
  ensureActionModal();
  ensureProductSheet();
  wireActionInterceptors();
  wireProductDetailSheet();
  renderHome();
  renderCombosPage();
  renderSeasonPage();
  renderCatalog();
  renderDetailPage();
  wireDirectWhatsAppLinks();
  wireNav();
  wireFormsToWhatsApp();
  wireComboIncludesToggle();
  syncMartHeaderOffset();
  window.addEventListener("resize", syncMartHeaderOffset);
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initDressShop);
} else {
  initDressShop();
}

window.openActionPopup = openActionPopup;
window.closeActionPopup = closeActionPopup;
