<?php
// index.php — Book Flights - VIM Airlines
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Flights - VIM Airlines</title>
    <link rel="stylesheet" href="css/book.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/main.css">
</head>

<body onload="initBookFlightsPage()">

    <!-- Launch Page Button -->
    <a href="https://wheatley.cs.up.ac.za/u25136608/index.php" class="launch-btn" style="color:white;">
        Launch Page
    </a>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">VIM Airlines</div>
        <ul>
            <li><a class="active" href="index.php">Book Flights</a></li>
            <li><a href="bookings.php">Bookings</a></li>
            <li><a href="planes.php">Planes</a></li>
            <li><a href="favourites.php">Favourites</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="signup.php">Register</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Book Your Flight</h1>

        <form onsubmit="return false;">

            <!-- Plane Type -->
            <div class="form-group">
                <label>Plane Type</label>
                <input type="text" id="planeSearch" placeholder="Type to search for a plane…" autocomplete="off" oninput="onPlaneSearchInput(this.value)">
                <input type="hidden" id="selectedPlaneId">
                <ul id="plane-suggestions"></ul>
            </div>

            <!-- Departure Airport -->
            <div class="form-group">
                <label>Departure Airport 🛫</label>
                <input type="text" id="departureSearch" placeholder="Search by name, city, country or code…" autocomplete="off" oninput="searchAirports('departureDropdown', this.value)">
                <select id="departureDropdown" onchange="fillSearch('departureSearch', this)">
                    <option disabled selected>Loading airports…</option>
                </select>
            </div>

            <!-- Arrival Airport -->
            <div class="form-group">
                <label>Arrival Airport 🛬</label>
                <input type="text" id="arrivalSearch" placeholder="Search by name, city, country or code…" autocomplete="off" oninput="searchAirports('arrivalDropdown', this.value)">
                <select id="arrivalDropdown" onchange="fillSearch('arrivalSearch', this)">
                    <option disabled selected>Loading airports…</option>
                </select>
            </div>

            <!-- Departure Date -->
            <div class="form-group">
                <label>Departure Date</label>
                <input type="date" id="departureDate">
            </div>

            <!-- Arrival Date -->
            <div class="form-group">
                <label>Arrival Date</label>
                <input type="date" id="arrivalDate">
            </div>

            <!-- Return Toggle -->
            <div class="form-group toggle-group">
                <label>Return Flight</label>
                <label class="switch">
                    <input type="checkbox" id="returnToggle"
                           onchange="document.getElementById('returnDateGroup').style.display =
                               this.checked ? 'flex' : 'none'">
                    <span class="slider"></span>
                </label>
            </div>

            <!-- Return Date -->
            <div class="form-group" id="returnDateGroup" style="display:none;">
                <label>Return Date</label>
                <input type="date" id="returnDate">
            </div>

            <!-- Passengers -->
            <div class="form-group">
                <label>Number of Passengers</label>
                <input type="number" id="passengers" min="1" max="10" value="1">
            </div>

            <!-- Cabin Class -->
            <div class="form-group">
                <label>Cabin Class</label>
                <select id="cabin">
                    <option disabled selected>Select a plane first…</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="form-group">
                <button type="submit" class="btn">Search Flights</button>
            </div>

        </form>
    </div>

    <script src="js/script.js"></script>
</body>
</html>