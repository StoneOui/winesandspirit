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

    // Variable to store search results
    $itemDetails = null;
    $stockMessage = ""; // Variable to hold the stock notification message
    $disableSellButton = false; // Variable to control the "Sell" button visibility

    // Check if search form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $productname = trim($_POST['productname']); // Trim any extra spaces

        // Fetch item details from the database
        $stmt = $pdo->prepare("SELECT * FROM productandservices WHERE productname = :productname LIMIT 1");
        $stmt->execute([':productname' => $productname]);
        $itemDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check stock level and set messages
        if ($itemDetails) {
            if ($itemDetails['quantity'] <= 0) {
                $stockMessage = "This product is out of stock.";
                $disableSellButton = true;
            } elseif ($itemDetails['quantity'] <= 5) {
                $stockMessage = "Warning: Low stock! Only " . $itemDetails['quantity'] . " units left.";
                $disableSellButton = true;
            } else {
                $stockMessage = "Product is in stock: " . $itemDetails['quantity'] . " units available.";
            }
        } else {
            $stockMessage = "No item found with the name \"$productname\".";
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
    <title>Search and Sell Item</title>
    <style type="text/css">
        .div_deg {
            background-color: skyblue;
            padding: 40px;
            width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-top: 50px;
        }
        .out-of-stock {
            color: red;
            font-weight: bold;
        }
        .low-stock {
            color: orange;
            font-weight: bold;
        }
        .in-stock {
            color: green;
            font-weight: bold;
        }
        .disabled-button {
            background-color: grey;
            cursor: not-allowed;
        }
    </style>
    <?php include 'admin_css.php'; ?>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <h2>Search for an Item</h2>

    <center>
        <div class="div_deg">
            <form action="search_item.php" method="POST">
                <label for="productname">Product Name:</label>
                <input type="text" id="productname" name="productname" required>
                <button type="submit">Search</button>
            </form>

            <?php if ($itemDetails): ?>
                <div class="content">
                    <h3>Item Details</h3>
                    <p><strong>Product Name:</strong> <?php echo htmlspecialchars($itemDetails['productname']); ?></p>
                    <p><strong>Quantity:</strong> <?php echo htmlspecialchars($itemDetails['quantity']); ?></p>
                    <p><strong>Selling Price:</strong> <?php echo htmlspecialchars($itemDetails['sellingprice']); ?></p>
                    <p><strong>Buying Price:</strong> <?php echo htmlspecialchars($itemDetails['buyingprice']); ?></p>
                    <p><strong>Price Date:</strong> <?php echo htmlspecialchars($itemDetails['pricedate']); ?></p>
                    <p><strong>Expiration Date:</strong> <?php echo htmlspecialchars($itemDetails['expiringdate']); ?></p>

                    <!-- Stock message with color coding based on stock level -->
                    <p class="<?php echo ($itemDetails['quantity'] <= 0) ? 'out-of-stock' : (($itemDetails['quantity'] <= 5) ? 'low-stock' : 'in-stock'); ?>">
                        <?php echo $stockMessage; ?>
                    </p>

                    <!-- Sell button: disabled if out of stock or low stock -->
                    <?php if (!$disableSellButton): ?>
                        <form action="search_item_action.php" method="POST">
                            <input type="hidden" name="productname" value="<?php echo htmlspecialchars($itemDetails['productname']); ?>">
                            <input type="hidden" name="quantity" value="<?php echo htmlspecialchars($itemDetails['quantity']); ?>">
                            <button type="submit">Sell Item</button>
                        </form>
                    <?php else: ?>
                        <button class="disabled-button" disabled>Sell Item</button>
                    <?php endif; ?>

                </div>
            <?php else: ?>
                <div class="content">
                    <p><?php echo $stockMessage; ?></p>
                </div>
            <?php endif; ?>
        </div>
    </center>
</div>

</body>
</html>
