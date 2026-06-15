console.log("shop.js wurde geladen");

const cartHandlerUrl = window.location.pathname.includes("/frontend/sites/")
    ? "../../backend/logic/cartHandler.php"
    : "../../backend/logic/cartHandler.php";

document.addEventListener("click", function (event) {
    const button = event.target.closest(".add-to-cart-btn");

    if (!button) return;
    if (button.dataset.soldOut === "true") return;

    const card = button.closest(".slide-product");

    const id = button.dataset.itemId;
    const name = card.querySelector(".slide-product-title").innerText;

    let priceText = card.querySelector(".price-current")
        ? card.querySelector(".price-current").innerText
        : card.querySelector(".slide-product-price").innerText;

    const price = parseFloat(
        priceText.replace("€", "").replace(",", ".").trim()
    );

    fetch(cartHandlerUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "add",
            id: id,
            name: name,
            price: price
        })
    })
        .then(response => response.json())
        .then(data => {
            console.log("Warenkorb Antwort:", data);

            if (data.success) {
                button.innerText = "Hinzugefügt";
                setTimeout(() => {
                    button.innerText = "In den Warenkorb";
                }, 1000);
            }
        })
        .catch(error => {
            console.error("Warenkorb Fehler:", error);
        });
});