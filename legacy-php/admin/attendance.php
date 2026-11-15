<?php
/**
 * Admin Console - Attendance Management
 */

declare(strict_types=1);

$adminPageTitle = 'Manage Attendance';
$currentAdminPage = 'attendance';

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
    } elseif ($action === 'update_attendance') {
        $attId = (int)$_POST['attendance_id'];
        $total = (int)$_POST['total_classes'];
        $attended = (int)$_POST['attended_classes'];

        if ($total < 0 || $attended < 0 || $attended > $total) {
            $error = 'Attended classes cannot exceed total classes.';
        } else {
            $pct = $total > 0 ? round(($attended / $total) * 100, 2) : 0.0;
            $rem = $pct < 75.0 ? 'Shortage Warning' : 'Good';

            $stmt = $db->prepare("UPDATE attendance SET total_classes = ?, attended_classes = ?, percentage = ?, remarks = ? WHERE id = ?");
            $stmt->execute([$total, $attended, $pct, $rem, $attId]);
            $message = 'Attendance record updated successfully.';
        }
    }
}

// Fetch subjects
$subjects = $db->query("SELECT id, code, title FROM subjects ORDER BY code ASC")->fetchAll();
$selectedSubId = (int)($_GET['subject_id'] ?? ($subjects[0]['id'] ?? 1));

$stmt = $db->prepare("
    SELECT a.*, s.usn, s.first_name, s.last_name, sub.code as subject_code, sub.title as subject_title
    FROM attendance a
    JOIN students s ON a.student_id = s.id
    JOIN subjects sub ON a.subject_id = sub.id
    WHERE a.subject_id = ?
    ORDER BY s.usn ASC
");
$stmt->execute([$selectedSubId]);
$attendanceList = $stmt->fetchAll();
?>

<div class="portal-wrapper">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="portal-main">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <div>
        <h4 class="fw-bold text-dark mb-1">Attendance Records Management</h4>
        <small class="text-muted">Review and update student attendance percentages</small>
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

    <!-- Subject Selector -->
    <div class="card border-0 shadow-sm p-3 mb-4 bg-white rounded-3">
      <form method="GET" class="row g-2 align-items-center">
        <div class="col-auto">
          <label class="form-label mb-0 fw-semibold small text-muted">Select Subject Course:</label>
        </div>
        <div class="col-auto">
          <select name="subject_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <?php foreach ($subjects as $sub): ?>
              <option value="<?= $sub['id'] ?>" <?= ($selectedSubId == $sub['id']) ? 'selected' : '' ?>>
                <?= e($sub['code']) ?> - <?= e($sub['title']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </div>

    <!-- Attendance Table -->
    <div class="content-card">
      <div class="content-card-body p-0">
        <div class="table-responsive">
          <table class="table table-institutional mb-0">
            <thead>
              <tr>
                <th>USN</th>
                <th>Student Name</th>
                <th class="text-center">Total Held</th>
                <th class="text-center">Attended</th>
                <th class="text-center">Percentage</th>
                <th class="text-center">Status</th>
                <th class="text-center">Update</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($attendanceList as $row): 
                $pct = (float)$row['percentage'];
                $isLow = $pct < 75.0;
              ?>
                <tr>
                  <td class="fw-bold text-dark"><?= e($row['usn']) ?></td>
                  <td class="fw-semibold"><?= e($row['first_name'] . ' ' . $row['last_name']) ?></td>
                  <form method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="update_attendance">
                    <input type="hidden" name="attendance_id" value="<?= $row['id'] ?>">
                    <td class="text-center" style="width: 110px;">
                      <input type="number" name="total_classes" class="form-control form-control-sm text-center" value="<?= (int)$row['total_classes'] ?>" min="0" required>
                    </td>
                    <td class="text-center" style="width: 110px;">
                      <input type="number" name="attended_classes" class="form-control form-control-sm text-center" value="<?= (int)$row['attended_classes'] ?>" min="0" required>
                    </td>
                    <td class="text-center fw-bold fs-6 <?= $isLow ? 'text-danger' : 'text-success' ?>">
                      <?= number_format($pct, 2) ?>%
                    </td>
                    <td class="text-center">
                      <?php if ($isLow): ?>
                        <span class="badge-shortage">SHORTAGE</span>
                      <?php else: ?>
                        <span class="badge-normal">ELIGIBLE</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center">
                      <button type="submit" class="btn btn-sm btn-outline-primary py-0 px-2">
                        <i class="fa-solid fa-floppy-disk"></i> Save
                      </button>
                    </td>
                  </form>
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
