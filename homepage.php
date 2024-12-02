
<?php

error_reporting(0);
session_start();
session_destroy();

if($_SESSION['message'])
{   
    $message=$_SESSION['message'];

    echo "<script type='text/javascript'> 
    
    alert('$message');
    
    </script>";
}

$host="localhost";
$user="root";
$password="";
$db="kamanja";


$data=mysqli_connect($host,$user,$password,$db);

$sql="SELECT * FROM news";


$result=mysqli_query($data,$sql);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Kamanja's Family</title>
    <link rel="stylesheet" href="styleshome.css?v=<?php echo time(); ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

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

     <div class="section1">
        <label class="img_text">The Kamanja  Brothers</label>
        <!-- <img class="main_img" src="familypic.png"> -->

     </div>

     <?php
// Array of images for the carousel
$images = [
    "coffee.jpg",
    "coffee2.jpg",
    "coffee3.jpg",
    "pinkcup.png",
    "coffee2.jpg",
    
];

?>
<div class="brotherbox">  
<div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php foreach ($images as $index => $image): ?>
            <div class="carousel-item <?php if ($index == 0) echo 'active'; ?>">
                <img src="<?php echo $image; ?>" class="d-block w-100" style="height: 450px; object-fit: contain; width: 700px;" alt="Slide <?php echo $index + 1; ?>">
            </div>
        <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

</div>



<div class="container">

<div class="row">
    <div class="col-md-4">
        <img class="welcome_img" src="familypic.png">
    </div>
    <div class="col-md-8">
        <h1>The Kamanja's</h1>
        <p>
        If men wish to live, then they are forced to kill others. The entire struggle for survival is a conquest of the means of existence, which in turn results in the elimination of others from these same sources of subsistence.
        If men wish to live, then they are forced to kill others. The entire struggle for survival is a conquest of the means of existence, which in turn results in the elimination of others from these same sources of subsistence. </p>

    </div>

</div>
</div>

<center>
<h1>Moments</h1>
</center>



<?php
// Array of images for the carousel
$images = [
    "coffee.jpg",
    "coffee2.jpg",
    "coffee3.jpg",
    "pinkcup.png",
    "coffee2.jpg",
    
];

?>

<div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php foreach ($images as $index => $image): ?>
            <div class="carousel-item <?php if ($index == 0) echo 'active'; ?>">
                <img src="<?php echo $image; ?>" class="d-block w-100" style="height: 500px; object-fit: contain; width: 700px;" alt="Slide <?php echo $index + 1; ?>">
            </div>
        <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>








<div class="container">

    <div class="row">


    </div>

</div>

<center>
<h1>Family News</h1>
</center>


<div class="containernews">
    <div class="row">
        <?php while ($info = $result->fetch_assoc()) { ?>
            <div class="col-md-4 news-item">
                <img class="news-image" src="<?php echo $info['image']; ?>" alt="News Image">
                <h3 class="news-topic"><?php echo htmlspecialchars($info['topic']); ?></h3>
                <h5 class="news-description"><?php echo htmlspecialchars($info['description']); ?></h5>
            </div>
        <?php } ?>
    </div>
</div>  





<br><br>
<footer>
    <h3 class="footer_text" >All @copyright reserved</h3>
</footer>


</body>
</html>
