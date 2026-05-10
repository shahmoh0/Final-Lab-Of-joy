<?php
// Load DB and session helpers
require_once '../includes/db.php';
require_once '../includes/session.php';

// Redirect already logged-in users to their correct page
if (isLoggedIn()) {
    $dest = isAdmin() ? '/LabOfJoy/admin/index.php' : '/LabOfJoy/aljury/categories.php';
    header('Location: ' . $dest);
    exit;
}

$error = '';

// Validate credentials and start session
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $stmt = getDB()->prepare('SELECT id, email, password, is_admin FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            setUserSession($user['id'], $user['email'], (int)$user['is_admin']);
            // Send admin to dashboard, regular user to shop
            if ($user['is_admin']) {
                header('Location: /LabOfJoy/admin/index.php');
            } else {
                $redirect = $_GET['redirect'] ?? '/LabOfJoy/aljury/categories.php';
                header('Location: ' . $redirect);
            }
            exit;
        } else {
            $error = 'Incorrect email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Lab of Joy</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/LabOfJoy/fatimah/Home%20%26%20Login.css">
<script src="login.js" defer></script>
<link rel="stylesheet" href="/LabOfJoy/accessibility.css">
<script src="/LabOfJoy/accessibility.js" defer></script>
</head>

<body>

<header class="siteHeader">
<h1 class="brandName">🎁 Lab of Joy</h1>
<p class="tagline">Welcome back! Let's create something special 💝</p>
</header>

<main class="container">

<h2>🔐 Login to Your Account</h2>

<p>Sign in to start creating your personalized gift box. 🎁</p>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
<?php if (isset($_GET['timeout'])): ?>
    <p style="color:orange;">Session expired. Please log in again.</p>
<?php endif; ?>

<form id="loginForm" method="POST" action="login.php" novalidate>

<fieldset>

<legend>👤 Account Information</legend>

<p>
<label>📧 Email</label>
<input type="email" name="email" placeholder="Enter your email"
       value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
<span class="field-error" id="emailErr"></span>
</p>

<p>
<label>🔑 Password</label>
<input type="password" name="password" placeholder="Enter your password" required>
<span class="field-error" id="passErr"></span>
</p>
