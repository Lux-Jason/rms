<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qiao's Handmade Menu</title>
    <link rel="icon" href="image/webpage/qiao_logo.svg" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>

<?php include 'connectdb.php'; ?>

<!-- Header section remains the same -->

<div id="reserved_area" style="height: 32px;"></div>

<div class="selector-outside">
    <!-- Selector container remains the same -->
</div>

<!-- Login Modal remains the same -->

<!-- Fetch and display dishes from the database -->
<?php
$dishesPerPage = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $dishesPerPage;

$filters = array();
$params = array();

// Handle search keyword
if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
    $filters[] = "dish_name LIKE :keyword OR description LIKE :keyword";
    $params[':keyword'] = '%' . $_GET['keyword'] . '%';
}

// Handle notes filter
if (isset($_GET['notes']) && $_GET['notes'] != '') {
    $filters[] = "note = :note";
    $params[':note'] = $_GET['notes'];
}

// Handle price filter
if (isset($_GET['price']) && $_GET['price'] != '') {
    switch ($_GET['price']) {
        case 'l10':
            $filters[] = "price < 10";
            break;
        case '1020':
            $filters[] = "price BETWEEN 10 AND 20";
            break;
        // Add cases for other price ranges
    }
}

// Handle content filter and dish type filter similarly

// Construct the SQL query
$sql = "SELECT * FROM menu";
if (!empty($filters)) {
    $sql .= " WHERE " . implode(" AND ", $filters);
}
$sql .= " LIMIT :offset, :limit";

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $dishesPerPage, PDO::PARAM_INT);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total number of dishes
$totalSql = "SELECT COUNT(*) AS total FROM menu";
if (!empty($filters)) {
    $totalSql .= " WHERE " . implode(" AND ", $filters);
}
$totalStmt = $conn->prepare($totalSql);
foreach ($params as $key => $value) {
    $totalStmt->bindValue($key, $value);
}
$totalStmt->execute();
$total = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($total / $dishesPerPage);
?>
<!-- Header -->
<header>
    <div class="logo-container">
        <div style="width: 20px;"></div>
        <a href="index.html"><img src="image/webpage/qiao_logo.svg" alt="Qiao's Handmade" class="logo" oncontextmenu="return false" onselectstart="return false" ondragstart="return false" onbeforecopy="return false" oncopy=document.selection.empty() onselect=document.selection.empty()></a>
        <div style="width: 20px;"></div>
        <nav>
            <a href="index.html" class="button">Home</a>
            <a href="menu.html" class="button" style="color: #007bff; background-color: white;">All Dishes</a>
            <a href="about.html" class="button">Reserve a table</a>
        </nav>
    </div>
    <div>
        <button class="button login-btn" onclick="loginNotice()">Login</button>
        <a href="#" class="button register-btn">register</a>
    </div>
</header>

<div id="reserved_area" style="height: 32px;"></div>

<div class="selector-outside">
    <div class="selector-container">
        <div class="dropdown">
            <button class="dropbtn">Notes&#8192;<span class="triangle-arrow">&#9652;</span>&#8192;</button>
            <div class="dropdown-content">
                <a href="#" data-category="notes" data-value="">All</a>
                <a href="#" data-category="notes" data-value="rd">recommended dishes</a>
                <a href="#" data-category="notes" data-value="hpd">highly preferred dishes</a>
                <a href="#" data-category="notes" data-value="ssd">specially selected dishes</a>
            </div>
        </div>

        <div class="dropdown">
            <button class="dropbtn">Price&#8192;<span class="triangle-arrow">&#9652;</span>&#8192;</button>
            <div class="dropdown-content">
                <a href="#" data-category="price" data-value="l10">less than $10</a>
                <a href="#" data-category="price" data-value="1020">$10-$20</a>
                <a href="#" data-category="price" data-value="2030">$20-$30</a>
                <a href="#" data-category="price" data-value="3040">$30-$40</a>
                <a href="#" data-category="price" data-value="4050">$40-$50</a>
                <a href="#" data-category="price" data-value="m50">more than $50</a>
            </div>
        </div>

        <div class="dropdown">
            <button class="dropbtn">Content&#8192;<span class="triangle-arrow">&#9652;</span>&#8192;</button>
            <div class="dropdown-content">
                <a href="#" data-category="content" data-value="">All</a>
                <a href="#" data-category="content" data-value="l100">less than 100g</a>
                <a href="#" data-category="content" data-value="100200">100g to 200g</a>
                <a href="#" data-category="content" data-value="200500">200g to 500g</a>
                <a href="#" data-category="content" data-value="m500">more than 500g</a>
                <a href="#" data-category="content" data-value="other">others</a>
            </div>
        </div>

        <div class="dropdown">
            <button class="dropbtn">Dish Type&#8192;<span class="triangle-arrow">&#9652;</span>&#8192;</button>
            <div class="dropdown-content">
                <a href="#" data-category="type" data-value="cd">Chinese Food</a>
                <a href="#" data-category="type" data-value="wf">Western Food</a>
                <a href="#" data-category="type" data-value="if">Indian Food</a>
                <a href="#" data-category="type" data-value="other">Other types</a>
            </div>
        </div>

        <div class="search-container-stock">
            <div class="keyword-search">
                <input type="text" id="keyword" placeholder="Search Keywords...">
                <button id="search-btn">Search</button>
            </div>
        </div>
    </div>
