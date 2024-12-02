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

    // Fetch all items from the items table
    $stmt = $pdo->query("SELECT * FROM productandservices"); // Ensure this is the correct table name
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Items</title>
    <?php include 'admin_css.php'; ?>

    <style type="text/css">
        .table_th {
            padding: 20px;
            font-size: 20px;
            background-color: #f2f2f2; /* Light gray background for header */
        }

        .table_td {
            padding: 20px;
            background-color: skyblue;
        }

        table {
            width: 100%;
            border-collapse: collapse; /* Ensures no space between borders */
        }

        th, td {
            border: 1px solid #ddd; /* Adds a border to cells */
        }

        tr:hover {
            background-color: #f1f1f1; /* Change background on hover */
        }

        .button {
            margin: 5px; /* Add some space around buttons */
            padding: 5px 10px;
            background-color: #4CAF50; /* Green background for buttons */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .button-delete {
            background-color: #f44336; /* Red background for delete button */
        }

        .button-update {
            background-color: #2196F3; /* Blue background for update button */
        }
    </style>
</head>

<body>
    <?php include 'admin_sidebar.php'; ?>

    <div class="content">
        <center>
            <h2>Items List</h2>
            <table>
                <thead>
                    <tr>
                        <th class="table_th">ID</th>
                        <th class="table_th">Product Name</th>
                        <th class="table_th">Quantity</th>
                        <th class="table_th">Initialstock</th>
                        <th class="table_th">Buying Price</th>
                        <th class="table_th">Selling Price</th>
                        <th class="table_th">Price Date</th>
                        <th class="table_th">Expiration Date</th>

                        <th class="table_th">Actions</th> <!-- New Actions Column -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="table_td"><?php echo htmlspecialchars($item['id']); ?></td>
                                <td class="table_td"><?php echo htmlspecialchars($item['productname']); ?></td>
                                <td class="table_td"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td class="table_td"><?php echo htmlspecialchars($item['initialstock']); ?></td>
                                <td class="table_td"><?php echo htmlspecialchars($item['buyingprice']); ?></td>
                                <td class="table_td"><?php echo htmlspecialchars($item['sellingprice']); ?></td>
                                <td class="table_td"><?php 
                                    // Format and display the price date
                                    $priceDate = new DateTime($item['pricedate']);
                                    echo htmlspecialchars($priceDate->format('Y-m-d')); 
                                ?></td>
                                <td class="table_td"><?php 
                                    // Format and display the expiration date
                                    $expirationDate = new DateTime($item['expiringdate']);
                                    echo htmlspecialchars($expirationDate->format('Y-m-d')); 
                                ?></td>
                                <td class="table_td">
                                    <!-- Delete Form -->
                                    <form action="delete_item.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                        <button type="submit" class="button button-delete">Delete</button>
                                    </form>

                                    <!-- Update Link -->
                                    <form action="update_item.php" method="GET" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                        <button type="submit" class="button button-update">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No items found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </center>
    </div>
</body>
</html>
