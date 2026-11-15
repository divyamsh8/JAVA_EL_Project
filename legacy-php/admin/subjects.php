<?php
/**
 * Admin Console - Subjects & Courses CRUD
 */

declare(strict_types=1);

$adminPageTitle = 'Manage Subjects';
$currentAdminPage = 'subjects';

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
    } elseif ($action === 'create_subject') {
        $code = strtoupper(sanitize_input($_POST['code'] ?? ''));
        $title = sanitize_input($_POST['title'] ?? '');
        $deptId = (int)$_POST['department_id'];
        $courseId = (int)$_POST['course_id'];
        $termId = (int)$_POST['term_id'];
        $sem = (int)$_POST['semester'];
        $credits = (float)$_POST['credits'];
        $type = sanitize_input($_POST['type'] ?? 'Theory');

        if (empty($code) || empty($title)) {
            $error = 'Code and Title are required.';
        } else {
            $stmt = $db->prepare("INSERT INTO subjects (department_id, course_id, term_id, semester, code, title, credits, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$deptId, $courseId, $termId, $sem, $code, $title, $credits, $type]);
            $message = "Subject {$code} - {$title} added successfully.";
        }
    } elseif ($action === 'delete_subject') {
        $subId = (int)$_POST['subject_id'];
        $db->prepare("DELETE FROM subjects WHERE id = ?")->execute([$subId]);
        $message = "Subject deleted.";
    }
}

$depts = $db->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll();
$courses = $db->query("SELECT * FROM courses ORDER BY name ASC")->fetchAll();
$terms = $db->query("SELECT * FROM terms ORDER BY id ASC")->fetchAll();

$deptFilter = (int)($_GET['dept'] ?? 0);
$q = "SELECT s.*, d.code as dept_code, t.name as term_name FROM subjects s JOIN departments d ON s.department_id = d.id JOIN terms t ON s.term_id = t.id";
$params = [];
if ($deptFilter > 0) {
    $q .= " WHERE s.department_id = ?";
    $params[] = $deptFilter;
}
$q .= " ORDER BY s.department_id ASC, s.semester ASC, s.code ASC";
$stmt = $db->prepare($q);
$stmt->execute($params);
$subjects = $stmt->fetchAll();
?>

<div class="portal-wrapper">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="portal-main">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <div>
        <h4 class="fw-bold text-dark mb-1">Subjects &amp; Course Curriculum</h4>
        <small class="text-muted">Total Courses: <?= count($subjects) ?></small>
      </div>
      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newSubjectModal">
        <i class="fa-solid fa-plus me-1"></i> Add Subject
      </button>
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

    <div class="content-card">
      <div class="content-card-body p-0">
        <div class="table-responsive">
          <table class="table table-institutional mb-0">
            <thead>
              <tr>
                <th>Code</th>
                <th>Subject Title</th>
                <th>Department</th>
                <th>Sem</th>
                <th>Credits</th>
                <th>Type</th>
                <th>Term</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($subjects as $s): ?>
                <tr>
                  <td class="fw-bold text-dark"><?= e($s['code']) ?></td>
                  <td class="fw-semibold"><?= e($s['title']) ?></td>
                  <td><span class="badge bg-light text-secondary border"><?= e($s['dept_code']) ?></span></td>
                  <td>Sem <?= (int)$s['semester'] ?></td>
                  <td><?= e((string)$s['credits']) ?></td>
                  <td><span class="badge bg-primary-subtle text-primary"><?= e($s['type']) ?></span></td>
                  <td class="small text-muted"><?= e($s['term_name']) ?></td>
                  <td class="text-center">
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete subject <?= e($s['code']) ?>?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete_subject">
                      <input type="hidden" name="subject_id" value="<?= $s['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2">
                        <i class="fa-solid fa-trash-can"></i>
                      </button>
                    </form>
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

<!-- Add Subject Modal -->
<div class="modal fade" id="newSubjectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold">Add New Subject</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASE_URL ?>/admin/subjects.php" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="create_subject">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Subject Code *</label>
            <input type="text" name="code" class="form-control form-control-sm" placeholder="e.g. 21CS45" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Subject Title *</label>
            <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. Object Oriented Programming" required>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-semibold">Department</label>
              <select name="department_id" class="form-select form-select-sm">
                <?php foreach ($depts as $d): ?>
                  <option value="<?= $d['id'] ?>"><?= e($d['code']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Course</label>
              <select name="course_id" class="form-select form-select-sm">
                <?php foreach ($courses as $c): ?>
                  <option value="<?= $c['id'] ?>"><?= e($c['code']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-4">
              <label class="form-label small fw-semibold">Semester</label>
              <input type="number" name="semester" class="form-control form-control-sm" value="4" min="1" max="8">
            </div>
            <div class="col-4">
              <label class="form-label small fw-semibold">Credits</label>
              <input type="number" step="0.5" name="credits" class="form-control form-control-sm" value="4.0">
            </div>
            <div class="col-4">
              <label class="form-label small fw-semibold">Type</label>
              <select name="type" class="form-select form-select-sm">
                <option value="Theory">Theory</option>
                <option value="Lab">Lab</option>
                <option value="Elective">Elective</option>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Academic Term</label>
            <select name="term_id" class="form-select form-select-sm">
              <?php foreach ($terms as $t): ?>
                <option value="<?= $t['id'] ?>"><?= e($t['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-primary fw-bold">Save Subject</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
