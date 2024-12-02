<?php
session_start();

// Check if the user is logged in and has the 'SalesClerk' role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'SalesClerk') {
    header("Location: login.php"); // Redirect to login if not logged in as SalesClerk
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Clerk Dashboard</title>
    <?php

include 'admin_css.php';

?>

<style type="text/css">

    .table_th
    {
        padding: 20px;
        font-size: 20px;
    }

    .table_td
    {
        padding: 20px;
        background-color: skyblue;
    }
</style>
</head>
<body>


<?php

include 'salesclerk_sidebar.php';

?>

    <div class="content">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>You are logged in as a Sales Clerk.</p>
        <ul>
            <li><a href="dailyitems_sold.php">Daily Items Sold</a></li>
            <li><a href="sell_products.php">Sell Products</a></li>
            <li><a href="sale_item.php">Sale Items</a></li>
        </ul>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
