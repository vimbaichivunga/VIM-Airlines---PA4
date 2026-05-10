<?php
// Vimbai Chivunga u25136608
include 'header.php';
?>

<div class="container">
    <h1>Explore Our Fleet</h1>

    <!-- Top Controls -->
    <div class="top-controls">
        <input id="search" type="text" placeholder="Search by manufacturer…" class="search-bar">
        <button onclick="searchPlanes()">Search</button>

        <select id="sort-select" class="sort-dropdown" onchange="sortPlanes()">
            <option value="">Sort by…</option>
            <option value="name_asc">Name (A → Z)</option>
            <option value="name_desc">Name (Z → A)</option>
            <option value="seats_asc">Seats (Low → High)</option>
            <option value="seats_desc">Seats (High → Low)</option>
        </select>

        <input id="minSeats" type="number" placeholder="Min seats" style="width:120px;">
        <input id="maxSeats" type="number" placeholder="Max seats" style="width:120px;">
        <button onclick="filterPlanes()">Filter</button>
        <button onclick="resetFilters()">Reset</button>
    </div>

    <!-- Seat Calculator -->
    <div class="seat-calculator">
        <h2>Fleet Seat Calculator</h2>
        <p>Enter a seat range to see how many planes match:</p>
        <div class="calc-controls">
            <input type="number" id="calcMin" placeholder="Min seats">
            <span>to</span>
            <input type="number" id="calcMax" placeholder="Max seats">
            <button onclick="calculateSeats()">Calculate</button>
        </div>
        <div id="calc-result"></div>
        <div class="calc-breakdown">
            <h3>Fleet Breakdown</h3>
            <div id="calc-breakdown-grid"></div>
        </div>
    </div>

    <!-- Not logged in message -->
    <div id="login-msg" style="display:none; text-align:center; padding:20px; color:#555;">
        <p>Please <a href="login.php" style="color:#173161; font-weight:bold;">log in</a> to view planes.</p>
    </div>

    <!-- Planes Grid -->
    <div id="planes-container" class="plane-grid"></div>
</div>

<link rel="stylesheet" href="css/planes.css">
<script src="js/script.js"></script>
<script>
window.onload = function () {
    var apikey = localStorage.getItem("apikey");
    if (!apikey) {
        document.getElementById("login-msg").style.display = "block";
    } else {
        loadPlanes();
    }
};
</script>
</body>
</html>