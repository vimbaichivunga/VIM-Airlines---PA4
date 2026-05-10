<?php
// Vimbai Chivunga u25136608
include 'header.php';
?>

<link rel="stylesheet" href="css/booking.css">

<div class="container">
    <h1>Your Booked Flights</h1>

    <!-- Not logged in message -->
    <div id="login-msg" style="display:none; text-align:center; padding:20px; color:#555;">
        <p>Please <a href="login.php" style="color:#173161; font-weight:bold;">log in</a> to view your bookings.</p>
    </div>

    <!-- Bookings container -->
    <div id="bookings-container"></div>
</div>

<script src="js/script.js"></script>
<script>
window.onload = function () {
    var apikey = localStorage.getItem("apikey");
    if (!apikey) {
        document.getElementById("login-msg").style.display = "block";
    } else {
        loadBookings();
    }
};
</script>
</body>
</html>