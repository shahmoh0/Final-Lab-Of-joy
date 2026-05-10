<?php
// Guard: admins only
require_once 'guard.php';

$db  = getDB();
$msg = '';
$err = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId   = (int) $_POST['order_id'];
    $newStatus = $_POST['status'] ?? '';
    $allowed   = ['pending', 'confirmed', 'delivered'];

    if (in_array($newStatus, $allowed, true)) {
        $db->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$newStatus, $orderId]);
        $msg = 'Order #' . $orderId . ' status updated to ' . ucfirst($newStatus) . '.';
    } else {
        $err = 'Invalid status value.';
    }
}

// Handle DELETE
if (isset($_GET['delete'])) {
    $db->prepare('DELETE FROM orders WHERE id = ?')->execute([(int)$_GET['delete']]);
    $msg = 'Order deleted.';
}

// Fetch single order detail view
$viewOrder = null;
$orderItems = [];
if (isset($_GET['view'])) {
    $stmt = $db->prepare('SELECT * FROM orders WHERE id = ?');
    $stmt->execute([(int)$_GET['view']]);
    $viewOrder = $stmt->fetch();

    if ($viewOrder) {
        // Fetch order line items
        $iStmt = $db->prepare(
            'SELECT oi.quantity, oi.unit_price, p.name
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ?'
        );
        $iStmt->execute([(int)$_GET['view']]);
        $orderItems = $iStmt->fetchAll();
    }
}

// Fetch all orders
$orders = $db->query(
    'SELECT o.id, o.full_name, o.email, o.phone, o.total_price, o.status, o.created_at
     FROM orders o ORDER BY o.created_at DESC'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders — Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin.css">
  <script src="admin.js" defer></script>
  <link rel="stylesheet" href="/LabOfJoy/accessibility.css">
  <script src="/LabOfJoy/accessibility.js" defer></script>
</head>
<body>

<header class="adminHeader">
  <h1>🎁 Lab of Joy — Admin</h1>
  <p>Manage Orders</p>
</header>

<nav class="navBar">
  <a class="pill" href="index.php">Dashboard</a>
  <a class="pill" href="categories.php">Categories</a>
  <a class="pill" href="products.php">Products</a>
  <a class="pill active" href="orders.php">Orders</a>
  <a class="pill" href="users.php">Users</a>
  <a class="pill" href="/LabOfJoy/aljury/categories.php">← Back to Site</a>
  <a class="pill" href="/LabOfJoy/fatimah/logout.php">Logout</a>
</nav>

<div class="adminPanel">

  <?php if ($msg): ?><div class="alert alert-ok"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-err"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

  <!-- Order detail view -->
  <?php if ($viewOrder): ?>
  <div class="section">
    <div class="sectionHead">
      <h2>Order #<?= (int)$viewOrder['id'] ?> Details</h2>
      <a href="orders.php" class="btn btn-ghost btn-sm">← Back to Orders</a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
      <div>
        <p><strong>Customer:</strong> <?= htmlspecialchars($viewOrder['full_name'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($viewOrder['email'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($viewOrder['phone'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>City:</strong> <?= htmlspecialchars($viewOrder['city'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
      </div>
      <div>
        <p><strong>Street:</strong> <?= htmlspecialchars($viewOrder['street_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>District:</strong> <?= htmlspecialchars($viewOrder['district'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Postal Code:</strong> <?= htmlspecialchars($viewOrder['postal_code'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($viewOrder['created_at'], ENT_QUOTES, 'UTF-8') ?></p>
      </div>
    </div>

    <!-- Order items -->
    <?php if (!empty($orderItems)): ?>
    <table class="adminTable" style="margin-bottom:16px;">
      <thead>
        <tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr>
      </thead>
      <tbody>
        <?php foreach ($orderItems as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= (int)$item['quantity'] ?></td>
          <td><?= number_format($item['unit_price'], 2) ?> SAR</td>
          <td><?= number_format($item['quantity'] * $item['unit_price'], 2) ?> SAR</td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>

    <p style="font-weight:700;font-size:1.05rem;">
      Total: <?= number_format($viewOrder['total_price'], 2) ?> SAR &nbsp;|&nbsp;
      Status: <span class="badge badge-<?= $viewOrder['status'] ?>"><?= ucfirst($viewOrder['status']) ?></span>
    </p>

    <!-- Update status form -->
    <form method="POST" action="orders.php" style="margin-top:16px;display:flex;gap:10px;align-items:center;">
      <input type="hidden" name="order_id" value="<?= (int)$viewOrder['id'] ?>">
      <select name="status" style="padding:8px 12px;border-radius:12px;border:1px solid #ddd;">
        <?php foreach (['pending','confirmed','delivered'] as $s): ?>
        <option value="<?= $s ?>" <?= $viewOrder['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
    </form>
  </div>
  <?php endif; ?>

  <!-- Orders table -->
  <div class="section">
    <div class="sectionHead"><h2>All Orders (<?= count($orders) ?>)</h2></div>

    <?php if (empty($orders)): ?>
      <p style="opacity:.6;">No orders yet.</p>
    <?php else: ?>
    <table class="adminTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Customer</th>
          <th>Email</th>
          <th>Total</th>
          <th>Status</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
          <td>#<?= (int)$o['id'] ?></td>
          <td><?= htmlspecialchars($o['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($o['email'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= number_format($o['total_price'], 2) ?> SAR</td>
          <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
          <td><?= htmlspecialchars(substr($o['created_at'], 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
          <td style="display:flex;gap:6px;">
            <a href="orders.php?view=<?= (int)$o['id'] ?>" class="btn btn-ghost btn-sm">View</a>
            <a href="orders.php?delete=<?= (int)$o['id'] ?>"
               class="btn btn-danger btn-sm confirm-delete"
               data-name="Order #<?= (int)$o['id'] ?>">Delete</a>
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
