<?php
// Load helpers and protect page
require_once '../includes/functions.php';
requireLogin();

$userId = getUserId();

// Handle remove single item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    removeFromCart((int) $_POST['remove_id'], $userId);
    header('Location: cart.php');
    exit;
}

// Handle update quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $cartId = (int) $_POST['cart_id'];
    $qty    = max(1, (int) $_POST['quantity']);
    getDB()->prepare('UPDATE cart_items SET quantity=? WHERE id=? AND user_id=?')
           ->execute([$qty, $cartId, $userId]);
    header('Location: cart.php');
    exit;
}

// Handle empty entire cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['empty_cart'])) {
    getDB()->prepare('DELETE FROM cart_items WHERE user_id=?')->execute([$userId]);
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
<link rel="stylesheet" href="/LabOfJoy/accessibility.css">
<script src="/LabOfJoy/accessibility.js" defer></script>
</head>

<body>

<div class="page">

<div class="topbar">
<div class="brand"><span>Lab of Joy 🎁</span></div>
<div class="badge">Your gift box is waiting 💗</div>
</div>

<div class="grid">

<div class="card">
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:6px;">
    <h2 class="h-title">Your Cart 🛒</h2>
    <?php if (!empty($items)): ?>
    <form method="POST" action="cart.php" id="emptyCartForm">
        <input type="hidden" name="empty_cart" value="1">
        <button type="submit" class="btn ghost" style="padding:6px 14px;font-size:0.82rem;color:#ff3b6b;border-color:#ff3b6b;">
            🗑 Empty Cart
        </button>
    </form>
    <?php endif; ?>
</div>
<p class="h-sub">Review, update quantities, or remove gifts.</p>

<?php if (empty($items)): ?>
<div class="empty-cart">
<h3>Your cart is empty 🛒</h3>
<p>Start adding gifts to create your joyful box.</p>
<div class="btns">
<a href="/LabOfJoy/aljury/categories.php" class="btn btn-primary">Start Shopping</a>
</div>
</div>

<?php else: ?>
<table class="cartTable">
<thead>
<tr>
  <th style="text-align:left;">Item</th>
  <th>Price</th>
  <th>Qty</th>
  <th>Subtotal</th>
  <th></th>
</tr>
</thead>
<tbody>
<?php foreach ($items as $item): ?>
<tr>
  <td><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></td>
  <td><?= number_format($item['price'], 2) ?> SAR</td>

  <!-- Quantity update form -->
  <td>
    <form method="POST" action="cart.php" class="qty-form">
      <input type="hidden" name="update_qty" value="1">
      <input type="hidden" name="cart_id" value="<?= (int)$item['id'] ?>">
      <div style="display:flex;align-items:center;gap:4px;">
        <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>"
               min="1" class="qty-input" aria-label="Quantity">
        <button type="submit" class="btn ghost btn-xs" title="Update">✓</button>
      </div>
    </form>
  </td>

  <td><?= number_format($item['subtotal'], 2) ?> SAR</td>

  <!-- Remove item form -->
  <td>
    <form method="POST" action="cart.php" class="remove-form">
      <input type="hidden" name="remove_id" value="<?= (int)$item['id'] ?>">
      <button type="submit" class="btn ghost btn-xs" style="color:#ff3b6b;" title="Remove">✕</button>
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
<p class="h-sub">Check your total before checkout.</p>

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
<nav class="btns" style="margin-top:14px;display:flex;flex-direction:column;gap:10px;">
    <a href="/LabOfJoy/lubna/checkout.php" class="btn btn-primary" style="width:100%;display:block;text-align:center;text-decoration:none;">
        🛍 Proceed to Checkout
    </a>
   
</nav>
<?php endif; ?>

</div>

</div>

</div>

</body>
</html>
