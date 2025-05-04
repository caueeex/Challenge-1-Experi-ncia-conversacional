<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *"); // Allow CORS for testing

// Database connection
$conn = new mysqli("localhost", "root", "", "furia_db");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Handle API requests
$method = $_SERVER["REQUEST_METHOD"];
$path = isset($_GET["path"]) ? explode("/", trim($_GET["path"], "/")) : [];

if ($method === "GET") {
    if ($path[0] === "news") {
        // Fetch news
        $result = $conn->query("SELECT * FROM news ORDER BY date DESC LIMIT 10");
        $news = [];
        while ($row = $result->fetch_assoc()) {
            $news[] = $row;
        }
        echo json_encode($news);
    } elseif ($path[0] === "matches") {
        // Fetch upcoming matches
        $result = $conn->query("SELECT * FROM matches WHERE date >= CURDATE() ORDER BY date ASC LIMIT 10");
        $matches = [];
        while ($row = $result->fetch_assoc()) {
            $matches[] = $row;
        }
        echo json_encode($matches);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Endpoint not found"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}

$conn->close();
?>