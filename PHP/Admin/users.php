<?php
// Guard: admins only
require_once 'guard.php';

$db  = getDB();
$msg = '';
$err = '';

//DELETE user
if (isset($_GET['delete'])) {
    $delId = (int) $_GET['delete'];
    // Prevent deleting yourself
    if ($delId === getUserId()) {
        $err = 'You cannot delete your own account.';
    } else {
        $db->prepare('DELETE FROM users WHERE id = ?')->execute([$delId]);
        $msg = 'User deleted.';
    }
}

// toggle admin flag
if (isset($_GET['toggle_admin'])) {
    $togId = (int) $_GET['toggle_admin'];
    if ($togId === getUserId()) {
        $err = 'You cannot change your own admin status.';
    } else {
        $db->prepare('UPDATE users SET is_admin = NOT is_admin WHERE id = ?')->execute([$togId]);
        $msg = 'User admin status updated.';
    }
}

//ADD user via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name    = trim($_POST['full_name'] ?? '');
    $email   = trim($_POST['email']    ?? '');
    $phone   = trim($_POST['phone']    ?? '');
    $pass    = $_POST['password']      ?? '';
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

    // Validate fields
    if (strlen($name) < 2) {
        $err = 'Full name must be at least 2 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Invalid email address.';
    } elseif (strlen($pass) < 6) {
        $err = 'Password must be at least 6 characters.';
    } else {
        // Check duplicate email
        $check = $db->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) {
            $err = 'Email already registered.';
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $db->prepare('INSERT INTO users (full_name, email, phone, password, is_admin) VALUES (?,?,?,?,?)')
               ->execute([$name, $email, $phone, $hash, $isAdmin]);
            $msg = 'User added successfully.';
        }
    }
}

// EDIT user via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $editId  = (int)   $_POST['user_id'];
    $name    = trim($_POST['full_name'] ?? '');
    $email   = trim($_POST['email']    ?? '');
    $phone   = trim($_POST['phone']    ?? '');
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;
    $newPass = trim($_POST['new_password'] ?? '');

    // Validate
    if (strlen($name) < 2) {
        $err = 'Full name must be at least 2 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Invalid email address.';
    } else {
        // Check duplicate email excluding this user
        $check = $db->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $check->execute([$email, $editId]);
        if ($check->fetch()) {
            $err = 'Email already used by another account.';
        } else {
            if ($newPass !== '') {
                // Update with new password
                if (strlen($newPass) < 6) {
                    $err = 'New password must be at least 6 characters.';
                } else {
                    $hash = password_hash($newPass, PASSWORD_BCRYPT);
                    $db->prepare('UPDATE users SET full_name=?,email=?,phone=?,password=?,is_admin=? WHERE id=?')
                       ->execute([$name, $email, $phone, $hash, $isAdmin, $editId]);
                    $msg = 'User updated with new password.';
                }
            } else {
                // Update without changing password
                $db->prepare('UPDATE users SET full_name=?,email=?,phone=?,is_admin=? WHERE id=?')
                   ->execute([$name, $email, $phone, $isAdmin, $editId]);
                $msg = 'User updated.';
            }
        }
    }
}

// Fetch all users with order count
$users = $db->query(
    'SELECT u.id, u.full_name, u.email, u.phone, u.is_admin, u.created_at,
            COUNT(o.id) AS order_count
     FROM users u
     LEFT JOIN orders o ON o.user_id = u.id
     GROUP BY u.id
     ORDER BY u.created_at DESC'
)->fetchAll();

// Load user for editing
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users — Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin.css">
  <script src="admin.js" defer></script>
</head>
<body>

<header class="adminHeader">
  <h1>🎁 Lab of Joy — Admin</h1>
  <p>Manage Users</p>
</header>

<nav class="navBar">
  <a class="pill" href="index.php">Dashboard</a>
  <a class="pill" href="categories.php">Categories</a>
  <a class="pill" href="products.php">Products</a>
  <a class="pill" href="orders.php">Orders</a>
  <a class="pill active" href="users.php">Users</a>
  <a class="pill" href="/LabOfJoy/aljury/categories.php">← Back to Site</a>
  <a class="pill" href="/LabOfJoy/fatimah/logout.php">Logout</a>
