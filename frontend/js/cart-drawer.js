const cartDrawerBtn = document.getElementById("cartDrawerBtn");
const cartDrawer = document.getElementById("cartDrawer");
const cartOverlay = document.getElementById("cartOverlay");
const cartDrawerClose = document.getElementById("cartDrawerClose");
const cartDrawerContent = document.getElementById("cartDrawerContent");

const cartDrawerUrl =  "/404webshopnotfound/backend/logic/cartHandler.php"

let currentCartCount = 0;

function openCartDrawer() {
    cartDrawer.classList.add("active");
    cartOverlay.classList.add("active");
    loadCartDrawer();
}

function closeCartDrawer() {
    cartDrawer.classList.remove("active");
    cartOverlay.classList.remove("active");
}

function sendDrawerCartAction(action, id = null) {
    fetch(cartDrawerUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: action,
            id: id
        })
    })
        .then(response => response.json())
        .then(data => {
            renderCartDrawer(data.cart || []);
        });
}

function loadCartDrawer() {
    sendDrawerCartAction("get");
}

function renderCartDrawer(cart) {

    currentCartCount = 0;

    if (cart) {
        cart.forEach(item => {
            currentCartCount += parseInt(item.quantity);
        });
    }

    if (!cart || cart.length === 0) {

        cartDrawerContent.innerHTML = `
            <p class="cart-drawer-info">ⓘ Ihr Warenkorb ist leer.</p>
        `;
        updateCartCount(0);
        return;
    }

    let html = "";
    let total = 0;
    let itemCount = 0;

    cart.forEach(item => {
        const price = parseFloat(item.price);
        const quantity = parseInt(item.quantity);
        const itemTotal = price * quantity;
        itemCount += quantity;

        total += itemTotal;

        html += `
            <div class="cart-drawer-item">
                <div class="cart-drawer-item-top">
                    <strong>${item.name}</strong>

                    <button class="cart-drawer-remove"
                        type="button"
                        onclick="sendDrawerCartAction('remove', ${item.id})">
                        ×
                    </button>
                </div>

                <p>${price.toFixed(2)} €</p>

                <div class="cart-drawer-quantity">
                    <button type="button" onclick="sendDrawerCartAction('decrease', ${item.id})">-</button>
                    <span>${quantity}</span>
                    <button type="button" onclick="sendDrawerCartAction('increase', ${item.id})">+</button>
                </div>

                <p><strong>Zwischensumme: ${itemTotal.toFixed(2)} €</strong></p>
            </div>
            <hr>
        `;
    });

    html += `
    <div class="cart-drawer-total">
        <strong>Gesamt: ${total.toFixed(2)} €</strong>
    </div>
`;

    cartDrawerContent.innerHTML = html;
    updateCartCount(itemCount);
}

function updateCartCount(count) {
    const cartCountElement = document.getElementById("cartCount");
    if (cartCountElement) {
        cartCountElement.textContent = count;
        cartCountElement.style.display = count > 0 ? "inline-flex" : "none";
    }
}
function checkoutClick() {
    const message = document.getElementById("checkoutMessage");
    const countElement = document.getElementById("cartCount");
    const count = countElement ? Number(countElement.textContent) : 0;

    if (count <= 0) {
        if (message) {
            message.textContent = "Bitte füge zuerst Produkte zum Warenkorb hinzu.";
        }
        return;
    }

    if (message) {
        message.textContent = "";
    }

    window.location.href = "/404webshopnotfound/frontend/sites/checkout.html";
}

if (cartDrawerBtn) {
    cartDrawerBtn.addEventListener("click", openCartDrawer);
}

if (cartDrawerClose) {
    cartDrawerClose.addEventListener("click", closeCartDrawer);
}

if (cartOverlay) {
    cartOverlay.addEventListener("click", closeCartDrawer);
}

// Lade Warenkorb-Anzahl beim Laden der Seite
document.addEventListener("DOMContentLoaded", function() {
    sendDrawerCartAction("get");
});