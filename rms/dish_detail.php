<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dish Details</title>
    <link rel="icon" href="image/webpage/qiao_logo.svg" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        .container {
            width: 80%;
            margin: 0 auto;
        }
        .dish {
            border: 1px solid #ccc;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            align-items: center; 
            width: 100%;
        }
        .dish img {
            margin-right: 20px;
            margin-left: 20px;
            vertical-align: middle;
            height: auto;
        }
    </style>
</head>
<body>
<?php
include 'connectdb.php';

// Get dish ID from URL parameter
$dish_id = $_GET['id'];

// Prepare and execute query to fetch dish details
$stmt = $conn->prepare("SELECT * FROM menu WHERE dish_id = :dish_id");
$stmt->bindParam(':dish_id', $dish_id);
$stmt->execute();
$dish = $stmt->fetch(PDO::FETCH_ASSOC);
?>
    <!-- Header -->
    <header>
        <div class="logo-container">
            <div style="width: 20px;"></div>
            <a href="index.html"><img src="image/webpage/qiao_logo.svg" alt="Qiao's Handmade" class="logo" oncontextmenu="return false" onselectstart="return false" ondragstart="return false" onbeforecopy="return false" oncopy=document.selection.empty() onselect=document.selection.empty()></a>
            <div style="width: 20px;"></div>
            <nav>
                <a href="index.html" class="button">Home</a>
                <a href="menu.php" class="button">All Dishes</a>
                <a href="reserve_tb.html" class="button">Reserve a table</a>
            </nav>
        </div>
        <div>
            <button class="button login-btn" onclick="loginNotice()">Login</button>
            <a href="#" class="button register-btn">register</a>
        </div>
    </header>

    <div id="reserved_area" style="height: 32px;"></div>

    <div class="container">
        <h1>Dish Details</h1>
        <div class="dish">
            <img src="<?php echo htmlspecialchars($dish['image_path']); ?>"
                 alt="<?php echo htmlspecialchars($dish['dish_name']); ?>"
                 style="width: 1265px;">
            <div>
                <h2><?php echo htmlspecialchars($dish['dish_name']); ?></h2>
                <p>Points: <?php echo htmlspecialchars($dish['points']); ?></p>
                <p>Introduction: <?php echo htmlspecialchars($dish['description']); ?></p>
                <p>Price: $<?php echo htmlspecialchars($dish['price']); ?></p>
                <button class="add-to-cart-button">Add to Cart</button>
            </div>
        </div>
    </div>

        <!-- Other Information -->
        <section class="other-information">
            <h2>About us</h2>
            <div class="info-container">
                <div class="info-box">
                    <h3>Running Time: </h3>
                    <p>Monday to Saturday<br>10:00 - 22:00</p>
                </div>
                <div class="info-box">
                    <h3>Contact us: </h3>
                    <p>Telephone: 19935820001</p>
                    <p>E-mail: info@qiaohandmade.com</p>
                </div>
                <div class="info-box">
                    <h3>Address: </h3>
                    <p>BNU-UIC 2nd D5B-A111</p>
                </div>
            </div>
        </section>
    
        <!-- Footer -->
        <footer>
            <div class="footer-content">
                <p>&copy; 2024 Qiao's Handmade. All rights reserved</p>
                <a href="admin.html">Admin Page</a>
            </div>
        </footer>

</body>
</html>