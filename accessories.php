<?php
// Load helpers and protect page
require_once '../includes/functions.php';
requireLogin();

$userId = getUserId();

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    addToCart($userId, (int) $_POST['product_id']);
    header('Location: accessories.php?added=1');
    exit;
}

// Fetch accessories from DB
$stmt = getDB()->prepare(
    'SELECT p.* FROM products p
     JOIN categories c ON c.id = p.category_id
     WHERE c.name = "Accessories"'
);
$stmt->execute();
$products  = $stmt->fetchAll();
$cartCount = getCartCount();

// Map product names to their images
$accessoryImages = [
    'Ribbon'        => '/LabOfJoy/accessory1.jpeg',
    'Greeting Card' => '/LabOfJoy/accessory2.jpeg',
    'Mini Teddy'    => '/LabOfJoy/accessory3.jpeg',
    'Flower Decor'  => '/LabOfJoy/accessory4.jpeg',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lab of JOY - Accessories</title>
<link rel="stylesheet" href="accessories.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="cart_actions.js" defer></script>
</head>

<body>

<header class="siteHeader">
<h1>Lab of JOY 🎁</h1>
<p>Add the final touch to your gift box</p>

<nav class="navBar">

<a class="pill" href="categories.php">Categories</a>
<a class="pill" href="/LabOfJoy/munira/box-customization.php">Box Customization</a>

<a class="pill" href="/LabOfJoy/shahad/about.php">About Us</a>
<a class="pill" href="/LabOfJoy/jana/cart.php">Cart (<?= $cartCount ?>)</a>

</nav>
</header>

<?php if (isset($_GET['added'])): ?>
<p class="added-msg">✔ Item added to cart!</p>
<?php endif; ?>

<section class="accessoriesSection">

<h2 class="accessoriesTitle">Accessories 🎀</h2>
<p class="accessoriesText">Choose lovely accessories to make your gift box more special</p>

<div class="accessoriesGrid">

<?php foreach ($products as $p):
    $img = $accessoryImages[$p['name']] ?? null;
?>
<div class="accessoryCard">
    <?php if ($img): ?>
    <img src="<?= $img ?>" alt="<?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>" class="accessoryImg">
    <?php else: ?>
    <span class="accessoryIcon">🎀</span>
    <?php endif; ?>
    <div class="accessoryBody">
        <h2><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p><?= htmlspecialchars($p['description'] ?? 'A lovely accessory for your gift box.', ENT_QUOTES, 'UTF-8') ?></p>
        <p class="accessoryPrice"><?= number_format($p['price'], 2) ?> SAR</p>
        <form method="POST" action="accessories.php">
            <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
            <button type="submit" class="accessoryBtn">Add to Box</button>
        </form>
    </div>
</div>
<?php endforeach; ?>

</div>

</section>

<footer class="siteFooter">
<p>&copy; 2026 Lab of JOY</p>
</footer>

</body>
</html>
