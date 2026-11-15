<?php
/**
 * Parent Portal - Attendance Module
 * Subject-wise breakdown, monthly records, Chart.js analytics, and shortage warning
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

// Subject-wise attendance
$stmt = $db->prepare("
    SELECT a.*, s.code as subject_code, s.title as subject_title, s.credits, s.type as subject_type
    FROM attendance a
    JOIN subjects s ON a.subject_id = s.id
    WHERE a.student_id = ? AND a.term_id = ?
    ORDER BY s.type DESC, s.code ASC
");
$stmt->execute([$studentId, $termId]);
$attendanceList = $stmt->fetchAll();

$totalHeld = 0;
$totalAttended = 0;
$hasShortage = false;
$chartLabels = [];
$chartData = [];
$chartColors = [];

foreach ($attendanceList as $row) {
    $totalHeld += (int)$row['total_classes'];
    $totalAttended += (int)$row['attended_classes'];
    $pct = (float)$row['percentage'];
    if ($pct < 75.0) {
        $hasShortage = true;
    }
    $chartLabels[] = $row['subject_code'];
    $chartData[] = $pct;
    $chartColors[] = $pct < 75.0 ? 'rgba(220, 38, 38, 0.85)' : 'rgba(2, 132, 199, 0.85)';
}

$overallPct = $totalHeld > 0 ? round(($totalAttended / $totalHeld) * 100, 2) : 0.00;
?>

<!-- Shortage Alert Banner -->
<?php if ($hasShortage): ?>
  <div class="alert alert-danger border-danger d-flex align-items-center gap-3 p-3 rounded-3 mb-4 shadow-sm">
    <div class="fs-2 text-danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <div>
      <h6 class="fw-bold mb-1 text-danger">ATTENDANCE SHORTAGE WARNING DETECTED (&lt; 75%)</h6>
      <p class="mb-0 small">
        One or more courses have attendance below the mandatory 75% requirement. A student with less than 75% attendance will NOT be permitted to write the Semester End Examinations in the respective course as per University Regulations.
      </p>
    </div>
  </div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-info">
        <h3><?= $overallPct ?>%</h3>
        <p>Overall Percentage</p>
        <small class="text-<?= $overallPct >= 75 ? 'success' : 'danger' ?> fw-bold">
          <?= $overallPct >= 75 ? 'Eligible for Exams' : 'Condonation Required' ?>
        </small>
      </div>
      <div class="stat-icon <?= $overallPct >= 75 ? 'success' : 'danger' ?>">
        <i class="fa-solid fa-percent"></i>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-info">
        <h3><?= $totalAttended ?> / <?= $totalHeld ?></h3>
        <p>Classes Attended / Held</p>
        <small class="text-secondary fw-semibold">Across <?= count($attendanceList) ?> Registered Courses</small>
      </div>
      <div class="stat-icon primary">
        <i class="fa-solid fa-user-check"></i>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-info">
        <h3><?= $hasShortage ? 'Attention Needed' : 'In Good Standing' ?></h3>
        <p>Eligibility Status</p>
        <small class="<?= $hasShortage ? 'text-danger' : 'text-success' ?> fw-bold">
          <?= $hasShortage ? 'Shortage in Course(s)' : 'Satisfies 75% Rule' ?>
        </small>
      </div>
      <div class="stat-icon <?= $hasShortage ? 'danger' : 'success' ?>">
        <i class="fa-solid <?= $hasShortage ? 'fa-triangle-exclamation' : 'fa-check-double' ?>"></i>
      </div>
    </div>
  </div>
</div>

<!-- Attendance Chart & Visual Analytics -->
<div class="content-card mb-4">
  <div class="content-card-header">
    <h5>
      <i class="fa-solid fa-chart-column text-primary"></i> 
      Course-Wise Attendance Graph &amp; Thresholds
    </h5>
    <span class="badge bg-light text-muted border">Minimum Required: 75%</span>
  </div>
  <div class="content-card-body p-4">
    <div style="height: 280px; position: relative;">
      <canvas id="attendanceChart"></canvas>
    </div>
  </div>
</div>

<!-- Subject-Wise Attendance Table -->
<div class="content-card">
  <div class="content-card-header">
    <h5>
      <i class="fa-solid fa-table-list text-primary"></i> 
      Subject-Wise Attendance Details (Term: <?= e($activeTerm['name']) ?>)
    </h5>
  </div>
  <div class="content-card-body p-0">
    <div class="table-responsive">
      <table class="table table-institutional mb-0">
        <thead>
          <tr>
            <th>Course Code</th>
            <th>Course Title</th>
            <th>Type</th>
            <th>Credits</th>
            <th class="text-center">Classes Held</th>
            <th class="text-center">Attended</th>
            <th class="text-center">Percentage</th>
            <th class="text-center">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($attendanceList as $att): 
            $pct = (float)$att['percentage'];
            $isLow = $pct < 75.0;
          ?>
            <tr class="<?= $isLow ? 'table-danger-subtle' : '' ?>">
              <td class="fw-bold text-dark"><?= e($att['subject_code']) ?></td>
              <td>
                <div class="fw-semibold"><?= e($att['subject_title']) ?></div>
              </td>
              <td>
                <span class="badge bg-light text-secondary border"><?= e($att['subject_type']) ?></span>
              </td>
              <td><?= e((string)$att['credits']) ?></td>
              <td class="text-center fw-semibold"><?= (int)$att['total_classes'] ?></td>
              <td class="text-center fw-semibold"><?= (int)$att['attended_classes'] ?></td>
              <td class="text-center">
                <span class="fw-bold <?= $isLow ? 'text-danger' : 'text-success' ?> fs-6">
                  <?= number_format($pct, 2) ?>%
                </span>
              </td>
              <td class="text-center">
                <?php if ($isLow): ?>
                  <span class="badge-shortage">
                    <i class="fa-solid fa-triangle-exclamation"></i> SHORTAGE
                  </span>
                <?php else: ?>
                  <span class="badge-normal">
                    <i class="fa-solid fa-check"></i> SATISFACTORY
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  var ctx = document.getElementById('attendanceChart');
  if (ctx) {
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
          label: 'Attendance %',
          data: <?= json_encode($chartData) ?>,
          backgroundColor: <?= json_encode($chartColors) ?>,
          borderRadius: 4,
          maxBarThickness: 45
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function(context) {
                return context.parsed.y + '% Attendance';
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            max: 100,
            ticks: {
              callback: function(value) { return value + '%'; }
            },
            grid: {
              color: function(context) {
                if (context.tick.value === 75) {
                  return 'rgba(220, 38, 38, 0.6)'; // Red threshold line
                }
                return 'rgba(226, 232, 240, 0.6)';
              },
              lineWidth: function(context) {
                return context.tick.value === 75 ? 2 : 1;
              }
            }
          },
          x: {
            grid: { display: false }
          }
        }
      }
    });
  }
});
</script>
