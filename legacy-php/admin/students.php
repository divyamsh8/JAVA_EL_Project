<?php
/**
 * Admin Console - Students & Parents CRUD Management
 */

declare(strict_types=1);

$adminPageTitle = 'Manage Students & Parents';
$currentAdminPage = 'students';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$db = get_db();
$message = '';
$error = '';

// Handle Actions (Add, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($token)) {
        $error = 'Security token invalid. Please try again.';
    } elseif ($action === 'create_student') {
        $usn = strtoupper(sanitize_input($_POST['usn'] ?? ''));
        $firstName = sanitize_input($_POST['first_name'] ?? '');
        $lastName = sanitize_input($_POST['last_name'] ?? '');
        $gender = sanitize_input($_POST['gender'] ?? 'MALE');
        $dob = sanitize_input($_POST['dob'] ?? '');
        $bloodGroup = sanitize_input($_POST['blood_group'] ?? 'O+');
        $deptId = (int)$_POST['department_id'];
        $courseId = (int)$_POST['course_id'];
        $currentSem = (int)($_POST['current_sem'] ?? 4);

        $fatherName = sanitize_input($_POST['father_name'] ?? '');
        $motherName = sanitize_input($_POST['mother_name'] ?? '');
        $phone = sanitize_input($_POST['phone'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $address = sanitize_input($_POST['address'] ?? '');

        if (empty($usn) || empty($firstName) || empty($lastName) || empty($dob) || empty($phone)) {
            $error = 'Please fill out all required fields.';
        } else {
            // Check USN uniqueness
            $check = $db->prepare("SELECT COUNT(*) FROM students WHERE usn = ?");
            $check->execute([$usn]);
            if ((int)$check->fetchColumn() > 0) {
                $error = "Student with USN {$usn} already exists!";
            } else {
                // 1. Create User
                $passHash = password_hash($dob, PASSWORD_DEFAULT);
                $uStmt = $db->prepare("INSERT INTO users (role_id, username, password_hash, email, phone, status) VALUES (4, ?, ?, ?, ?, 'ACTIVE')");
                $uStmt->execute([$usn, $passHash, $email, $phone]);
                $userId = (int)$db->lastInsertId();

                // 2. Create Parent
                $pStmt = $db->prepare("INSERT INTO parents (user_id, father_name, mother_name, primary_mobile, email, address) VALUES (?, ?, ?, ?, ?, ?)");
                $pStmt->execute([$userId, $fatherName, $motherName, $phone, $email, $address]);
                $parentId = (int)$db->lastInsertId();

                // 3. Create Student
                $sStmt = $db->prepare("INSERT INTO students (user_id, parent_id, department_id, course_id, usn, first_name, last_name, gender, dob, blood_group, admission_year, current_sem) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $sStmt->execute([$userId, $parentId, $deptId, $courseId, $usn, $firstName, $lastName, $gender, $dob, $bloodGroup, 2024, $currentSem]);

                $message = "Student {$firstName} {$lastName} ({$usn}) registered successfully!";
            }
        }
    } elseif ($action === 'delete_student') {
        $studentId = (int)$_POST['student_id'];
        // Find user_id
        $s = $db->prepare("SELECT user_id, usn FROM students WHERE id = ?");
        $s->execute([$studentId]);
        $stu = $s->fetch();
        if ($stu) {
            $db->prepare("DELETE FROM users WHERE id = ?")->execute([(int)$stu['user_id']]);
            $message = "Student {$stu['usn']} deleted successfully.";
        }
    }
}

// Fetch departments and courses for dropdown
$depts = $db->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll();
$courses = $db->query("SELECT * FROM courses ORDER BY name ASC")->fetchAll();

// Filter
$deptFilter = (int)($_GET['dept'] ?? 0);
$query = "
    SELECT s.*, d.code as dept_code, c.name as course_name, p.father_name, p.primary_mobile
    FROM students s
    JOIN departments d ON s.department_id = d.id
    JOIN courses c ON s.course_id = c.id
    JOIN parents p ON s.parent_id = p.id
";
$params = [];
if ($deptFilter > 0) {
    $query .= " WHERE s.department_id = ?";
    $params[] = $deptFilter;
}
$query .= " ORDER BY s.department_id ASC, s.usn ASC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll();
?>

<div class="portal-wrapper">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="portal-main">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <div>
        <h4 class="fw-bold text-dark mb-1">Student &amp; Parent Roster Management</h4>
        <small class="text-muted">Total Enrolled: <?= count($students) ?> students</small>
      </div>
      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newStudentModal">
        <i class="fa-solid fa-user-plus me-1"></i> Register New Student
      </button>
    </div>

    <?php if ($message): ?>
      <div class="alert alert-success alert-dismissible fade show small" role="alert">
        <i class="fa-solid fa-check-circle me-1"></i> <?= e($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert alert-danger alert-dismissible fade show small" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= e($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- Filter by Department -->
    <div class="card border-0 shadow-sm p-3 mb-4 bg-white rounded-3">
      <form method="GET" class="row g-2 align-items-center">
        <div class="col-auto">
          <label class="form-label mb-0 fw-semibold small text-muted">Filter Department:</label>
        </div>
        <div class="col-auto">
          <select name="dept" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="0">All Departments</option>
            <?php foreach ($depts as $d): ?>
              <option value="<?= $d['id'] ?>" <?= ($deptFilter == $d['id']) ? 'selected' : '' ?>>
                <?= e($d['name']) ?> (<?= e($d['code']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </div>

    <!-- Students Table -->
    <div class="content-card">
      <div class="content-card-body p-0">
        <div class="table-responsive">
          <table class="table table-institutional mb-0">
            <thead>
              <tr>
                <th>USN</th>
                <th>Student Name</th>
                <th>Dept</th>
                <th>Sem</th>
                <th>DOB (Password)</th>
                <th>Parent Name</th>
                <th>Primary Contact</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($students as $st): ?>
                <tr>
                  <td class="fw-bold text-dark"><?= e($st['usn']) ?></td>
                  <td class="fw-semibold"><?= e($st['first_name'] . ' ' . $st['last_name']) ?></td>
                  <td><span class="badge bg-light text-secondary border"><?= e($st['dept_code']) ?></span></td>
                  <td>Sem <?= (int)$st['current_sem'] ?></td>
                  <td class="small text-secondary"><?= e($st['dob']) ?></td>
                  <td class="small"><?= e($st['father_name']) ?></td>
                  <td class="small"><?= e($st['primary_mobile']) ?></td>
                  <td class="text-center">
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete student <?= e($st['usn']) ?>?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete_student">
                      <input type="hidden" name="student_id" value="<?= $st['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete Student">
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

<!-- Register New Student Modal -->
<div class="modal fade" id="newStudentModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-plus me-2"></i> Register New Student &amp; Parent</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASE_URL ?>/admin/students.php" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="create_student">
        <div class="modal-body p-4">
          <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Student Information</h6>
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label small fw-semibold">USN *</label>
              <input type="text" name="usn" class="form-control form-control-sm" placeholder="e.g. 1DM24CS011" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">First Name *</label>
              <input type="text" name="first_name" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Last Name *</label>
              <input type="text" name="last_name" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-semibold">Gender</label>
              <select name="gender" class="form-select form-select-sm">
                <option value="MALE">Male</option>
                <option value="FEMALE">Female</option>
                <option value="OTHER">Other</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-semibold">DOB (YYYY-MM-DD) *</label>
              <input type="date" name="dob" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-semibold">Blood Group</label>
              <input type="text" name="blood_group" class="form-control form-control-sm" value="O+">
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-semibold">Current Semester</label>
              <select name="current_sem" class="form-select form-select-sm">
                <option value="4">4th Semester</option>
                <option value="2">2nd Semester</option>
                <option value="6">6th Semester</option>
                <option value="8">8th Semester</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Department *</label>
              <select name="department_id" class="form-select form-select-sm" required>
                <?php foreach ($depts as $d): ?>
                  <option value="<?= $d['id'] ?>"><?= e($d['name']) ?> (<?= e($d['code']) ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Course *</label>
              <select name="course_id" class="form-select form-select-sm" required>
                <?php foreach ($courses as $c): ?>
                  <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <h6 class="fw-bold text-success mb-3 border-bottom pb-2">Parent / Guardian Contact Details</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Father's Name *</label>
              <input type="text" name="father_name" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Mother's Name</label>
              <input type="text" name="mother_name" class="form-control form-control-sm">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Primary Mobile *</label>
              <input type="text" name="phone" class="form-control form-control-sm" placeholder="10-digit mobile" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Parent Email</label>
              <input type="email" name="email" class="form-control form-control-sm" placeholder="name@example.com">
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Residential Address</label>
              <textarea name="address" class="form-control form-control-sm" rows="2" placeholder="House no, Street, City, Pincode"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">Save Student Record</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
