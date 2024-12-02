<?php
// Database connection details
$host = "localhost";
$user = "root";
$password = "";
$db = "winesandspirit";

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $productname = $_POST['productname'];
        $buyingprice = $_POST['buyingprice'];
        $sellingprice = $_POST['sellingprice'];
        $pricedate = $_POST['pricedate'];
        $expiringdate = $_POST['expiringdate'];

        // Insert data into the items table
        $stmt = $pdo->prepare("INSERT INTO productandservices (productname, buyingprice, sellingprice, pricedate, expiringdate) VALUES (:productname, :buyingprice, :sellingprice, :pricedate, :expiringdate)");
        $stmt->execute([
            ':productname' => $productname,
            ':buyingprice' => $buyingprice,
            ':sellingprice' => $sellingprice,
            ':pricedate' => $pricedate,
            ':expiringdate' => $expiringdate
        ]);

        echo "Item added successfully!";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
