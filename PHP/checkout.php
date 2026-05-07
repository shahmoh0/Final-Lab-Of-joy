<?php
// Load helpers and protect page
require_once '../includes/functions.php';
requireLogin();

$userId = getUserId();
$items  = getCartItems($userId);
$total  = getCartTotal($userId);

// Redirect if cart is empty
if (empty($items)) {
    header('Location: /LabOfJoy/jana/cart.php');
    exit;
}

$error = '';

// Validate delivery info and save order to DB
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['full_name']         ?? '');
    $email      = trim($_POST['email']             ?? '');
    $phone      = trim($_POST['phone']             ?? '');
    $city       = trim($_POST['city']              ?? '');
    $building   = trim($_POST['building_number']   ?? '');
    $street     = trim($_POST['street_name']       ?? '');
    $district   = trim($_POST['district']          ?? '');
    $postal     = trim($_POST['postal_code']       ?? '');
    $additional = trim($_POST['additional_number'] ?? '');

    if (strlen($name) < 2) {
        $error = 'Full name is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (!preg_match('/^05\d{8}$/', $phone)) {
        $error = 'Phone must be a valid Saudi number (05xxxxxxxx).';
    } elseif (!empty($postal) && !preg_match('/^\d{5}$/', $postal)) {
        $error = 'Postal code must be 5 digits.';
    } else {
        $db = getDB();
        $db->beginTransaction();
        try {
            $db->prepare(
                'INSERT INTO orders
                 (user_id,full_name,email,phone,city,building_number,
                  street_name,district,postal_code,additional_number,total_price)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?)'
            )->execute([$userId,$name,$email,$phone,$city,$building,
                        $street,$district,$postal,$additional,$total]);
            $orderId  = (int) $db->lastInsertId();
            $itemStmt = $db->prepare(
                'INSERT INTO order_items (order_id,product_id,quantity,unit_price) VALUES (?,?,?,?)'
            );
            foreach ($items as $item) {
                $itemStmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
            }
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout</title>
<link rel="stylesheet" href="style.css">
<script src="checkout.js" defer></script>
</head>

<body>

<div class="container">

<h1>Checkout</h1>
<p>Almost There ✨</p>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<p><strong>Order Total: <?= number_format($total, 2) ?> SAR</strong></p>

<form id="checkoutForm" method="POST" action="checkout.php" novalidate>

<h3>Delivery Information</h3>

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

<h3>Saudi National Address</h3>

<div class="form-group">
<label>Building Number</label>
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
<label>Additional Number</label>
<input type="text" name="additional_number" placeholder="6789"
       value="<?= htmlspecialchars($_POST['additional_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
</div>

<nav class="btns">
<button type="submit" class="btn btn-primary" style="width:100%">
  Place Order
</button>
</nav>

</form>

</div>

</body>
</html>
