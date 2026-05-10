/*
  Name: Vimbai Chivunga
  Student Number: u25136608
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
    var min   = parseInt(document.getElementById("calcMin").value) || 0;
    var max   = parseInt(document.getElementById("calcMax").value) || 99999;
    var count = allPlanes.filter(function (p) { return p.seats >= min && p.seats <= max; }).length;
    document.getElementById("calc-result").textContent = count + " plane(s) match.";
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
    if (!apikey) { container.innerHTML = "<p>Please <a href='login.php'>log in</a> to view favourites.</p>"; return; }
    myApiRequest({ type: "GetFavourites" }, function (data) { displayPlanes(data || []); });
}

function removeFavourite(planeId) {
    myApiRequest({ type: "RemoveFavourite", plane_id: planeId }, function () {
        loadFavourites();
    }, function (res) { alert(res.data || "Could not remove."); });
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
    if (!query.trim()) return;
    var q = query.toLowerCase();
    var matches = allPlanes.filter(function (p) {
        return (p.manufacturer && p.manufacturer.toLowerCase().includes(q)) ||
               (p.model && p.model.toLowerCase().includes(q));
    });
    matches.slice(0, 8).forEach(function (p) {
        var li = document.createElement("li");
        li.textContent = p.manufacturer + " " + p.model + " (" + p.seats + " seats)";
        li.onclick = function () {
            document.getElementById("planeSearch").value = li.textContent;
            document.getElementById("selectedPlaneId").value = p.id;
            list.innerHTML = "";
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