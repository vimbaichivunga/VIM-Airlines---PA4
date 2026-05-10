/*
  Name: Vimbai Chivunga
  Student Number: u25136608
  PA4 script.js
*/

var MY_API_URL = "https://wheatley.cs.up.ac.za/u25136608/api.php";
var allPlanes   = [];
var allAirports = [];

function myApiRequest(body, callback, errorCallback) {
    var apikey = localStorage.getItem("apikey") || "";
    if (apikey) body.apikey = apikey;
    var xhr = new XMLHttpRequest();
    xhr.open("POST", MY_API_URL, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onload = function () {
        try {
            var response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                callback(response.data);
            } else {
                console.error("API error:", response.data);
                if (errorCallback) errorCallback(response);
            }
        } catch (e) { console.error("Parse error:", e); }
    };
    xhr.onerror = function () { console.error("Network error"); };
    xhr.send(JSON.stringify(body));
}

function loadPlanes() {
    myApiRequest({ type: "GetAllPlanes", limit: 80, sort: "id", order: "asc" }, function (data) {
        allPlanes = data || [];
        displayPlanes(allPlanes);
        buildBreakdown();
    });
}

function displayPlanes(planes) {
    var container = document.getElementById("planes-container");
    if (!container) return;
    container.innerHTML = "";
    if (!planes || !planes.length) { container.innerHTML = "<p>No planes found.</p>"; return; }
    planes.forEach(function (plane) {
        var card = document.createElement("div");
        card.className = "plane-card";
        card.innerHTML =
            "<img src='" + (plane.image_url || "img/placeholder.png") + "' class='plane-img' onerror=\"this.src='img/placeholder.png'\">" +
            "<h3>" + plane.model + "</h3>" +
            "<p><strong>Manufacturer:</strong> " + plane.manufacturer + "</p>" +
            "<p><strong>Seats:</strong> " + plane.seats + "</p>" +
            "<p><strong>Classes:</strong> " + (plane.classes || "N/A") + "</p>" +
            "<div class='btn-group'>" +
                "<button class='btn' onclick='viewDetails(" + plane.id + ")'>View Details</button>" +
                "<button class='btn btn-outline' onclick='addToFavourites(" + plane.id + ")'>❤ Save</button>" +
            "</div>";
        container.appendChild(card);
    });
}

function searchPlanes() {
    var query = document.getElementById("search").value.trim();
    myApiRequest({ type: "GetAllPlanes", search: query, limit: 80 }, function (data) {
        allPlanes = data || [];
        displayPlanes(allPlanes);
    });
}

function sortPlanes() {
    var sortVal = document.getElementById("sort-select").value;
    var map = {
        name_asc:  { sort: "manufacturer", order: "asc"  },
        name_desc: { sort: "manufacturer", order: "desc" },
        seats_asc: { sort: "seats",        order: "asc"  },
        seats_desc:{ sort: "seats",        order: "desc" }
    };
    var chosen = map[sortVal] || { sort: "id", order: "asc" };
    myApiRequest({ type: "GetAllPlanes", limit: 80, sort: chosen.sort, order: chosen.order }, function (data) {
        allPlanes = data || [];
        displayPlanes(allPlanes);
    });
}

function filterPlanes() {
    var body = { type: "GetAllPlanes", limit: 80 };
    var min = document.getElementById("minSeats").value;
    var max = document.getElementById("maxSeats").value;
    if (min) body.min_seats = parseInt(min);
    if (max) body.max_seats = parseInt(max);
    myApiRequest(body, function (data) { allPlanes = data || []; displayPlanes(allPlanes); });
}

function resetFilters() {
    document.getElementById("minSeats").value    = "";
    document.getElementById("maxSeats").value    = "";
    document.getElementById("search").value      = "";
    document.getElementById("sort-select").value = "";
    loadPlanes();
}

function calculateSeats() {
    var min     = parseInt(document.getElementById("calcMin").value) || 0;
    var max     = parseInt(document.getElementById("calcMax").value) || 99999;
    var matches = allPlanes.filter(function (p) {
        return p.seats >= min && p.seats <= max;
    });
    document.getElementById("calc-result").textContent =
        matches.length + " plane(s) match.";
    displayPlanes(matches);
}

