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
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Check if items are available in the session
if (!isset($_SESSION['selectedItems']) || empty($_SESSION['selectedItems'])) {
    echo "No items in the sale. Please add items to the sale first.";
    exit();
}

$selectedItems = $_SESSION['selectedItems'];
$totalCost = $_SESSION['totalCost'];
$balance = 0;
$payment = 0;

// Handle delete item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_item'])) {
    $index = $_POST['item_index'];
    if (isset($selectedItems[$index])) {
        // Deduct the item's total price from total cost
        $totalCost -= $selectedItems[$index]['total_price'];
        unset($selectedItems[$index]);
        $_SESSION['selectedItems'] = array_values($selectedItems); // Re-index array
        $_SESSION['totalCost'] = $totalCost;
    }
}

// Handle update quantity
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_quantity'])) {
    $index = $_POST['item_index'];
    $newQuantity = $_POST['quantity'];
    
    if (isset($selectedItems[$index]) && $newQuantity > 0) {
        // Update total cost by removing old item cost and adding the new one
        $totalCost -= $selectedItems[$index]['total_price'];
        $selectedItems[$index]['quantity'] = $newQuantity;
        $selectedItems[$index]['total_price'] = $newQuantity * $selectedItems[$index]['sellingprice'];
        $totalCost += $selectedItems[$index]['total_price'];
        
        $_SESSION['selectedItems'] = $selectedItems;
        $_SESSION['totalCost'] = $totalCost;
    }
}

// Handle form submission to calculate balance
$formSubmitted = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cash_given'])) {
    $payment = (float)$_POST['cash_given'];
    $balance = $payment - $totalCost;
    $formSubmitted = true;

    // Insert each sold item into the sales table
    if ($balance >= 0) {
        try {
            foreach ($selectedItems as $item) {
                $stmt = $pdo->prepare("
                    INSERT INTO sales (productname, quantity, sellingprice, buyingprice, saledate) 
                    VALUES (:productname, :quantity, :sellingprice, :buyingprice, :saledate)
                ");
                $stmt->execute([
                    ':productname' => $item['productname'],
                    ':quantity' => $item['quantity'],
                    ':sellingprice' => $item['sellingprice'],
                    ':buyingprice' => $item['buyingprice'],
                    ':saledate' => date("Y-m-d H:i:s")
                ]);
            }
            
            // Clear session data after successful insertion
            unset($_SESSION['selectedItems'], $_SESSION['totalCost']);
        } catch (PDOException $e) {
            echo "Error inserting sale: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <?php include 'admin_css.php'; ?>
    <style>
        .table_th { padding: 20px; font-size: 20px; }
        .table_td { padding: 20px; background-color: skyblue; }
    </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>

<div class="content">
<center>
    <h2>Checkout Summary</h2>

    <table border="1px">
        <thead>
            <tr>
                <th class="table_th">Product Name</th>
                <th class="table_th">Quantity</th>
                <th class="table_th">Price per Item</th>
                <th class="table_th">Total Price</th>
                <th class="table_th">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($selectedItems as $index => $item): ?>
                <tr>
                    <td class="table_td"><?php echo htmlspecialchars($item['productname']); ?></td>
                    <td class="table_td">
                        <form action="checkout_product.php" method="POST" style="display: inline;">
                            <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1">
                            <input type="hidden" name="item_index" value="<?php echo $index; ?>">
                            <button type="submit" name="update_quantity">Update</button>
                        </form>
                    </td>
                    <td class="table_td"><?php echo number_format($item['sellingprice'], 2); ?></td>
                    <td class="table_td"><?php echo number_format($item['total_price'], 2); ?></td>
                    <td class="table_td">
                        <form action="checkout_product.php" method="POST" style="display: inline;">
                            <input type="hidden" name="item_index" value="<?php echo $index; ?>">
                            <button type="submit" name="delete_item">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p>Total Cost: <?php echo number_format($totalCost, 2); ?></p>
    <p>Date: <?php echo date("Y-m-d"); ?></p>
    <p>Time: <?php echo date("H:i:s"); ?></p>

    <?php if (!$formSubmitted): ?>
        <form action="checkout_product.php" method="POST">
            <label for="cash_given">Amount Paid by Customer:</label>
            <input type="number" name="cash_given" id="cash_given" step="0.01" min="0" required>
            <button type="submit">Calculate Balance</button>
        </form>
    <?php endif; ?>

    <?php if ($formSubmitted): ?>
        <p>Amount Paid: <?php echo number_format($payment, 2); ?></p>
        <p>Balance: <?php echo number_format($balance, 2); ?></p>
    <?php endif; ?>
</center>
</div>
</body>
</html>
