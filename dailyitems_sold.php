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

    // Query to fetch daily sales data with calculated profit and product name
    $stmt = $pdo->prepare("
        SELECT DATE(s.saledate) AS sale_day, 
               p.productname, 
               SUM(s.quantity) AS total_items_sold, 
               SUM((s.sellingprice - s.buyingprice) * s.quantity) AS daily_profit
        FROM sales s
        JOIN productandservices p ON s.productname = p.productname
        GROUP BY sale_day, p.productname
        ORDER BY sale_day DESC
    ");
    
    // Execute the query
    $stmt->execute();
    $dailySales = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Sales Summary</title>

    <?php include 'admin_css.php'; ?>

    <style type="text/css">
        .table_th { padding: 20px; font-size: 20px; }
        .table_td { padding: 20px; background-color: skyblue; }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <center>
        <h2>Daily Sales Summary</h2>
        <?php if (!empty($dailySales)): ?>
            <table border="1px">
                <thead>
                    <tr>
                        <th class="table_th">Date</th>
                        <th class="table_th">Product Name</th>
                        <th class="table_th">Total Items Sold</th>
                        <th class="table_th">Daily Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dailySales as $day): ?>
                        <tr>
                            <td class="table_td"><?php echo htmlspecialchars($day['sale_day']); ?></td>
                            <td class="table_td"><?php echo htmlspecialchars($day['productname']); ?></td>
                            <td class="table_td"><?php echo htmlspecialchars($day['total_items_sold']); ?></td>
                            <td class="table_td"><?php echo number_format($day['daily_profit'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No sales data available for the selected period.</p>
        <?php endif; ?>
    </center>
</div>

</body>
</html>