function buildBreakdown() {
    var grid = document.getElementById("calc-breakdown-grid");
    if (!grid) return;
    var ranges = [
        { label: "Small (<100)",      min: 0,   max: 99    },
        { label: "Medium (100-199)",  min: 100, max: 199   },
        { label: "Large (200-299)",   min: 200, max: 299   },
        { label: "Very Large (300+)", min: 300, max: 99999 }
    ];
    grid.innerHTML = "";
    ranges.forEach(function (r) {
        var count = allPlanes.filter(function (p) { return p.seats >= r.min && p.seats <= r.max; }).length;
        var box = document.createElement("div");
        box.className = "breakdown-box";
        box.innerHTML = "<strong>" + count + "</strong><br>" + r.label;
        grid.appendChild(box);
    });
}

function addToFavourites(planeId) {
    var apikey = localStorage.getItem("apikey");
    if (!apikey) { alert("Please log in to save favourites."); return; }
    myApiRequest({ type: "AddFavourite", plane_id: planeId }, function () {
        alert("Added to favourites!");
    }, function (res) { alert(res.data || "Could not add to favourites."); });
}

function loadFavourites() {
    var container = document.getElementById("planes-container");
    if (!container) return;

    var apikey = localStorage.getItem("apikey");
    if (!apikey) {
        container.innerHTML = "<p>Please <a href='login.php'>log in</a> to view favourites.</p>";
        return;
    }

    myApiRequest({ type: "GetFavourites" }, function (data) {
        displayFavourites(data || []);
    });
}

function displayFavourites(planes) {
    var container = document.getElementById("planes-container");
    if (!container) return;

    container.innerHTML = "";

    if (!planes || !planes.length) {
        container.innerHTML = "<p style='text-align:center; color:#555; padding:20px;'>No favourite planes yet. <a href='planes.php'>Browse planes</a></p>";
        return;
    }

    planes.forEach(function (plane) {
        var card = document.createElement("div");
        card.className = "plane-card";
        card.innerHTML =
            "<img src='" + (plane.image_url || "img/placeholder.png") + "' class='plane-img' onerror=\"this.src='img/placeholder.png'\">" +
            "<h3>" + plane.model + "</h3>" +
            "<p><strong>Manufacturer:</strong> " + plane.manufacturer + "</p>" +
            "<p><strong>Seats:</strong> " + plane.seats + "</p>" +
            "<p><strong>Classes:</strong> " + (plane.classes || "N/A") + "</p>" +
            "<div class='btn-group'>" +
                "<button class='btn' onclick='viewDetails(" + plane.id + ")'>View Details</button>" +
                "<button class='btn btn-outline' onclick='removeFavouriteAndRefresh(" + plane.id + ")'>🗑 Remove</button>" +
            "</div>";
        container.appendChild(card);
    });
}

function removeFavouriteAndRefresh(planeId) {
    myApiRequest({ type: "RemoveFavourite", plane_id: planeId }, function () {
        loadFavourites();
    }, function (res) {
        alert(res.data || "Could not remove.");
    });
}

function viewDetails(id) { window.location.href = "view.php?id=" + id; }

function initBookFlightsPage() {
    var apikey = localStorage.getItem("apikey");
    if (!apikey) {
        setDropdownMessage("departureDropdown", "Please log in to load airports");
        setDropdownMessage("arrivalDropdown",   "Please log in to load airports");
        return;
    }
    loadAirportsForBooking();
    loadPlanesForSearch();
}

function loadAirportsForBooking() {
    myApiRequest({ type: "GetAllAirports", limit: 100, page: 1 }, function (airports) {
        allAirports = airports || [];
        populateAirportDropdown("departureDropdown", allAirports);
        populateAirportDropdown("arrivalDropdown",   allAirports);
    });
}

function populateAirportDropdown(dropdownId, airports) {
    var select = document.getElementById(dropdownId);
    if (!select) return;
    select.innerHTML = "<option value='' disabled selected>Select an airport...</option>";
    airports.forEach(function (a) {
        var opt = document.createElement("option");
        opt.value = a.code;
        opt.textContent = a.name + " (" + a.code + ") - " + a.city + ", " + a.country;
        select.appendChild(opt);
    });
}

function setDropdownMessage(dropdownId, msg) {
    var select = document.getElementById(dropdownId);
    if (!select) return;
    select.innerHTML = "<option disabled selected>" + msg + "</option>";
}

