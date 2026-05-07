<?php
// Guard: admins only
require_once 'guard.php';
require_once '../includes/functions.php';

$db = getDB();

// Fetch live weather for admin dashboard
$weather     = getWeather('Jubail');
$currentHour = (int) date('H');
$wHumidity   = $weather['hourly']['relativehumidity_2m'][$currentHour]      ?? null;
$wFeelsLike  = $weather['hourly']['apparent_temperature'][$currentHour]      ?? null;
$wRainChance = $weather['hourly']['precipitation_probability'][$currentHour] ?? null;

// Fetch summary counts for stat cards
$totalProducts  = $db->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalCategories= $db->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$totalOrders    = $db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$totalUsers     = $db->query('SELECT COUNT(*) FROM users WHERE is_admin = 0')->fetchColumn();
$pendingOrders  = $db->query('SELECT COUNT(*) FROM orders WHERE status = "pending"')->fetchColumn();


// Fetch 5 latest orders for the preview table
$latestOrders = $db->query(
    'SELECT o.id, o.full_name, o.total_price, o.status, o.created_at
     FROM orders o ORDER BY o.created_at DESC LIMIT 5'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard — Lab of Joy</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin.css">
</head>
<body>

<header class="adminHeader">
  <h1>🎁 Lab of Joy — Admin</h1>
  <p>Welcome back! Manage your store below.</p>

  <!-- Compact weather badge — same as customer page -->
  <?php if ($weather):
      $cur   = $weather['current_weather'];
      $code  = (int) $cur['weathercode'];
      $emoji = getWeatherEmoji($code);
      $desc  = getWeatherDesc($code);
  ?>
  <a href="/LabOfJoy/weather.php" class="weatherBadge" title="Click for full weather details">
      <span class="wbEmoji"><?= $emoji ?></span>
      <span class="wbTemp"><?= round($cur['temperature']) ?>°C</span>
      <span class="wbSep">|</span>
      <span class="wbDesc"><?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?></span>
      <span class="wbSep">|</span>
      <span class="wbExtra">💧 <?= $wHumidity ?? '—' ?>%</span>
      <span class="wbExtra">🌬️ <?= $cur['windspeed'] ?> km/h</span>
      <span class="wbCity">📍 Jubail</span>
  </a>
  <?php endif; ?>
</header>

<!-- Navigation -->
<nav class="navBar">
  <a class="pill active" href="index.php">Dashboard</a>
  <a class="pill" href="categories.php">Categories</a>
  <a class="pill" href="products.php">Products</a>
  <a class="pill" href="orders.php">Orders</a>
  <a class="pill" href="users.php">Users</a>
  <a class="pill" href="/LabOfJoy/weather.php">🌤️ Weather</a>
  <a class="pill" href="/LabOfJoy/aljury/categories.php">← Back to Site</a>
  <a class="pill" href="/LabOfJoy/fatimah/logout.php">Logout</a>
</nav>

<div class="adminPanel">

  <!-- Stat cards -->
  <div class="statsRow">
    <div class="statCard">
      <div class="statNum"><?= (int)$totalProducts ?></div>
      <div class="statLabel">Products</div>
    </div>
    <div class="statCard">
      <div class="statNum"><?= (int)$totalCategories ?></div>
      <div class="statLabel">Categories</div>
    </div>
    <div class="statCard">
      <div class="statNum"><?= (int)$totalOrders ?></div>
      <div class="statLabel">Total Orders</div>
    </div>
    <div class="statCard">
      <div class="statNum"><?= (int)$pendingOrders ?></div>
      <div class="statLabel">Pending Orders</div>
    </div>
    <div class="statCard">
      <div class="statNum"><?= (int)$totalUsers ?></div>
      <div class="statLabel">Customers</div>
    </div>
    
  </div>

  <!-- Latest orders preview -->
  <div class="section">
    <div class="sectionHead">
      <h2>Latest Orders</h2>
      <a href="orders.php" class="btn btn-ghost btn-sm">View All</a>
    </div>

    <?php if (empty($latestOrders)): ?>
      <p style="opacity:.6;">No orders yet.</p>
    <?php else: ?>
    <table class="adminTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Customer</th>
          <th>Total</th>
          <th>Status</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($latestOrders as $o): ?>
        <tr>
          <td>#<?= (int)$o['id'] ?></td>
          <td><?= htmlspecialchars($o['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= number_format($o['total_price'], 2) ?> SAR</td>
          <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
          <td><?= htmlspecialchars(substr($o['created_at'], 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
          <td><a href="orders.php?view=<?= (int)$o['id'] ?>" class="btn btn-ghost btn-sm">View</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

 
  </div>

</div>

<footer class="siteFooter">© 2026 Lab of Joy — Admin Panel</footer>

</body>
</html>
