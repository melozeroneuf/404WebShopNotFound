
const backendBaseUrl = "/404webshopnotfound/backend/logic/";


const guestMenu = document.getElementById("guestMenu");
const userMenu = document.getElementById("userMenu");
const welcomeUser = document.getElementById("welcomeUser");

fetch(backendBaseUrl + "checkLogin.php")
    .then(response => response.json())
    .then(data => {

        if (data.loggedIn) {

            if (guestMenu) {
                guestMenu.style.display = "none";
            }

            if (userMenu) {
                userMenu.style.display = "block";

                userMenu.innerHTML = `
                    <h3>Hallo ${data.username}</h3>

                    <a href="${window.location.pathname.includes("/frontend/sites/") ? "konto.html" : "sites/konto.html"}">
                        Mein Konto
                    </a>
                    
                    <button id="logoutLink" class="logout-dropdown-btn">
                        Logout
                    </button>
                `;

                document.getElementById("logoutLink")
                    .addEventListener("click", function (event) {

                        event.preventDefault();

                        fetch(backendBaseUrl + "requestHandler.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                action: "logout"
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    window.location.reload();
                                }
                            });
                    });
            }

        } else {

            if (guestMenu) {
                guestMenu.style.display = "block";
            }

            if (userMenu) {
                userMenu.style.display = "none";
            }
        }
    });

const accountBtn = document.getElementById("accountBtn");
const accountDropdown = document.getElementById("accountDropdown");

if (accountBtn && accountDropdown) {

    accountBtn.addEventListener("click", function (e) {

        e.stopPropagation();

        accountDropdown.classList.toggle("active");
    });

    document.addEventListener("click", function () {

        accountDropdown.classList.remove("active");
    });
}