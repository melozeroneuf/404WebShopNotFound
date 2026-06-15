console.log("cart.js wurde geladen")

let couponDiscount = 0;

const backendBaseUrl = "/404webshopnotfound/backend/logic/";

function sendCartAction(action, id = null) {

    console.log("Cart Action:", action, id);

    fetch(backendBaseUrl + "cartHandler.php", {
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

            console.log("Backend Antwort:", data);


            if (data.success) {
                renderCart(data.cart);
            }
        })
        .catch(error => {
            console.error("Cart Fehler: ", error);
        });
}

function showCart() {
    sendCartAction("get");
}

function renderCart(cart) {

    const cartDiv = document.getElementById("cart");
    const summary = document.querySelector(".cart-summary strong");

    if (!cart || cart.length === 0) {

        cartDiv.innerHTML = "<p>Dein Warenkorb ist leer.</p>";
        summary.innerText = "0,00 EUR";
        return;
    }

    let html = "";
    let total = 0;

    cart.forEach(item => {

        const price = parseFloat(item.price);
        const quantity = parseInt(item.quantity);

        const itemTotal = price * quantity;

        total += itemTotal;

        html += `
            <div class="cart-item">

                <h3>${item.name}</h3>

                <p>Preis: ${price.toFixed(2)} €</p>

                <div class="cart-quantity">

                    <button type="button"
                        onclick="sendCartAction('decrease', ${item.id})">
                        -
                    </button>

                    <span>${quantity}</span>

                    <button type="button"
                        onclick="sendCartAction('increase', ${item.id})">
                        +
                    </button>

                </div>

                <p>
                    <strong>
                        Zwischensumme: ${itemTotal.toFixed(2)} €
                    </strong>
                </p>

            </div>

            <hr>
        `;
    });

    total = Math.max(0, total - couponDiscount);

    html += `
        <button type="button"
            class="cart-clear"
            onclick="sendCartAction('clear')">

            Warenkorb leeren

        </button>
    `;

    cartDiv.innerHTML = html;

    summary.innerText =
        total.toFixed(2).replace(".", ",") + " EUR";
}

document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM geladen");
    showCart();
});

const applyCouponBtn = document.getElementById("applyCouponBtn");

if (applyCouponBtn) {
    applyCouponBtn.addEventListener("click", () => {
        const code = document.getElementById("couponCode").value;

        fetch(backendBaseUrl + "couponHandler.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ code: code })
        })
            .then(response => response.json())
            .then(data => {
                document.getElementById("couponMessage").innerText = data.message;

                if (data.success) {
                    couponDiscount = parseFloat(data.value);
                    showCart();
                }
            });
    });
}