</div>

<!-- Login Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeLoginModal()">&times;</span>
        <h2>Login</h2>
        <form action="#" method="post">
            <input type="text" name="username" placeholder="User Name" id="username" onfocus="inputFocus(this)" required>
            <input type="password" name="password" placeholder="Password" id="password" onfocus="inputFocus(this)" required>
            <button type="submit">Login</button>
        </form>
        <p><a href="#">Forget password? </a></p>
        <p>Haven't got an account? <a href="#">register</a></p>
    </div>
</div>
<!-- Display dishes -->
<section class="dishes-display">
    <div class="dishes-container" style="margin-left: 3%; margin-right: 3%;">
        <?php foreach ($dishes as $dish): ?>
            <div class="dish">
                <img src="<?php echo $dish['image']; ?>" alt="<?php echo $dish['dish_name']; ?>" oncontextmenu="return false" onselectstart="return false" ondragstart="return false" onbeforecopy="return false" oncopy="document.selection.empty()" onselect="document.selection.empty()">
                <div class="dish-info">
                    <div class="name"><?php echo $dish['dish_name']; ?></div>
                    <div class="price"><?php echo '$' . $dish['price']; ?></div>
                    <button class="add-to-cart-button">Add to Cart</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Page Navigation -->
<section class="page-navigation">
    <div class="pagination">
        <?php if ($page > 1): ?>
            <button class="page-number prev" onclick="window.location.href='menu.php?page=<?php echo $page - 1; ?>' + '<?php echo http_build_query($_GET, '', '&', PHP_URL_QUERY); ?>'">&laquo;</button>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <button class="page-number <?php if ($i == $page) echo 'current-page'; ?>" onclick="window.location.href='menu.php?page=<?php echo $i; ?>' + '<?php echo http_build_query($_GET, '', '&', PHP_URL_QUERY); ?>'"><?php echo $i; ?></button>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <button class="page-number next" onclick="window.location.href='menu.php?page=<?php echo $page + 1; ?>' + '<?php echo http_build_query($_GET, '', '&', PHP_URL_QUERY); ?>'">&raquo;</button>
        <?php endif; ?>
    </div>
</section>

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

<script>
    let slideIndex = 0;
    showSlides();

    function showSlides() {
        let i;
        let slides = document.getElementsByClassName("mySlides");
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex > slides.length) {slideIndex = 1}
        slides[slideIndex-1].style.display = "block";
        setTimeout(showSlides, 3000); // Change image every 3 seconds
    }

    function loginNotice() {
        document.getElementById("loginModal").style.display = "block";
    }

    function closeLoginModal() {
        document.getElementById("loginModal").style.display = "none";
    }

    function inputFocus(input) {
        document.getElementById("username").style.backgroundColor = "white";
        document.getElementById("password").style.backgroundColor = "white";
    }

    let currentPage = 1;
    const totalPages = 5; // let page sum be 5, this can be finalized by calculating directly

    function changePage(direction) {
        currentPage += direction;
        if (currentPage < 1) {
            currentPage = 1;
        } else if (currentPage > totalPages) {
            currentPage = totalPages;
        }
        document.getElementById("currentPage").textContent = currentPage;
        loadDishesForPage(currentPage);
    }

    // Only finished the turning pages function, but ever written the show next page's dishes function
</script>
</body>
</html>