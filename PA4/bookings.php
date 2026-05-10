<?php
// bookings.php — Bookings - VIM Airlines
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings - VIM Airlines</title>
    <link rel="stylesheet" href="css/booking.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <a href="https://wheatley.cs.up.ac.za/u25136608/index.php" style="color: white;" class="launch-btn">
        Launch Page
    </a>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">VIM Airlines</div>
        <ul>
            <li><a href="index.php">Book Flights</a></li>
            <li><a class="active" href="bookings.php">Bookings</a></li>
            <li><a href="planes.php">Planes</a></li>
            <li><a href="favourites.php">Favourites</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="login.php">Register</a></li>
        </ul>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <h1>Your Booked Flights</h1>

        <!-- Flight Card 1 -->
        <div class="flight-card">
            <div class="flight-header">
                <h2>NovaCraft Zephyr X9</h2>
                <span class="status confirmed">Confirmed</span>
            </div>
            <p><strong>Route:</strong> Johannesburg (JNB) → Dubai (DXB)</p>
            <p><strong>Distance:</strong> 6 400 km</p>
            <p><strong>Price:</strong> R 12 500</p>
            <p><strong>Estimated Flight Time:</strong> 8h 10min</p>
            <button class="cancel-btn">Cancel Flight</button>
        </div>

        <!-- Flight Card 2 -->
        <div class="flight-card">
            <div class="flight-header">
                <h2>StratoForge TitanWing 700</h2>
                <span class="status confirmed">Confirmed</span>
            </div>
            <p><strong>Route:</strong> Cape Town (CPT) → London (LHR)</p>
            <p><strong>Distance:</strong> 9 700 km</p>
            <p><strong>Price:</strong> R 15 900</p>
            <p><strong>Estimated Flight Time:</strong> 11h 30min</p>
            <button class="cancel-btn">Cancel Flight</button>
        </div>

        <!-- Flight Card 3 -->
        <div class="flight-card">
            <div class="flight-header">
                <h2>Aether V-300 SkyLark</h2>
                <span class="status confirmed">Confirmed</span>
            </div>
            <p><strong>Route:</strong> Durban (DUR) → Singapore (SIN)</p>
            <p><strong>Distance:</strong> 8 600 km</p>
            <p><strong>Price:</strong> R 14 200</p>
            <p><strong>Estimated Flight Time:</strong> 10h 45min</p>
            <button class="cancel-btn">Cancel Flight</button>
        </div>

    </div>

</body>
</html>