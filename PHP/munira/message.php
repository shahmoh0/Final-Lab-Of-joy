<?php
// Load helpers and protect page
require_once '../includes/functions.php';
requireLogin();

$userId   = getUserId();
$db       = getDB();
$error    = '';
$saved    = false;

// Redirect if no customization exists yet
$customId = $_SESSION['customization_id'] ?? null;

if (!$customId) {
    header('Location: box-customization.php');
    exit;
}

// Load existing message card if saved before
$msgRow = $db->prepare('SELECT * FROM message_cards WHERE customization_id=?');
$msgRow->execute([$customId]);
$card = $msgRow->fetch() ?: [];

// Validate and save message card
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text      = trim($_POST['message']    ?? '');
    $fontType  = trim($_POST['font_type']  ?? '');
    $fontColor = trim($_POST['font_color'] ?? '#222222');
    $cardStyle = trim($_POST['card_style'] ?? '');

    $allowedFonts  = ['Serif','Sans-serif','Cursive','Monospace'];
    $allowedStyles = ['Classic','Romantic','Birthday','Minimal'];

    if (empty($text)) {
        $error = 'Message cannot be empty.';
    } elseif (strlen($text) > 500) {
        $error = 'Message must be under 500 characters.';
    } elseif (!in_array($fontType, $allowedFonts, true)) {
        $error = 'Invalid font type.';
    } elseif (!preg_match('/^#[0-9a-fA-F]{6}$/', $fontColor)) {
        $error = 'Invalid font color.';
    } elseif (!in_array($cardStyle, $allowedStyles, true)) {
        $error = 'Invalid card style.';
    } else {
        // التحقق مما إذا كانت الرسالة موجودة مسبقاً لتحديثها أو إنشائها
        if (!empty($card['id'])) {
            $db->prepare('UPDATE message_cards SET message_text=?, font_type=?, font_color=?, card_style=? WHERE id=?')
               ->execute([$text, $fontType, $fontColor, $cardStyle, $card['id']]);
        } else {
            $db->prepare('INSERT INTO message_cards (customization_id, message_text, font_type, font_color, card_style) VALUES (?,?,?,?,?)')
               ->execute([$customId, $text, $fontType, $fontColor, $cardStyle]);
        }

        // التوجيه المباشر إلى صفحة التشك أوت بعد الحفظ الناجح
        header('Location: /LabOfJoy/lubna/checkout.php');
        exit;
    }
}

// Set preview values from saved card or defaults
$previewText  = htmlspecialchars($card['message_text'] ?? 'Happy Birthday! Wishing you joy and beautiful moments always.', ENT_QUOTES, 'UTF-8');
$previewFont  = htmlspecialchars($card['font_type']    ?? 'Serif', ENT_QUOTES, 'UTF-8');
$previewColor = htmlspecialchars($card['font_color']   ?? '#222222', ENT_QUOTES, 'UTF-8');
$previewStyle = htmlspecialchars($card['card_style']   ?? 'Classic', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lab of JOY | Message</title>
  <link rel="stylesheet" href="labofjoy.css" />
  <script src="message.js" defer></script>
</head>
<body>
  <div class="wrap">
    <header class="top">
      <h1 class="brand">Lab of JOY 💌
        <small>Make it personal — choose a card and write your message ✨</small>
      </h1>

      <nav class="pills">
        <a class="pill" href="box-customization.php">Box Customization</a>
        <a class="pill active" href="message.php">Message</a>
        <a class="pill" href="/LabOfJoy/lubna/checkout.php">Checkout</a>
        <a class="pill" href="/LabOfJoy/lubna/order_success.php">Order Success</a>
      </nav>
    </header>

    <main class="panel">
      <h2>Message Card ✨</h2>
      <p>Fill the form then Continue.</p>

      <?php if ($error): ?>
          <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>

      <div class="grid-2">
        <section class="card">
          <h3>1) Your Message</h3>

          <form id="messageForm" method="POST" action="message.php" novalidate>
            <label class="field">
              <span>Write Message</span>
              <textarea name="message" id="msgText" placeholder="Write something sweet..." required maxlength="500"><?= $previewText ?></textarea>
              <span class="field-error" id="msgErr"></span>
              <small id="charCount">0 / 500</small>
            </label>

            <div class="row">
              <label class="field">
                <span>Font Type</span>
                <select name="font_type" id="fontType" required>
                  <option value="" disabled>Select</option>
                  <?php foreach (['Serif','Sans-serif','Cursive','Monospace'] as $f): ?>
                  <option value="<?= $f ?>" <?= $previewFont === $f ? 'selected' : '' ?>><?= $f ?></option>
                  <?php endforeach; ?>
                </select>
              </label>

              <label class="field">
                <span>Font Color</span>
                <input type="color" name="font_color" id="fontColor" value="<?= $previewColor ?>">
              </label>
            </div>

            <h3 style="margin-top:6px;">2) Card Style</h3>
            <div class="options">
              <?php
              $styles = ['Classic'=>['Clean &amp; simple','✨'],'Romantic'=>['Soft pink','💗'],'Birthday'=>['Fun vibes','🎉'],'Minimal'=>['Neutral','—']];
              $csIds  = ['Classic'=>'cs1','Romantic'=>'cs2','Birthday'=>'cs3','Minimal'=>'cs4'];
              foreach ($styles as $val => [$desc, $badge]):
                  $checked = $previewStyle === $val ? 'checked' : '';
              ?>
              <input class="sr" type="radio" id="<?= $csIds[$val] ?>" name="card_style" value="<?= $val ?>" <?= $checked ?> required>
              <label class="option-card" for="<?= $csIds[$val] ?>">
                <div><strong><?= $val ?></strong><br><em><?= $desc ?></em></div>
                <span class="badge"><?= $badge ?></span>
              </label>
              <?php endforeach; ?>
            </div>

            <div class="actions">
              <a class="btn ghost" href="box-customization.php">← Back</a>
              <button type="submit" class="btn">Save & Continue to Checkout →</button>
            </div>
          </form>
        </section>

        <aside class="card">
          <h3>Saved Preview</h3>
          <p class="mini">This box shows a preview of your message.</p>

          <div class="item" style="flex-direction:column; align-items:flex-start; gap:10px;">
            <span class="badge" id="previewStyle"><?= $previewStyle ?></span>
            <div id="previewText"
                 style="font-weight:800; font-size:18px; line-height:1.3;
                        color:<?= $previewColor ?>; font-family:<?= $previewFont ?>;">
              <?= $previewText ?>
            </div>
            <div class="mini" id="previewFont">Font: <?= $previewFont ?></div>
          </div>
        </aside>
      </div>

      <div class="footer">© Lab of Joy — Educational (No real payment/delivery)</div>
    </main>
  </div>
</body>
</html>
