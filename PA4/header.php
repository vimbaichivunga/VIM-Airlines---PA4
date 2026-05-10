<?php
// Vimbai Chivunga u25136608
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VIM Airlines</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">VIM Airlines</div>
    <ul>
        <li><a href="index.php">Book Flights</a></li>
        <li><a href="bookings.php">Bookings</a></li>
        <li><a href="planes.php">Planes</a></li>
        <li><a href="favourites.php">Favourites</a></li>
        <li id="nav-welcome" style="display:none;"></li>
        <li id="nav-login"  ><a href="login.php">Login</a></li>
        <li id="nav-register"><a href="signup.php">Register</a></li>
        <li id="nav-logout" style="display:none;"><a href="logout.php">Logout</a></li>
    </ul>
</nav>


<script>
// Show/hide nav items based on login state
(function () {
    var apikey = localStorage.getItem("apikey");
    var name   = localStorage.getItem("name");
    if (apikey) {
        document.getElementById("nav-login").style.display    = "none";
        document.getElementById("nav-register").style.display = "none";
        document.getElementById("nav-logout").style.display   = "block";
        var wel = document.getElementById("nav-welcome");
        wel.style.display = "block";
        wel.textContent   = "Hi, " + (name || "User");
    }
})();
</script>