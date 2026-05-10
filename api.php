<?php
// Vimbai Chivunga u25136608

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

error_reporting(E_ALL);
ini_set("display_errors", 0);

require_once __DIR__ . "/COS216/PA4/config.php";

function respond($code, $status, $data) {
    http_response_code($code);
    echo json_encode([
        "status"    => $status,
        "timestamp" => round(microtime(true) * 1000),
        "data"      => $data
    ]);
    exit();
}

function isValidApiKey($conn, $key) {
    $stmt = mysqli_prepare($conn, "SELECT id FROM Users WHERE api_key = ?");
    mysqli_stmt_bind_param($stmt, "s", $key);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $valid = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    return $valid;
}

function getUserId($conn, $apikey) {
    $stmt = mysqli_prepare($conn, "SELECT id FROM Users WHERE api_key = ?");
    mysqli_stmt_bind_param($stmt, "s", $apikey);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row    = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row ? $row["id"] : null;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    respond(405, "error", "Only POST requests allowed");
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["type"])) {
    respond(400, "error", "Post parameters are missing");
}

switch ($data["type"]) {
    case "Register":       handleRegister($conn, $data);       break;
    case "Login":          handleLogin($conn, $data);          break;
    case "GetAllPlanes":   handleGetAllPlanes($conn, $data);   break;
    case "GetAllAirports": handleGetAllAirports($conn, $data); break;
    case "AddFavourite":   handleAddFavourite($conn, $data);   break;
    case "GetFavourites":  handleGetFavourites($conn, $data);  break;
    case "RemoveFavourite":handleRemoveFavourite($conn, $data);break;
    case "ClearFavourites":handleClearFavourites($conn, $data);break;
    default: respond(400, "error", "Invalid type");
}

function handleRegister($conn, $data) {
    foreach (["name", "surname", "email", "password", "user_type"] as $field) {
        if (empty($data[$field])) respond(400, "error", "Post parameters are missing");
    }
    $name     = trim($data["name"]);
    $surname  = trim($data["surname"]);
    $email    = trim($data["email"]);
    $password = $data["password"];
    $type     = trim($data["user_type"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) respond(400, "error", "Invalid email address");
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password))
        respond(400, "error", "Password too weak");
    if ($type !== "Passenger" && $type !== "ATC") respond(400, "error", "Invalid user type");

    $check = mysqli_prepare($conn, "SELECT id FROM Users WHERE email = ?");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);
    if (mysqli_stmt_num_rows($check) > 0) respond(409, "error", "Email already registered");
    mysqli_stmt_close($check);

    $salt    = bin2hex(random_bytes(16));
    $hash    = hash("sha256", $salt . $password);
    $api_key = bin2hex(random_bytes(16));

    $stmt = mysqli_prepare($conn, "INSERT INTO Users (name, surname, email, password, type, api_key, salt) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssssss", $name, $surname, $email, $hash, $type, $api_key, $salt);
    if (mysqli_stmt_execute($stmt)) {
        respond(201, "success", ["apikey" => $api_key]);
    } else {
        respond(500, "error", "Registration failed");
    }
    mysqli_stmt_close($stmt);
}

function handleLogin($conn, $data) {
    if (empty($data["email"]) || empty($data["password"])) respond(400, "error", "Post parameters are missing");
    $email    = trim($data["email"]);
    $password = $data["password"];

    $stmt = mysqli_prepare($conn, "SELECT id, name, surname, email, password, salt, api_key, type FROM Users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$user) respond(401, "error", "Invalid email or password");
    $hash = hash("sha256", $user["salt"] . $password);
    if ($hash !== $user["password"]) respond(401, "error", "Invalid email or password");

    respond(200, "success", [
        "apikey"  => $user["api_key"],
        "name"    => $user["name"],
        "surname" => $user["surname"],
        "email"   => $user["email"],
        "type"    => $user["type"]
    ]);
}

function handleGetAllPlanes($conn, $data) {
    if (empty($data["apikey"]) || !isValidApiKey($conn, $data["apikey"]))
        respond(401, "error", "Invalid or missing API key");

    $where = []; $params = []; $types = "";

    if (!empty($data["search"])) {
        $search  = "%" . trim($data["search"]) . "%";
        $where[] = "(manufacturer LIKE ? OR model LIKE ? OR description LIKE ?)";
        $params  = array_merge($params, [$search, $search, $search]);
        $types  .= "sss";
    }
    if (isset($data["min_seats"]) && is_numeric($data["min_seats"])) {
        $where[] = "seats >= ?"; $params[] = (int)$data["min_seats"]; $types .= "i";
    }
    if (isset($data["max_seats"]) && is_numeric($data["max_seats"])) {
        $where[] = "seats <= ?"; $params[] = (int)$data["max_seats"]; $types .= "i";
    }

    $sql = "SELECT * FROM planes";
    if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);

    $validCols   = ["id","seats","max_range_km","max_cargo_kg","max_speed_kmh","manufacturer","model"];
    $validOrders = ["asc","desc"];
    $sort  = isset($data["sort"])  && in_array($data["sort"], $validCols)                        ? $data["sort"]               : "id";
    $order = isset($data["order"]) && in_array(strtolower($data["order"]), $validOrders) ? strtolower($data["order"]) : "asc";
    $sql  .= " ORDER BY $sort $order";
    if (!empty($data["limit"]) && is_numeric($data["limit"]) && (int)$data["limit"] > 0)
        $sql .= " LIMIT " . (int)$data["limit"];

    $stmt = mysqli_prepare($conn, $sql);
    if (!empty($params)) mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $planes = [];
    while ($row = mysqli_fetch_assoc($result)) $planes[] = $row;
    mysqli_stmt_close($stmt);
    respond(200, "success", $planes);
}

