(function () {
  var CART_KEY = "azores_dress_cart_v1";
  var WHATSAPP = (window.DRESS_CONFIG && window.DRESS_CONFIG.whatsapp) || "";

  function formatINR(n) {
    n = Number(n) || 0;
    try {
      return "₹" + Math.round(n).toLocaleString("en-IN");
    } catch (e) {
      return "₹" + Math.round(n);
    }
  }

  function parsePrice(value) {
    if (typeof value === "number" && isFinite(value)) return value;
    if (!value) return 0;
    var n = parseFloat(String(value).replace(/[^\d.]/g, ""));
    return isFinite(n) ? n : 0;
  }

  function readCart() {
    try {
      return JSON.parse(localStorage.getItem(CART_KEY) || "[]");
    } catch (e) {
      return [];
    }
  }

  function writeCart(items) {
    localStorage.setItem(CART_KEY, JSON.stringify(items));
    updateCartBadge();
    window.dispatchEvent(new CustomEvent("cart:updated"));
  }

  function findProduct(id) {
    return window.getDressProduct ? getDressProduct(id) : null;
  }

  function getCart() {
    return readCart();
  }

  function getCartCount() {
    return readCart().reduce(function (sum, item) {
      return sum + (item.qty || 0);
    }, 0);
  }

  function addToCart(productId, qty) {
    qty = qty || 1;
    var product = findProduct(productId);
    if (!product) return;
    var stock = product.stock === null || product.stock === undefined || product.stock === "" ? null : parseInt(product.stock, 10);
    if (stock !== null && isNaN(stock)) stock = null;
    var items = readCart();
    var existing = items.find(function (i) {
      return i.id === productId;
    });
    var inCart = existing ? existing.qty : 0;
    if (stock === 0 || (stock !== null && inCart >= stock)) {
      showToast(stock === 0 ? product.name + " is out of stock" : "Only " + stock + " left");
      return;
    }
    if (stock !== null) qty = Math.min(qty, stock - inCart);
    if (existing) {
      existing.qty += qty;
    } else {
      items.push({
        id: product.id,
        name: product.name,
        price: parsePrice(product.price),
        priceLabel: product.price || "On quote",
        image: product.image,
        qty: qty,
      });
    }
    writeCart(items);
    showToast(product.name + " added to cart");
  }

  function setQty(productId, qty) {
    var product = findProduct(productId);
    var stock = product && product.stock !== null && product.stock !== undefined && product.stock !== "" ? parseInt(product.stock, 10) : null;
    if (stock !== null && !isNaN(stock)) qty = Math.min(qty, Math.max(stock, 1));
    writeCart(
      readCart()
        .map(function (i) {
          if (i.id === productId) i.qty = Math.max(1, qty);
          return i;
        })
        .filter(function (i) {
          return i.qty > 0;
        })
    );
  }

  function removeFromCart(productId) {
    writeCart(
      readCart().filter(function (i) {
        return i.id !== productId;
      })
    );
  }

  function clearCart() {
    writeCart([]);
  }

  function cartTotal() {
    return readCart().reduce(function (sum, i) {
      return sum + (Number(i.price) || 0) * (i.qty || 0);
    }, 0);
  }

  function openWhatsAppForCart() {
    var items = readCart();
    if (!items.length) {
      showToast("Cart is empty");
      return;
    }
    var lines = ["Hi Richierich,", "", "I'd like to enquire about these items:", ""];
    items.forEach(function (item, idx) {
      var price = item.price ? formatINR(item.price) : item.priceLabel || "";
      lines.push(idx + 1 + ". " + item.name + " × " + item.qty + (price ? " — " + price : ""));
    });
    lines.push("");
    lines.push("Please confirm availability and total.");
    lines.push("Thank you.");
    window.open(
      "https://wa.me/" + WHATSAPP + "?text=" + encodeURIComponent(lines.join("\n")),
      "_blank",
      "noopener,noreferrer"
    );
  }

  function updateCartBadge() {
    var count = getCartCount();
    document.querySelectorAll("[data-cart-count]").forEach(function (el) {
      el.textContent = String(count);
      el.hidden = count === 0;
      el.classList.toggle("is-empty", count === 0);
    });
  }

  function showToast(message) {
    var existing = document.getElementById("dress-toast");
    if (existing) existing.remove();
    var toast = document.createElement("div");
    toast.id = "dress-toast";
    toast.className = "dress-toast";
    toast.textContent = message;
    document.body.appendChild(toast);
    requestAnimationFrame(function () {
      toast.classList.add("show");
    });
    setTimeout(function () {
      toast.classList.remove("show");
      setTimeout(function () {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
      }, 250);
    }, 2200);
  }

  function bindGlobalActions() {
    document.addEventListener("click", function (e) {
      var addBtn = e.target.closest("[data-add-cart]");
      if (addBtn) {
        e.preventDefault();
        e.stopPropagation();
        var qty = 1;
        if (addBtn.classList.contains("detail-add")) {
          var qtyEl = document.getElementById("detail-qty");
          qty = Math.max(1, parseInt(qtyEl && qtyEl.value, 10) || 1);
        }
        addToCart(addBtn.getAttribute("data-add-cart"), qty);
      }
    });
    updateCartBadge();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", bindGlobalActions);
  } else {
    bindGlobalActions();
  }

  window.formatINR = window.formatINR || formatINR;
  window.Cart = {
    getCart: getCart,
    getCartCount: getCartCount,
    addToCart: addToCart,
    setQty: setQty,
    removeFromCart: removeFromCart,
    clearCart: clearCart,
    cartTotal: cartTotal,
    openWhatsAppForCart: openWhatsAppForCart,
    updateCartBadge: updateCartBadge,
    showToast: showToast,
    formatINR: formatINR,
    WHATSAPP: WHATSAPP,
  };
})();
