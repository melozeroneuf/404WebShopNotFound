// admin.js — Admin-Panel: Benutzer, Produkte, Bestellungen, Gutscheine verwalten
// Kommentare vor jeder Funktion erklären kurz Zweck und Verhalten.
console.log("admin.js wurde geladen");

let currentlyEditedProduct = null;
let currentOrderDetails = null;

const adminHandlerUrl = "/404webshopnotfound/backend/logic/adminHandler.php";

const usersTableBody = document.getElementById("usersTableBody");
const productsTableBody = document.getElementById("productsTableBody");
const adminMessage = document.getElementById("adminMessage");

const ordersTableBody = document.getElementById("ordersTableBody");
const orderDetailsContainer = document.getElementById("orderDetailsContainer");
const orderDetailsBody = document.getElementById("orderDetailsBody");

const refreshUsersBtn = document.getElementById("refreshUsersBtn");
const showProductFormBtn = document.getElementById("showProductFormBtn");
const productForm = document.getElementById("productForm");

const couponsTableBody = document.getElementById("couponsTableBody");
const couponForm = document.getElementById("couponForm");
const generateCouponBtn = document.getElementById("generateCouponBtn");

// Zeigt eine kurze Admin-Nachricht (Status/Fehlermeldung)
function showAdminMessage(message) {
    if (adminMessage) {
        adminMessage.innerText = message;
    }
}

/* =========================
   BENUTZER
========================= */

// Lädt Benutzerliste vom Server und rendert sie
function loadUsers() {
    fetch(adminHandlerUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "getUsers"
        })
    })
        .then(response => response.json())
        .then(data => {
            console.log("User Antwort:", data);

            if (!data.success) {
                usersTableBody.innerHTML = `<tr><td colspan="6">${data.message}</td></tr>`;
                return;
            }

            renderUsers(data.users);
        })
        .catch(error => {
            console.error("User Fehler:", error);
            usersTableBody.innerHTML = `<tr><td colspan="6">Fehler beim Laden der Benutzer.</td></tr>`;
        });
}

// Rendert die Benutzer-Tabelle mit Role/Status Controls
function renderUsers(users) {
    if (!users || users.length === 0) {
        usersTableBody.innerHTML = `<tr><td colspan="6">Keine Benutzer gefunden.</td></tr>`;
        return;
    }

    usersTableBody.innerHTML = "";

    users.forEach(user => {
        const tr = document.createElement("tr");

        tr.innerHTML = `
            <td>${user.id}</td>
            <td>${user.username}</td>
            <td>${user.email}</td>

            <td>
                <select class="role-select">
                    <option value="customer" ${user.role === "customer" ? "selected" : ""}>
                        Customer
                    </option>
                    <option value="admin" ${user.role === "admin" ? "selected" : ""}>
                        Admin
                    </option>
                </select>
            </td>

            <td>
                <select class="status-select">
                    <option value="1" ${user.is_active == 1 ? "selected" : ""}>
                        Aktiv
                    </option>
                    <option value="0" ${user.is_active == 0 ? "selected" : ""}>
                        Inaktiv
                    </option>
                </select>
            </td>

            <td>
                <button class="admin-delete-btn">Löschen</button>
            </td>
        `;

        tr.querySelector(".role-select").addEventListener("change", (event) => {
            updateUserRole(user.id, event.target.value);
        });

        tr.querySelector(".status-select").addEventListener("change", (event) => {
            updateUserStatus(user.id, event.target.value);
        });

        tr.querySelector(".admin-delete-btn").addEventListener("click", () => {
            deleteUser(user.id);
        });

        usersTableBody.appendChild(tr);
    });
}
// Aktualisiert die Rolle eines Benutzers via Backend
function updateUserRole(userId, role) {
    fetch(adminHandlerUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "updateRole",
            userId: userId,
            role: role
        })
    })
        .then(response => response.json())
        .then(data => {
            showAdminMessage(data.message);
            loadUsers();
        });
}

// Löscht einen Benutzer (zeigt Bestätigungsdialog)
function deleteUser(userId) {
    if (!confirm("Benutzer wirklich löschen?")) {
        return;
    }

    fetch(adminHandlerUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "deleteUser",
            userId: userId
        })
    })
        .then(response => response.json())
        .then(data => {
            showAdminMessage(data.message);
            loadUsers();
        });
}

/* =========================
   PRODUKTE
========================= */

