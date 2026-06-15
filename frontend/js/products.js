const productHandlerUrl = "/404webshopnotfound/backend/logic/productHandler.php";

let allProducts = [];

fetch(productHandlerUrl)
    .then(response => response.json())
    .then(data => {
        const grid = document.getElementById("productsGrid");

        if (!data.success || data.products.length === 0) {
            grid.innerHTML = "<p>Keine Produkte gefunden.</p>";
            return;
        }

        allProducts = data.products;

        const params = new URLSearchParams(window.location.search);
        const selectedCategory = params.get("kategorie");

        if (selectedCategory) {
            allProducts = allProducts.filter(
                product => product.category === selectedCategory
            );
        }

        renderProducts(allProducts);
        setupSearch();

        });

        function renderProducts(products) {
            const grid = document.getElementById("productsGrid");

        let html = "";

        products.forEach(product => {
            html += `
                <div class="slide-product">
                    <div class="slide-product-top">
                        <span class="badge-new">Neu</span>

                        <button class="wishlist-btn" type="button" aria-label="Auf die Merkliste" data-item-id="${product.id}">
                            <i class="bi bi-heart"></i>
                        </button>
                    </div>

                    <img src="../img/${product.image}" class="img-fluid slide-product-image" alt="${product.name}">

                    <h3 class="slide-product-title">${product.name}</h3>

                    <p class="slide-product-meta">${product.description}</p>

                    <p class="slide-product-price">${Number(product.price).toFixed(2).replace(".", ",")} €</p>

                    <div class="slide-product-actions">
                        <button class="btn btn-dark add-to-cart-btn" data-item-id="${product.id}" type="button">
                            In den Warenkorb
                        </button>

                        <a class="slide-product-note" href="#">Preise inkl. MwSt. zzgl. Versandkosten</a>
                    </div>
                </div>
            `;
        });

        grid.innerHTML = html || "<p>Keine Produkte gefunden.</p>";
}

function setupSearch() {
    const searchInput = document.getElementById("productSearch");
    if (!searchInput) return;
    
    searchInput.addEventListener("input", () => {
        const searchText = searchInput.value.toLowerCase();
        const filteredProducts = allProducts.filter(product =>
            product.name.toLowerCase().includes(searchText) ||
            product.description.toLowerCase().includes(searchText)
        );
        
        renderProducts(filteredProducts);
    });
}