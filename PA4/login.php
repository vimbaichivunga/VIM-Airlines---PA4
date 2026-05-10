<?php // Vimbai Chivunga u25136608 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VIM Airlines</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            background: linear-gradient(to right, #173161, #2a5298);
            display: flex;
            flex-direction: column;
        }
        nav {
            background: #0f1f3d;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo { color: white; font-size: 1.4rem; font-weight: bold; }
        nav ul { list-style: none; display: flex; gap: 25px; }
        nav ul li a { color: white; text-decoration: none; font-size: 0.95rem; }
        nav ul li a:hover, nav ul li a.active { text-decoration: underline; }
        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        .card h1 {
            text-align: center;
            color: #173161;
            margin-bottom: 8px;
            font-size: 1.8rem;
        }
        .card p.subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.95rem;
            outline: none;
            transition: border 0.2s;
        }
        .form-group input:focus { border-color: #2a5298; }
        .btn {
            width: 100%;
            padding: 12px;
            background: #173161;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.2s;
        }
        .btn:hover { background: #2a5298; }
        .msg { text-align: center; margin-top: 14px; font-size: 0.9rem; }
        .msg a { color: #2a5298; text-decoration: none; }
        .msg a:hover { text-decoration: underline; }
        #error-msg {
            background: #ffe0e0;
            color: #c00;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 15px;
            display: none;
            font-size: 0.9rem;
        }
        #success-msg {
            background: #e0ffe0;
            color: #060;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 15px;
            display: none;
            font-size: 0.9rem;
        }
        .readme-link {
            text-align: center;
            margin-top: 15px;
            font-size: 0.85rem;
        }
        .readme-link a { color: #2a5298; text-decoration: none; }
        .readme-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<nav>
    <div class="logo">VIM Airlines</div>
    <ul>
        <li><a href="index.php">Book Flights</a></li>
        <li><a href="bookings.php">Bookings</a></li>
        <li><a href="planes.php">Planes</a></li>
        <li><a href="favourites.php">Favourites</a></li>
        <li><a class="active" href="login.php">Login</a></li>
        <li id="nav-register"><a href="signup.php">Register</a></li>
        <li id="nav-logout" style="display:none;"><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="container">
    <div class="card">
        <h1>Welcome Back</h1>
        <p class="subtitle">Login to your VIM Airlines account</p>

        <div id="error-msg"></div>
        <div id="success-msg"></div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" id="email" placeholder="tony@starkindustries.com">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" id="password" placeholder="••••••••">
        </div>

        <button class="btn" onclick="doLogin()">Login</button>

        <div class="msg">
            Don't have an account? <a href="signup.php">Register here</a>
        </div>

        <div class="readme-link">
            <a href="readme.txt" target="_blank">📄 ReadMe / Help</a>
        </div>
    </div>
</div>

<script>
var API_URL = "https://wheatley.cs.up.ac.za/u25136608/api.php";

// Check if already logged in
window.onload = function () {
    var apikey = localStorage.getItem("apikey");
    if (apikey) {
        document.getElementById("nav-logout").style.display = "block";
        document.getElementById("nav-register").style.display = "none";
    }
};

function doLogin() {
    var email    = document.getElementById("email").value.trim();
    var password = document.getElementById("password").value;
    var errEl    = document.getElementById("error-msg");
    var sucEl    = document.getElementById("success-msg");

    errEl.style.display = "none";
    sucEl.style.display = "none";

    if (!email || !password) {
        errEl.textContent    = "Please enter your email and password.";
        errEl.style.display  = "block";
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", API_URL, true);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function () {
        try {
            var res = JSON.parse(xhr.responseText);
            if (res.status === "success") {
                // Store apikey and user info in localStorage
                localStorage.setItem("apikey",  res.data.apikey);
                localStorage.setItem("name",    res.data.name);
                localStorage.setItem("surname", res.data.surname);
                localStorage.setItem("email",   res.data.email);
                localStorage.setItem("type",    res.data.type);

                sucEl.textContent   = "Login successful! Redirecting…";
                sucEl.style.display = "block";

                setTimeout(function () {
                    window.location.href = "index.php";
                }, 1000);
            } else {
                errEl.textContent   = res.data || "Login failed.";
                errEl.style.display = "block";
            }
        } catch (e) {
            errEl.textContent   = "Unexpected error. Please try again.";
            errEl.style.display = "block";
        }
    };

    xhr.onerror = function () {
        errEl.textContent   = "Network error. Please try again.";
        errEl.style.display = "block";
    };

    xhr.send(JSON.stringify({ type: "Login", email: email, password: password }));
}

// Allow pressing Enter to login
document.addEventListener("keydown", function (e) {
    if (e.key === "Enter") doLogin();
});
</script>

</body>
</html>