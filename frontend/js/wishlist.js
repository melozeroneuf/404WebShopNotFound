console.log("wishlist.js wurde geladen");

const wishlistStorageKey = "wishlistItems";

const wishlistHandlerUrl = window.location.pathname.includes("/frontend/sites/")
    ? "../../backend/logic/wishlistHandler.php"
    : "backend/logic/wishlistHandler.php";

const checkLoginUrl = window.location.pathname.includes("/frontend/sites/")
    ? "../../backend/logic/checkLogin.php"
    : "backend/logic/checkLogin.php";

let isUserLoggedIn = false;

const productCatalog = {
    1: {
        id: 1,
        title: "JUAN LAURA Schokoladen | Kakaomasse<br>Peru »Pichari – Chuncho« 100% | 70g",
        meta: "<strong>Inhalt:</strong> 0.07 kg (141,43 € / 1 kg)",
        price: "9,90 €",
        rating: 5,
        image: "../img/schoko1.png",
        alt: "Schoko 1"
    },
    2: {
        id: 2,
        title: "PUMATIY | Dunkle Schokolade &amp; Chili<br>Peru »Cusco – Aji« 70% | 50g",
        meta: "<strong>Inhalt:</strong> 0.05 kg (198,00 € / 1 kg)",
        price: "9,90 €",
        image: "../img/schoko2.png",
        alt: "PUMATIY Dunkle Schokolade &amp; Chili"
    },
    3: {
        id: 3,
        title: "PUMATIY Schokolade | Kakaomasse<br>Zeremonieller Kakao Peru »Cusco – Chuncho« 100% | 100g",
        meta: "<strong>Inhalt:</strong> 0.1 kg (149,00 € / 1 kg)",
        price: "14,90 €",
        rating: 5,
        image: "../img/schoko3.png",
        alt: "PUMATIY Schokolade | Kakaomasse"
    },
    4: {
        id: 4,
        title: "KUYAY | Dunkle Schokolade &amp; Blaubeeren Peru »Chocolat with<br>Blueberry« 70% | 70g",
        meta: "<strong>Inhalt:</strong> 0.07 kg (141,43 € / 1 kg)",
        price: "9,90 €",
        image: "../img/schoko4.png",
        alt: "KUYAY Dunkle Schokolade &amp; Blaubeeren"
    },
    5: {
        id: 5,
        title: "FJAK Chocolate | Dunkle Schokolade Belize »Trio Reserve Microlot« 70% | BIO | 60g",
        meta: "<strong>Inhalt:</strong> 0.06 kg (248,33 € / 1 kg)",
        price: "14,90 €",
        image: "../img/schoko5.png",
        alt: "FJAK Chocolate Dunkle &amp; Schokolade Belize"
    },
    6: {
        id: 6,
        title: "CHOCOLATE &amp; Love | Dunkle Schokolade &amp; Minze »Mint Crunch« 67% | BIO | 80g",
        meta: "<strong>Inhalt:</strong> 0.08 kg (72,50 € / 1 kg)",
        price: "5,80 €",
        priceOld: "7,60 €",
        savings: "(23,68% gespart)",
        isSale: true,
        image: "../img/schoko6.png",
        alt: "CHOCOLATE &amp; Love Dunkle Schokolade &amp; Minze"
    }
};

function getWishlistItems() {
    try {
        const raw = localStorage.getItem(wishlistStorageKey);
        return raw ? JSON.parse(raw) : [];
    } catch (error) {
        console.error("Wishlist lesen fehlgeschlagen:", error);
        return [];
    }
}

function setWishlistItems(items) {
    localStorage.setItem(wishlistStorageKey, JSON.stringify(items));
}

function updateWishlistIcon(button, isActive) {
    const icon = button.querySelector("i");
    if (!icon) {
        return;
    }
    icon.classList.toggle("bi-heart", !isActive);
    icon.classList.toggle("bi-heart-fill", isActive);
}

function isSoldOut(button) {
    return button.dataset.soldOut === "true";
}

function applyWishlistState(items) {
    const itemSet = new Set(items);
    document.querySelectorAll(".wishlist-btn").forEach(button => {
        const itemId = button.dataset.itemId;
        updateWishlistIcon(button, itemId && itemSet.has(itemId));
    });

    const badge = document.getElementById("wishlistCount");
    if (badge) {
        badge.textContent = String(items.length);
        badge.style.display = items.length > 0 ? "inline-flex" : "none";
    }

    renderWishlistPage(items);
}

