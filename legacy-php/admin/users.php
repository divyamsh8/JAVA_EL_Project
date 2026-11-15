<?php
/**
 * Admin Console - User Accounts & Roles Management
 */

declare(strict_types=1);

$adminPageTitle = 'Manage User Accounts';
$currentAdminPage = 'users';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$db = get_db();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($token)) {
        $error = 'Security token invalid.';
    } elseif ($action === 'reset_password') {
        $userId = (int)$_POST['user_id'];
        $newPlain = sanitize_input($_POST['new_password'] ?? '');

        if (empty($newPlain)) {
            $error = 'Password cannot be empty.';
        } else {
            $newHash = password_hash($newPlain, PASSWORD_DEFAULT);
            $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$newHash, $userId]);
            $message = 'User password reset successfully.';
        }
    } elseif ($action === 'toggle_status') {
        $userId = (int)$_POST['user_id'];
        $db->prepare("UPDATE users SET status = CASE WHEN status = 'ACTIVE' THEN 'SUSPENDED' ELSE 'ACTIVE' END WHERE id = ?")->execute([$userId]);
        $message = 'User account status toggled.';
    }
}

$roleFilter = (int)($_GET['role'] ?? 0);
$q = "SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id";
$params = [];
if ($roleFilter > 0) {
    $q .= " WHERE u.role_id = ?";
    $params[] = $roleFilter;
}
$q .= " ORDER BY u.role_id ASC, u.username ASC LIMIT 100";
$stmt = $db->prepare($q);
$stmt->execute($params);
$users = $stmt->fetchAll();

$roles = $db->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll();
?>

<div class="portal-wrapper">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="portal-main">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <div>
        <h4 class="fw-bold text-dark mb-1">User Accounts &amp; Security Roles</h4>
        <small class="text-muted">Total Accounts: <?= count($users) ?></small>
      </div>
    </div>

    <?php if ($message): ?>
      <div class="alert alert-success alert-dismissible fade show small" role="alert">
        <?= e($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert alert-danger alert-dismissible fade show small" role="alert">
        <?= e($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- Role Filter -->
    <div class="card border-0 shadow-sm p-3 mb-4 bg-white rounded-3">
      <form method="GET" class="row g-2 align-items-center">
        <div class="col-auto">
          <label class="form-label mb-0 fw-semibold small text-muted">Filter Role:</label>
        </div>
        <div class="col-auto">
          <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="0">All System Roles</option>
            <?php foreach ($roles as $r): ?>
              <option value="<?= $r['id'] ?>" <?= ($roleFilter == $r['id']) ? 'selected' : '' ?>>
                <?= e($r['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </div>

    <div class="content-card">
      <div class="content-card-body p-0">
        <div class="table-responsive">
          <table class="table table-institutional mb-0">
            <thead>
              <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Email Contact</th>
                <th>Phone</th>
                <th>Last Login</th>
                <th class="text-center">Status</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): ?>
                <tr>
                  <td class="fw-bold text-dark"><?= e($u['username']) ?></td>
                  <td>
                    <span class="badge bg-secondary-subtle text-secondary border"><?= e($u['role_name']) ?></span>
                  </td>
                  <td class="small"><?= e($u['email'] ?: '-') ?></td>
                  <td class="small"><?= e($u['phone'] ?: '-') ?></td>
                  <td class="small text-muted"><?= $u['last_login_at'] ? format_date($u['last_login_at']) . ' ' . date('H:i', strtotime($u['last_login_at'])) : 'Never' ?></td>
                  <td class="text-center">
                    <span class="badge bg-<?= ($u['status'] === 'ACTIVE') ? 'success' : 'danger' ?>">
                      <?= e($u['status']) ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <form method="POST" style="display:inline;">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="toggle_status">
                      <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-outline-warning py-0 px-2" title="Toggle Status">
                        <i class="fa-solid fa-power-off"></i>
                      </button>
                    </form>
                    <button class="btn btn-sm btn-outline-primary py-0 px-2 ms-1" data-bs-toggle="modal" data-bs-target="#resetModal<?= $u['id'] ?>" title="Reset Password">
                      <i class="fa-solid fa-key"></i>
                    </button>

                    <!-- Reset Modal -->
                    <div class="modal fade text-start" id="resetModal<?= $u['id'] ?>" tabindex="-1">
                      <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                          <div class="modal-header bg-primary text-white">
                            <h6 class="modal-title fw-bold">Reset Password</h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                          </div>
                          <form method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="reset_password">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <div class="modal-body p-3">
                              <small class="text-muted d-block mb-2">User: <strong><?= e($u['username']) ?></strong></small>
                              <label class="form-label small fw-semibold">New Password / DOB (YYYY-MM-DD)</label>
                              <input type="text" name="new_password" class="form-control form-control-sm" placeholder="e.g. 2005-04-15" required>
                            </div>
                            <div class="modal-footer bg-light p-2">
                              <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-sm btn-primary fw-bold">Update</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