function handleGetAllAirports($conn, $data) {
    if (empty($data["apikey"]) || !isValidApiKey($conn, $data["apikey"]))
        respond(401, "error", "Invalid or missing API key");

    $where = []; $params = []; $types = "";

    if (!empty($data["search"])) {
        $search  = "%" . trim($data["search"]) . "%";
        $where[] = "(name LIKE ? OR city LIKE ? OR country LIKE ? OR code LIKE ?)";
        $params  = array_merge($params, [$search, $search, $search, $search]);
        $types  .= "ssss";
    }
    if (!empty($data["code"])) {
        $where[] = "code = ?"; $params[] = trim($data["code"]); $types .= "s";
    }

    $sql = "SELECT * FROM airports";
    if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);

    $page   = isset($data["page"])  && is_numeric($data["page"])  && $data["page"]  > 0 ? (int)$data["page"]  : 1;
    $limit  = isset($data["limit"]) && is_numeric($data["limit"]) && $data["limit"] > 0 ? (int)$data["limit"] : 50;
    $offset = ($page - 1) * $limit;
    $sql   .= " LIMIT $limit OFFSET $offset";

    $stmt = mysqli_prepare($conn, $sql);
    if (!empty($params)) mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result   = mysqli_stmt_get_result($stmt);
    $airports = [];
    while ($row = mysqli_fetch_assoc($result)) $airports[] = $row;
    mysqli_stmt_close($stmt);
    respond(200, "success", $airports);
}

function handleAddFavourite($conn, $data) {
    if (empty($data["apikey"]) || !isValidApiKey($conn, $data["apikey"]))
        respond(401, "error", "Invalid or missing API key");
    if (empty($data["plane_id"])) respond(400, "error", "Missing plane_id");

    $userId  = getUserId($conn, $data["apikey"]);
    $planeId = (int)$data["plane_id"];

    $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO favourites (user_id, plane_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ii", $userId, $planeId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    respond(200, "success", "Plane added to favourites");
}

function handleGetFavourites($conn, $data) {
    if (empty($data["apikey"]) || !isValidApiKey($conn, $data["apikey"]))
        respond(401, "error", "Invalid or missing API key");

    $userId = getUserId($conn, $data["apikey"]);
    $stmt   = mysqli_prepare($conn,
        "SELECT p.* FROM planes p INNER JOIN favourites f ON p.id = f.plane_id WHERE f.user_id = ?"
    );
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $planes = [];
    while ($row = mysqli_fetch_assoc($result)) $planes[] = $row;
    mysqli_stmt_close($stmt);
    respond(200, "success", $planes);
}

function handleRemoveFavourite($conn, $data) {
    if (empty($data["apikey"]) || !isValidApiKey($conn, $data["apikey"]))
        respond(401, "error", "Invalid or missing API key");
    if (empty($data["plane_id"])) respond(400, "error", "Missing plane_id");

    $userId  = getUserId($conn, $data["apikey"]);
    $planeId = (int)$data["plane_id"];

    $stmt = mysqli_prepare($conn, "DELETE FROM favourites WHERE user_id = ? AND plane_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $userId, $planeId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    respond(200, "success", "Removed from favourites");
}

function handleClearFavourites($conn, $data) {
    if (empty($data["apikey"]) || !isValidApiKey($conn, $data["apikey"]))
        respond(401, "error", "Invalid or missing API key");

    $userId = getUserId($conn, $data["apikey"]);
    $stmt   = mysqli_prepare($conn, "DELETE FROM favourites WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    respond(200, "success", "All favourites cleared");
}
?>