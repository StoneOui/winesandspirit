<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$db = "winesandspirit";

try {
    // Database connection
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
        // Fetch user data from form
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare SQL statement
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);

        // Fetch the user
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists and password is correct
        if ($user && password_verify($password, $user['password'])) {
            // Store user session data
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            if ($user['role'] === 'Admin') {
                header("Location: admin_view_dashboard.php");
                exit(); // Make sure to stop further execution
            } elseif ($user['role'] === 'SalesClerk') {
                header("Location: salesclerk_dashboard.php");
                exit();
            } else {
                echo "You do not have the required privileges.";
            }
        } else {
            echo "Invalid username or password.";
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
    <title>Login</title>
    <?php include 'admin_css.php'; ?>
</head>
<body>
    <div class="content">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br><br>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
