<?php
// delete_item.php
$host = "localhost";
$user = "root";
$password = "";
$db = "winesandspirit";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'];
        
        // Prepare and execute the deletion query
        $stmt = $pdo->prepare("DELETE FROM productandservices WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // Redirect back to view_item.php
        header("Location: view_item.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
