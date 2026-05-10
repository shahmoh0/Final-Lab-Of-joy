<?php
// Load helpers and protect page
require_once '../includes/functions.php';
requireLogin();

$userId = getUserId();
$db     = getDB();
$error  = '';
$saved  = false;

// Load existing customization for this user
$existing = $db->prepare('SELECT * FROM box_customizations WHERE user_id=? ORDER BY created_at DESC LIMIT 1');
$existing->execute([$userId]);
$custom = $existing->fetch() ?: [];

// Validate and save customization choices
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_custom'])) {
    $budget    = (float) ($_POST['budget']    ?? 0);
    $occasion  = trim($_POST['occasion']  ?? '');
    $boxType   = trim($_POST['box_type']  ?? '');
    $packaging = trim($_POST['packaging'] ?? '');

    $allowed_occasions = ['Birthday','Graduation','Wedding','Thank You'];
    $allowed_box_types = ['Classic Box','Luxury Box','Wooden Box','Transparent Box'];
    $allowed_packaging = ['Ribbon','Flowers Wrap','Paper Wrap','No Wrap'];

    if ($budget < 50) {
        $error = 'Budget must be at least 50 SAR.';
    } elseif (!in_array($occasion, $allowed_occasions, true)) {
        $error = 'Invalid occasion selected.';
    } elseif (!in_array($boxType, $allowed_box_types, true)) {
        $error = 'Invalid box type selected.';
    } elseif (!in_array($packaging, $allowed_packaging, true)) {
        $error = 'Invalid packaging selected.';
    } else {
        if (!empty($custom['id'])) {
            $db->prepare('UPDATE box_customizations SET budget=?,occasion=?,box_type=?,packaging=? WHERE id=? AND user_id=?')
               ->execute([$budget, $occasion, $boxType, $packaging, $custom['id'], $userId]);
            $customId = $custom['id'];
        } else {
            $db->prepare('INSERT INTO box_customizations (user_id,budget,occasion,box_type,packaging) VALUES (?,?,?,?,?)')
               ->execute([$userId, $budget, $occasion, $boxType, $packaging]);
            $customId = (int) $db->lastInsertId();
        }
        $_SESSION['customization_id'] = $customId;
        $saved = true;
        if ($saved && isset($_POST['save_custom'])) {
        header('Location: message.php');
        exit;
        }
        $existing->execute([$userId]);
        $custom = $existing->fetch() ?: [];
    }
}

// Get cart items to show in the box preview
$cartItems = getCartItems($userId);
$cartTotal = getCartTotal($userId);
$budget    = (float) ($custom['budget'] ?? 200);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lab of JOY | Box Customization</title>
  <link rel="stylesheet" href="labofjoy.css" />
  <script src="customization.js" defer></script>
  <link rel="stylesheet" href="/LabOfJoy/accessibility.css">
  <script src="/LabOfJoy/accessibility.js" defer></script>
</head>
<body>
  <div class="wrap">
    <header class="top">
      <h1 class="brand">Lab of JOY 🎁
        <small>Your order is about to become a moment of JOY 💝</small>
        <br>
      </h1>

<nav class="navBar">


<a class="pill" href="/LabOfJoy/aljury/categories.php">Categories</a>
<a class="pill" href="/LabOfJoy/munira/box-customization.php">Box Customization</a>
<a class="pill" href="/LabOfJoy/shahad/about.php">About Us</a>
<a class="pill" href="/LabOfJoy/jana/cart.php">🛒 Cart </a>

