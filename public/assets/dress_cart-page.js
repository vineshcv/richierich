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
    var pay = document.getElementById("btn-pay");
    var canPay = items.length && Cart.cartTotal() > 0;
    if (pay) pay.hidden = !canPay;
    var checkout = document.getElementById("checkout-form");
    if (checkout) checkout.hidden = !canPay;

    var layout = document.querySelector(".cart-layout");
    if (layout) layout.classList.toggle("is-empty", !items.length);

    if (!items.length) {
      var shop = (window.DRESS_CONFIG && window.DRESS_CONFIG.routes && window.DRESS_CONFIG.routes.shop) || "/shop";
      root.innerHTML =
        '<div class="cart-empty">' +
        '<div class="cart-empty-art" aria-hidden="true">' +
        '<svg class="cart-empty-icon" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.6">' +
        '<path stroke-linecap="round" stroke-linejoin="round" d="M14 22h36l-3.2 18.2a4 4 0 01-3.9 3.3H21.1a4 4 0 01-3.9-3.3L14 22z"/>' +
        '<path stroke-linecap="round" d="M24 22l3.2-7.2A3 3 0 0130 13h4a3 3 0 012.8 1.8L40 22"/>' +
        '<circle cx="26" cy="50" r="2.2" fill="currentColor" stroke="none"/>' +
        '<circle cx="42" cy="50" r="2.2" fill="currentColor" stroke="none"/>' +
        '<path stroke-linecap="round" d="M18 16l-2-3M46 15l3-2M50 24l4 1"/>' +
        "</svg></div>" +
        "<h2>Your cart is empty</h2>" +
        "<p>Explore our collection and find something you'll love.</p>" +
        '<a class="btn btn-primary cart-empty-btn" href="' + shop + '">' +
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 8h12l-1 11H7L6 8z"/><path stroke-linecap="round" d="M9 8V7a3 3 0 016 0v1"/></svg>' +
        "Browse products" +
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>' +
        "</a></div>";
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

    var pay = document.getElementById("btn-pay");
    if (pay) {
      pay.addEventListener("click", function () {
        if (!checkoutReady()) return;
        startRazorpay(pay);
      });
    }
    var accountToggle = document.getElementById("create-account");
    var accountFields = document.getElementById("account-fields");
    if (accountToggle && accountFields) {
      accountToggle.addEventListener("change", function () {
        accountFields.hidden = !accountToggle.checked;
      });
    }
    var loginToggle = document.getElementById("checkout-login-toggle");
    var loginBox = document.getElementById("checkout-login");
    if (loginToggle && loginBox) {
      loginToggle.addEventListener("click", function () {
        loginBox.hidden = !loginBox.hidden;
      });
    }
    var loginSubmit = document.getElementById("checkout-login-submit");
    if (loginSubmit) {
      loginSubmit.addEventListener("click", function () {
        loginCustomer(loginSubmit);
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

  function csrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute("content") : "";
  }

  function setPayStatus(text) {
    var note = document.getElementById("cart-pay-status");
    if (!note) return;
    note.hidden = !text;
    note.textContent = text || "";
  }

  function loadRazorpay() {
    if (window.Razorpay) return Promise.resolve();
    return new Promise(function (resolve, reject) {
      var script = document.createElement("script");
      script.src = "https://checkout.razorpay.com/v1/checkout.js";
      script.onload = function () { resolve(); };
      script.onerror = function () { reject(new Error("Razorpay failed to load")); };
      document.head.appendChild(script);
    });
  }

  function fieldValue(id) {
    var el = document.getElementById(id);
    return el ? String(el.value || "").trim() : "";
  }

  function markInvalid(id, invalid) {
    var el = document.getElementById(id);
    if (el) el.classList.toggle("is-invalid", invalid);
  }

  function checkoutReady() {
    var checks = [
      ["ship-first", fieldValue("ship-first") !== ""],
      ["ship-last", fieldValue("ship-last") !== ""],
      ["ship-address", fieldValue("ship-address") !== ""],
      ["ship-city", fieldValue("ship-city") !== ""],
      ["ship-state", fieldValue("ship-state") !== ""],
      ["ship-pin", /^[1-9][0-9]{5}$/.test(fieldValue("ship-pin"))],
      ["ship-phone", /^(?:\+91[\s-]?)?[6-9][0-9]{9}$/.test(fieldValue("ship-phone"))],
      ["ship-email", /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(fieldValue("ship-email"))],
    ];
    var account = document.getElementById("create-account");
    if (account && account.checked) {
      checks.push(["account-password", fieldValue("account-password").length >= 8]);
    }
    var firstBad = null;
    checks.forEach(function (check) {
      markInvalid(check[0], !check[1]);
      if (!check[1] && !firstBad) firstBad = check[0];
    });
    if (firstBad) {
      var node = document.getElementById(firstBad);
      if (node) node.focus();
      setPayStatus(firstBad === "account-password" ? "Account password must be at least 8 characters." : "Please complete the shipping address.");
      return false;
    }
    return true;
  }

  function shippingPayload() {
    var account = document.getElementById("create-account");
    var payload = {
      items: Cart.getCart().map(function (item) {
        return { id: item.id, qty: item.qty };
      }),
      shipping: {
        first_name: fieldValue("ship-first"),
        last_name: fieldValue("ship-last"),
        address_1: fieldValue("ship-address"),
        address_2: fieldValue("ship-address-2"),
        city: fieldValue("ship-city"),
        state: fieldValue("ship-state"),
        postcode: fieldValue("ship-pin"),
        country: fieldValue("ship-country") || "India",
        phone: fieldValue("ship-phone"),
        email: fieldValue("ship-email"),
      },
      create_account: !!(account && account.checked),
    };
    if (payload.create_account) payload.password = fieldValue("account-password");
    return payload;
  }

  function rememberCsrf(token) {
    if (!token) return;
    var meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) meta.setAttribute("content", token);
  }

  function loginCustomer(button) {
    var form = document.getElementById("checkout-form");
    var status = document.getElementById("checkout-login-status");
    if (!form) return;
    button.disabled = true;
    if (status) {
      status.hidden = false;
      status.textContent = "Logging in…";
    }
    fetch(form.getAttribute("data-login"), {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-CSRF-TOKEN": csrfToken(),
      },
      body: JSON.stringify({
        email: fieldValue("login-email"),
        password: fieldValue("login-password"),
      }),
    })
      .then(function (res) {
        return res.json().then(function (body) {
          if (!res.ok) throw new Error(body.message || "Could not log in");
          return body;
        });
      })
      .then(function (body) {
        rememberCsrf(body.csrf);
        if (status) status.textContent = "Logged in as " + body.email;
        var email = document.getElementById("ship-email");
        var first = document.getElementById("ship-first");
        if (email && !email.value) email.value = body.email || "";
        if (first && !first.value && body.name) first.value = body.name;
        var create = document.getElementById("create-account");
        var accountFields = document.getElementById("account-fields");
        if (create) {
          create.checked = false;
          create.closest(".checkout-create").hidden = true;
        }
        if (accountFields) accountFields.hidden = true;
      })
      .catch(function (err) {
        if (status) status.textContent = err.message || "Could not log in";
      })
      .finally(function () {
        button.disabled = false;
      });
  }

  function startRazorpay(button) {
    var payload = shippingPayload();
    if (!payload.items.length) return;
    button.disabled = true;
    setPayStatus("Opening payment…");
    fetch(button.getAttribute("data-create"), {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-CSRF-TOKEN": csrfToken(),
      },
      body: JSON.stringify(payload),
    })
      .then(function (res) {
        return res.json().then(function (body) {
          if (!res.ok) throw new Error(body.message || "Could not start payment");
          return body;
        });
      })
      .then(function (order) {
        return loadRazorpay().then(function () { return order; });
      })
      .then(function (order) {
        rememberCsrf(order.csrf);
        var checkout = new window.Razorpay({
          key: order.key,
          amount: order.amount,
          currency: order.currency,
          name: order.name,
          description: "Order payment",
          order_id: order.order_id,
          prefill: order.prefill || {},
          theme: { color: "#26140a" },
          handler: function (response) {
            verifyPayment(response);
          },
          modal: {
            ondismiss: function () {
              button.disabled = false;
              setPayStatus("Payment was closed before it finished.");
            },
          },
        });
        checkout.on("payment.failed", function () {
          button.disabled = false;
          setPayStatus("Payment failed. You can try again.");
        });
        checkout.open();
        button.disabled = false;
      })
      .catch(function (err) {
        button.disabled = false;
        setPayStatus(err.message || "Could not start payment");
      });
  }

  function verifyPayment(response) {
    setPayStatus("Confirming payment…");
    var pay = document.getElementById("btn-pay");
    fetch(pay ? pay.getAttribute("data-verify") : "", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-CSRF-TOKEN": csrfToken(),
      },
      body: JSON.stringify(response),
    })
      .then(function (res) {
        return res.json().then(function (body) {
          if (!res.ok) throw new Error(body.message || "Payment could not be verified");
          return body;
        });
      })
      .then(function () {
        Cart.clearCart();
        setPayStatus("Payment received. Thank you.");
      })
      .catch(function (err) {
        setPayStatus(err.message || "Payment could not be verified");
      });
  }
})();
