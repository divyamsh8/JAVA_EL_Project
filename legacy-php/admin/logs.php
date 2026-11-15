<?php
/**
 * Admin Console - Authentication & Security Audit Trail Viewer
 */

declare(strict_types=1);

$adminPageTitle = 'Login Audit Logs';
$currentAdminPage = 'logs';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$db = get_db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token = $_POST['csrf_token'] ?? '';
    if (verify_csrf_token($token) && $action === 'clear_old_logs') {
        $db->query("DELETE FROM login_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $message = 'Logs older than 30 days cleared.';
    }
}

$statusFilter = sanitize_input($_GET['status'] ?? '');
$searchUser = sanitize_input($_GET['search'] ?? '');

$q = "SELECT * FROM login_logs WHERE 1=1";
$params = [];

if (!empty($statusFilter)) {
    $q .= " AND status = ?";
    $params[] = $statusFilter;
}
if (!empty($searchUser)) {
    $q .= " AND username LIKE ?";
    $params[] = '%' . $searchUser . '%';
}
$q .= " ORDER BY id DESC LIMIT 150";

$stmt = $db->prepare($q);
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>

<div class="portal-wrapper">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="portal-main">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <div>
        <h4 class="fw-bold text-dark mb-1">Security Audit Trail &amp; Login Logs</h4>
        <small class="text-muted">Live event logging of all authentication attempts and lockout triggers</small>
      </div>
      <form method="POST" onsubmit="return confirm('Clear logs older than 30 days?');">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="clear_old_logs">
        <button type="submit" class="btn btn-sm btn-outline-secondary">
          <i class="fa-solid fa-broom me-1"></i> Cleanup Old Logs
        </button>
      </form>
    </div>

    <?php if ($message): ?>
      <div class="alert alert-success alert-dismissible fade show small" role="alert">
        <?= e($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- Filter Form -->
    <div class="card border-0 shadow-sm p-3 mb-4 bg-white rounded-3">
      <form method="GET" class="row g-2 align-items-center">
        <div class="col-auto">
          <label class="form-label mb-0 fw-semibold small text-muted">Status:</label>
        </div>
        <div class="col-auto">
          <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="SUCCESS" <?= ($statusFilter === 'SUCCESS') ? 'selected' : '' ?>>SUCCESS</option>
            <option value="FAILED" <?= ($statusFilter === 'FAILED') ? 'selected' : '' ?>>FAILED</option>
            <option value="LOCKED" <?= ($statusFilter === 'LOCKED') ? 'selected' : '' ?>>LOCKED</option>
          </select>
        </div>
        <div class="col-auto ms-3">
          <label class="form-label mb-0 fw-semibold small text-muted">Search Username:</label>
        </div>
        <div class="col-auto">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="e.g. 1DM24CS001" value="<?= e($searchUser) ?>">
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        </div>
      </form>
    </div>

    <div class="content-card">
      <div class="content-card-body p-0">
        <div class="table-responsive">
          <table class="table table-institutional mb-0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Timestamp</th>
                <th>Username</th>
                <th>IP Address</th>
                <th class="text-center">Status</th>
                <th>Failure Reason</th>
                <th>User Agent Details</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($logs as $log): 
                $st = $log['status'];
                $badgeClass = ($st === 'SUCCESS') ? 'bg-success' : (($st === 'LOCKED') ? 'bg-danger' : 'bg-warning text-dark');
              ?>
                <tr>
                  <td class="text-muted small">#<?= (int)$log['id'] ?></td>
                  <td class="small fw-semibold"><?= e($log['created_at']) ?></td>
                  <td class="fw-bold text-dark"><?= e($log['username']) ?></td>
                  <td><code><?= e($log['ip_address']) ?></code></td>
                  <td class="text-center">
                    <span class="badge <?= $badgeClass ?> px-2 py-1"><?= e($st) ?></span>
                  </td>
                  <td class="small text-danger fw-semibold"><?= e($log['failure_reason'] ?: '-') ?></td>
                  <td class="small text-muted text-truncate" style="max-width: 250px;" title="<?= e($log['user_agent']) ?>">
                    <?= e($log['user_agent'] ?: 'Standard Browser') ?>
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
