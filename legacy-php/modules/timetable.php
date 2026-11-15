<?php
/**
 * Parent Portal - Timetable Module
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

$user = current_user();
$activeTerm = get_active_term();
$termId = (int)$activeTerm['id'];
$deptId = (int)($user['dept_id'] ?? 1);
$currentSem = (int)($user['current_sem'] ?? 4);
$db = get_db();

// Fetch timetable entries for student's department, term, and semester
$stmt = $db->prepare("
    SELECT t.*, s.code as subject_code, s.title as subject_title, s.type as subject_type
    FROM timetable t
    JOIN subjects s ON t.subject_id = s.id
    WHERE t.term_id = ? AND t.department_id = ? AND t.semester = ?
    ORDER BY FIELD(t.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), t.period_number ASC
");
$stmt->execute([$termId, $deptId, $currentSem]);
$entries = $stmt->fetchAll();

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$periods = [
    1 => ['name' => 'Period 1', 'time' => '09:00 AM - 10:00 AM'],
    2 => ['name' => 'Period 2', 'time' => '10:00 AM - 11:00 AM'],
    3 => ['name' => 'Period 3', 'time' => '11:15 AM - 12:15 PM'],
    4 => ['name' => 'Period 4', 'time' => '12:15 PM - 01:15 PM'],
    5 => ['name' => 'Period 5 / Lab', 'time' => '02:00 PM - 04:00 PM']
];

$grid = [];
foreach ($days as $d) {
    foreach ($periods as $pNum => $pData) {
        $grid[$d][$pNum] = null;
    }
}

foreach ($entries as $e) {
    $grid[$e['day_of_week']][(int)$e['period_number']] = $e;
}
?>

<div class="content-card mb-4">
  <div class="content-card-header">
    <div>
      <h5 class="mb-1">
        <i class="fa-solid fa-calendar-days text-primary"></i> 
        Weekly Class Timetable &bull; Semester <?= $currentSem ?> (<?= e($user['dept_code']) ?>)
      </h5>
      <small class="text-muted">Academic Term: <?= e($activeTerm['name']) ?> &bull; Effective Academic Calendar</small>
    </div>
    <button class="btn btn-sm btn-outline-secondary d-print-none" onclick="window.print()">
      <i class="fa-solid fa-print me-1"></i> Print Timetable
    </button>
  </div>

  <div class="content-card-body p-0">
    <div class="table-responsive">
      <table class="table table-bordered table-institutional mb-0 text-center">
        <thead>
          <tr>
            <th style="width: 120px;" class="bg-light">Day / Period</th>
            <?php foreach ($periods as $num => $p): ?>
              <th>
                <div class="fw-bold"><?= e($p['name']) ?></div>
                <small class="text-muted fw-normal"><?= e($p['time']) ?></small>
              </th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($days as $day): ?>
            <tr>
              <td class="fw-bold bg-light text-dark align-middle"><?= e($day) ?></td>
              <?php foreach ($periods as $pNum => $p): 
                $slot = $grid[$day][$pNum] ?? null;
              ?>
                <td class="p-2 align-middle <?= $slot ? 'bg-white' : 'bg-light-subtle' ?>" style="min-width: 150px;">
                  <?php if ($slot): ?>
                    <div class="p-2 border rounded shadow-none bg-white">
                      <div class="fw-bold text-primary mb-1"><?= e($slot['subject_code']) ?></div>
                      <div class="small fw-semibold text-dark text-truncate" title="<?= e($slot['subject_title']) ?>">
                        <?= e($slot['subject_title']) ?>
                      </div>
                      <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top small text-muted">
                        <span><i class="fa-solid fa-door-open me-1"></i><?= e($slot['room_no']) ?></span>
                        <span class="text-truncate" style="max-width: 90px;" title="<?= e($slot['faculty_name']) ?>">
                          <i class="fa-solid fa-chalkboard-user me-1"></i><?= e($slot['faculty_name']) ?>
                        </span>
                      </div>
                    </div>
                  <?php else: ?>
                    <span class="text-muted small"><em>-</em></span>
                  <?php endif; ?>
                </td>
              <?php endforeach; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
