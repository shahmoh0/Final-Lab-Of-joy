<?php
// Check session and get order ID
require_once '../includes/session.php';

if (!isLoggedIn() || !isset($_SESSION['last_order_id'])) {
    header('Location: /LabOfJoy/fatimah/home.php');
    exit;
}

// Read order ID once then clear it from session
$orderId = (int) $_SESSION['last_order_id'];
unset($_SESSION['last_order_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Success</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container success">

<h1>Order Successful 🎉</h1>

<h2>✔ Thank You!</h2>

<p>Your order has been placed successfully.</p>

<p>Order Number: <strong>#<?= $orderId ?></strong></p>

<p>We will contact you soon to confirm delivery.</p>

<br>

<a href="/LabOfJoy/aljury/categories.php" class="btn btn-primary">Back to Shop</a>

</div>

</body>
</html>
