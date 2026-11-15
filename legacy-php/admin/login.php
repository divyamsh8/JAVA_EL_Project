<?php
/**
 * Administrator & Faculty Dedicated Login Portal
 * Demo Institute of Engineering
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

if (is_admin_logged_in()) {
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!verify_csrf_token($token)) {
        $error = 'Security token expired. Please try again.';
    } elseif (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $db = get_db();
        $stmt = $db->prepare("
            SELECT u.*, r.name as role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.username = ? AND u.role_id IN (1, 2, 3) AND u.status = 'ACTIVE'
            LIMIT 1
        ");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_user_id'] = $admin['id'];
            $_SESSION['admin_role_id'] = $admin['role_id'];
            $_SESSION['admin_role_name'] = $admin['role_name'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_last_activity'] = time();

            // Log login
            $ip = get_client_ip();
            $logStmt = $db->prepare("INSERT INTO login_logs (username, ip_address, status) VALUES (?, ?, 'SUCCESS')");
            $logStmt->execute([$admin['username'], $ip]);

            header('Location: ' . BASE_URL . '/admin/index.php');
            exit;
        } else {
            $error = 'Invalid administrator credentials or unauthorized role.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Portal Login - <?= APP_NAME ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/assets/images/crest.svg">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      padding: 20px;
    }
    .admin-card {
      background: #ffffff;
      border-radius: 10px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
      width: 100%;
      max-width: 440px;
      overflow: hidden;
    }
    .admin-card-header {
      background: #1b2a47;
      color: #ffffff;
      padding: 25px 20px;
      text-align: center;
    }
  </style>
</head>
<body>

  <div class="admin-card">
    <div class="admin-card-header">
      <img src="<?= BASE_URL ?>/assets/images/crest.svg" alt="Crest" width="70" height="70" class="mb-2">
      <h5 class="fw-bold mb-0 text-white"><?= APP_NAME ?></h5>
      <small class="text-white-50">Faculty &amp; Administrative Management Portal</small>
    </div>

    <div class="p-4">
      <?php if ($error): ?>
        <div class="alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-3">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <div><?= e($error) ?></div>
        </div>
      <?php endif; ?>

      <form action="<?= BASE_URL ?>/admin/login.php" method="POST">
        <?= csrf_field() ?>

        <div class="mb-3">
          <label class="form-label fw-bold small text-secondary">Admin Username</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-user-shield"></i></span>
            <input type="text" name="username" class="form-control" placeholder="e.g. admin or hod_cse" required autofocus value="<?= e($_POST['username'] ?? '') ?>">
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label fw-bold small text-secondary">Password</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-key"></i></span>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm mb-3" style="background-color: #1b2a47; border-color: #1b2a47;">
          <i class="fa-solid fa-right-to-bracket me-1"></i> Sign In to Admin Console
        </button>
      </form>

      <!-- Demo Credentials Helper Card -->
      <div class="card bg-light border-0 p-3 small text-muted rounded-3">
        <div class="fw-bold text-dark mb-1"><i class="fa-solid fa-circle-info text-primary me-1"></i> Demo Credentials:</div>
        <div class="d-flex justify-content-between py-1 border-bottom">
          <span>Super Admin:</span> <code>admin</code> / <code>admin@123</code>
        </div>
        <div class="d-flex justify-content-between py-1 border-bottom">
          <span>Dept Admin (CSE):</span> <code>hod_cse</code> / <code>admin@123</code>
        </div>
        <div class="d-flex justify-content-between pt-1">
          <span>Parent Portal:</span> <a href="<?= BASE_URL ?>/index.php" class="text-primary text-decoration-none fw-bold">Parent Login</a>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
