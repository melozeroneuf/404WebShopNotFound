console.log("auth.js wurde geladen");

$(document).ready(function () {

    console.log("jQuery läuft");

    $("#registerForm").on("submit", function (event) {
        event.preventDefault();

        console.log("Register Submit wurde abgefangen");

        $.ajax({
            url: "../../backend/logic/requestHandler.php",
            method: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({
                action: "register",
                username: $("#regUsername").val(),
                email: $("#regEmail").val(),
                password: $("#regPassword").val()
            }),
            success: function (response) {
                console.log("Backend Antwort:", response);
                alert(response.message);

                if (response.success) {
                    window.location.href = "../index.html";
                }
            },
            error: function (xhr) {
                console.log("Fehler vom Backend:");
                console.log(xhr.responseText);
                alert("Fehler bei der Registrierung");
            }
        });
    });

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
                remember: $("rememberMe").is(":checked")
            }),

            success: function (response) {

                alert(response.message);

                if(response.success){

                    window.location.href = "../index.html";
                }
            },

            error: function (xhr) {

                console.log(xhr.responseText);
                alert("Fehler beim Login");
            }
        });
    });
});


