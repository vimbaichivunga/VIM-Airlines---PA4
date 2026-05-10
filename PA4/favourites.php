<?php
// Vimbai Chivunga u25136608
include 'header.php';
?>

<link rel="stylesheet" href="css/favourites.css">

<div class="container">
    <h1>Your Favourite Planes</h1>
    <p class="subtitle">Planes you've saved</p>

    <!-- Not logged in message -->
    <div id="login-msg" style="display:none; text-align:center; padding:20px; color:#555;">
        <p>Please <a href="login.php" style="color:#173161; font-weight:bold;">log in</a> to view your favourites.</p>
    </div>

    <!-- Favourites Grid -->
    <div class="favourites-grid" id="planes-container"></div>

    <!-- Action buttons -->
    <div class="action-buttons" style="margin-top:20px;">
        <a href="planes.php" class="btn">Browse More Planes</a>
        <button class="btn btn-outline" onclick="clearAllFavourites()">Clear All Favourites</button>
    </div>
</div>

<script src="js/script.js"></script>
<script>
window.onload = function () {
    var apikey = localStorage.getItem("apikey");
    if (!apikey) {
        document.getElementById("login-msg").style.display      = "block";
        document.getElementById("planes-container").style.display = "none";
    } else {
        loadFavourites();
    }
};

function clearAllFavourites() {
    if (!confirm("Remove all favourites?")) return;
    myApiRequest({ type: "ClearFavourites" }, function () {
        loadFavourites();
    }, function () {
        alert("Could not clear favourites.");
    });
}
</script>
</body>
</html>