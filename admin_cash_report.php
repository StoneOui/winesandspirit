<?php
session_start();
date_default_timezone_set('Africa/Nairobi'); // Set to your timezone

// Database connection details
$host = "localhost";
$user = "root";
$password = "";
$db = "winesandspirit";

// Define the password for accessing the report
$adminPassword = "1234"; // Change this to your desired password

// Flag to track if the user has entered the correct password
$isAuthenticated = false;

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the entered password matches the predefined password
    if (isset($_POST['password']) && $_POST['password'] === $adminPassword) {
        $_SESSION['authenticated'] = true; // Set session variable indicating that the user is authenticated
        $isAuthenticated = true;
    } else {
        $errorMessage = "Incorrect password. Please try again."; // Error message if the password is wrong
    }
}

// Destroy session and prompt for password on page refresh or leaving the page
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // If not authenticated, show the password prompt
    echo '<form method="POST" action="admin_cash_report.php">
            <label for="password">Enter Password to Access the Report:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Submit</button>
          </form>';
    if (isset($errorMessage)) {
        echo '<p style="color:red;">' . $errorMessage . '</p>';
    }
    exit(); // Exit here to prevent access to the report if the user isn't authenticated
}

// Clear authentication session after the page is visited
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    unset($_SESSION['authenticated']); // Destroy the session variable to require password again
}

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the current date and calculate the week and month ranges
    $currentDate = new DateTime();
    $startDateWeek = $currentDate->modify('this week')->format('Y-m-d');
    $startDateMonth = $currentDate->modify('first day of this month')->format('Y-m-d');

    // Query to fetch weekly sales, total expense, and profit
    $stmt = $pdo->prepare("
        SELECT 
            p.productname,
            p.initialstock,
            SUM(s.quantity) AS total_items_sold,
            SUM((s.sellingprice - s.buyingprice) * s.quantity) AS daily_profit,
            SUM(s.buyingprice * s.quantity) AS total_expense
        FROM sales s
        JOIN productandservices p ON s.productname = p.productname
        WHERE s.saledate >= :startDate
        GROUP BY p.productname
    ");

    // Weekly data
    $stmt->execute(['startDate' => $startDateWeek]);
    $weeklyData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Monthly data
    $stmt->execute(['startDate' => $startDateMonth]);
    $monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cash Report</title>

    <?php include 'admin_css.php'; ?>

    <style type="text/css">
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
        <h2>Weekly and Monthly Cash Report</h2>

        <!-- Weekly Report Table -->
        <h3>Weekly Report</h3>
        <table border="1px">
            <thead>
                <tr>
                    <th class="table_th">Product Name</th>
                    <th class="table_th">Initial Stock</th>
                    <th class="table_th">Total Items Sold</th>
                    <th class="table_th">Profit</th>
                    <th class="table_th">Expense</th>
                    <th class="table_th">Remaining Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($weeklyData as $data): ?>
                    <?php
                    $remainingStock = $data['initialstock'] - $data['total_items_sold'];
                    ?>
                    <tr>
                        <td class="table_td"><?php echo htmlspecialchars($data['productname']); ?></td>
                        <td class="table_td"><?php echo htmlspecialchars($data['initialstock']); ?></td>
                        <td class="table_td"><?php echo htmlspecialchars($data['total_items_sold']); ?></td>
                        <td class="table_td"><?php echo number_format($data['daily_profit'], 2); ?></td>
                        <td class="table_td"><?php echo number_format($data['total_expense'], 2); ?></td>
                        <td class="table_td"><?php echo $remainingStock; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Monthly Report Table -->
        <h3>Monthly Report</h3>
        <table border="1px">
            <thead>
                <tr>
                    <th class="table_th">Product Name</th>
                    <th class="table_th">Initial Stock</th>
                    <th class="table_th">Total Items Sold</th>
                    <th class="table_th">Profit</th>
                    <th class="table_th">Expense</th>
                    <th class="table_th">Remaining Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($monthlyData as $data): ?>
                    <?php
                    $remainingStock = $data['initialstock'] - $data['total_items_sold'];
                    ?>
                    <tr>
                        <td class="table_td"><?php echo htmlspecialchars($data['productname']); ?></td>
                        <td class="table_td"><?php echo htmlspecialchars($data['initialstock']); ?></td>
                        <td class="table_td"><?php echo htmlspecialchars($data['total_items_sold']); ?></td>
                        <td class="table_td"><?php echo number_format($data['daily_profit'], 2); ?></td>
                        <td class="table_td"><?php echo number_format($data['total_expense'], 2); ?></td>
                        <td class="table_td"><?php echo $remainingStock; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </center>
</div>

</body>
</html>
