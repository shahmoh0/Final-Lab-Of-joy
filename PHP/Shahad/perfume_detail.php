<?php
// Load helpers and protect page
require_once '../includes/functions.php';
requireLogin();

$userId    = getUserId();

// Get product ID from URL
$productId = (int) ($_GET['id'] ?? 0);

if (!$productId) {
    header('Location: perfume_page.php');
    exit;
}

// Fetch the perfume product from DB
$stmt = getDB()->prepare(
    'SELECT p.*, c.name AS category FROM products p
     JOIN categories c ON c.id = p.category_id
     WHERE p.id = ? AND c.name = "Perfume"'
);
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: perfume_page.php');
    exit;
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qty = max(1, min(10, (int) ($_POST['quantity'] ?? 1)));
    addToCart($userId, $productId, $qty);
    header('Location: perfume_detail.php?id=' . $productId . '&added=1');
    exit;
}

// Static product info for each perfume
$details = [
    'Guerlain Insolence' => [
        'tagline'     => 'Elegant floral fragrance',
        'description' => 'A soft and feminine floral perfume with a powdery and elegant scent.',
        'ingredients' => ['Red berries','Violet','Orange blossom','Iris'],
        'size'        => '75 ml',
    ],
    'YSL Libre Intense' => [
        'tagline'     => 'Bold warm floral fragrance',
        'description' => 'A rich and elegant floral perfume with a warm and bold scent, perfect for special occasions.',
        'ingredients' => ['Lavender essence','Orange blossom','Orchid accord'],
        'size'        => '90 ml',
    ],
    'Prada Paradoxe Intense' => [
        'tagline'     => 'Modern floral amber fragrance',
        'description' => 'A modern and elegant floral amber fragrance with a rich feminine scent and a sophisticated finish.',
        'ingredients' => ['Jasmine','Amber accord','Moss accord','Ambrofix'],
        'size'        => '90 ml',
    ],
    'Miss Dior Blooming Bouquet' => [
        'tagline'     => 'Fresh floral fragrance',
        'description' => 'A delicate and fresh floral fragrance inspired by blooming flowers and soft femininity.',
        'ingredients' => ['Peony','Damask rose','White musk','Bergamot'],
        'size'        => '100 ml',
    ],
];

$info = $details[$product['name']] ?? [
    'tagline'     => 'Luxury fragrance',
    'description' => 'A luxurious fragrance.',
    'ingredients' => [],
    'size'        => '—',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="miss (1).css">
  <link rel="stylesheet" href="/LabOfJoy/accessibility.css">
  <script src="/LabOfJoy/accessibility.js" defer></script>
</head>
<body>

<header class="Header">
  <h1 class="Title"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h1>
  <p class="line"><?= htmlspecialchars($info['tagline'], ENT_QUOTES, 'UTF-8') ?></p>
</header>

<main class="detailsContainer">

  <div class="productSection">

    <div class="productImages">
      <img src="images/<?= htmlspecialchars($product['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
           alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
           class="mainImage" onerror="this.style.display='none'">
    </div>

    <div class="productInfo">
      <h2>About the Fragrance</h2>
      <p><?= htmlspecialchars($info['description'], ENT_QUOTES, 'UTF-8') ?></p>

      <?php if (!empty($info['ingredients'])): ?>
      <h2>Ingredients</h2>
      <ul>
        <?php foreach ($info['ingredients'] as $ing): ?>
        <li><?= htmlspecialchars($ing, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>

      <h2>Size</h2>
      <p><?= htmlspecialchars($info['size'], ENT_QUOTES, 'UTF-8') ?></p>

      <h2>Price</h2>
      <p class="price"><?= number_format($product['price'], 2) ?> SAR</p>
    </div>

    <div class="cartCard">
      <h3>Add to Cart</h3>
      <?php if (isset($_GET['added'])): ?>
        <p style="color:green;">✔ Added to cart!</p>
      <?php endif; ?>
      <form method="POST" action="perfume_detail.php?id=<?= $productId ?>">
        <label for="quantity">Quantity</label>
        <input type="number" id="quantity" name="quantity" min="1" max="10" value="1">
        <button type="submit" class="cartBtn">Add to Cart</button>
      </form>
      <br>
      <a href="/LabOfJoy/lubna/checkout.php" class="cartBtn" style="display:block;text-align:center;margin-bottom:8px;">Proceed to Checkout</a>
      <a href="perfume_page.php" style="font-size:0.9rem;">← Back to Perfumes</a>
    </div>

  </div>

</main>

<footer class="siteFooter">
  © Lab of Joy
</footer>

</body>
</html>
