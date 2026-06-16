// auth.js — Registrierung und Login (Frontend-Validierung + AJAX)
console.log("auth.js wurde geladen");

$(document).ready(function () {

    console.log("jQuery läuft");

    // Registrierungs-Handler: Validiert Eingaben und sendet an Backend
    $("#registerForm").on("submit", function (event) {
        event.preventDefault();

        console.log("Register Submit wurde abgefangen");

        const username = $("#regUsername").val().trim();
        const firstname = $("#firstname").val().trim();
        const lastname = $("#lastname").val().trim();
        const address = $("#address").val().trim();
        const zip = $("#zip").val().trim();
        const city = $("#city").val().trim();

        const password = $("#regPassword").val();
        const passwordRepeat = $("#regPasswordRepeat").val();

        if (
            firstname === "" ||
            lastname === "" ||
            address === "" ||
            zip === "" ||
            city === ""
        ) {
            $("#registerMessage")
                .removeClass("success")
                .addClass("error")
                .text("Bitte alle Pflichtfelder ausfüllen")
                .fadeIn();

            return;
        }

        if (username.length < 3) {
            $("#registerMessage")
                .removeClass("success")
                .addClass("error")
                .text("Der Benutzername muss mindestens 3 Zeichen lang sein")
                .fadeIn();

            return;
        }

        if (!/^\d{4}$/.test(zip)) {
            $("#registerMessage")
                .removeClass("success")
                .addClass("error")
                .text("Bitte eine gültige PLZ eingeben")
                .fadeIn();

            return;
        }
        if (password !== passwordRepeat) {
            $("#registerMessage")
                .removeClass("success")
                .addClass("error")
                .text("Die Passwörter stimmen nicht überein")
                .fadeIn();

            return;
        }

        if (password.length < 8) {
            $("#registerMessage")
                .removeClass("success")
                .addClass("error")
                .text("Das Passwort muss mindestens 8 Zeichen lang sein")
                .fadeIn();

            return;
        }

        $.ajax({
            url: "../../backend/logic/requestHandler.php",
            method: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({
                action: "register",

                salutation: $("#salutation").val(),
                firstname: $("#firstname").val(),
                lastname: $("#lastname").val(),
                address: $("#address").val(),
                zip: $("#zip").val(),
                city: $("#city").val(),

                username: $("#regUsername").val(),
                email: $("#regEmail").val(),
                password: password,
                passwordRepeat: passwordRepeat,

                payment_info: $("#paymentInfo").val()
            }),
            success: function (response) {
                console.log("Backend Antwort:", response);

                const messageBox = $("#registerMessage");

                messageBox
                    .removeClass("success error")
                    .addClass(response.success ? "success" : "error")
                    .text(response.message)
                    .fadeIn();

                if (response.success) {
                    setTimeout(() => {
                        window.location.href = "../index.html";
                    }, 1200);
                }
            },
            error: function (xhr) {
                console.log("Fehler vom Backend:");
                console.log(xhr.responseText);

                const messageBox = $("#registerMessage");

                messageBox
                    .removeClass("success")
                    .addClass("error")
                    .text("Fehler bei der Registrierung")
                    .fadeIn();
            }
        });
    });

    // Login-Handler: sendet Anmelde-Daten an Backend und zeigt Ergebnis
    $("#loginForm").on("submit", function (event) {

        event.preventDefault();

        $.ajax({

            url: "../../backend/logic/requestHandler.php",
            method: "POST",
            contentType: "application/json",
            dataType: "json",

            data: JSON.stringify({
                action: "login",
                login: $("#login").val(),
                password: $("#password").val(),
                remember: $("#rememberMe").is(":checked")
            }),

            success: function (response) {

                const messageBox = $("#loginMessage");

                messageBox
                    .removeClass("success error")
                    .addClass(response.success ? "success" : "error")
                    .text(response.message)
                    .fadeIn();

                if (response.success) {

                    setTimeout(() => {
                        window.location.href = "../index.html";
                    }, 1200);
                }
            },

            error: function (xhr) {

                console.log(xhr.responseText);

                const messageBox = $("#loginMessage");

                messageBox
                    .removeClass("success")
                    .addClass("error")
                    .text("Fehler beim Login")
                    .fadeIn();
            }
        });
    });
});