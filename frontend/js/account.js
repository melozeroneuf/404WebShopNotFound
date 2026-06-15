$(document).ready(function () {

    loadAccountData();

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

    $("#accountForm").on("submit", function (event) {
        event.preventDefault();

        const currentPassword = prompt("Bitte gib dein aktuelles Passwort ein:");

        if (currentPassword === null || currentPassword.trim() === "") {
            alert("Änderungen wurden nicht gespeichert.");
            return;
        }

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
                currentPassword: currentPassword
            }),

            success: function (response) {
                alert(response.message);

                if (response.success) {
                    loadAccountData();
                }
            },

            error: function (xhr) {
                console.log(xhr.responseText);
                alert("Fehler beim Speichern");
            }
        });
    });

});