<?php
// Load helpers and protect page
require_once '../includes/functions.php';
requireLogin();

// Fetch all categories from DB
$categories = getDB()->query('SELECT * FROM categories ORDER BY id')->fetchAll();
$cartCount  = getCartCount();

// Fetch live weather for Jubail from Open-Meteo API
$cityName = 'Jubail';
$weather  = getWeather($cityName);

// Extract hourly values at current hour
$currentHour  = (int) date('H');
$wHumidity    = $weather['hourly']['relativehumidity_2m'][$currentHour]      ?? null;
$wFeelsLike   = $weather['hourly']['apparent_temperature'][$currentHour]      ?? null;
$wRainChance  = $weather['hourly']['precipitation_probability'][$currentHour] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab of JOY - Categories</title>
    <link rel="stylesheet" href="categories.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/LabOfJoy/accessibility.css">
    <script src="/LabOfJoy/accessibility.js" defer></script>
</head>
<body>

<header class="siteHeader">
    <h1>Lab of JOY 🎁</h1>
    <p>Select a category to start building your gift box</p>

    <!-- Compact weather badge in header -->
    <?php if ($weather):
        $cur   = $weather['current_weather'];
        $code  = (int) $cur['weathercode'];
        $emoji = getWeatherEmoji($code);
        $desc  = getWeatherDesc($code);
    ?>
    <a href="/LabOfJoy/weather.php" class="weatherBadge" title="Click for full weather details">
        <span class="wbEmoji"><?= $emoji ?></span>
        <span class="wbTemp"><?= round($cur['temperature']) ?>°C</span>
        <span class="wbSep">|</span>
        <span class="wbDesc"><?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?></span>
        <span class="wbSep">|</span>
        <span class="wbExtra">💧 <?= $wHumidity ?? '—' ?>%</span>
        <span class="wbExtra">🌬️ <?= $cur['windspeed'] ?> km/h</span>
        <span class="wbCity">📍 Jubail</span>
    </a>
    <?php endif; ?>

    <nav class="navBar">
 
    <a class="pill" href="categories.php">Categories</a>
    <a class="pill" href="/LabOfJoy/munira/box-customization.php">Box Customization</a>
    <a class="pill" href="/LabOfJoy/shahad/about.php">About Us</a>
    <a class="pill" href="/LabOfJoy/weather.php">🌤️ Weather</a>
    <a class="pill" href="/LabOfJoy/jana/cart.php">🛒 Cart (<?= $cartCount ?>)</a>
    <a class="pill" href="/LabOfJoy/fatimah/logout.php">Logout</a>
    
    </nav>
</header>

<section class="categoriesSection">
    <h2 class="categoriesTitle">Gift Categories</h2>

    <div class="categoriesGrid">
        <?php foreach ($categories as $cat):
            // Map each category to its product page
            $links = [
                'Chocolate'   => '/LabOfJoy/sarah/chocolate.php',
                'Perfume'     => '/LabOfJoy/shahad/perfume_page.php',
                'Accessories' => '/LabOfJoy/aljury/accessories.php',
                'Flowers'     => '/LabOfJoy/sarah/flowers.php',
            ];
            $href = $links[$cat['name']] ?? '#';
        ?>
        <div class="categoryCard">
            <?php
            // Map each category to its image
            $images = [
                'Chocolate'   => '/LabOfJoy/Category4.jpeg',
                'Perfume'     => '/LabOfJoy/Category2.jpeg',
                'Accessories' => '/LabOfJoy/Category3.jpeg',
                'Flowers'     => '/LabOfJoy/Category1.jpeg',
            ];
            $img = $images[$cat['name']] ?? null;
            ?>
            <?php if ($img): ?>
            <img src="<?= $img ?>" alt="<?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>" class="categoryImg">
            <?php else: ?>
            <span class="categoryIcon"><?= htmlspecialchars($cat['icon'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
            <h2><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></h2>
            <a href="<?= $href ?>" class="categoryBtn">View</a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<footer class="siteFooter">
    <p>&copy; 2026 Lab of JOY</p>
</footer>
<div id="helpSection">
    <button class="help-btn" onclick="toggleHelp()">?</button>
    <div id="helpMenu" class="help-menu">
        <h3>Need Help? ✨</h3>
        <p>Using our website is super easy! ✨</p>
        <ul>
            <li><b>1.</b> Choose a category.</li>
            <li><b>2.</b> Select your gifts.</li>
            <li><b>3.</b> Complete your joy box!</li>
        </ul>
        <small>For more information,</small>
        <a href="/LabOfJoy/shahad/about.php" style="color: #ff69b4; font-weight: bold; text-decoration: underline;">
                Visit About Us </a>
        <button class="close-btn" onclick="toggleHelp()">Close</button>
    </div>
</div>

<script>
function toggleHelp() {
    var menu = document.getElementById("helpMenu");
    if (menu.style.display === "block") {
        menu.style.display = "none";
    } else {
        menu.style.display = "block";
    }
}
</script>
</body>
</html>