function renderWishlistPage(items) {
    const container = document.getElementById("wishlistItems");
    if (!container) {
        return;
    }

    if (!items || items.length === 0) {
        container.innerHTML = "<p class=\"wishlist-empty\">Deine Merkliste ist noch leer.</p>";
        return;
    }

    const html = items
        .map(itemId => productCatalog[itemId])
        .filter(Boolean)
        .map(product => {
            const ratingMarkup = product.rating
                ? `
                    <div class="rating-stars" aria-label="Bewertung: ${product.rating} von 5 Sternen">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                `
                : "";
            const saleBadge = product.isSale ? "<span class=\"badge-sale\" aria-label=\"Sale\">%</span>" : "";
            const priceMarkup = product.isSale
                ? `
                    <p class="slide-product-price slide-product-price--sale">
                        <span class="price-current">${product.price}</span>
                        <span class="price-old">${product.priceOld}</span>
                    </p>
                    <p class="price-savings">${product.savings}</p>
                `
                : `<p class="slide-product-price">${product.price}</p>`;
            return `
                <div class="slide-product">
                    <div class="slide-product-top">
                        <span class="badge-new">Neu</span>
                        <button class="wishlist-remove" type="button" aria-label="Aus der Merkliste entfernen" data-item-id="${product.id}">
                            &times;
                        </button>
                    </div>
                    ${saleBadge}
                    <img src="${product.image}" class="img-fluid slide-product-image" alt="${product.alt}">
                    ${ratingMarkup}
                    <h3 class="slide-product-title">${product.title}</h3>
                    <p class="slide-product-meta">${product.meta}</p>
                    ${priceMarkup}
                    <div class="slide-product-actions">
                        <button class="btn btn-dark add-to-cart-btn" data-item-id="${product.id}" type="button">In den Warenkorb</button>
                        <a class="slide-product-note" href="#">Preise inkl. MwSt. zzgl. Versandkosten</a>
                    </div>
                </div>
            `;
        })
        .join("");

    container.innerHTML = html;
}

async function fetchLoginStatus() {
    try {
        const response = await fetch(checkLoginUrl);
        const data = await response.json();
        isUserLoggedIn = !!data.loggedIn;
        return isUserLoggedIn;
    } catch (error) {
        console.error("Login-Status Fehler:", error);
        return false;
    }
}

async function fetchWishlist(action, itemId) {
    try {
        const response = await fetch(wishlistHandlerUrl, {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                action: action,
                id: itemId
            })
        });

        const data = await response.json();
        if (data.success && Array.isArray(data.items)) {
            return data.items.map(String);
        }
        return null;
    } catch (error) {
        console.error("Wishlist Fehler:", error);
        return null;
    }
}

function toggleWishlist(button) {
    if (isSoldOut(button)) {
        return;
    }

    const itemId = button.dataset.itemId;
    if (!itemId) {
        console.warn("Wishlist ohne item id:", button);
        return;
    }

    if (isUserLoggedIn) {
        fetchWishlist("toggle", itemId).then(items => {
            if (items) {
                setWishlistItems(items);
                applyWishlistState(items);
                return;
            }

            const localItems = getWishlistItems();
            const index = localItems.indexOf(itemId);
            if (index === -1) {
                localItems.push(itemId);
            } else {
                localItems.splice(index, 1);
            }
            setWishlistItems(localItems);
            applyWishlistState(localItems);
        });
        return;
    }

    const items = getWishlistItems();
    const index = items.indexOf(itemId);
    if (index === -1) {
        items.push(itemId);
    } else {
        items.splice(index, 1);
    }
    setWishlistItems(items);
    applyWishlistState(items);
}

function initWishlistButtons() {
    const items = getWishlistItems();
    applyWishlistState(items);

    document.addEventListener("click", function (event) {
        const button = event.target.closest(".wishlist-btn");

        if (!button) {
            return;
        }

        if (isSoldOut(button)) {
            return;
        }

        toggleWishlist(button);
    });

    const wishlistContainer = document.getElementById("wishlistItems");
    if (wishlistContainer) {
        wishlistContainer.addEventListener("click", event => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            const removeButton = target.closest(".wishlist-remove");
            if (!removeButton) {
                return;
            }

            const itemId = removeButton.dataset.itemId;
            if (!itemId) {
                return;
            }

            if (isUserLoggedIn) {
                fetchWishlist("toggle", itemId).then(items => {
                    if (items) {
                        setWishlistItems(items);
                        applyWishlistState(items);
                    }
                });
                return;
            }

            const items = getWishlistItems();
            const index = items.indexOf(itemId);
            if (index !== -1) {
                items.splice(index, 1);
                setWishlistItems(items);
                applyWishlistState(items);
            }
        });
    }

    fetchLoginStatus().then(loggedIn => {
        if (!loggedIn) {
            return;
        }

        fetchWishlist("get").then(serverItems => {
            if (!serverItems) {
                return;
            }

            setWishlistItems(serverItems);
            applyWishlistState(serverItems);
        });
    });

}

document.addEventListener("DOMContentLoaded", initWishlistButtons);
