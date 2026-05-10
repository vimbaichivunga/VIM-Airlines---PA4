<?php
// Vimbai Chivunga u25136608

define('DB_HOST', 'localhost');
define('DB_USER', 'u25136608');
define('DB_PASS', 'A2BQES7NWEJ5RE4COL6N73H4IPRT3M72');
define('DB_NAME', 'u25136608_pa3');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    http_response_code(500);
    echo json_encode([
        "status"    => "error",
        "timestamp" => round(microtime(true) * 1000),
        "data"      => "Database connection failed: " . mysqli_connect_error()
    ]);
    exit();
}
?>