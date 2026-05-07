<?php
// Load DB and session helpers
require_once '../includes/db.php';
require_once '../includes/session.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /LabOfJoy/aljury/categories.php');
    exit;
}

$error = '';

// Validate input, register user, and start session
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['full_name']        ?? '');
    $email   = trim($_POST['email']            ?? '');
    $phone   = trim($_POST['phone']            ?? '');
    $pass    = $_POST['password']              ?? '';
    $confirm = $_POST['confirm_password']      ?? '';

    if (empty($name) || strlen($name) < 2) {
        $error = 'Full name must be at least 2 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (!empty($phone) && !preg_match('/^05\d{8}$/', $phone)) {
        $error = 'Phone must be a valid Saudi number (05xxxxxxxx).';
    } elseif (strlen($pass) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($pass !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $db    = getDB();
        $check = $db->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'This email is already registered.';
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $db->prepare('INSERT INTO users (full_name, email, phone, password) VALUES (?,?,?,?)')
               ->execute([$name, $email, $phone, $hash]);
            $userId = (int) $db->lastInsertId();
            setUserSession($userId, $email);
            header('Location: /LabOfJoy/aljury/categories.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lab of Joy - Sign Up</title>
<link rel="stylesheet" href="style.css">
<script src="signup.js" defer></script>
</head>

<body>

<div class="page">

<div class="topbar">
<div class="brand"><span>Lab of Joy 🎁</span></div>
<div class="badge">Create your account 💗</div>
</div>

<div class="card form-card">

<h2 class="h-title">Sign Up</h2>
<p class="h-sub">Create your account in seconds.</p>

<?php if ($error): ?>
    <p style="color:red;margin:0 0 10px;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<form id="signupForm" method="POST" action="signup.php" novalidate>

<div class="field">
<label>Full Name</label>
<input class="input" type="text" name="full_name"
       value="<?= htmlspecialchars($_POST['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
<span class="field-error" id="nameErr"></span>
</div>

<div class="row">

<div class="field">
<label>Email</label>
<input class="input" type="email" name="email"
       value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
<span class="field-error" id="emailErr"></span>
</div>

<div class="field">
<label>Phone</label>
<input class="input" type="tel" name="phone" placeholder="05xxxxxxxx"
       value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
<span class="field-error" id="phoneErr"></span>
</div>

</div>

<div class="row">

<div class="field">
<label>Password</label>
<input class="input" type="password" name="password" required>
<span class="field-error" id="passErr"></span>
</div>

<div class="field">
<label>Confirm Password</label>
<input class="input" type="password" name="confirm_password" required>
<span class="field-error" id="confirmErr"></span>
</div>

</div>

<nav class="btns">
  <ul>
    <li>
      <button type="submit" class="btn btn-primary">Create Account</button>
    </li>
  </ul>
</nav>

<p style="margin-top:10px">
Already have an account?
<a class="link" href="/LabOfJoy/fatimah/login.php">Login</a>
</p>

</form>

</div>

</div>

</body>
</html>
