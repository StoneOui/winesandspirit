<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<body>
<h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
<p>Your role: <?php echo htmlspecialchars($_SESSION['role']); ?></p>

<?php if ($_SESSION['role'] === 'Admin'): ?>
    <p>Admin Controls: [Manage Products, View Reports, etc.]</p>
<?php elseif ($_SESSION['role'] === 'SalesClerk'): ?>
    <p>Sales Clerk Controls: [Sell Products]</p>
<?php endif; ?>

<a href="logout.php">Logout</a>
</body>
</html>
