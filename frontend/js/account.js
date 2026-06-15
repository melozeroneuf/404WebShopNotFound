$(document).ready(function () {

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

            $("#address").val(
                (user.address ?? "") +
                ", " +
                (user.zip ?? "") +
                " " +
                (user.city ?? "")
            );
        },

        error: function (xhr) {
            console.log(xhr.responseText);
        }
    });

});