<?php
// Redirect logged-in users away from home
require_once '../includes/session.php';
if (isLoggedIn()) {
    header('Location: /LabOfJoy/aljury/categories.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lab of Joy</title>
    <link rel="stylesheet" href="/LabOfJoy/fatimah/Home%20%26%20Login.css">
</head>
<body>

<header>
    <h1>Lab of Joy 🎁</h1>
    <p>Create your perfect customized gift box ✨</p>
</header>

<main class="container">
    <h2>Welcome to Lab of Joy 🎉</h2>
    <p>
        Build your own gift box based on your budget and preferences.
        Choose gifts, packaging, and add a special message 💌
    </p>

    <nav class="siteNav">
        <a class="btnPrimary" href="login.php">Login 🔑</a>
        <a class="btnPrimary" href="/LabOfJoy/jana/signup.php">Sign Up 📝</a>
    </nav>
</main>

<footer>
    <p>© 2026 Lab of Joy</p>
</footer>

</body>
</html>
