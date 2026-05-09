<?php
// Guard: admins only
require_once 'guard.php';

$db  = getDB();
$msg = '';
$err = '';

// DELETE
if (isset($_GET['delete'])) {
    $db->prepare('DELETE FROM products WHERE id = ?')->execute([(int)$_GET['delete']]);
    $msg = 'Product deleted.';
}

// ADD / EDIT submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catId  = (int)   ($_POST['category_id'] ?? 0);
    $name   = trim($_POST['name']        ?? '');
    $desc   = trim($_POST['description'] ?? '');
    $price  = (float) ($_POST['price']   ?? 0);
    $image  = trim($_POST['image']       ?? '');
    $editId = (int)   ($_POST['edit_id'] ?? 0);

    // Validate
    if (strlen($name) < 2) {
        $err = 'Product name must be at least 2 characters.';
    } elseif ($catId < 1) {
        $err = 'Please select a category.';
    } elseif ($price <= 0) {
        $err = 'Price must be greater than 0.';
    } else {
        if ($editId) {
            // Update existing product
            $db->prepare(
                'UPDATE products SET category_id=?,name=?,description=?,price=?,image=? WHERE id=?'
            )->execute([$catId, $name, $desc, $price, $image, $editId]);
            $msg = 'Product updated.';
        } else {
            // Insert new product
            $db->prepare(
                'INSERT INTO products (category_id,name,description,price,image) VALUES (?,?,?,?,?)'
            )->execute([$catId, $name, $desc, $price, $image]);
            $msg = 'Product added.';
        }
    }
}

// Fetch all products with category name
$products = $db->query(
    'SELECT p.*, c.name AS cat_name FROM products p
     JOIN categories c ON c.id = p.category_id
     ORDER BY c.name, p.name'
)->fetchAll();

// Fetch categories for the select dropdown
$categories = $db->query('SELECT * FROM categories ORDER BY name')->fetchAll();

// If editing, load the row
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products — Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin.css">
  <script src="admin.js" defer></script>
</head>
<body>

<header class="adminHeader">
  <h1>🎁 Lab of Joy — Admin</h1>
  <p>Manage Products</p>
</header>

<nav class="navBar">
  <a class="pill" href="index.php">Dashboard</a>
  <a class="pill" href="categories.php">Categories</a>
  <a class="pill active" href="products.php">Products</a>
  <a class="pill" href="orders.php">Orders</a>
  <a class="pill" href="users.php">Users</a>
  <a class="pill" href="/LabOfJoy/aljury/categories.php">← Back to Site</a>
  <a class="pill" href="/LabOfJoy/fatimah/logout.php">Logout</a>
</nav>

<div class="adminPanel">

  <?php if ($msg): ?><div class="alert alert-ok"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-err"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

  <!-- Add / Edit form -->
  <div class="section">
    <div class="sectionHead">
      <h2><?= $editing ? 'Edit Product' : 'Add Product' ?></h2>
    </div>

    <form method="POST" action="products.php" id="prodForm">
      <?php if ($editing): ?>
        <input type="hidden" name="edit_id" value="<?= (int)$editing['id'] ?>">
      <?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">

        <div class="formGroup">
          <label>Product Name</label>
          <input type="text" name="name" required
                 value="<?= htmlspecialchars($editing['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <span class="fieldErr" id="nameErr"></span>
        </div>

        <div class="formGroup">
          <label>Category</label>
          <select name="category_id" required>
            <option value="">Select category</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>"
              <?= ($editing['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="formGroup">
          <label>Price (SAR)</label>
          <input type="number" name="price" min="0.01" step="0.01" required
                 value="<?= htmlspecialchars($editing['price'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <span class="fieldErr" id="priceErr"></span>
        </div>

        <div class="formGroup">
          <label>Image filename</label>
          <input type="text" name="image" placeholder="e.g. choco1.jpg"
                 value="<?= htmlspecialchars($editing['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="formGroup" style="grid-column:1/-1;">
          <label>Description</label>
          <textarea name="description"><?= htmlspecialchars($editing['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

      </div>

      <div class="formActions">
        <?php if ($editing): ?>
          <a href="products.php" class="btn btn-ghost">Cancel</a>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary"><?= $editing ? 'Update Product' : 'Add Product' ?></button>
      </div>
    </form>
  </div>

  <!-- Products table -->
  <div class="section">
    <div class="sectionHead"><h2>All Products (<?= count($products) ?>)</h2></div>

    <?php if (empty($products)): ?>
      <p style="opacity:.6;">No products yet.</p>
    <?php else: ?>
    <table class="adminTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Category</th>
          <th>Price</th>
          <th>Image</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td><?= (int)$p['id'] ?></td>
          <td><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($p['cat_name'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= number_format($p['price'], 2) ?> SAR</td>
          <td style="font-size:0.8rem;opacity:.7;"><?= htmlspecialchars($p['image'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td style="display:flex;gap:8px;">
            <a href="products.php?edit=<?= (int)$p['id'] ?>" class="btn btn-ghost btn-sm">Edit</a>
            <a href="products.php?delete=<?= (int)$p['id'] ?>"
               class="btn btn-danger btn-sm confirm-delete"
               data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

</div>

<footer class="siteFooter">© 2026 Lab of Joy — Admin Panel</footer>

</body>
</html>
