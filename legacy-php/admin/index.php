<?php
/**
 * Admin Console - Overview Dashboard
 */

declare(strict_types=1);

$adminPageTitle = 'Dashboard Overview';
$currentAdminPage = 'dashboard';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$db = get_db();

// Counts
$totalStudents = (int)$db->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalSubjects = (int)$db->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
$totalNotices = (int)$db->query("SELECT COUNT(*) FROM notices WHERE is_active = 1")->fetchColumn();
$totalFeeCollected = (float)$db->query("SELECT SUM(paid_amount) FROM fee_records")->fetchColumn();
$totalLoginsToday = (int)$db->query("SELECT COUNT(*) FROM login_logs WHERE status = 'SUCCESS'")->fetchColumn();

// Recent Login Logs
$recentLogs = $db->query("SELECT * FROM login_logs ORDER BY id DESC LIMIT 8")->fetchAll();
?>

<div class="portal-wrapper">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="portal-main">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <div>
        <h4 class="fw-bold text-dark mb-1">Administrative Control Center</h4>
        <small class="text-muted">Academic Session 2025-2026 &bull; Demo Institute of Engineering</small>
      </div>
      <div>
        <a href="<?= BASE_URL ?>/admin/students.php?action=new" class="btn btn-sm btn-primary">
          <i class="fa-solid fa-user-plus me-1"></i> Add Student
        </a>
        <a href="<?= BASE_URL ?>/admin/notices.php" class="btn btn-sm btn-outline-secondary ms-2">
          <i class="fa-solid fa-bullhorn me-1"></i> Manage Notices
        </a>
      </div>
    </div>

    <!-- Metric Cards -->
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
          <div class="stat-info">
            <h3><?= $totalStudents ?></h3>
            <p>Enrolled Students</p>
            <small class="text-primary fw-semibold">Across 3 Departments</small>
          </div>
          <div class="stat-icon primary"><i class="fa-solid fa-user-graduate"></i></div>
        </div>
      </div>

      <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
          <div class="stat-info">
            <h3><?= $totalSubjects ?></h3>
            <p>Active Courses</p>
            <small class="text-purple fw-semibold">Theory &amp; Laboratory</small>
          </div>
          <div class="stat-icon purple"><i class="fa-solid fa-book"></i></div>
        </div>
      </div>

      <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
          <div class="stat-info">
            <h3><?= format_currency($totalFeeCollected) ?></h3>
            <p>Total Collections</p>
            <small class="text-success fw-semibold">Tuition &amp; Amenities</small>
          </div>
          <div class="stat-icon success"><i class="fa-solid fa-hand-holding-dollar"></i></div>
        </div>
      </div>

      <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
          <div class="stat-info">
            <h3><?= $totalLoginsToday ?></h3>
            <p>Parent Access Events</p>
            <small class="text-info fw-semibold">Audit Logged</small>
          </div>
          <div class="stat-icon warning"><i class="fa-solid fa-shield-halved"></i></div>
        </div>
      </div>
    </div>

    <!-- Recent Login Logs Audit Trail -->
    <div class="content-card mb-4">
      <div class="content-card-header">
        <h5><i class="fa-solid fa-list-check text-primary"></i> Real-time Authentication &amp; Security Audit Trail</h5>
        <a href="<?= BASE_URL ?>/admin/logs.php" class="btn btn-sm btn-outline-primary">View Full Logs</a>
      </div>
      <div class="content-card-body p-0">
        <div class="table-responsive">
          <table class="table table-institutional mb-0">
            <thead>
              <tr>
                <th>Timestamp</th>
                <th>Username (USN)</th>
                <th>IP Address</th>
                <th class="text-center">Status</th>
                <th>Remarks / Failure Reason</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentLogs as $log): 
                $st = $log['status'];
                $badgeClass = ($st === 'SUCCESS') ? 'bg-success' : (($st === 'LOCKED') ? 'bg-danger' : 'bg-warning text-dark');
              ?>
                <tr>
                  <td class="small text-muted"><?= format_date($log['created_at']) ?> <?= date('H:i:s', strtotime($log['created_at'])) ?></td>
                  <td class="fw-bold text-dark"><?= e($log['username']) ?></td>
                  <td class="small text-secondary"><?= e($log['ip_address']) ?></td>
                  <td class="text-center">
                    <span class="badge <?= $badgeClass ?> px-2 py-1"><?= e($st) ?></span>
                  </td>
                  <td class="small text-muted"><?= e($log['failure_reason'] ?? 'Normal Login') ?></td>
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
