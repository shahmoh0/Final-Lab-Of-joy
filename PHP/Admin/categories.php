<?php
// Guard: admins only
require_once 'guard.php';

$db  = getDB();
$msg = '';
$err = '';

// DELETE
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    // Prevent delete if products exist under this category
    $count = $db->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
    $count->execute([$id]);
    if ((int)$count->fetchColumn() > 0) {
        $err = 'Cannot delete: category has products. Remove them first.';
    } else {
        $db->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
        $msg = 'Category deleted.';
    }
}

//  ADD / EDIT submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $icon = trim($_POST['icon'] ?? '');
    $editId = (int) ($_POST['edit_id'] ?? 0);

    // Validate
    if (strlen($name) < 2) {
        $err = 'Name must be at least 2 characters.';
    } else {
        if ($editId) {
            // Update existing category
            $db->prepare('UPDATE categories SET name=?, icon=? WHERE id=?')
               ->execute([$name, $icon, $editId]);
            $msg = 'Category updated.';
        } else {
            // Insert new category
            $db->prepare('INSERT INTO categories (name, icon) VALUES (?,?)')
               ->execute([$name, $icon]);
            $msg = 'Category added.';
        }
    }
}

// Fetch all categories with product count
$categories = $db->query(
    'SELECT c.*, COUNT(p.id) AS product_count
     FROM categories c
     LEFT JOIN products p ON p.category_id = c.id
     GROUP BY c.id ORDER BY c.id'
)->fetchAll();

// If editing, load the row
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Categories — Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin.css">
  <script src="admin.js" defer></script>
</head>
<body>

<header class="adminHeader">
  <h1>🎁 Lab of Joy — Admin</h1>
  <p>Manage Categories</p>
</header>

<nav class="navBar">
  <a class="pill" href="index.php">Dashboard</a>
  <a class="pill active" href="categories.php">Categories</a>
  <a class="pill" href="products.php">Products</a>
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
      <h2><?= $editing ? 'Edit Category' : 'Add Category' ?></h2>
    </div>

    <form method="POST" action="categories.php" id="catForm">
      <?php if ($editing): ?>
        <input type="hidden" name="edit_id" value="<?= (int)$editing['id'] ?>">
      <?php endif; ?>

      <div style="display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end;">
        <div class="formGroup" style="flex:1;min-width:180px;">
          <label>Category Name</label>
          <input type="text" name="name" required
                 value="<?= htmlspecialchars($editing['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <span class="fieldErr" id="nameErr"></span>
        </div>
        <div class="formGroup" style="width:100px;">
          <label>Icon (emoji)</label>
          <input type="text" name="icon" maxlength="5"
                 value="<?= htmlspecialchars($editing['icon'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div style="display:flex;gap:8px;margin-bottom:14px;">
          <button type="submit" class="btn btn-primary"><?= $editing ? 'Update' : 'Add' ?></button>
          <?php if ($editing): ?>
            <a href="categories.php" class="btn btn-ghost">Cancel</a>
          <?php endif; ?>
        </div>
      </div>
    </form>
  </div>

  <!-- Categories table -->
  <div class="section">
    <div class="sectionHead"><h2>All Categories</h2></div>

    <?php if (empty($categories)): ?>
      <p style="opacity:.6;">No categories yet.</p>
    <?php else: ?>
    <table class="adminTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Icon</th>
          <th>Name</th>
          <th>Products</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $cat): ?>
        <tr>
          <td><?= (int)$cat['id'] ?></td>
          <td style="font-size:1.5rem;"><?= htmlspecialchars($cat['icon'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= (int)$cat['product_count'] ?></td>
          <td style="display:flex;gap:8px;">
            <a href="categories.php?edit=<?= (int)$cat['id'] ?>" class="btn btn-ghost btn-sm">Edit</a>
            <a href="categories.php?delete=<?= (int)$cat['id'] ?>"
               class="btn btn-danger btn-sm confirm-delete"
               data-name="<?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>">Delete</a>
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
