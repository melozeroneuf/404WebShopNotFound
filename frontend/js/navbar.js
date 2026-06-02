const backendBaseUrl = window.location.pathname.includes("/frontend/sites/")
    ? "../../backend/logic/"
    : "../backend/logic/";

fetch(backendBaseUrl + "checkLogin.php")
    .then(response => response.json())
    .then(data => {

        if (data.loggedIn) {

            const loginLink = document.getElementById("loginLink");

            if (loginLink) {

                loginLink.outerHTML = `
                    <a href="#" id="logoutLink">Logout</a>
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
        }
    });