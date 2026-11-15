<?php
/**
 * Admin Console - Weekly Timetable Management
 */

declare(strict_types=1);

$adminPageTitle = 'Manage Timetable';
$currentAdminPage = 'timetable';

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
    } elseif ($action === 'create_slot') {
        $termId = (int)$_POST['term_id'];
        $deptId = (int)$_POST['department_id'];
        $sem = (int)$_POST['semester'];
        $day = sanitize_input($_POST['day_of_week']);
        $period = (int)$_POST['period_number'];
        $startTime = sanitize_input($_POST['start_time']);
        $endTime = sanitize_input($_POST['end_time']);
        $subId = (int)$_POST['subject_id'];
        $room = sanitize_input($_POST['room_no']);
        $faculty = sanitize_input($_POST['faculty_name']);

        $stmt = $db->prepare("INSERT INTO timetable (term_id, department_id, semester, day_of_week, period_number, start_time, end_time, subject_id, room_no, faculty_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$termId, $deptId, $sem, $day, $period, $startTime, $endTime, $subId, $room, $faculty]);
        $message = 'Timetable slot created successfully.';
    } elseif ($action === 'delete_slot') {
        $id = (int)$_POST['slot_id'];
        $db->prepare("DELETE FROM timetable WHERE id = ?")->execute([$id]);
        $message = 'Slot deleted.';
    }
}

$depts = $db->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll();
$subjects = $db->query("SELECT * FROM subjects ORDER BY code ASC")->fetchAll();
$terms = $db->query("SELECT * FROM terms ORDER BY id ASC")->fetchAll();

$deptFilter = (int)($_GET['dept'] ?? 1);
$stmt = $db->prepare("
    SELECT t.*, d.code as dept_code, sub.code as subject_code, sub.title as subject_title
    FROM timetable t
    JOIN departments d ON t.department_id = d.id
    JOIN subjects sub ON t.subject_id = sub.id
    WHERE t.department_id = ?
    ORDER BY FIELD(t.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), t.period_number ASC
");
$stmt->execute([$deptFilter]);
$slots = $stmt->fetchAll();
?>

<div class="portal-wrapper">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="portal-main">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <div>
        <h4 class="fw-bold text-dark mb-1">Weekly Class Timetable Schedule</h4>
        <small class="text-muted">Total Scheduled Slots: <?= count($slots) ?></small>
      </div>
      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newSlotModal">
        <i class="fa-solid fa-plus me-1"></i> Add Timetable Slot
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
                <th>Day</th>
                <th>Period</th>
                <th>Time Window</th>
                <th>Course</th>
                <th>Room Hall</th>
                <th>Faculty In-Charge</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($slots as $sl): ?>
                <tr>
                  <td class="fw-bold text-dark"><?= e($sl['day_of_week']) ?></td>
                  <td>Period #<?= (int)$sl['period_number'] ?></td>
                  <td class="small text-muted"><?= date('h:i A', strtotime($sl['start_time'])) ?> - <?= date('h:i A', strtotime($sl['end_time'])) ?></td>
                  <td>
                    <span class="fw-bold text-primary"><?= e($sl['subject_code']) ?></span> - 
                    <small class="text-secondary"><?= e($sl['subject_title']) ?></small>
                  </td>
                  <td><span class="badge bg-light text-dark border"><?= e($sl['room_no']) ?></span></td>
                  <td class="small"><?= e($sl['faculty_name']) ?></td>
                  <td class="text-center">
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this slot?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete_slot">
                      <input type="hidden" name="slot_id" value="<?= $sl['id'] ?>">
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

<!-- Add Slot Modal -->
<div class="modal fade" id="newSlotModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold">Add Timetable Slot</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASE_URL ?>/admin/timetable.php" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="create_slot">
        <div class="modal-body p-4">
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-semibold">Academic Term</label>
              <select name="term_id" class="form-select form-select-sm">
                <?php foreach ($terms as $t): ?>
                  <option value="<?= $t['id'] ?>"><?= e($t['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Department</label>
              <select name="department_id" class="form-select form-select-sm">
                <?php foreach ($depts as $d): ?>
                  <option value="<?= $d['id'] ?>"><?= e($d['code']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-4">
              <label class="form-label small fw-semibold">Semester</label>
              <input type="number" name="semester" class="form-control form-control-sm" value="4">
            </div>
            <div class="col-4">
              <label class="form-label small fw-semibold">Day of Week</label>
              <select name="day_of_week" class="form-select form-select-sm">
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
              </select>
            </div>
            <div class="col-4">
              <label class="form-label small fw-semibold">Period No</label>
              <input type="number" name="period_number" class="form-control form-control-sm" value="1" min="1" max="6">
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-semibold">Start Time</label>
              <input type="time" name="start_time" class="form-control form-control-sm" value="09:00" required>
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">End Time</label>
              <input type="time" name="end_time" class="form-control form-control-sm" value="10:00" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Course Subject *</label>
            <select name="subject_id" class="form-select form-select-sm" required>
              <?php foreach ($subjects as $sb): ?>
                <option value="<?= $sb['id'] ?>"><?= e($sb['code']) ?> - <?= e($sb['title']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-semibold">Room / Hall</label>
              <input type="text" name="room_no" class="form-control form-control-sm" value="LH-201" required>
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Faculty In-Charge</label>
              <input type="text" name="faculty_name" class="form-control form-control-sm" placeholder="e.g. Dr. Ramesh Kumar" required>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-primary fw-bold">Save Slot</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