// Lädt Produktliste für die Admin-Übersicht
function loadProducts() {
    fetch(adminHandlerUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "getProducts"
        })
    })
        .then(response => response.json())
        .then(data => {
            console.log("Produkt Antwort:", data);

            if (!data.success) {
                productsTableBody.innerHTML = `<tr><td colspan="8">${data.message}</td></tr>`;
                return;
            }

            renderProducts(data.products);
        })
        .catch(error => {
            console.error("Produkt Fehler:", error);
            productsTableBody.innerHTML = `<tr><td colspan="8">Fehler beim Laden der Produkte.</td></tr>`;
        });
}
// Lädt Bestellungen für die Admin-Übersicht
function loadOrders() {

    fetch(adminHandlerUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "getOrders"
        })
    })
        .then(response => response.json())
        .then(data => {

            console.log("Orders:", data);

            if (!data.success) {
                ordersTableBody.innerHTML =
                    `<tr><td colspan="6">${data.message}</td></tr>`;
                return;
            }

            renderOrders(data.orders);
        });
}

// Rendert Bestellungen in der Orders-Tabelle
function renderOrders(orders) {

    if (!orders || orders.length === 0) {

        ordersTableBody.innerHTML =
            `<tr><td colspan="7">Keine Bestellungen vorhanden.</td></tr>`;

        return;
    }

    let html = "";

    orders.forEach(order => {

        html += `
            <tr>
                <td>${order.id}</td>
                <td>${order.username}</td>
                <td>${order.email}</td>
                <td>${parseFloat(order.total).toFixed(2)} €</td>

                <td>
                    <select class="status-select" onchange="updateOrderStatus(${order.id}, this.value)">
                        <option value="offen" ${order.status === "offen" ? "selected" : ""}>Offen</option>
                        <option value="bezahlt" ${order.status === "bezahlt" ? "selected" : ""}>Bezahlt</option>
                        <option value="versendet" ${order.status === "versendet" ? "selected" : ""}>Versendet</option>
                        <option value="storniert" ${order.status === "storniert" ? "selected" : ""}>Storniert</option>
                    </select>
                </td>

                <td>${order.created_at}</td>

                <td>
                    <button class="admin-role-btn" onclick="loadOrderDetails(${order.id})">
                        Details
                    </button>
                </td>
            </tr>
        `;
    });

    ordersTableBody.innerHTML = html;
}

// Rendert die Produktliste in der Admin-Tabelle
function renderProducts(products) {
    if (!products || products.length === 0) {
        productsTableBody.innerHTML = `<tr><td colspan="8">Keine Produkte gefunden.</td></tr>`;
        return;
    }

    productsTableBody.innerHTML = "";

    products.forEach(product => {
        const tr = document.createElement("tr");

        tr.innerHTML = `
            <td>${product.id}</td>
            <td>${product.name}</td>
            <td>${product.category}</td>
            <td>${parseFloat(product.price).toFixed(2)} €</td>
            <td>${product.rating}</td>
            <td>${product.image}</td>
            <td>${product.is_active == 1 ? "Aktiv" : "Inaktiv"}</td>
            <td>
                <button class="admin-role-btn">Bearbeiten</button>
                <button class="admin-delete-btn">Löschen</button>
            </td>
        `;

        tr.querySelector(".admin-role-btn").addEventListener("click", () => {
            editProduct(product);
        });

        tr.querySelector(".admin-delete-btn").addEventListener("click", () => {
            deleteProduct(product.id);
        });

        productsTableBody.appendChild(tr);
    });
}

// Öffnet das Formular zum Bearbeiten eines Produkts und füllt Werte
function editProduct(product) {

    if (
        currentlyEditedProduct === product.id &&
        !productForm.classList.contains("hidden")
    ) {
        productForm.classList.add("hidden");
        currentlyEditedProduct = null;
        return;
    }

    currentlyEditedProduct = product.id;

    document.getElementById("productId").value = product.id;
    document.getElementById("productName").value = product.name;
    document.getElementById("productDescription").value = product.description;
    document.getElementById("productCategory").value = product.category;
    document.getElementById("productPrice").value = product.price;
    document.getElementById("productRating").value = product.rating;
    document.getElementById("productImage").value = product.image;
    document.getElementById("productImageFile").value = "";
    document.getElementById("productActive").value = product.is_active;

    productForm.classList.remove("hidden");

    productForm.scrollIntoView({
        behavior: "smooth",
        block: "start"
    });
}

// Löscht ein Produkt nach Bestätigung (Backend-Aufruf)
function deleteProduct(productId) {
    if (!confirm("Produkt wirklich löschen?")) {
        return;
    }

    fetch(adminHandlerUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "deleteProduct",
            productId: productId
        })
    })
        .then(response => response.json())
        .then(data => {
            showAdminMessage(data.message);
            loadProducts();
        });
}

// Setzt Aktiv-/Inaktiv-Status eines Benutzers
function updateUserStatus(userId, status) {

    fetch(adminHandlerUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "updateUserStatus",
            userId: userId,
            is_active: status
        })
    })
        .then(response => response.json())
        .then(data => {
            showAdminMessage(data.message);
            loadUsers();
        });
}

