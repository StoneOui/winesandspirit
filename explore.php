<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$db = "kamanja";
$data = mysqli_connect($host, $user, $password, $db);

// Check for connection errors
if (!$data) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all moments data
$sql = "SELECT * FROM moments";
$result = mysqli_query($data, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Moments</title>
    <link rel="stylesheet" href="styleshome.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .news-item {
            margin-bottom: 30px;
            border: 1px solid #ddd; /* Add a border for better separation */
            border-radius: 5px; /* Rounded corners */
            overflow: hidden; /* Ensure images do not overflow */
        }
        .news-image {
            width: 100%;
            height: auto;
        }
        footer {
            text-align: center;
            padding: 20px 0;
            background-color: #f8f9fa; /* Light background for footer */
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<nav>
    <label class="logo">Kamanja Family</label>
    <ul>
       <li><a href="homepage.php">Home</a></li> 
        <li><a href="explore.php">Explore</a></li>
        <li><a href="search.php">Search</a></li>
        <li><a href="">Family Tree</a></li>
        <li><a href="login.php" class="btn btn-success">Adm</a></li>
    </ul>
</nav>

<main>
    <center>
        <h1>Moments</h1>
    </center>

    <div class="container">
        <div class="row">
            <?php while ($info = $result->fetch_assoc()) { ?>
                <div class="col-md-4 news-item">
                    <img class="news-image" src="<?php echo htmlspecialchars($info['image']); ?>" alt="Moment Image">
                    <div class="news-description">
                        <h3><?php echo htmlspecialchars($info['description']); ?></h3>
                    </div>
                    <?php if (!empty($info['video'])): ?>
                        <video controls style="width:100%; height:auto;">
                            <source src="<?php echo htmlspecialchars($info['video']); ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    <?php endif; ?>
                </div>
            <?php } ?>
        </div>
    </div>
</main>

<footer>
    <h3 class="footer_text">All @copyright reserved</h3>
</footer>

</body>
</html>
