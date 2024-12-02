<?php
session_start();

date_default_timezone_set('Africa/Nairobi');

// Database connection details
$host = "localhost";
$user = "root";
$password = "";
$db = "winesandspirit";

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Initialize variables
    $searchResults = [];
    if (!isset($_SESSION['selectedItems'])) {
        $_SESSION['selectedItems'] = [];
    }

    // Handle search request
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
        $productname = $_POST['productname'];
        $stmt = $pdo->prepare("SELECT * FROM productandservices WHERE productname LIKE :productname");
        $stmt->execute([':productname' => '%' . $productname . '%']);
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle add to sale
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_sale'])) {
        $productID = $_POST['productID'];
        $quantity = (int)$_POST['quantity'];

        // Fetch item details based on product ID
        $stmt = $pdo->prepare("SELECT * FROM productandservices WHERE id = :productID");
        $stmt->execute([':productID' => $productID]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // Check the sales quantity for this product
            $stmt = $pdo->prepare("SELECT SUM(quantity) AS total_sold FROM sales WHERE productname = :productname");
            $stmt->execute([':productname' => $item['productname']]);
            $salesData = $stmt->fetch(PDO::FETCH_ASSOC);

            // Calculate the remaining stock by subtracting total sales from initial stock
            $remainingStock = $item['initialstock'] - $salesData['total_sold'];

            // Check if enough stock is available
            if ($remainingStock < $quantity) {
                $errorMessage = "Not enough stock available. Only " . $remainingStock . " units left.";
            } else {
                // Calculate total price for the selected quantity
                $item['quantity'] = $quantity;
                $item['total_price'] = $quantity * $item['sellingprice'];

                // Add the selected item to the session cart
                $_SESSION['selectedItems'][] = $item;

                // Recalculate total cost
                $_SESSION['totalCost'] = array_sum(array_column($_SESSION['selectedItems'], 'total_price'));
                $successMessage = "Item added to sale successfully!";
            }
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
    <title>Sell Products</title>

    <?php include 'admin_css.php'; ?>

    <style type="text/css">
        .table_th { padding: 20px; font-size: 20px; }
        .table_td { padding: 20px; background-color: skyblue; }
        .error-message { color: red; font-weight: bold; }
        .success-message { color: green; font-weight: bold; }
        .div_deg { background-color: skyblue; padding: 40px; width: 500px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); margin-top: 50px; }

        /* Styling the layout */
        .container {
            width: 80%;
            margin: 0 auto;
        }

        .form-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 20px 0;
        }

        .form-container input[type="text"] {
            width: 75%;
            padding: 10px;
            font-size: 16px;
        }

        .form-container button {
            padding: 10px 15px;
            font-size: 16px;
        }

        .results-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
        }

        .result-item {
            width: 30%;
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .result-item p {
            margin: 0;
            font-size: 16px;
        }

        .sale-container {
            background-color: #f9f9f9;
            padding: 20px;
            margin-top: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: left;
            max-height: 400px;
            overflow-y: auto;
        }

        .sale-container h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .sale-item {
            list-style: none;
            padding: 10px;
            border-bottom: 1px solid #ccc;
            font-size: 16px;
        }

        .sale-item:last-child {
            border-bottom: none;
        }

        .sale-container p {
            font-weight: bold;
            font-size: 18px;
            margin-top: 15px;
        }

        .sale-container button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            text-align: center;
        }

        .sale-container button:hover {
            background-color: #0056b3;
        }

        .sale-container .empty-message {
            color: #666;
            font-style: italic;
        }

    </style>

</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="container">
    <div class="form-container">
        <h2>Search for Products</h2>
        <form action="sell_products.php" method="POST">
            <input type="text" name="productname" placeholder="Product Name" required>
            <button type="submit" name="search">Search</button>
        </form>
    </div>

    <?php if (!empty($searchResults)): ?>
        <div class="results-container">
            <h3>Search Results</h3>
            <?php foreach ($searchResults as $item): ?>
                <div class="result-item">
                    <span><?php echo htmlspecialchars($item['productname']); ?> (Price: <?php echo htmlspecialchars($item['sellingprice']); ?>)</span>

                    <!-- Error or Success message -->
                    <?php if (isset($errorMessage)): ?>
                        <p class="error-message"><?php echo $errorMessage; ?></p>
                    <?php endif; ?>

                    <?php if (isset($successMessage)): ?>
                        <p class="success-message"><?php echo $successMessage; ?></p>
                    <?php endif; ?>

                    <!-- Check stock and display accordingly -->
                    <?php
                    $stmt = $pdo->prepare("SELECT SUM(quantity) AS total_sold FROM sales WHERE productname = :productname");
                    $stmt->execute([':productname' => $item['productname']]);
                    $salesData = $stmt->fetch(PDO::FETCH_ASSOC);
                    $remainingStock = $item['initialstock'] - $salesData['total_sold'];
                    ?>

                    <?php if ($remainingStock < 5): ?>
                        <p class="error-message">Out of stock! Only <?php echo $remainingStock; ?> units left.</p>
                        <button type="button" disabled>Add to Sale</button>
                    <?php else: ?>
                        <form action="sell_products.php" method="POST" style="display:inline;">
                            <input type="hidden" name="productID" value="<?php echo htmlspecialchars($item['id']); ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $remainingStock; ?>" required>
                            <button type="submit" name="add_to_sale" <?php echo ($remainingStock < 1) ? 'disabled' : ''; ?>>Add to Sale</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])): ?>
        <div class="results-container">
            <p>No products found for "<?php echo htmlspecialchars($productname); ?>"</p>
        </div>
    <?php endif; ?>

    <div class="sale-container">
        <h3>Items in Sale</h3>
        <?php if (!empty($_SESSION['selectedItems'])): ?>
            <ul>
                <?php foreach ($_SESSION['selectedItems'] as $item): ?>
                    <li class="sale-item">
                        <?php echo htmlspecialchars($item['productname']) . ' - Quantity: ' . $item['quantity'] . ' - Total Price: ' . $item['total_price']; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p>Total Cost: <?php echo $_SESSION['totalCost']; ?></p>
            <form action="checkout_product.php" method="POST">
                <button type="submit">Proceed to Checkout</button>
            </form>
        <?php else: ?>
            <p class="empty-message">No items added to sale yet.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
