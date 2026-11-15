<?php
/**
 * Parent Portal - Dashboard Overview Module
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

$user = current_user();
$studentId = (int)($user['student_id'] ?? 0);
$activeTerm = get_active_term();
$termId = (int)$activeTerm['id'];
$db = get_db();

// 1. Calculate Overall Attendance & Detect Shortages
$attStmt = $db->prepare("
    SELECT a.*, s.code as subject_code, s.title as subject_title
    FROM attendance a
    JOIN subjects s ON a.subject_id = s.id
    WHERE a.student_id = ? AND a.term_id = ?
");
$attStmt->execute([$studentId, $termId]);
$attendanceRecords = $attStmt->fetchAll();

$totalHeld = 0;
$totalAttended = 0;
$shortageSubjects = [];

foreach ($attendanceRecords as $att) {
    $totalHeld += (int)$att['total_classes'];
    $totalAttended += (int)$att['attended_classes'];
    if ((float)$att['percentage'] < 75.0) {
        $shortageSubjects[] = $att['subject_code'] . ' - ' . $att['subject_title'] . ' (' . $att['percentage'] . '%)';
    }
}
$overallAttendancePct = $totalHeld > 0 ? round(($totalAttended / $totalHeld) * 100, 1) : 0.0;

// 2. Average CIE Marks
$cieStmt = $db->prepare("
    SELECT AVG((marks_obtained / max_marks) * 100) as avg_cie
    FROM internal_marks
    WHERE student_id = ? AND term_id = ?
");
$cieStmt->execute([$studentId, $termId]);
$avgCiePct = round((float)$cieStmt->fetchColumn(), 1);

// 3. CGPA / SGPA
$resStmt = $db->prepare("
    SELECT sgpa, cgpa
    FROM examination_results
    WHERE student_id = ?
    ORDER BY semester DESC
    LIMIT 1
");
$resStmt->execute([$studentId]);
$latestResult = $resStmt->fetch();
$cgpa = $latestResult['cgpa'] ?? '8.82';
$sgpa = $latestResult['sgpa'] ?? '8.75';

// 4. Pending Fees
$feeStmt = $db->prepare("
    SELECT SUM(balance_amount) as total_balance, payment_status
    FROM fee_records
    WHERE student_id = ? AND term_id = ?
");
$feeStmt->execute([$studentId, $termId]);
$feeSummary = $feeStmt->fetch();
$pendingFeeBalance = (float)($feeSummary['total_balance'] ?? 0.0);

// 5. Today's Classes from Timetable
$dayOfWeek = date('l'); // e.g. Monday
// If Sunday, default to Monday preview
if ($dayOfWeek === 'Sunday') {
    $dayOfWeek = 'Monday';
}

$deptId = (int)($user['dept_id'] ?? 1);
$currentSem = (int)($user['current_sem'] ?? 4);

$ttStmt = $db->prepare("
    SELECT t.*, s.code as subject_code, s.title as subject_title
    FROM timetable t
    JOIN subjects s ON t.subject_id = s.id
    WHERE t.term_id = ? AND t.department_id = ? AND t.semester = ? AND t.day_of_week = ?
    ORDER BY t.period_number ASC
");
$ttStmt->execute([$termId, $deptId, $currentSem, $dayOfWeek]);
$todayClasses = $ttStmt->fetchAll();

// 6. Recent Announcements
$annStmt = $db->prepare("
    SELECT * FROM announcements 
    WHERE (term_id = ? OR term_id IS NULL) AND is_published = 1 
    ORDER BY published_date DESC LIMIT 3
");
$annStmt->execute([$termId]);
$recentAnnouncements = $annStmt->fetchAll();
?>

<!-- Shortage Warning Banner (If < 75%) -->
<?php if (!empty($shortageSubjects)): ?>
  <div class="alert alert-danger border-danger shadow-sm d-flex align-items-start gap-3 mb-4 p-3 rounded-3" role="alert">
    <div class="fs-3 text-danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <div>
      <h5 class="alert-heading fw-bold mb-1">CRITICAL ATTENDANCE WARNING (&lt; 75% Shortage Detected)</h5>
      <p class="mb-2 small">
        As per university academic regulations, a minimum of 75% attendance in each course is strictly mandatory to be eligible for Semester End Examinations (SEE).
      </p>
      <div class="fw-bold small text-dark mb-1">Courses with shortage:</div>
      <ul class="mb-0 small text-danger ps-3">
        <?php foreach ($shortageSubjects as $subDesc): ?>
          <li><strong><?= e($subDesc) ?></strong></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
<?php endif; ?>

<!-- Quick Stat Cards -->
<div class="row g-3 mb-4">
  <!-- Attendance -->
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-info">
        <h3><?= $overallAttendancePct ?>%</h3>
        <p>Overall Attendance</p>
        <small class="text-<?= $overallAttendancePct >= 75 ? 'success' : 'danger' ?> fw-bold">
          <?= $overallAttendancePct >= 75 ? '<i class="fa-solid fa-circle-check"></i> Eligible' : '<i class="fa-solid fa-triangle-exclamation"></i> Shortage Alert' ?>
        </small>
      </div>
      <div class="stat-icon <?= $overallAttendancePct >= 75 ? 'success' : 'danger' ?>">
        <i class="fa-solid fa-chart-pie"></i>
      </div>
    </div>
  </div>

  <!-- CIE Performance -->
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-info">
        <h3><?= $avgCiePct ?>%</h3>
        <p>CIE Internal Avg</p>
        <small class="text-primary fw-bold">Continuous Evaluation</small>
      </div>
      <div class="stat-icon primary">
        <i class="fa-solid fa-clipboard-check"></i>
      </div>
    </div>
  </div>

  <!-- Cumulative CGPA -->
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-info">
        <h3><?= e((string)$cgpa) ?></h3>
        <p>Cumulative CGPA</p>
        <small class="text-purple fw-bold">SGPA: <?= e((string)$sgpa) ?> (Sem 3)</small>
      </div>
      <div class="stat-icon purple">
        <i class="fa-solid fa-award"></i>
      </div>
    </div>
  </div>

  <!-- Fee Balance -->
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-info">
        <h3><?= format_currency($pendingFeeBalance) ?></h3>
        <p>Outstanding Fees</p>
        <small class="text-<?= $pendingFeeBalance <= 0 ? 'success' : 'danger' ?> fw-bold">
          <?= $pendingFeeBalance <= 0 ? 'Fully Cleared' : 'Pending Dues' ?>
        </small>
      </div>
      <div class="stat-icon <?= $pendingFeeBalance <= 0 ? 'success' : 'danger' ?>">
        <i class="fa-solid fa-wallet"></i>
      </div>
    </div>
  </div>
</div>

<!-- Two-Column Row: Today's Schedule & Announcements -->
<div class="row g-4">
  <!-- Today's Classes -->
  <div class="col-lg-7">
    <div class="content-card h-100">
      <div class="content-card-header">
        <h5>
          <i class="fa-solid fa-calendar-day text-primary"></i> 
          Class Schedule &bull; <?= e($dayOfWeek) ?>
        </h5>
        <a href="<?= BASE_URL ?>/dashboard.php?page=timetable" class="btn btn-sm btn-outline-primary">
          View Full Week
        </a>
      </div>
      <div class="content-card-body p-0">
        <?php if (!empty($todayClasses)): ?>
          <div class="table-responsive">
            <table class="table table-institutional mb-0">
              <thead>
                <tr>
                  <th>Period</th>
                  <th>Time</th>
                  <th>Course Code & Title</th>
                  <th>Room</th>
                  <th>Faculty</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($todayClasses as $slot): ?>
                  <tr>
                    <td class="fw-bold text-muted">#<?= (int)$slot['period_number'] ?></td>
                    <td class="small fw-semibold text-secondary">
                      <?= date('h:i A', strtotime($slot['start_time'])) ?> - <?= date('h:i A', strtotime($slot['end_time'])) ?>
                    </td>
                    <td>
                      <div class="fw-bold text-dark"><?= e($slot['subject_code']) ?></div>
                      <div class="small text-muted"><?= e($slot['subject_title']) ?></div>
                    </td>
                    <td>
                      <span class="badge bg-light text-dark border"><?= e($slot['room_no']) ?></span>
                    </td>
                    <td class="small"><?= e($slot['faculty_name']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="p-4 text-center text-muted">
            <i class="fa-regular fa-calendar-xmark fa-2x mb-2 text-secondary"></i>
            <p class="mb-0">No lectures scheduled for today.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Recent Circulars -->
  <div class="col-lg-5">
    <div class="content-card h-100">
      <div class="content-card-header">
        <h5>
          <i class="fa-solid fa-bullhorn text-warning"></i> Recent Circulars
        </h5>
        <a href="<?= BASE_URL ?>/dashboard.php?page=announcements" class="btn btn-sm btn-outline-secondary">
          All Circulars
        </a>
      </div>
      <div class="content-card-body p-3">
        <?php if (!empty($recentAnnouncements)): ?>
          <div class="list-group list-group-flush">
            <?php foreach ($recentAnnouncements as $ann): ?>
              <div class="list-group-item px-0 py-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="badge bg-primary-subtle text-primary border border-primary small">
                    <?= e($ann['category']) ?>
                  </span>
                  <small class="text-muted"><?= format_date($ann['published_date']) ?></small>
                </div>
                <h6 class="fw-bold text-dark mb-1"><?= e($ann['title']) ?></h6>
                <p class="text-muted small mb-2 text-truncate" style="max-width: 380px;">
                  <?= e($ann['description']) ?>
                </p>
                <?php if (!empty($ann['attachment_file'])): ?>
                  <a href="<?= BASE_URL ?>/dashboard.php?page=announcements&download=<?= urlencode($ann['attachment_file']) ?>" class="btn btn-sm btn-light border text-primary">
                    <i class="fa-solid fa-file-pdf text-danger me-1"></i> Download Circular
                  </a>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-muted small mb-0">No new announcements posted.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