// Aktualisiert Status einer Bestellung (offen/bezahlt/versendet/storniert)
function updateOrderStatus(orderId, status) {
    fetch(adminHandlerUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "updateOrderStatus",
            orderId: orderId,
            status: status
        })
    })
        .then(response => response.json())
        .then(data => {
            showAdminMessage(data.message);
            loadOrders();
        });
}

// Lädt und zeigt Details zu einer Bestellung (Items)
function loadOrderDetails(orderId) {

    if (
        currentOrderDetails === orderId &&
        !orderDetailsContainer.classList.contains("hidden")
    ) {
        orderDetailsContainer.classList.add("hidden");
        currentOrderDetails = null;
        return;
    }

    fetch(adminHandlerUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "getOrderDetails",
            orderId: orderId
        })
    })
        .then(response => response.json())
        .then(data => {

            if (!data.success) {
                return;
            }

            let html = "";

            data.items.forEach(item => {

                html += `
                <tr>
                    <td>${item.name}</td>
                    <td>${parseFloat(item.price).toFixed(2)} €</td>
                    <td>${item.quantity}</td>
                </tr>
            `;
            });

            orderDetailsBody.innerHTML = html;

            currentOrderDetails = orderId;

            orderDetailsContainer.classList.remove("hidden");

            orderDetailsContainer.scrollIntoView({
                behavior: "smooth"
            });
        });
}

// Lädt Gutscheine vom Backend
function loadCoupons() {
    fetch(adminHandlerUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "getCoupons"
        })
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                couponsTableBody.innerHTML =
                    `<tr><td colspan="4">${data.message}</td></tr>`;
                return;
            }

            renderCoupons(data.coupons);
        });
}

// Rendert die Gutschein-Tabelle
function renderCoupons(coupons) {
    if (!coupons || coupons.length === 0) {
        couponsTableBody.innerHTML =
            `<tr><td colspan="4">Keine Gutscheine vorhanden.</td></tr>`;
        return;
    }

    let html = "";

    coupons.forEach(coupon => {
        html += `
            <tr>
                <td>${coupon.id}</td>
                <td>${coupon.code}</td>
                <td>${coupon.value}%</td>
                <td>${coupon.expires_at}</td>
            </tr>
        `;
    });

    couponsTableBody.innerHTML = html;
}
// Erzeugt einen zufälligen Gutschein-Code und füllt das Eingabefeld
function generateCouponCode() {
    const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    let code = "SHOP";

    for (let i = 0; i < 6; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }

    document.getElementById("couponCodeInput").value = code;
}

if (showProductFormBtn) {
    showProductFormBtn.addEventListener("click", () => {

        if (!productForm.classList.contains("hidden")) {
            productForm.classList.add("hidden");
            return;
        }

        productForm.reset();
        document.getElementById("productId").value = "";

        productForm.classList.remove("hidden");
    });
}

if (productForm) {
    productForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const productId = document.getElementById("productId").value;

        const formData = new FormData();

        formData.append("action", productId ? "updateProduct" : "createProduct");
        formData.append("productId", productId);
        formData.append("name", document.getElementById("productName").value);
        formData.append("description", document.getElementById("productDescription").value);
        formData.append("category", document.getElementById("productCategory").value);
        formData.append("price", document.getElementById("productPrice").value);
        formData.append("rating", document.getElementById("productRating").value);
        formData.append("image", document.getElementById("productImage").value);
        formData.append("is_active", document.getElementById("productActive").value);

        const imageFile = document.getElementById("productImageFile").files[0];

        if (imageFile) {
            formData.append("imageFile", imageFile);
        }

        fetch(adminHandlerUrl, {
            method: "POST",
            credentials: "same-origin",
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                showAdminMessage(data.message);

                if (data.success) {
                    productForm.reset();

                    currentlyEditedProduct = null;
                    productForm.classList.add("hidden");

                    loadProducts();
                }
            });
    });
}

if (generateCouponBtn) {
    generateCouponBtn.addEventListener("click", generateCouponCode);
}

if (couponForm) {
    couponForm.addEventListener("submit", function (event) {
        event.preventDefault();

        fetch(adminHandlerUrl, {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                action: "createCoupon",
                code: document.getElementById("couponCodeInput").value,
                value: document.getElementById("couponValueInput").value,
                expires_at: document.getElementById("couponExpiresInput").value
            })
        })
            .then(response => response.json())
            .then(data => {
                showAdminMessage(data.message);

                if (data.success) {
                    couponForm.reset();
                    loadCoupons();
                }
            });
    });
}

document.addEventListener("DOMContentLoaded", () => {
    loadUsers();
    loadProducts();
    loadOrders();
    loadCoupons();
});

if (refreshUsersBtn) {
    refreshUsersBtn.addEventListener("click", loadUsers);
}