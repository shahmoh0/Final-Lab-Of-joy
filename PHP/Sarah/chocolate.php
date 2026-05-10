<?php
// Load helpers and protect page
require_once '../includes/functions.php';
requireLogin();

$userId = getUserId();

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    addToCart($userId, (int) $_POST['product_id']);
    header('Location: chocolate.php?added=1');
    exit;
}

// Fetch chocolate products from DB
$stmt = getDB()->prepare(
    'SELECT p.* FROM products p
     JOIN categories c ON c.id = p.category_id
     WHERE c.name = "Chocolate"'
);
$stmt->execute();
$products  = $stmt->fetchAll();
$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chocolate - Lab of JOY</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit&family=Playfair+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="chocolate.css">
    <script src="product_actions.js" defer></script>
    <link rel="stylesheet" href="/LabOfJoy/accessibility.css">
    <script src="/LabOfJoy/accessibility.js" defer></script>
</head>

<body>

    <h1>Lab of JOY 🎁</h1>
    <h2>Chocolate Collection </h2>

<nav class="navBar">
<a class="pill" href="/LabOfJoy/aljury/categories.php">Categories</a>
<a class="pill" href="/LabOfJoy/munira/box-customization.php">Box Customization</a>
<a class="pill" href="/LabOfJoy/shahad/about.php">About Us</a>
<a class="pill" href="/LabOfJoy/jana/cart.php">🛒 Cart (<?= $cartCount ?>)</a>

</nav>

<?php if (isset($_GET['added'])): ?>
<p class="added-msg" style="text-align:center;color:green;padding:8px;">✔ Added to cart!</p>
<?php endif; ?>

    <div class="container">

        <?php foreach ($products as $p): ?>
        <div class="card">
            <img src="images/<?= htmlspecialchars($p['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 alt="<?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>"
                 onerror="this.style.display='none'">
            <h3><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p><?= number_format($p['price'], 2) ?> SAR</p>
            <form method="POST" action="chocolate.php">
                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                <button type="submit">Add to Cart</button>
            </form>
        </div>
        <?php endforeach; ?>

    </div>

    <footer>
    <p>© 2026 Lab of JOY</p>
    <p>Made with 💖 for chocolate lovers</p>
</footer>

</body>
</html>
