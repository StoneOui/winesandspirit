<?php
session_start();
date_default_timezone_set('Africa/Nairobi'); // Set to your timezone

// Database connection details
$host = "localhost";
$user = "root";
$password = "";
$db = "winesandspirit";

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query for Sales Trends (daily sales for the last 30 days)
    $stmt = $pdo->prepare("SELECT DATE(saledate) AS sale_date, SUM(sellingprice * quantity) AS total_sales 
                           FROM sales
                           WHERE saledate >= CURDATE() - INTERVAL 30 DAY
                           GROUP BY DATE(saledate)
                           ORDER BY saledate ASC");
    $stmt->execute();
    $salesTrends = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query for Top-Selling Products (based on quantity sold)
    $stmt = $pdo->prepare("SELECT productname, SUM(quantity) AS total_sold
                           FROM sales
                           GROUP BY productname
                           ORDER BY total_sold DESC
                           LIMIT 10");
    $stmt->execute();
    $topSellingProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query for Profit/Loss Over Time (monthly profit for the last 6 months)
    $stmt = $pdo->prepare("SELECT DATE_FORMAT(saledate, '%Y-%m') AS month, 
                                  SUM((sellingprice - buyingprice) * quantity) AS profit
                           FROM sales
                           WHERE saledate >= CURDATE() - INTERVAL 6 MONTH
                           GROUP BY month
                           ORDER BY month ASC");
    $stmt->execute();
    $profitLossOverTime = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php include 'admin_css.php'; ?>
    <style>
        .chart-container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }
        .table_th {
            padding: 20px;
            font-size: 20px;
        }
        .table_td {
            padding: 20px;
            background-color: skyblue;
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <center>
        <h2>Admin Dashboard</h2>

        <!-- Sales Trends Chart (Last 30 Days) -->
        <div class="chart-container">
            <h3>Sales Trends (Last 30 Days)</h3>
            <canvas id="salesTrendsChart"></canvas>
        </div>

        <!-- Top-Selling Products Chart -->
        <div class="chart-container">
            <h3>Top-Selling Products</h3>
            <canvas id="topSellingProductsChart"></canvas>
        </div>

        <!-- Profit/Loss Over Time (Last 6 Months) -->
        <div class="chart-container">
            <h3>Profit/Loss Over Time (Last 6 Months)</h3>
            <canvas id="profitLossChart"></canvas>
        </div>

        <!-- Sales Data Table for More Information -->
        <h3>Sales Data (Last 30 Days)</h3>
        <table border="1px">
            <thead>
                <tr>
                    <th class="table_th">Date</th>
                    <th class="table_th">Total Sales</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salesTrends as $data): ?>
                    <tr>
                        <td class="table_td"><?php echo htmlspecialchars($data['sale_date']); ?></td>
                        <td class="table_td"><?php echo number_format($data['total_sales'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Top Selling Products Table -->
        <h3>Top-Selling Products</h3>
        <table border="1px">
            <thead>
                <tr>
                    <th class="table_th">Product Name</th>
                    <th class="table_th">Total Items Sold</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topSellingProducts as $data): ?>
                    <tr>
                        <td class="table_td"><?php echo htmlspecialchars($data['productname']); ?></td>
                        <td class="table_td"><?php echo $data['total_sold']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Profit/Loss Data Table -->
        <h3>Profit/Loss Over Time (Last 6 Months)</h3>
        <table border="1px">
            <thead>
                <tr>
                    <th class="table_th">Month</th>
                    <th class="table_th">Profit/Loss (Ksh)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($profitLossOverTime as $data): ?>
                    <tr>
                        <td class="table_td"><?php echo htmlspecialchars($data['month']); ?></td>
                        <td class="table_td"><?php echo number_format($data['profit'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </center>
</div>

<script>
    // Data for Sales Trends (Last 30 Days)
    var salesTrendsData = <?php echo json_encode($salesTrends); ?>;
    var salesDates = salesTrendsData.map(function(item) { return item.sale_date; });
    var salesAmounts = salesTrendsData.map(function(item) { return item.total_sales; });

    // Data for Top-Selling Products
    var topSellingProductsData = <?php echo json_encode($topSellingProducts); ?>;
    var productNames = topSellingProductsData.map(function(item) { return item.productname; });
    var productSales = topSellingProductsData.map(function(item) { return item.total_sold; });

    // Data for Profit/Loss Over Time (Last 6 Months)
    var profitLossData = <?php echo json_encode($profitLossOverTime); ?>;
    var months = profitLossData.map(function(item) { return item.month; });
    var profits = profitLossData.map(function(item) { return item.profit; });

    // Create the Sales Trends Chart
    var ctx1 = document.getElementById('salesTrendsChart').getContext('2d');
    var salesTrendsChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: salesDates,
            datasets: [{
                label: 'Total Sales (in Ksh)',
                data: salesAmounts,
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: false
            }]
        }
    });

    // Create the Top-Selling Products Chart
    var ctx2 = document.getElementById('topSellingProductsChart').getContext('2d');
    var topSellingProductsChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: productNames,
            datasets: [{
                label: 'Items Sold',
                data: productSales,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        }
    });

    // Create the Profit/Loss Over Time Chart
    var ctx3 = document.getElementById('profitLossChart').getContext('2d');
    var profitLossChart = new Chart(ctx3, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Monthly Profit/Loss (in Ksh)',
                data: profits,
                borderColor: 'rgba(153, 102, 255, 1)',
                fill: false
            }]
        }
    });
</script>

</body>
</html>
