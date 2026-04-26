fetch("../backend/test/dbtest.php")
    .then(response => response.json())
    .then(data => {
        document.getElementById("dbStatus").innerText = data.message;
    })
    .catch(error => {
        document.getElementById("dbStatus").innerText = "DB Fehler";
        console.error(error);
    });


document.getElementById("jsonTestBtn").addEventListener("click", function () {
    fetch("../backend/logic/requestHandler.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "test",
            test: "Hallo Backend"
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById("jsonStatus").innerText = data.message;
        console.log(data);
    })
    .catch(error => {
        document.getElementById("jsonStatus").innerText = "JSON Fehler";
        console.error(error);
    });
});