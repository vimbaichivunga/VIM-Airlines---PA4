<?php
// Vimbai Chivunga u25136608
include 'header.php';
?>

<link rel="stylesheet" href="css/book.css">

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
                <option disabled selected>Select an airport...</option>
            </select>
        </div>

        <!-- Arrival Airport -->
        <div class="form-group">
            <label>Arrival Airport 🛬</label>
            <input type="text" id="arrivalSearch" placeholder="Search by name, city, country or code…" autocomplete="off" oninput="searchAirports('arrivalDropdown', this.value)">
            <select id="arrivalDropdown" onchange="fillSearch('arrivalSearch', this)">
                <option disabled selected>Select an airport...</option>
            </select>
        </div>

        <!-- Departure Date -->
        <div class="form-group">
            <label>Departure Date</label>
            <input type="date" id="departureDate">
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
            <button type="button" class="btn" onclick="searchFlights()">Book Flights</button>
        </div>

    </form>

    <!-- Results -->
    <div id="flight-results" style="margin-top:20px;"></div>
</div>

<script src="js/script.js"></script>
<script>
window.onload = function () {
    initBookFlightsPage();
    var today = new Date().toISOString().split("T")[0];
    document.getElementById("departureDate").min = today;
    document.getElementById("returnDate").min    = today;
};
</script>
</body>
</html>