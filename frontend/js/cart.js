function showCart() {
    const cartDiv = document.getElementById("cart");
    const cart = JSON.parse(localStorage.getItem("cart")) || [];

    if (cart.length === 0) {
        cartDiv.innerHTML = "<p>Dein Warenkorb ist leer.</p>";
        return;
    }

    let html = "";
    let total = 0;

    cart.forEach(item => {
        total += item.price * item.quantity;

        html += `
            <div>
                <h3>${item.name}</h3>
                <p>Preis: ${item.price} €</p>
                <p>Menge: ${item.quantity}</p>
            </div>
            <hr>
        `;
    });

    html += `<h2>Gesamt: ${total.toFixed(2)} €</h2>`;
    cartDiv.innerHTML = html;
}

showCart();