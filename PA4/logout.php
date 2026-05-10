<?php // Vimbai Chivunga u25136608 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logout - VIM Airlines</title>
</head>
<body>
<script>
    localStorage.removeItem("apikey");
    localStorage.removeItem("name");
    localStorage.removeItem("surname");
    localStorage.removeItem("email");
    localStorage.removeItem("type");
    window.location.href = "login.php";
</script>
</body>
</html>