</nav>

    </header>

    <main class="panel">
      <h2>Customize Your Box ✨</h2>
      <p>Pick your box type, packaging style, and keep your selections within your budget.</p>

      <?php if ($error): ?>
          <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>
      <?php if ($saved): ?>
          <p style="color:green;">✔ Customization saved!</p>
      <?php endif; ?>

      <div class="grid-2">
        <section class="card">
          <h3>1) Budget & Details</h3>

          <form id="customForm" method="POST" action="box-customization.php">
            <div class="row">
              <label class="field">
                <span>Budget (SAR)</span>
                <input type="number" name="budget" id="budgetInput"
                       min="50" step="10"
                       value="<?= htmlspecialchars($custom['budget'] ?? '200', ENT_QUOTES, 'UTF-8') ?>" required>
                <span class="field-error" id="budgetErr"></span>
              </label>

              <label class="field">
                <span>Occasion</span>
                <select name="occasion" required>
                  <option value="" disabled>Select</option>
                  <?php foreach (['Birthday','Graduation','Wedding','Thank You'] as $occ): ?>
                  <option value="<?= $occ ?>" <?= ($custom['occasion'] ?? 'Birthday') === $occ ? 'selected' : '' ?>><?= $occ ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
            </div>

            <h3 style="margin-top:6px;">2) Box Type</h3>
            <div class="options">
              <?php
              $boxTypes = ['Classic Box'=>['Soft &amp; simple','Popular'],'Luxury Box'=>['Premium feel','⭐'],'Wooden Box'=>['Elegant touch','New'],'Transparent Box'=>['Show the joy','Cute']];
              $bxIds    = ['Classic Box'=>'bx1','Luxury Box'=>'bx2','Wooden Box'=>'bx3','Transparent Box'=>'bx4'];
              foreach ($boxTypes as $val => [$desc, $badge]):
                  $checked = ($custom['box_type'] ?? 'Classic Box') === $val ? 'checked' : '';
              ?>
              <input class="sr" type="radio" id="<?= $bxIds[$val] ?>" name="box_type" value="<?= $val ?>" <?= $checked ?> required>
              <label class="option-card" for="<?= $bxIds[$val] ?>">
                <div><strong><?= $val ?></strong><br><em><?= $desc ?></em></div>
                <span class="badge"><?= $badge ?></span>
              </label>
              <?php endforeach; ?>
            </div>

            <h3 style="margin-top:14px;">3) Packaging Style</h3>
            <div class="options">
              <?php
              $packTypes = ['Ribbon'=>['Classic ribbon','🎀'],'Flowers Wrap'=>['Fresh look','🌸'],'Paper Wrap'=>['Minimal style','🧻'],'No Wrap'=>['Just the box','—']];
              $pkIds     = ['Ribbon'=>'pk1','Flowers Wrap'=>'pk2','Paper Wrap'=>'pk3','No Wrap'=>'pk4'];
              foreach ($packTypes as $val => [$desc, $badge]):
                  $checked = ($custom['packaging'] ?? 'Ribbon') === $val ? 'checked' : '';
              ?>
              <input class="sr" type="radio" id="<?= $pkIds[$val] ?>" name="packaging" value="<?= $val ?>" <?= $checked ?> required>
              <label class="option-card" for="<?= $pkIds[$val] ?>">
                <div><strong><?= $val ?></strong><br><em><?= $desc ?></em></div>
                <span class="badge"><?= $badge ?></span>
              </label>
              <?php endforeach; ?>
            </div>

            <div class="actions">
    <button class="btn ghost" type="submit" name="save_custom" value="1">Save Choices</button>
    
    <button type="submit" name="save_custom" value="1" class="btn" onclick="this.form.action='box-customization.php';">
        Save & Continue to Message →
    </button>
    
    <button class="btn ghost" type="reset">Reset Items</button>
</div>
          </form>

          <div class="summary-box" style="margin-top:12px;">
            <div class="tag">Budget: SAR <?= number_format($budget, 0) ?></div>
            <div class="tag" id="totalTag">Total: SAR <?= number_format($cartTotal, 2) ?></div>
            <div class="tag" id="remainTag">Remaining: SAR <?= number_format(max(0, $budget - $cartTotal), 2) ?></div>
          </div>
        </section>

        <aside class="card">
          <h3>Your Box Items</h3>

          <?php if (empty($cartItems)): ?>
            <p>No items yet. <a href="/LabOfJoy/aljury/categories.php">Browse categories</a>.</p>
          <?php else: ?>
          <div class="list">
            <?php foreach ($cartItems as $item): ?>
            <div class="item">
              <div class="meta">
                <b><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></b>
                <small>SAR <?= number_format($item['price'], 2) ?></small>
              </div>
              <div style="display:flex; gap:8px; align-items:center;">
                <form method="POST" action="/LabOfJoy/jana/cart.php" style="display:inline;">
                  <input type="hidden" name="remove_id" value="<?= (int)$item['id'] ?>">
                  <button class="btn ghost" type="submit" style="padding:9px 12px;">Remove</button>
                </form>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          <div class="total">
            <span>Total</span>
            <span>SAR <?= number_format($cartTotal, 2) ?></span>
          </div>
          <?php endif; ?>
        </aside>
      </div>

      <div class="footer">© Lab of Joy — Educational (No real payment/delivery)</div>
    </main>
  </div>
</body>
</html>
