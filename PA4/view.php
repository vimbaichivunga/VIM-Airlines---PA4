<?php
// Vimbai Chivunga u25136608
include 'header.php';
?>

<link rel="stylesheet" href="css/view.css">

<div class="container view-container">
    <a href="planes.php" class="back-btn">← Back to Planes</a>

    <!-- Loader -->
    <div id="loader" style="display:none; text-align:center; padding:20px; color:#555;">
        Loading plane details...
    </div>

    <!-- Plane not found -->
    <div id="not-found" style="display:none; text-align:center; padding:20px; color:#c00;">
        Plane not found.
    </div>

    <!-- Main card -->
    <div class="view-card" id="view-card" style="display:none;">

        <!-- Image -->
        <div class="view-image">
            <img id="plane-img" src="img/placeholder.png" alt="Plane Image"
                 onerror="this.src='img/placeholder.png'">
        </div>

        <!-- Details -->
        <div class="view-details">
            <h1 id="plane-title"></h1>
            <p class="description" id="plane-desc"></p>

            <div class="spec-grid">
                <div class="spec-box">
                    <h4>Manufacturer</h4>
                    <p id="spec-manufacturer"></p>
                </div>
                <div class="spec-box">
                    <h4>Seats</h4>
                    <p id="spec-seats"></p>
                </div>
                <div class="spec-box">
                    <h4>Max Speed</h4>
                    <p id="spec-speed"></p>
                </div>
                <div class="spec-box">
                    <h4>Max Range</h4>
                    <p id="spec-range"></p>
                </div>
                <div class="spec-box">
                    <h4>Max Cargo</h4>
                    <p id="spec-cargo"></p>
                </div>
                <div class="spec-box">
                    <h4>Cabin Classes</h4>
                    <p id="spec-classes"></p>
                </div>
            </div>

            <button id="view-fav-btn" onclick="toggleFavourite()">❤ Save to Favourites</button>
        </div>
    </div>
</div>

<script src="js/script.js"></script>
<script>
var currentPlaneId = null;
var isFavourited   = false;

window.onload = function () {
    var params = new URLSearchParams(window.location.search);
    var id     = params.get("id");

    if (!id) {
        document.getElementById("not-found").style.display = "block";
        return;
    }

    var apikey = localStorage.getItem("apikey");
    if (!apikey) {
        document.getElementById("not-found").innerHTML =
            "Please <a href='login.php'>log in</a> to view plane details.";
        document.getElementById("not-found").style.display = "block";
        return;
    }

    document.getElementById("loader").style.display = "block";

    myApiRequest({ type: "GetAllPlanes", limit: 200 }, function (planes) {
        document.getElementById("loader").style.display = "none";

        var plane = null;
        for (var i = 0; i < planes.length; i++) {
            if (planes[i].id == id) { plane = planes[i]; break; }
        }

        if (!plane) {
            document.getElementById("not-found").style.display = "block";
            return;
        }

        currentPlaneId = plane.id;

        document.getElementById("plane-title").textContent        = plane.manufacturer + " " + plane.model;
        document.getElementById("plane-desc").textContent         = plane.description || "No description available.";
        document.getElementById("spec-manufacturer").textContent  = plane.manufacturer;
        document.getElementById("spec-seats").textContent         = plane.seats + " seats";
        document.getElementById("spec-speed").textContent         = plane.max_speed_kmh + " km/h";
        document.getElementById("spec-range").textContent         = plane.max_range_km + " km";
        document.getElementById("spec-cargo").textContent         = plane.max_cargo_kg + " kg";
        document.getElementById("spec-classes").textContent       = plane.classes || "Economy";

        if (plane.image_url) {
            document.getElementById("plane-img").src = plane.image_url;
        }

        document.getElementById("view-card").style.display = "grid";

        // Check if already favourited
        checkIfFavourited(plane.id);
    });
};

function checkIfFavourited(planeId) {
    myApiRequest({ type: "GetFavourites" }, function (favs) {
        for (var i = 0; i < favs.length; i++) {
            if (favs[i].id == planeId) {
                isFavourited = true;
                updateFavBtn();
                break;
            }
        }
    });
}

function toggleFavourite() {
    if (!currentPlaneId) return;
    if (isFavourited) {
        myApiRequest({ type: "RemoveFavourite", plane_id: currentPlaneId }, function () {
            isFavourited = false;
            updateFavBtn();
        });
    } else {
        myApiRequest({ type: "AddFavourite", plane_id: currentPlaneId }, function () {
            isFavourited = true;
            updateFavBtn();
        });
    }
}

function updateFavBtn() {
    var btn = document.getElementById("view-fav-btn");
    if (isFavourited) {
        btn.textContent = "💔 Remove from Favourites";
        btn.classList.add("fav-active");
    } else {
        btn.textContent = "❤ Save to Favourites";
        btn.classList.remove("fav-active");
    }
}
</script>
</body>
</html>