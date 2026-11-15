<?php
/**
 * Admin Console - Examination Results Management
 */

declare(strict_types=1);

$adminPageTitle = 'Manage Exam Results';
$currentAdminPage = 'results';

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
    } elseif ($action === 'update_result') {
        $resId = (int)$_POST['result_id'];
        $grade = strtoupper(sanitize_input($_POST['grade_letter'] ?? 'A'));
        $status = sanitize_input($_POST['result_status'] ?? 'P');

        $gPoints = ['S' => 10.0, 'A' => 9.0, 'B' => 8.0, 'C' => 7.0, 'D' => 6.0, 'E' => 5.0, 'F' => 0.0];
        $gp = $gPoints[$grade] ?? 0.0;
        if ($grade === 'F') $status = 'F';

        $stmt = $db->prepare("UPDATE examination_results SET grade_letter = ?, grade_points = ?, result_status = ? WHERE id = ?");
        $stmt->execute([$grade, $gp, $status, $resId]);
        $message = 'Examination result record updated.';
    }
}

$semFilter = (int)($_GET['sem'] ?? 3);

$stmt = $db->prepare("
    SELECT r.*, s.usn, s.first_name, s.last_name, sub.code as subject_code, sub.title as subject_title
    FROM examination_results r
    JOIN students s ON r.student_id = s.id
    JOIN subjects sub ON r.subject_id = sub.id
    WHERE r.semester = ?
    ORDER BY s.usn ASC, sub.code ASC
");
$stmt->execute([$semFilter]);
$resultsList = $stmt->fetchAll();
?>

<div class="portal-wrapper">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="portal-main">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <div>
        <h4 class="fw-bold text-dark mb-1">Semester Examination Results Publishing</h4>
        <small class="text-muted">Provisional Grades &amp; SGPA/CGPA Records</small>
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

    <div class="card border-0 shadow-sm p-3 mb-4 bg-white rounded-3">
      <form method="GET" class="row g-2 align-items-center">
        <div class="col-auto">
          <label class="form-label mb-0 fw-semibold small text-muted">Select Semester:</label>
        </div>
        <div class="col-auto">
          <select name="sem" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="3" <?= ($semFilter === 3) ? 'selected' : '' ?>>3rd Semester (Nov/Dec 2025 Regular)</option>
            <option value="2" <?= ($semFilter === 2) ? 'selected' : '' ?>>2nd Semester</option>
            <option value="1" <?= ($semFilter === 1) ? 'selected' : '' ?>>1st Semester</option>
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
                <th>USN</th>
                <th>Student Name</th>
                <th>Subject</th>
                <th class="text-center">Credits</th>
                <th class="text-center">Grade</th>
                <th class="text-center">Points</th>
                <th class="text-center">Status</th>
                <th class="text-center">Update</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($resultsList as $r): ?>
                <tr>
                  <td class="fw-bold text-dark"><?= e($r['usn']) ?></td>
                  <td class="fw-semibold"><?= e($r['first_name'] . ' ' . $r['last_name']) ?></td>
                  <td>
                    <span class="fw-bold"><?= e($r['subject_code']) ?></span> - 
                    <small class="text-muted"><?= e($r['subject_title']) ?></small>
                  </td>
                  <td class="text-center"><?= number_format((float)$r['credits'], 1) ?></td>
                  <form method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="update_result">
                    <input type="hidden" name="result_id" value="<?= $r['id'] ?>">
                    <td class="text-center" style="width: 100px;">
                      <select name="grade_letter" class="form-select form-select-sm fw-bold">
                        <?php foreach (['S', 'A', 'B', 'C', 'D', 'E', 'F'] as $gr): ?>
                          <option value="<?= $gr ?>" <?= ($r['grade_letter'] === $gr) ? 'selected' : '' ?>><?= $gr ?></option>
                        <?php endforeach; ?>
                      </select>
                    </td>
                    <td class="text-center fw-bold text-dark"><?= number_format((float)$r['grade_points'], 1) ?></td>
                    <td class="text-center" style="width: 110px;">
                      <select name="result_status" class="form-select form-select-sm">
                        <option value="P" <?= ($r['result_status'] === 'P') ? 'selected' : '' ?>>Pass (P)</option>
                        <option value="F" <?= ($r['result_status'] === 'F') ? 'selected' : '' ?>>Fail (F)</option>
                      </select>
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