function loadPlanesForSearch() {
    myApiRequest({ type: "GetAllPlanes", limit: 80, sort: "id", order: "asc" }, function (data) {
        allPlanes = data || [];
    });
}

function onPlaneSearchInput(query) {
    var list = document.getElementById("plane-suggestions");
    if (!list) return;
    list.innerHTML = "";
    list.style.display = "none";
    if (!query.trim()) return;
    var q = query.toLowerCase();
    var matches = allPlanes.filter(function (p) {
        return (p.manufacturer && p.manufacturer.toLowerCase().includes(q)) ||
               (p.model && p.model.toLowerCase().includes(q));
    });
    if (!matches.length) return;
    list.style.display = "block";
    matches.slice(0, 8).forEach(function (p) {
        var li = document.createElement("li");
        li.textContent = p.manufacturer + " " + p.model + " (" + p.seats + " seats)";
        li.onclick = function () {
            document.getElementById("planeSearch").value     = li.textContent;
            document.getElementById("selectedPlaneId").value = p.id;
            list.style.display = "none";
            list.innerHTML     = "";
            loadCabinClasses(p);
        };
        list.appendChild(li);
    });
}

function loadCabinClasses(plane) {
    var select = document.getElementById("cabin");
    if (!select) return;
    select.innerHTML = "";
    var classes = plane.classes ? plane.classes.split(",") : ["Economy"];
    classes.forEach(function (c) {
        var opt = document.createElement("option");
        opt.value = c.trim(); opt.textContent = c.trim();
        select.appendChild(opt);
    });
}

function searchAirports(dropdownId, query) {
    query = query.toLowerCase().trim();
    var filtered = allAirports.filter(function (a) {
        return a.name.toLowerCase().includes(query) || a.city.toLowerCase().includes(query) ||
               a.country.toLowerCase().includes(query) || a.code.toLowerCase().includes(query);
    });
    populateAirportDropdown(dropdownId, filtered);
}

function fillSearch(inputId, selectEl) {
    var input = document.getElementById(inputId);
    if (input) input.value = selectEl.options[selectEl.selectedIndex].text;
}

// ============================================================
// SEARCH FLIGHTS & BOOK
// ============================================================
var pendingBookingData = null;

function searchFlights() {
    var planeId    = document.getElementById("selectedPlaneId").value;
    var depCode    = document.getElementById("departureDropdown").value;
    var arrCode    = document.getElementById("arrivalDropdown").value;
    var depDate    = document.getElementById("departureDate").value;
    var passengers = document.getElementById("passengers").value;
    var isReturn   = document.getElementById("returnToggle").checked;
    var retDate    = isReturn ? document.getElementById("returnDate").value : null;
    var resultsDiv = document.getElementById("flight-results");

    if (!planeId || !depCode || !arrCode || !depDate || !passengers) {
        resultsDiv.innerHTML = "<p style='color:red;'>Please fill in all fields and select a plane.</p>";
        return;
    }
    if (depCode === arrCode) {
        resultsDiv.innerHTML = "<p style='color:red;'>Departure and arrival airports cannot be the same.</p>";
        return;
    }
    if (isReturn && !retDate) {
        resultsDiv.innerHTML = "<p style='color:red;'>Please select a return date.</p>";
        return;
    }

    resultsDiv.innerHTML = "<p>Searching...</p>";

    // Store booking data for confirmation
    pendingBookingData = {
        type:              "BookFlight",
        plane_id:          parseInt(planeId),
        departure_airport: depCode,
        arrival_airport:   arrCode,
        departure_date:    depDate,
        passengers:        parseInt(passengers),
        is_return:         isReturn,
        return_date:       retDate
    };

    // First calculate distance/time by doing a preview request
    // We show details and ask for confirmation
    var planeName = document.getElementById("planeSearch").value;
    var hours     = "?";
    var dist      = "?";

    var html = "<div style='background:#f0f4ff; border:2px solid #173161; padding:20px; border-radius:10px; margin-top:15px;'>" +
        "<h3 style='color:#173161; margin-bottom:15px;'>✈ Flight Preview</h3>" +
        "<p><strong>Plane:</strong> " + planeName + "</p>" +
        "<p><strong>Route:</strong> " + depCode + " → " + arrCode + "</p>" +
        "<p><strong>Departure Date:</strong> " + depDate + "</p>" +
        (isReturn ? "<p><strong>Return Date:</strong> " + retDate + "</p>" : "") +
        "<p><strong>Passengers:</strong> " + passengers + "</p>" +
        "<br>" +
        "<p style='color:#555; font-size:0.9rem;'>Distance and flight time will be calculated on confirmation.</p>" +
        "<br>" +
        "<div style='display:flex; gap:10px;'>" +
            "<button onclick='confirmBooking()' style='flex:1; padding:12px; background:#173161; color:white; border:none; border-radius:6px; cursor:pointer; font-size:1rem;'>✅ Confirm Booking</button>" +
            "<button onclick='cancelPreview()' style='flex:1; padding:12px; background:#6b1a1a; color:white; border:none; border-radius:6px; cursor:pointer; font-size:1rem;'>❌ Cancel</button>" +
        "</div>" +
    "</div>";

    resultsDiv.innerHTML = html;
}

