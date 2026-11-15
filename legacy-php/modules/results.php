<?php
/**
 * Parent Portal - Examination Results Module
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

$user = current_user();
$studentId = (int)($user['student_id'] ?? 0);
$db = get_db();

// Selected semester from query parameter (default to 3)
$selectedSem = (int)($_GET['sem'] ?? 3);

// Fetch available semesters for this student
$semStmt = $db->prepare("
    SELECT DISTINCT semester, sgpa, cgpa, exam_session, declared_date
    FROM examination_results
    WHERE student_id = ?
    ORDER BY semester DESC
");
$semStmt->execute([$studentId]);
$availableSemesters = $semStmt->fetchAll();

// Fetch subject results for selected semester
$resStmt = $db->prepare("
    SELECT r.*, s.code as subject_code, s.title as subject_title, s.type as subject_type
    FROM examination_results r
    JOIN subjects s ON r.subject_id = s.id
    WHERE r.student_id = ? AND r.semester = ?
    ORDER BY s.type DESC, s.code ASC
");
$resStmt->execute([$studentId, $selectedSem]);
$results = $resStmt->fetchAll();

$currSemInfo = null;
foreach ($availableSemesters as $sem) {
    if ((int)$sem['semester'] === $selectedSem) {
        $currSemInfo = $sem;
        break;
    }
}
?>

<!-- Semester Navigation Tabs -->
<ul class="nav nav-pills mb-4 gap-2">
  <?php foreach ($availableSemesters as $sem): $sNum = (int)$sem['semester']; ?>
    <li class="nav-item">
      <a class="nav-link <?= ($sNum === $selectedSem) ? 'active bg-primary' : 'bg-white border text-dark' ?>" 
         href="?page=results&sem=<?= $sNum ?>">
        <i class="fa-solid fa-graduation-cap me-1"></i> Semester <?= $sNum ?> Results
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<!-- Results Content Card -->
<div class="content-card mb-4">
  <div class="content-card-header">
    <div>
      <h5 class="mb-1">
        <i class="fa-solid fa-award text-warning"></i> 
        Provisional Grade Sheet &bull; Semester <?= $selectedSem ?>
      </h5>
      <small class="text-muted">
        Exam Session: <?= e($currSemInfo['exam_session'] ?? 'Nov/Dec 2025 Regular') ?> &bull; 
        Declared: <?= format_date($currSemInfo['declared_date'] ?? '2026-01-10') ?>
      </small>
    </div>
    <button class="btn btn-sm btn-outline-secondary d-print-none" onclick="window.print()">
      <i class="fa-solid fa-print me-1"></i> Print Grade Sheet
    </button>
  </div>

  <div class="content-card-body p-0">
    <div class="table-responsive">
      <table class="table table-institutional mb-0">
        <thead>
          <tr>
            <th>Course Code</th>
            <th>Course Title</th>
            <th>Type</th>
            <th class="text-center">Credits</th>
            <th class="text-center">Grade Letter</th>
            <th class="text-center">Grade Points</th>
            <th class="text-center">Result Status</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $totalCredits = 0;
          $totalPointsEarned = 0;
          foreach ($results as $res): 
            $c = (float)$res['credits'];
            $gp = (float)$res['grade_points'];
            $totalCredits += $c;
            $totalPointsEarned += ($c * $gp);
            $isPass = ($res['result_status'] === 'P');
          ?>
            <tr>
              <td class="fw-bold text-dark"><?= e($res['subject_code']) ?></td>
              <td class="fw-semibold"><?= e($res['subject_title']) ?></td>
              <td><span class="badge bg-light text-secondary border"><?= e($res['subject_type']) ?></span></td>
              <td class="text-center"><?= number_format($c, 1) ?></td>
              <td class="text-center">
                <span class="badge bg-primary fs-6 px-2 py-1"><?= e($res['grade_letter']) ?></span>
              </td>
              <td class="text-center fw-bold text-dark"><?= number_format($gp, 2) ?></td>
              <td class="text-center">
                <?php if ($isPass): ?>
                  <span class="badge bg-success-subtle text-success border border-success fw-bold">PASS</span>
                <?php else: ?>
                  <span class="badge bg-danger-subtle text-danger border border-danger fw-bold">FAIL</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Summary Footer Strip -->
    <div class="bg-light p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-3">
      <div>
        <span class="text-muted fw-semibold me-3">Total Registered Credits: <strong><?= number_format($totalCredits, 1) ?></strong></span>
        <span class="text-muted fw-semibold">Credits Earned: <strong><?= number_format($totalCredits, 1) ?></strong></span>
      </div>
      <div class="d-flex gap-4">
        <div>
          <span class="text-muted small d-block">Semester SGPA</span>
          <span class="fs-4 fw-bold text-primary"><?= e((string)($currSemInfo['sgpa'] ?? '8.75')) ?></span>
        </div>
        <div class="border-start ps-4">
          <span class="text-muted small d-block">Cumulative CGPA</span>
          <span class="fs-4 fw-bold text-success"><?= e((string)($currSemInfo['cgpa'] ?? '8.82')) ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 bg-white shadow-sm p-3 rounded-3">
  <h6 class="fw-bold mb-2 text-dark"><i class="fa-solid fa-circle-question text-info me-1"></i> Grading System Reference</h6>
  <div class="row g-2 text-muted small">
    <div class="col-sm-4"><strong>S Grade:</strong> Outstanding (10 Points) &bull; Marks &ge; 90%</div>
    <div class="col-sm-4"><strong>A Grade:</strong> Excellent (9 Points) &bull; Marks 80 - 89%</div>
    <div class="col-sm-4"><strong>B Grade:</strong> Very Good (8 Points) &bull; Marks 70 - 79%</div>
    <div class="col-sm-4"><strong>C Grade:</strong> Good (7 Points) &bull; Marks 60 - 69%</div>
    <div class="col-sm-4"><strong>D Grade:</strong> Satisfactory (6 Points) &bull; Marks 50 - 59%</div>
    <div class="col-sm-4"><strong>F Grade:</strong> Fail (0 Points) &bull; Marks &lt; 40%</div>
  </div>
</div>
