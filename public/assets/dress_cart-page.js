(function () {
  function unitPrice(item) {
    if (!item.price) return item.priceLabel || "On quote";
    return Cart.formatINR(item.price);
  }

  function render() {
    if (!window.Cart) return;
    var items = Cart.getCart();
    var root = document.getElementById("cart-items");
    var countEl = document.getElementById("summary-count");
    var totalEl = document.getElementById("summary-total");
    if (!root || !countEl || !totalEl) return;

    countEl.textContent = String(Cart.getCartCount());
    totalEl.textContent = Cart.cartTotal() > 0 ? Cart.formatINR(Cart.cartTotal()) : "On quote";

    if (!items.length) {
      root.innerHTML =
        '<div class="cart-empty">' +
        "<p>Your cart is empty.</p>" +
        '<a class="btn btn-primary" href="' + ((window.DRESS_CONFIG && window.DRESS_CONFIG.routes && window.DRESS_CONFIG.routes.shop) || "/shop") + '">Browse products</a>' +
        "</div>";
      return;
    }

    root.innerHTML = items
      .map(function (item) {
        var product = window.getDressProduct ? getDressProduct(item.id) : null;
        var tag = (product && product.tag) || "In cart";
        var short = (product && (product.short || product.blurb)) || "";
        var pid = encodeURIComponent(item.id);
        var safeName = String(item.name).replace(/"/g, "&quot;");

        return (
          '<article class="shop-card cart-product-card">' +
          '<div class="shop-card-media-wrap">' +
          '<div class="shop-card-media">' +
          '<img src="' +
          item.image +
          '" alt="' +
          safeName +
          '" loading="lazy" /></div>' +
          '<div class="shop-price">' +
          unitPrice(item) +
          "</div>" +
          "</div>" +
          '<div class="shop-card-body">' +
          "<h3>" +
          item.name +
          "</h3>" +
          '<p class="shop-tag">' +
          tag +
          "</p>" +
          (short ? "<p>" + short + "</p>" : "") +
          '<div class="cart-row-controls">' +
          '<button type="button" class="qty-btn" data-qty-minus="' +
          item.id +
          '" aria-label="Decrease">−</button>' +
          '<span class="cart-qty">' +
          item.qty +
          "</span>" +
          '<button type="button" class="qty-btn" data-qty-plus="' +
          item.id +
          '" aria-label="Increase">+</button>' +
          '<button type="button" class="cart-remove" data-remove="' +
          item.id +
          '">Remove</button>' +
          "</div></div></article>"
        );
      })
      .join("");
  }

  document.addEventListener("DOMContentLoaded", function () {
    render();
    var root = document.getElementById("cart-items");
    if (root) {
      root.addEventListener("click", function (e) {
        var minus = e.target.closest("[data-qty-minus]");
        var plus = e.target.closest("[data-qty-plus]");
        var remove = e.target.closest("[data-remove]");
        if (minus) {
          var id = minus.getAttribute("data-qty-minus");
          var item = Cart.getCart().find(function (i) {
            return i.id === id;
          });
          if (item) {
            if (item.qty <= 1) Cart.removeFromCart(id);
            else Cart.setQty(id, item.qty - 1);
          }
          render();
        }
        if (plus) {
          var pid = plus.getAttribute("data-qty-plus");
          var found = Cart.getCart().find(function (i) {
            return i.id === pid;
          });
          if (found) Cart.setQty(pid, found.qty + 1);
          render();
        }
        if (remove) {
          Cart.removeFromCart(remove.getAttribute("data-remove"));
          render();
        }
      });
    }

    var wa = document.getElementById("btn-wa-cart");
    if (wa) {
      wa.addEventListener("click", function () {
        Cart.openWhatsAppForCart();
      });
    }
    var clear = document.getElementById("btn-clear-cart");
    if (clear) {
      clear.addEventListener("click", function () {
        Cart.clearCart();
        render();
      });
    }

    window.addEventListener("cart:updated", render);
  });
})();
