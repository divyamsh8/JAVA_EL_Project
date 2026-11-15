<?php
/**
 * Admin Console - Internal Assessment (CIE) Marks Management
 */

declare(strict_types=1);

$adminPageTitle = 'Manage CIE Marks';
$currentAdminPage = 'marks';

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
    } elseif ($action === 'update_mark') {
        $markId = (int)$_POST['mark_id'];
        $obtained = (float)$_POST['marks_obtained'];
        $max = (float)$_POST['max_marks'];

        if ($obtained < 0 || $obtained > $max) {
            $error = "Marks obtained cannot exceed maximum marks ({$max}).";
        } else {
            $stmt = $db->prepare("UPDATE internal_marks SET marks_obtained = ? WHERE id = ?");
            $stmt->execute([$obtained, $markId]);
            $message = 'CIE score updated.';
        }
    }
}

$subjects = $db->query("SELECT id, code, title FROM subjects ORDER BY code ASC")->fetchAll();
$selectedSubId = (int)($_GET['subject_id'] ?? ($subjects[0]['id'] ?? 1));
$selectedTest = sanitize_input($_GET['test_name'] ?? 'CIE-1');

$stmt = $db->prepare("
    SELECT m.*, s.usn, s.first_name, s.last_name, sub.code as subject_code
    FROM internal_marks m
    JOIN students s ON m.student_id = s.id
    JOIN subjects sub ON m.subject_id = sub.id
    WHERE m.subject_id = ? AND m.test_name = ?
    ORDER BY s.usn ASC
");
$stmt->execute([$selectedSubId, $selectedTest]);
$marksList = $stmt->fetchAll();
?>

<div class="portal-wrapper">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="portal-main">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <div>
        <h4 class="fw-bold text-dark mb-1">Continuous Internal Evaluation (CIE) Grading</h4>
        <small class="text-muted">Enter and verify test and assignment evaluation scores</small>
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

    <!-- Filter Form -->
    <div class="card border-0 shadow-sm p-3 mb-4 bg-white rounded-3">
      <form method="GET" class="row g-2 align-items-center">
        <div class="col-auto">
          <label class="form-label mb-0 fw-semibold small text-muted">Subject:</label>
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
        <div class="col-auto ms-3">
          <label class="form-label mb-0 fw-semibold small text-muted">Assessment Test:</label>
        </div>
        <div class="col-auto">
          <select name="test_name" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="CIE-1" <?= ($selectedTest === 'CIE-1') ? 'selected' : '' ?>>CIE-1 (Max 50)</option>
            <option value="CIE-2" <?= ($selectedTest === 'CIE-2') ? 'selected' : '' ?>>CIE-2 (Max 50)</option>
            <option value="Assignment" <?= ($selectedTest === 'Assignment') ? 'selected' : '' ?>>Assignment (Max 10)</option>
          </select>
        </div>
      </form>
    </div>

    <!-- Marks Table -->
    <div class="content-card">
      <div class="content-card-body p-0">
        <div class="table-responsive">
          <table class="table table-institutional mb-0">
            <thead>
              <tr>
                <th>USN</th>
                <th>Student Name</th>
                <th>Assessment</th>
                <th class="text-center">Max Marks</th>
                <th class="text-center">Marks Obtained</th>
                <th class="text-center">Update</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($marksList as $m): ?>
                <tr>
                  <td class="fw-bold text-dark"><?= e($m['usn']) ?></td>
                  <td class="fw-semibold"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></td>
                  <td><span class="badge bg-primary-subtle text-primary border border-primary"><?= e($m['test_name']) ?></span></td>
                  <td class="text-center"><?= number_format((float)$m['max_marks'], 1) ?></td>
                  <form method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="update_mark">
                    <input type="hidden" name="mark_id" value="<?= $m['id'] ?>">
                    <input type="hidden" name="max_marks" value="<?= (float)$m['max_marks'] ?>">
                    <td class="text-center" style="width: 140px;">
                      <input type="number" step="0.5" name="marks_obtained" class="form-control form-control-sm text-center fw-bold" 
                             value="<?= number_format((float)$m['marks_obtained'], 1) ?>" min="0" max="<?= (float)$m['max_marks'] ?>" required>
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
