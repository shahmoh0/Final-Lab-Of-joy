<?php
// Load helpers and protect page
require_once '../includes/functions.php';
requireLogin();

$userId = getUserId();

// Handle remove item from cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    removeFromCart((int) $_POST['remove_id'], $userId);
    header('Location: cart.php');
    exit;
}

// Handle add item to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    addToCart($userId, (int) $_POST['product_id'], max(1, (int) ($_POST['quantity'] ?? 1)));
    header('Location: cart.php');
    exit;
}

// Fetch cart data for display
$items = getCartItems($userId);
$total = getCartTotal($userId);
$count = array_sum(array_column($items, 'quantity'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lab of Joy - Cart</title>
<link rel="stylesheet" href="style.css">
<script src="cart.js" defer></script>
</head>

<body>

<div class="page">

<div class="topbar">
<div class="brand"><span>Lab of Joy 🎁</span></div>
<div class="badge">Your gift box is waiting 💗</div>
</div>

<div class="grid">

<div class="card">
<h2 class="h-title">Your Cart</h2>
<p class="h-sub">Review your selected gifts before checkout.</p>

<?php if (empty($items)): ?>
<div class="empty-cart">
<h3>Your cart is empty 🛒</h3>
<p>Start adding gifts to create your joyful box.</p>

<div class="btns">
<a href="/LabOfJoy/aljury/categories.php" class="btn btn-primary">Start Shopping</a>
</div>
</div>

<?php else: ?>
<table style="width:100%;border-collapse:collapse;margin-top:10px;">
<thead>
<tr>
  <th style="text-align:left;padding:8px;">Item</th>
  <th style="padding:8px;">Qty</th>
  <th style="padding:8px;">Price</th>
  <th style="padding:8px;">Subtotal</th>
  <th style="padding:8px;"></th>
</tr>
</thead>
<tbody>
<?php foreach ($items as $item): ?>
<tr>
  <td style="padding:8px;"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></td>
  <td style="text-align:center;padding:8px;"><?= (int)$item['quantity'] ?></td>
  <td style="text-align:center;padding:8px;"><?= number_format($item['price'], 2) ?> SAR</td>
  <td style="text-align:center;padding:8px;"><?= number_format($item['subtotal'], 2) ?> SAR</td>
  <td style="padding:8px;">
    <form method="POST" action="cart.php" class="remove-form">
      <input type="hidden" name="remove_id" value="<?= (int)$item['id'] ?>">
      <button type="submit" class="btn ghost" style="padding:6px 12px;">Remove</button>
    </form>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>

</div>

<div class="card">

<h2 class="h-title">Summary</h2>
<p class="h-sub">Check your budget and total price.</p>

<div class="total-box">

<div class="total-line">
<span>Items</span>
<strong><?= $count ?></strong>
</div>

<div class="total-line">
<span>Total</span>
<strong><?= number_format($total, 2) ?> SAR</strong>
</div>

<div class="total-line">
<span>Status</span>
<strong class="<?= empty($items) ? 'note-bad' : 'note-ok' ?>">
    <?= empty($items) ? 'Cart Empty' : 'Ready' ?>
</strong>
</div>

</div>

<?php if (!empty($items)): ?>
<nav class="btns" style="margin-top:14px;">
<a href="/LabOfJoy/lubna/checkout.php" class="btn btn-primary" style="width:100%;display:block;text-align:center;text-decoration:none;">
  Proceed to Checkout
</a>
</nav>
<?php endif; ?>

</div>

</div>

</div>

</body>
</html>
