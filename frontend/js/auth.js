console.log("auth.js wurde geladen");

$(document).ready(function () {

    console.log("jQuery läuft");

    $("#registerForm").on("submit", function (event) {
        event.preventDefault();

        console.log("Register Submit wurde abgefangen");

        const password = $("#regPassword").val();
        const passwordRepeat = $("#regPasswordRepeat").val();

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
            url: "http://localhost:3000/logic/requestHandler.php",
            method: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({
                action: "register",
                username: $("#regUsername").val(),
                email: $("#regEmail").val(),
                password: password,
                passwordRepeat: passwordRepeat
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

    $("#loginForm").on("submit", function (event) {

        event.preventDefault();

        $.ajax({

            url: "http://localhost:3000/logic/requestHandler.php",
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