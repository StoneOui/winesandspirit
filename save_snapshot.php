<?php
// Set header for JSON response
header('Content-Type: application/json');

// Database connection details
$host = "localhost";
$dbname = "kamanja";
$username = "root";
$password = "";

try {
    // Establish the PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the JSON data from the request
    $data = json_decode(file_get_contents("php://input"), true);

    // Insert the snapshot data into the database
    $stmt = $pdo->prepare("INSERT INTO snapshots (timestamp, page_url, message) VALUES (:timestamp, :pageUrl, :message)");
    $stmt->execute([
        ':timestamp' => $data['timestamp'],
        ':pageUrl' => $data['pageUrl'],
        ':message' => $data['message']
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Snapshot saved successfully.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
