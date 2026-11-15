<?php
/**
 * Parent Portal - Fees Structure & Payment Receipts Module
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

// Handle receipt view/download
if (isset($_GET['receipt'])) {
    $receiptNo = sanitize_input($_GET['receipt']);
    $rStmt = $db->prepare("
        SELECT f.*, s.first_name, s.last_name, s.usn, d.name as dept_name, c.name as course_name
        FROM fee_records f
        JOIN students s ON f.student_id = s.id
        JOIN departments d ON s.department_id = d.id
        JOIN courses c ON s.course_id = c.id
        WHERE f.receipt_no = ? AND f.student_id = ?
        LIMIT 1
    ");
    $rStmt->execute([$receiptNo, $studentId]);
    $receiptData = $rStmt->fetch();
}

// Fetch fee records for this student and term
$stmt = $db->prepare("
    SELECT * FROM fee_records 
    WHERE student_id = ? AND term_id = ?
    ORDER BY id DESC
");
$stmt->execute([$studentId, $termId]);
$feeRecords = $stmt->fetchAll();

$totalFee = 0.0;
$totalPaid = 0.0;
$totalBalance = 0.0;

foreach ($feeRecords as $rec) {
    $totalFee += (float)$rec['total_amount'];
    $totalPaid += (float)$rec['paid_amount'];
    $totalBalance += (float)$rec['balance_amount'];
}
?>

<!-- Financial Summary Cards -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-info">
        <h3><?= format_currency($totalFee) ?></h3>
        <p>Total Term Fee</p>
        <small class="text-secondary fw-semibold">Academic Year <?= e($activeTerm['academic_year']) ?></small>
      </div>
      <div class="stat-icon primary">
        <i class="fa-solid fa-receipt"></i>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-info">
        <h3><?= format_currency($totalPaid) ?></h3>
        <p>Total Amount Paid</p>
        <small class="text-success fw-bold">Verified Bank Remittance</small>
      </div>
      <div class="stat-icon success">
        <i class="fa-solid fa-circle-check"></i>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-info">
        <h3><?= format_currency($totalBalance) ?></h3>
        <p>Outstanding Dues</p>
        <small class="text-<?= $totalBalance <= 0 ? 'success' : 'danger' ?> fw-bold">
          <?= $totalBalance <= 0 ? 'No Pending Dues' : 'Payment Overdue / Pending' ?>
        </small>
      </div>
      <div class="stat-icon <?= $totalBalance <= 0 ? 'success' : 'danger' ?>">
        <i class="fa-solid fa-wallet"></i>
      </div>
    </div>
  </div>
</div>

<!-- Receipt Modal if clicked -->
<?php if (!empty($receiptData)): ?>
  <div class="card border-primary shadow-sm mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h6 class="mb-0 fw-bold"><i class="fa-solid fa-receipt me-2"></i> Official Fee Receipt &bull; <?= e($receiptData['receipt_no']) ?></h6>
      <a href="<?= BASE_URL ?>/dashboard.php?page=fees" class="btn btn-sm btn-light py-0">Close</a>
    </div>
    <div class="card-body p-4 bg-white">
      <div class="text-center pb-3 border-bottom mb-3">
        <h5 class="fw-bold text-dark mb-0"><?= APP_NAME ?></h5>
        <small class="text-muted">Accounts &amp; Finance Section &bull; Computer Generated Fee Voucher</small>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-sm-6">
          <small class="text-muted d-block">Student Name</small>
          <strong><?= e($receiptData['first_name'] . ' ' . $receiptData['last_name']) ?> (<?= e($receiptData['usn']) ?>)</strong>
        </div>
        <div class="col-sm-6">
          <small class="text-muted d-block">Department &amp; Course</small>
          <span><?= e($receiptData['dept_name']) ?></span>
        </div>
        <div class="col-sm-6">
          <small class="text-muted d-block">Receipt No &amp; Date</small>
          <span><?= e($receiptData['receipt_no']) ?> &bull; <?= format_date($receiptData['payment_date']) ?></span>
        </div>
        <div class="col-sm-6">
          <small class="text-muted d-block">Payment Mode</small>
          <span><?= e($receiptData['payment_mode']) ?></span>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered mb-0">
          <thead class="bg-light">
            <tr>
              <th>Fee Category Description</th>
              <th class="text-end">Amount Paid</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?= e($receiptData['fee_category']) ?></td>
              <td class="text-end fw-bold text-success fs-5"><?= format_currency($receiptData['paid_amount']) ?></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
        <small class="text-muted">Status: <span class="badge bg-success">Remitted</span></small>
        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
          <i class="fa-solid fa-print me-1"></i> Print Receipt
        </button>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Fee Records Table -->
<div class="content-card mb-4">
  <div class="content-card-header">
    <h5>
      <i class="fa-solid fa-file-invoice-dollar text-primary"></i> 
      Institutional Fee Details &bull; Term: <?= e($activeTerm['name']) ?>
    </h5>
  </div>
  <div class="content-card-body p-0">
    <div class="table-responsive">
      <table class="table table-institutional mb-0">
        <thead>
          <tr>
            <th>Academic Year</th>
            <th>Fee Category</th>
            <th class="text-end">Total Amount</th>
            <th class="text-end">Paid Amount</th>
            <th class="text-end">Balance Due</th>
            <th class="text-center">Status</th>
            <th class="text-center">Action / Receipt</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($feeRecords as $fee): ?>
            <tr>
              <td class="fw-bold text-muted"><?= e($fee['academic_year']) ?></td>
              <td class="fw-semibold text-dark"><?= e($fee['fee_category']) ?></td>
              <td class="text-end"><?= format_currency($fee['total_amount']) ?></td>
              <td class="text-end fw-bold text-success"><?= format_currency($fee['paid_amount']) ?></td>
              <td class="text-end fw-bold <?= (float)$fee['balance_amount'] > 0 ? 'text-danger' : 'text-muted' ?>">
                <?= format_currency($fee['balance_amount']) ?>
              </td>
              <td class="text-center">
                <?php if ($fee['payment_status'] === 'Paid'): ?>
                  <span class="badge bg-success-subtle text-success border border-success fw-bold">PAID</span>
                <?php elseif ($fee['payment_status'] === 'Partial'): ?>
                  <span class="badge bg-warning-subtle text-warning border border-warning fw-bold">PARTIAL</span>
                <?php else: ?>
                  <span class="badge bg-danger-subtle text-danger border border-danger fw-bold">PENDING</span>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <?php if (!empty($fee['receipt_no'])): ?>
                  <a href="<?= BASE_URL ?>/dashboard.php?page=fees&receipt=<?= urlencode($fee['receipt_no']) ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fa-solid fa-receipt me-1"></i> <?= e($fee['receipt_no']) ?>
                  </a>
                <?php else: ?>
                  <span class="text-muted small">No Receipt</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
