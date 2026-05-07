<?php

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0);
    session_start();
}

// Regenerate session ID to prevent fixation
function regenerateSession(): void {
    session_regenerate_id(true);
}

// Check if a user is logged in
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

// Get the logged-in user's ID
function getUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

// Store user data in session after login
function setUserSession(int $userId, string $email, int $isAdmin = 0): void {
    regenerateSession();
    $_SESSION['user_id']   = $userId;
    $_SESSION['email']     = $email;
    $_SESSION['is_admin']  = $isAdmin;
    $_SESSION['last_activity'] = time();
}

// Check if the logged-in user is an admin
function isAdmin(): bool {
    return isset($_SESSION['is_admin']) && (bool) $_SESSION['is_admin'];
}

// Clear session and cookie on logout
function logoutUser(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

// Auto-logout after 30 minutes of inactivity
if (isLoggedIn() && isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > 1800) {
        logoutUser();
        header('Location: /LabOfJoy/fatimah/login.php?timeout=1');
        exit;
    }
}
$_SESSION['last_activity'] = time();
