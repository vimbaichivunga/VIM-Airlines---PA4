<?php
// Vimbai Chivunga u25136608
include 'header.php';
?>

    <div class="container">
        <h1>Create an Account</h1>

        <div id="error-msg" style="color:red; display:none;"></div>
        <div id="success-msg" style="color:green; display:none;"></div>

        <form id="signup-form" onsubmit="return false;">

            <div class="form-group">
                <label>Name</label>
                <input type="text" id="name" placeholder="Enter your name">
            </div>

            <div class="form-group">
                <label>Surname</label>
                <input type="text" id="surname" placeholder="Enter your surname">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="text" id="email" placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" id="password" placeholder="Enter your password">
            </div>

            <div class="form-group">
                <label>User Type</label>
                <select id="type">
                    <option value="">Select type…</option>
                    <option value="Passenger">Passenger</option>
                    <option value="ATC">ATC</option>
                </select>
            </div>

            <div class="form-group">
                <button type="button" onclick="submitSignup()">Register</button>
            </div>

        </form>

        <div id="apikey-display" style="display:none;">
            <h3>Your API Key:</h3>
            <p id="apikey-value"></p>
        </div>
    </div>

<script>
function submitSignup() {
    var name = document.getElementById('name').value.trim();
    var surname = document.getElementById('surname').value.trim();
    var email = document.getElementById('email').value.trim();
    var password = document.getElementById('password').value.trim();
    var type = document.getElementById('type').value;

    var errorDiv = document.getElementById('error-msg');
    var successDiv = document.getElementById('success-msg');
    errorDiv.style.display = 'none';
    successDiv.style.display = 'none';

    // Validate name
    if (name === '') {
        errorDiv.innerText = 'Name is required.';
        errorDiv.style.display = 'block';
        return;
    }

    // Validate surname
    if (surname === '') {
        errorDiv.innerText = 'Surname is required.';
        errorDiv.style.display = 'block';
        return;
    }

    // Validate email
    var emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
    if (!emailRegex.test(email)) {
        errorDiv.innerText = 'Please enter a valid email address.';
        errorDiv.style.display = 'block';
        return;
    }

    // Validate password
    var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/;
    if (!passwordRegex.test(password)) {
        errorDiv.innerText = 'Password must be at least 8 characters, contain uppercase and lowercase letters, a number, and a symbol.';
        errorDiv.style.display = 'block';
        return;
    }

    // Validate type
    if (type === '') {
        errorDiv.innerText = 'Please select a user type.';
        errorDiv.style.display = 'block';
        return;
    }

    // All valid — send to API
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/u25136608/api.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            var response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                successDiv.innerHTML = 'Account created successfully! <br><br><a href="login.php" style="display:inline-block; padding:10px 20px; background:#173161; color:white; text-decoration:none; border-radius:6px;">Go to Login</a>';
                successDiv.style.display = 'block';
                document.getElementById('signup-form').style.display = 'none';
        } else {
                errorDiv.innerText = response.data;
                errorDiv.style.display = 'block';
            }
        }
    };

    xhr.send(JSON.stringify({
        type: 'Register',
        name: name,
        surname: surname,
        email: email,
        password: password,
        user_type: type
    }));
}
</script>

</body>
</html>