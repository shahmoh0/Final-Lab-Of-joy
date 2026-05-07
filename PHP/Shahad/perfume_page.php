<?php
// Load helpers and protect page
require_once '../includes/functions.php';
requireLogin();

$userId = getUserId();

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    addToCart($userId, (int) $_POST['product_id']);
    header('Location: perfume_page.php?added=1');
    exit;
}

// Fetch perfumes, filter by search if provided
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $stmt = getDB()->prepare(
        'SELECT p.* FROM products p
         JOIN categories c ON c.id = p.category_id
         WHERE c.name = "Perfume" AND p.name LIKE ?'
    );
    $stmt->execute(['%' . $search . '%']);
} else {
    $stmt = getDB()->prepare(
        'SELECT p.* FROM products p
         JOIN categories c ON c.id = p.category_id
         WHERE c.name = "Perfume"'
    );
    $stmt->execute();
}
$products  = $stmt->fetchAll();
$cartCount = getCartCount();

// Badge labels for specific products
$badges = [
    'Guerlain Insolence'         => 'Best Seller',
    'Prada Paradoxe Intense'     => 'Popular',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Perfumes - Lab of Joy</title>
  <link rel="stylesheet" href="perfum page.css">
  <script src="perfume_search.js" defer></script>
</head>
<body>

  <header class="siteHeader">
    <h1 class="Title">Perfume Collection</h1>
    <p class="tagline"><strong>Choose your favorite scent</strong></p>
  </header>

  <nav class="navBar">

<a class="pill" href="/LabOfJoy/aljury/categories.php">Categories</a>
<a class="pill" href="/LabOfJoy/munira/box-customization.php">Box Customization</a>
<a class="pill" href="about.php">About Us</a>
<a class="pill" href="/LabOfJoy/jana/cart.php">Cart (<?= $cartCount ?>)</a>

</nav>

<?php if (isset($_GET['added'])): ?>
  <p style="text-align:center;color:green;padding:8px;">✔ Added to cart!</p>
<?php endif; ?>

  <main class="container">

    <form method="GET" action="perfume_page.php" id="searchForm">
    <div class="searchBar">
      <span class="searchIcon">🔍</span>
      <input type="text" name="q" id="searchInput"
             placeholder="Search perfumes..."
             value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
    </div>
    </form>

    <div class="filters">
      <span>All</span>
      <span>Floral</span>
      <span>Sweet</span>
      <span>Soft</span>
      <span>Luxury</span>
    </div>

    <div class="perfumeGrid">

      <?php foreach ($products as $p): ?>
      <div class="perfumeCard">
        <?php if (isset($badges[$p['name']])): ?>
        <div class="badge"><?= $badges[$p['name']] ?></div>
        <?php endif; ?>
        <div class="heart">♡</div>
        <img src="images/<?= htmlspecialchars($p['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             alt="<?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>"
             onerror="this.style.display='none'">
        <h2><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="rating">★★★★★</p>
        <p class="price"><?= number_format($p['price'], 2) ?> SAR</p>
        <div class="cardButtons">
          <a href="perfume_detail.php?id=<?= (int)$p['id'] ?>" class="detailsBtn">View Details</a>
          <form method="POST" action="perfume_page.php" style="display:inline;">
            <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
            <button type="submit" class="cartBtn">Add to Cart</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>

      <?php if (empty($products)): ?>
        <p>No perfumes found for "<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>".</p>
      <?php endif; ?>

    </div>

  </main>

  <footer class="siteFooter">
    © Lab of Joy
  </footer>

</body>
</html>
