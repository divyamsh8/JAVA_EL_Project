<?php
/**
 * Parent Portal - Continuous Internal Evaluation (CIE) Module
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

// Fetch subjects and their internal marks for this student and term
$stmt = $db->prepare("
    SELECT s.id as subject_id, s.code, s.title, s.type, s.credits,
           m.test_name, m.max_marks, m.marks_obtained, m.remarks
    FROM subjects s
    LEFT JOIN internal_marks m ON m.subject_id = s.id AND m.student_id = ? AND m.term_id = ?
    WHERE s.department_id = ? AND s.term_id = ?
    ORDER BY s.type DESC, s.code ASC, m.test_name ASC
");
$deptId = (int)($user['dept_id'] ?? 1);
$stmt->execute([$studentId, $termId, $deptId, $termId]);
$rawMarks = $stmt->fetchAll();

// Group by subject
$subjectMarks = [];
foreach ($rawMarks as $row) {
    $sId = $row['subject_id'];
    if (!isset($subjectMarks[$sId])) {
        $subjectMarks[$sId] = [
            'code' => $row['code'],
            'title' => $row['title'],
            'type' => $row['type'],
            'credits' => $row['credits'],
            'cie1' => null,
            'cie2' => null,
            'assignment' => null
        ];
    }
    if ($row['test_name'] === 'CIE-1') {
        $subjectMarks[$sId]['cie1'] = (float)$row['marks_obtained'];
    } elseif ($row['test_name'] === 'CIE-2') {
        $subjectMarks[$sId]['cie2'] = (float)$row['marks_obtained'];
    } elseif ($row['test_name'] === 'Assignment') {
        $subjectMarks[$sId]['assignment'] = (float)$row['marks_obtained'];
    }
}
?>

<div class="content-card mb-4">
  <div class="content-card-header">
    <h5>
      <i class="fa-solid fa-clipboard-check text-primary"></i> 
      Continuous Internal Evaluation (CIE) Marks &bull; Term: <?= e($activeTerm['name']) ?>
    </h5>
    <span class="badge bg-primary-subtle text-primary border border-primary">
      Passing Criteria: Min 20.0 / 50.0 (40%)
    </span>
  </div>
  <div class="content-card-body p-0">
    <div class="table-responsive">
      <table class="table table-institutional mb-0">
        <thead>
          <tr>
            <th>Course Code</th>
            <th>Course Title</th>
            <th>Type</th>
            <th class="text-center">CIE-1 (50)</th>
            <th class="text-center">CIE-2 (50)</th>
            <th class="text-center">Assignment (10)</th>
            <th class="text-center">Calculated Internal (50)</th>
            <th class="text-center">Internal Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($subjectMarks as $s): 
            $cie1 = $s['cie1'] ?? 0.0;
            $cie2 = $s['cie2'] ?? 0.0;
            $ass = $s['assignment'] ?? 0.0;
            
            // Typical VTU/Autonomous scheme: Average of (CIE1 + CIE2) scaled to 40 + Assignment (10) = 50 total
            $avgTest = ($cie1 + $cie2) / 2.0;
            $scaledTest = ($avgTest / 50.0) * 40.0;
            $totalCie = round($scaledTest + $ass, 1);
            $isEligible = $totalCie >= 20.0;
          ?>
            <tr>
              <td class="fw-bold text-dark"><?= e($s['code']) ?></td>
              <td>
                <div class="fw-semibold"><?= e($s['title']) ?></div>
                <small class="text-muted">Credits: <?= e((string)$s['credits']) ?></small>
              </td>
              <td><span class="badge bg-light text-secondary border"><?= e($s['type']) ?></span></td>
              <td class="text-center fw-semibold"><?= $s['cie1'] !== null ? number_format($cie1, 1) : '-' ?></td>
              <td class="text-center fw-semibold"><?= $s['cie2'] !== null ? number_format($cie2, 1) : '-' ?></td>
              <td class="text-center fw-semibold"><?= $s['assignment'] !== null ? number_format($ass, 1) : '-' ?></td>
              <td class="text-center">
                <span class="fs-6 fw-bold <?= $isEligible ? 'text-primary' : 'text-danger' ?>">
                  <?= number_format($totalCie, 1) ?> / 50
                </span>
                <div class="progress mt-1" style="height: 4px;">
                  <div class="progress-bar <?= $isEligible ? 'bg-primary' : 'bg-danger' ?>" role="progressbar" style="width: <?= ($totalCie / 50.0) * 100 ?>%;"></div>
                </div>
              </td>
              <td class="text-center">
                <?php if ($isEligible): ?>
                  <span class="badge bg-success-subtle text-success border border-success">
                    <i class="fa-solid fa-check"></i> QUALIFIED
                  </span>
                <?php else: ?>
                  <span class="badge bg-danger-subtle text-danger border border-danger">
                    <i class="fa-solid fa-xmark"></i> NOT QUALIFIED
                  </span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="alert alert-light border small text-muted">
  <i class="fa-solid fa-circle-info text-primary me-1"></i>
  <strong>Evaluation Policy:</strong> Scaled Continuous Internal Evaluation (CIE) consists of 40 marks derived from the average of CIE Tests plus 10 marks for assignments, quizzes, and course projects. Students scoring below 20.0 (40%) are detained in that course for the semester.
</div>
