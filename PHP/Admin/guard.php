<?php
// Load DB and session, then verify admin access
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

// Redirect non-logged-in users to login
if (!isLoggedIn()) {
    header('Location: /LabOfJoy/fatimah/login.php');
    exit;
}

// Redirect non-admins back to the shop
if (!isAdmin()) {
    header('Location: /LabOfJoy/aljury/categories.php');
    exit;
}
