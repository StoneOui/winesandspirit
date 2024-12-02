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

    // Check if the ID is set in the URL
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Fetch the item details from the database
        $stmt = $pdo->prepare("SELECT * FROM productandservices WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if item exists
        if (!$item) {
            die("Item not found.");
        }
    }

    // Check if form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $productname = $_POST['productname'];
        $quantity = $_POST['quantity'];
        $initialstock = $_POST['initialstock'];
        $buyingprice = $_POST['buyingprice'];
        $sellingprice = $_POST['sellingprice'];
        $pricedate = $_POST['pricedate'];
        $expiringdate = $_POST['expiringdate'];

        // Ensure date formats are correct (YYYY-MM-DD)
        $formattedPriceDate = DateTime::createFromFormat('Y-m-d', $pricedate);
        $formattedExpirationDate = DateTime::createFromFormat('Y-m-d', $expiringdate);

        if ($formattedPriceDate && $formattedExpirationDate) {
            // Update item in the database
            $stmt = $pdo->prepare("
                UPDATE productandservices 
                SET productname = :productname, 
                    quantity = :quantity, 
                    initialstock = :initialstock, 
                    buyingprice = :buyingprice, 
                    sellingprice = :sellingprice, 
                    pricedate = :pricedate, 
                    expiringdate = :expiringdate 
                WHERE id = :id
            ");
            $stmt->execute([
                ':productname' => $productname,
                ':quantity' => $quantity,
                ':initialstock' => $initialstock,
                ':buyingprice' => $buyingprice,
                ':sellingprice' => $sellingprice,
                ':pricedate' => $formattedPriceDate->format('Y-m-d'),
                ':expiringdate' => $formattedExpirationDate->format('Y-m-d'),
                ':id' => $id
            ]);

            echo "Item updated successfully!";
            // Optionally, redirect to view items page
            header("Location: view_item.php");
            exit();
        } else {
            echo "Invalid date format. Please enter the date as YYYY-MM-DD.";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Item</title>

    <?php include 'admin_css.php'; ?>

    <style type="text/css">
        label {
            display: inline-block;
            width: 150px;
            text-align: right;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .form_deg {
            background-color: skyblue;
            width: 600px;
            padding-top: 70px;
            padding-bottom: 70px;
        }
    </style>
</head>

<body>
<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <center>
        <h2>Update Item</h2>

        <form action="" method="POST" class="form_deg">
            <label for="productname">Product Name:</label>
            <input type="text" name="productname" value="<?php echo htmlspecialchars($item['productname']); ?>" required><br>

            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" required><br>

            <label for="initialstock">Initial Stock:</label>
            <input type="number" name="initialstock" value="<?php echo htmlspecialchars($item['initialstock']); ?>" required><br>

            <label for="buyingprice">Buying Price:</label>
            <input type="number" step="0.01" name="buyingprice" value="<?php echo htmlspecialchars($item['buyingprice']); ?>" required><br>

            <label for="sellingprice">Selling Price:</label>
            <input type="number" step="0.01" name="sellingprice" value="<?php echo htmlspecialchars($item['sellingprice']); ?>" required><br>

            <label for="pricedate">Price Date:</label>
            <input type="date" name="pricedate" value="<?php echo htmlspecialchars($item['pricedate']); ?>" required><br>

            <label for="expiringdate">Expiration Date:</label>
            <input type="date" name="expiringdate" value="<?php echo htmlspecialchars($item['expiringdate']); ?>" required><br>

            <button type="submit">Update Item</button>
        </form>
    </center>
</div>
</body>
</html>
