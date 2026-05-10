<?php
// Load helpers and protect page
require_once '../includes/functions.php';
requireLogin();

$userId = getUserId();
$error  = '';

// Handle remove single item from cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    removeFromCart((int) $_POST['remove_id'], $userId);
    header('Location: checkout.php');
    exit;
}

// Handle update quantity from checkout page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $cartId = (int) $_POST['cart_id'];
    $qty    = max(1, (int) $_POST['quantity']);
    getDB()->prepare('UPDATE cart_items SET quantity=? WHERE id=? AND user_id=?')
           ->execute([$qty, $cartId, $userId]);
    header('Location: checkout.php');
    exit;
}

// Handle empty entire cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['empty_cart'])) {
    getDB()->prepare('DELETE FROM cart_items WHERE user_id=?')->execute([$userId]);
    header('Location: /LabOfJoy/jana/cart.php');
    exit;
}

// Handle BUY — save order and empty cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_now'])) {
    $name       = trim($_POST['full_name']         ?? '');
    $email      = trim($_POST['email']             ?? '');
    $phone      = trim($_POST['phone']             ?? '');
    $city       = trim($_POST['city']              ?? '');
    $building   = trim($_POST['building_number']   ?? '');
    $street     = trim($_POST['street_name']       ?? '');
    $district   = trim($_POST['district']          ?? '');
    $postal     = trim($_POST['postal_code']       ?? '');
    $additional = trim($_POST['additional_number'] ?? '');

    // Validate delivery fields
    if (strlen($name) < 2) {
        $error = 'Full name is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (!preg_match('/^05\d{8}$/', $phone)) {
        $error = 'Phone must be a valid Saudi number (05xxxxxxxx).';
    } elseif (!empty($postal) && !preg_match('/^\d{5}$/', $postal)) {
        $error = 'Postal code must be 5 digits.';
    } else {
        $items = getCartItems($userId);
        $total = getCartTotal($userId);

        if (empty($items)) {
            header('Location: /LabOfJoy/jana/cart.php');
            exit;
        }

        $db = getDB();
        $db->beginTransaction();
        try {
            // Insert order
            $db->prepare(
                'INSERT INTO orders
                 (user_id,full_name,email,phone,city,building_number,
                  street_name,district,postal_code,additional_number,total_price)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?)'
            )->execute([$userId,$name,$email,$phone,$city,$building,
                        $street,$district,$postal,$additional,$total]);
            $orderId = (int) $db->lastInsertId();

            // Insert order items snapshot
            $itemStmt = $db->prepare(
                'INSERT INTO order_items (order_id,product_id,quantity,unit_price) VALUES (?,?,?,?)'
            );
            foreach ($items as $item) {
                $itemStmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
            }

            // Empty the shopping cart
            $db->prepare('DELETE FROM cart_items WHERE user_id=?')->execute([$userId]);

            $db->commit();
            $_SESSION['last_order_id'] = $orderId;
            header('Location: order_success.php');
            exit;
        } catch (Exception $e) {
            $db->rollBack();
            error_log('Order failed: ' . $e->getMessage());
            $error = 'Something went wrong. Please try again.';
        }
    }
}

// Fetch current cart items
$items = getCartItems($userId);
$total = getCartTotal($userId);
$count = array_sum(array_column($items, 'quantity'));

// Redirect if cart is empty and not a buy attempt
if (empty($items) && !isset($_POST['buy_now'])) {
    header('Location: /LabOfJoy/jana/cart.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout — Lab of Joy</title>
<link rel="stylesheet" href="style.css">
<script src="checkout.js" defer></script>
<link rel="stylesheet" href="/LabOfJoy/accessibility.css">
<script src="/LabOfJoy/accessibility.js" defer></script>
</head>

<body>

<div class="checkoutWrap">

<!-- Left: Order summary with edit controls -->
<div class="checkoutLeft">

    <div class="checkoutCard">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:14px;">
            <h2 class="checkoutTitle">🛒 Your Order</h2>
            <form method="POST" action="checkout.php" id="emptyCartForm">
                <input type="hidden" name="empty_cart" value="1">
                <button type="submit" class="btn-link-danger">🗑 Empty Cart</button>
            </form>
        </div>

        <?php if ($error): ?>
        <p class="checkout-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if (!empty($items)): ?>
        <table class="orderTable">
            <thead>
                <tr>
                    <th>Product</th>
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

                <!-- Modify quantity -->
                <td>
                    <form method="POST" action="checkout.php" class="qty-form">
                        <input type="hidden" name="update_qty" value="1">
                        <input type="hidden" name="cart_id" value="<?= (int)$item['id'] ?>">
                        <div style="display:flex;align-items:center;gap:4px;">
                            <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>"
                                   min="1" class="qty-input" aria-label="Quantity">
                            <button type="submit" class="btn-update" title="Update quantity">✓</button>
                        </div>
                    </form>
                </td>

                <td><?= number_format($item['subtotal'], 2) ?> SAR</td>

                <!-- Delete item -->
                <td>
                    <form method="POST" action="checkout.php" class="remove-form">
                        <input type="hidden" name="remove_id" value="<?= (int)$item['id'] ?>">
                        <button type="submit" class="btn-remove" title="Remove item">✕</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="orderTotalRow">
            <span><?= $count ?> item(s)</span>
            <strong>Total: <?= number_format($total, 2) ?> SAR</strong>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- Right: Delivery form + Buy button -->
<div class="checkoutRight">

    <div class="checkoutCard">
        <h2 class="checkoutTitle">📦 Delivery Information</h2>

        <form id="checkoutForm" method="POST" action="checkout.php" novalidate>
            <input type="hidden" name="buy_now" value="1">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Enter your name"
                       value="<?= htmlspecialchars($_POST['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                <span class="field-error" id="nameErr"></span>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                <span class="field-error" id="emailErr"></span>
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" placeholder="05xxxxxxxx"
                       value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                <span class="field-error" id="phoneErr"></span>
            </div>

            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" placeholder="Enter your city"
                       value="<?= htmlspecialchars($_POST['city'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <h3 style="margin:14px 0 10px;font-size:0.95rem;font-weight:700;">Saudi National Address</h3>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div class="form-group">
                    <label>Building No.</label>
                    <input type="text" name="building_number" placeholder="1234"
                           value="<?= htmlspecialchars($_POST['building_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                    <label>Street Name</label>
                    <input type="text" name="street_name" placeholder="Street name"
                           value="<?= htmlspecialchars($_POST['street_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                    <label>District</label>
                    <input type="text" name="district" placeholder="District"
                           value="<?= htmlspecialchars($_POST['district'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                    <label>Postal Code</label>
                    <input type="text" name="postal_code" placeholder="12345"
                           value="<?= htmlspecialchars($_POST['postal_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <span class="field-error" id="postalErr"></span>
                </div>
                <div class="form-group">
                    <label>Additional No.</label>
                    <input type="text" name="additional_number" placeholder="6789"
                           value="<?= htmlspecialchars($_POST['additional_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <!-- BUY button -->
            <button type="submit" class="buyBtn" <?= empty($items) ? 'disabled' : '' ?>>
                🛍 Buy Now — <?= number_format($total, 2) ?> SAR
            </button>

            <a href="/LabOfJoy/jana/cart.php" class="backToCart">← Back to Cart</a>

        </form>
    </div>

</div>

</div>

</body>
</html>