</nav>

<div class="adminPanel">

  <?php if ($msg): ?><div class="alert alert-ok"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-err"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

  <!-- Add / Edit user form -->
  <div class="section">
    <div class="sectionHead">
      <h2><?= $editing ? 'Edit User' : 'Add New User' ?></h2>
      <?php if ($editing): ?><a href="users.php" class="btn btn-ghost btn-sm">Cancel</a><?php endif; ?>
    </div>

    <form method="POST" action="users.php" id="userForm">
      <input type="hidden" name="<?= $editing ? 'edit_user' : 'add_user' ?>" value="1">
      <?php if ($editing): ?>
        <input type="hidden" name="user_id" value="<?= (int)$editing['id'] ?>">
      <?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">

        <div class="formGroup">
          <label>Full Name</label>
          <input type="text" name="full_name" required
                 value="<?= htmlspecialchars($editing['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <span class="fieldErr" id="nameErr"></span>
        </div>

        <div class="formGroup">
          <label>Email</label>
          <input type="email" name="email" required
                 value="<?= htmlspecialchars($editing['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <span class="fieldErr" id="emailErr"></span>
        </div>

        <div class="formGroup">
          <label>Phone</label>
          <input type="tel" name="phone" placeholder="05xxxxxxxx"
                 value="<?= htmlspecialchars($editing['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="formGroup">
          <label><?= $editing ? 'New Password (leave blank to keep)' : 'Password' ?></label>
          <input type="password" name="<?= $editing ? 'new_password' : 'password' ?>"
                 <?= $editing ? '' : 'required' ?> placeholder="Min 6 characters">
          <span class="fieldErr" id="passErr"></span>
        </div>

        <div class="formGroup" style="grid-column:1/-1;display:flex;align-items:center;gap:10px;">
          <input type="checkbox" name="is_admin" id="isAdmin" value="1"
                 <?= ($editing['is_admin'] ?? 0) ? 'checked' : '' ?>>
          <label for="isAdmin" style="margin:0;font-weight:600;">Grant Admin Access</label>
        </div>

      </div>

      <div class="formActions">
        <button type="submit" class="btn btn-primary">
          <?= $editing ? 'Update User' : 'Add User' ?>
        </button>
      </div>
    </form>
  </div>

  <!-- Users table -->
  <div class="section">
    <div class="sectionHead"><h2>All Users (<?= count($users) ?>)</h2></div>

    <table class="adminTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Role</th>
          <th>Orders</th>
          <th>Joined</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td><?= (int)$u['id'] ?></td>
          <td><?= htmlspecialchars($u['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($u['phone'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td>
            <span class="badge <?= $u['is_admin'] ? 'badge-confirmed' : 'badge-pending' ?>">
              <?= $u['is_admin'] ? 'Admin' : 'Customer' ?>
            </span>
          </td>
          <td><?= (int)$u['order_count'] ?></td>
          <td><?= htmlspecialchars(substr($u['created_at'], 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
          <td style="display:flex;gap:6px;flex-wrap:wrap;">
            <a href="users.php?edit=<?= (int)$u['id'] ?>" class="btn btn-ghost btn-sm">Edit</a>
            <a href="users.php?toggle_admin=<?= (int)$u['id'] ?>"
               class="btn btn-ghost btn-sm confirm-delete"
               data-name="admin status for <?= htmlspecialchars($u['full_name'], ENT_QUOTES, 'UTF-8') ?>">
              <?= $u['is_admin'] ? 'Revoke Admin' : 'Make Admin' ?>
            </a>
            <?php if ($u['id'] !== getUserId()): ?>
            <a href="users.php?delete=<?= (int)$u['id'] ?>"
               class="btn btn-danger btn-sm confirm-delete"
               data-name="<?= htmlspecialchars($u['full_name'], ENT_QUOTES, 'UTF-8') ?>">Delete</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>

<footer class="siteFooter">© 2026 Lab of Joy — Admin Panel</footer>

</body>
</html>
