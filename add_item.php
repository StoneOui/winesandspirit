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
        $quantity = $_POST['quantity'];
        $initialstock = $_POST['initialstock'];
        $buyingprice = $_POST['buyingprice'];
        $sellingprice = $_POST['sellingprice'];
        $pricedate = $_POST['pricedate'];
        $expiringdate = $_POST['expiringdate'];
       

        // Ensure dates are in the correct format (YYYY-MM-DD)
        $formattedPriceDate = DateTime::createFromFormat('Y-m-d', $pricedate);
        $formattedExpirationDate = DateTime::createFromFormat('Y-m-d', $expiringdate);

        if ($formattedPriceDate && $formattedExpirationDate) {
            // Insert data into the productandservices table
            $stmt = $pdo->prepare("INSERT INTO productandservices (productname, quantity, initialstock, buyingprice, sellingprice, pricedate, expiringdate) VALUES (:productname, :quantity,:initialstock, :buyingprice, :sellingprice, :pricedate, :expiringdate)");
            $stmt->execute([
                ':productname' => $productname,
                ':quantity' => $quantity,
                ':initialstock'=> $initialstock,
                ':buyingprice' => $buyingprice,
                ':sellingprice' => $sellingprice,
                ':pricedate' => $formattedPriceDate->format('Y-m-d'),
                ':expiringdate' => $formattedExpirationDate->format('Y-m-d'),
               
            ]);

            echo "Item added successfully!";
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
    <title>Add Item</title>
    <style type="text/css">
        .div_deg {
            background-color: skyblue;
            padding: 40px;
            width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-top: 50px;
        }
        .div_deg label {
            font-weight: bold;
        }
        .div_deg input[type="file"],
        .div_deg textarea {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
        }
    </style>
    <?php include 'admin_css.php'; ?>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>


<div class="content">
    <center>
        
    <h2>Add New Item</h2>

    <div class="div_deg">

    <form action="" method="POST">

        <div>

        <label for="productname">Product Name:</label>
        <input type="text" name="productname" required>

        </div><br>

        <div>
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" required>
        </div><br>

        <div>
        <label for="initialstock">Initialstock:</label>
        <input type="number"  name="initialstock" required>
        </div><br>

        <div>
        <label for="buyingprice">Buying Price:</label>
        <input type="number" step="0.01" name="buyingprice" required>
        </div><br>

        <div>
        <label for="sellingprice">Selling Price:</label>
        <input type="number" step="0.01" name="sellingprice" required>
        </div><br>
  
        <div>
        <label for="pricedate">Price Date:</label>
        <input type="date" name="pricedate" required>
        </div><br>

        <div>
        <label for="expiringdate">Expiration Date:</label>
        <input type="date" name="expiringdate" required>
        </div><br>

        

    

    <button type="submit">Add Item</button>
</form>
    </div>

    </center>
</div>

</body>
</html>
