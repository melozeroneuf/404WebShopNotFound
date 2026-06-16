// account.js — Account-Seite: lädt und speichert Nutzerdaten
// Kurze Kommentare über wichtigen Funktionen für schnelle Orientierung.
$(document).ready(function () {

    loadAccountData();

    $("#togglePasswordChange").on("click", function () {
        $("#passwordChangeBox").toggleClass("hidden");
    });

    // Lädt Account-Daten vom Backend und füllt das Formular
    function loadAccountData() {
        $.ajax({
            url: "../../backend/logic/requestHandler.php",
            method: "POST",
            contentType: "application/json",
            dataType: "json",

            data: JSON.stringify({
                action: "getAccountData"
            }),

            success: function (response) {
                if (!response.success) {
                    return;
                }

                const user = response.user;

                $("#username").val(user.username);
                $("#email").val(user.email);
                $("#firstname").val(user.firstname);
                $("#lastname").val(user.lastname);
                $("#address").val(user.address);
                $("#zip").val(user.zip);
                $("#city").val(user.city);
                $("#paymentInfo").val(user.payment_info);
            },

            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    }

    // Submit-Handler für das Account-Formular: sendet Änderungen an Backend
    $("#accountForm").on("submit", function (event) {
        event.preventDefault();

        $.ajax({
            url: "../../backend/logic/requestHandler.php",
            method: "POST",
            contentType: "application/json",
            dataType: "json",

            data: JSON.stringify({
                action: "updateAccountData",
                username: $("#username").val(),
                email: $("#email").val(),
                firstname: $("#firstname").val(),
                lastname: $("#lastname").val(),
                address: $("#address").val(),
                zip: $("#zip").val(),
                city: $("#city").val(),
                payment_info: $("#paymentInfo").val(),
                currentPassword: $("#currentPassword").val(),
                newPassword: $("#newPassword").val(),
                newPasswordRepeat: $("#newPasswordRepeat").val()
            }),

            success: function (response) {
                if (response.success) {
                    $("#accountMessage")
                        .removeClass("error")
                        .addClass("success")
                        .text("Änderungen erfolgreich gespeichert.");

                    $("#currentPassword").val("");
                    $("#newPassword").val("");
                    $("#newPasswordRepeat").val("");

                    setTimeout(function () {
                        window.location.href = "../index.html";
                    }, 2000);
                } else {
                    $("#accountMessage")
                        .removeClass("success")
                        .addClass("error")
                        .text(response.message);
                }
            },

            error: function (xhr) {
                console.log(xhr.responseText);
                alert("Fehler beim Speichern");
            }
        });
    });

});