function confirmBooking() {
    var resultsDiv = document.getElementById("flight-results");
    if (!pendingBookingData) return;

    resultsDiv.innerHTML = "<p>Booking your flight...</p>";

    myApiRequest(pendingBookingData, function (data) {
        var html = "<div style='background:#e0ffe0; padding:20px; border-radius:10px; margin-top:10px; border:2px solid #1a6b3a;'>" +
            "<h3 style='color:#1a6b3a;'>✅ Flight Booked Successfully!</h3>" +
            "<br>" +
            "<p><strong>Distance:</strong> " + data.distance_km + " km</p>" +
            "<p><strong>Flight Time:</strong> " + Math.floor(data.flight_time_mins / 60) + "h " +
                Math.round(data.flight_time_mins % 60) + "min</p>" +
            "<p><strong>Booking ID(s):</strong> " + data.booking_ids.join(", ") + "</p>" +
            "<br>" +
            "<a href='bookings.php' style='display:inline-block; padding:10px 20px; background:#173161; color:white; text-decoration:none; border-radius:6px;'>View My Bookings →</a>" +
            "</div>";
        resultsDiv.innerHTML = html;
        pendingBookingData = null;
    }, function (res) {
        resultsDiv.innerHTML = "<p style='color:red;'>" + (res.data || "Booking failed. Please try again.") + "</p>";
    });
}

function cancelPreview() {
    document.getElementById("flight-results").innerHTML = "";
    pendingBookingData = null;
}

// ============================================================
// LOAD BOOKINGS
// ============================================================
function loadBookings() {
    var container = document.getElementById("bookings-container");
    if (!container) return;

    myApiRequest({ type: "GetBookings" }, function (bookings) {
        if (!bookings || !bookings.length) {
            container.innerHTML = "<p style='text-align:center; padding:20px; color:#555;'>No bookings yet. <a href='index.php'>Book a flight</a></p>";
            return;
        }

        var html = "";
        bookings.forEach(function (b) {
            var hours = Math.floor(b.flight_time / 60);
            var mins  = Math.round(b.flight_time % 60);
            html += "<div class='flight-card'>" +
                "<div class='flight-header'>" +
                    "<h2>" + b.manufacturer + " " + b.model + "</h2>" +
                    "<span class='status confirmed'>Confirmed</span>" +
                "</div>" +
                "<p><strong>Route:</strong> " + b.departure_airport + " → " + b.arrival_airport + "</p>" +
                "<p><strong>Date:</strong> " + b.departure_date + "</p>" +
                "<p><strong>Distance:</strong> " + parseFloat(b.distance).toFixed(0) + " km</p>" +
                "<p><strong>Flight Time:</strong> " + hours + "h " + mins + "min</p>" +
                "<p><strong>Passengers:</strong> " + b.passengers + "</p>" +
                "<button class='cancel-btn' onclick='cancelBooking(" + b.booking_id + ")'>Cancel Flight</button>" +
                "</div>";
        });
        container.innerHTML = html;
    }, function () {
        container.innerHTML = "<p style='color:red;'>Failed to load bookings.</p>";
    });
}

function cancelBooking(bookingId) {
    if (!confirm("Cancel this booking?")) return;
    myApiRequest({ type: "CancelBooking", booking_id: bookingId }, function () {
        loadBookings();
    }, function (res) {
        alert(res.data || "Could not cancel booking.");
    });
}