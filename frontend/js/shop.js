console.log("shop.js wurde geladen");

const products = {
    1: { id: 1, name: "JUAN LAURA Schokoladen", price: 9.90 },
    2: { id: 2, name: "PUMATIY Chili", price: 9.90 },
    3: { id: 3, name: "PUMATIY Kakaomasse", price: 14.90 },
    4: { id: 4, name: "KUYAY Blaubeeren", price: 9.90 },
    5: { id: 5, name: "FJAK Belize", price: 14.90 },
    6: { id: 6, name: "Mint Crunch", price: 5.80 }
};

const cartHandlerUrl = (() => {
    const path = window.location.pathname;
    if (path.includes("/frontend/sites/")) {
        return "../../backend/logic/cartHandler.php";
    }
    return "../backend/logic/cartHandler.php";
})();

document.querySelectorAll(".add-to-cart-btn").forEach(button => {
    button.addEventListener("click", function () {
        console.log("Button geklickt");

        const id = this.dataset.itemId;
        const product = products[id];

        console.log(product);

        fetch(cartHandlerUrl, {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                action: "add",
                id: product.id,
                name: product.name,
                price: product.price
            })
        })
            .then(response => response.json())
            .then(data => {
                console.log("Backend Antwort:", data);

                if (data.success) {
                    alert("Produkt wurde zum Warenkorb hinzugefügt");
                }
            })
            .catch(error => {
                console.error("Fehler:", error);
                alert("Fehler beim Hinzufügen");
            });
    });
});