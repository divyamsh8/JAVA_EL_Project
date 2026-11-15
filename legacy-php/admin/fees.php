<?php
/**
 * Admin Console - Fee Records & Payments Management
 */

declare(strict_types=1);

$adminPageTitle = 'Manage Fees';
$currentAdminPage = 'fees';

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
    } elseif ($action === 'record_payment') {
        $feeId = (int)$_POST['fee_id'];
        $addAmount = (float)$_POST['payment_amount'];
        $paymentMode = sanitize_input($_POST['payment_mode'] ?? 'Online');

        // Fetch current record
        $f = $db->prepare("SELECT * FROM fee_records WHERE id = ?");
        $f->execute([$feeId]);
        $rec = $f->fetch();

        if ($rec) {
            $newPaid = (float)$rec['paid_amount'] + $addAmount;
            $newBalance = max(0.0, (float)$rec['total_amount'] - $newPaid);
            $status = ($newBalance <= 0) ? 'Paid' : (($newPaid > 0) ? 'Partial' : 'Pending');
            $receiptNo = 'REC-2026-' . sprintf('%04d', rand(1000, 9999));
            $payDate = date('Y-m-d');

            $up = $db->prepare("UPDATE fee_records SET paid_amount = ?, balance_amount = ?, payment_status = ?, receipt_no = ?, payment_date = ?, payment_mode = ? WHERE id = ?");
            $up->execute([$newPaid, $newBalance, $status, $receiptNo, $payDate, $paymentMode, $feeId]);
            $message = "Payment of " . format_currency($addAmount) . " recorded. Receipt: {$receiptNo}";
        }
    }
}

$stmt = $db->query("
    SELECT f.*, s.usn, s.first_name, s.last_name, d.code as dept_code
    FROM fee_records f
    JOIN students s ON f.student_id = s.id
    JOIN departments d ON s.department_id = d.id
    ORDER BY f.id DESC
");
$feeList = $stmt->fetchAll();
?>

<div class="portal-wrapper">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="portal-main">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <div>
        <h4 class="fw-bold text-dark mb-1">Fee Accounts &amp; Collection Management</h4>
        <small class="text-muted">Record student tuition collections and issue payment receipts</small>
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

    <div class="content-card">
      <div class="content-card-body p-0">
        <div class="table-responsive">
          <table class="table table-institutional mb-0">
            <thead>
              <tr>
                <th>USN</th>
                <th>Student Name</th>
                <th>Category</th>
                <th class="text-end">Total</th>
                <th class="text-end">Paid</th>
                <th class="text-end">Balance</th>
                <th class="text-center">Status</th>
                <th>Receipt</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($feeList as $row): ?>
                <tr>
                  <td class="fw-bold text-dark"><?= e($row['usn']) ?></td>
                  <td class="fw-semibold"><?= e($row['first_name'] . ' ' . $row['last_name']) ?></td>
                  <td class="small"><?= e($row['fee_category']) ?></td>
                  <td class="text-end"><?= format_currency($row['total_amount']) ?></td>
                  <td class="text-end fw-bold text-success"><?= format_currency($row['paid_amount']) ?></td>
                  <td class="text-end fw-bold <?= (float)$row['balance_amount'] > 0 ? 'text-danger' : 'text-muted' ?>">
                    <?= format_currency($row['balance_amount']) ?>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-<?= ($row['payment_status'] === 'Paid') ? 'success' : (($row['payment_status'] === 'Partial') ? 'warning' : 'danger') ?> px-2 py-1">
                      <?= e($row['payment_status']) ?>
                    </span>
                  </td>
                  <td class="small"><?= e($row['receipt_no'] ?: '-') ?></td>
                  <td class="text-center">
                    <?php if ((float)$row['balance_amount'] > 0): ?>
                      <button class="btn btn-sm btn-outline-success py-0 px-2" data-bs-toggle="modal" data-bs-target="#payModal<?= $row['id'] ?>">
                        <i class="fa-solid fa-plus me-1"></i> Add Pay
                      </button>

                      <!-- Pay Modal -->
                      <div class="modal fade text-start" id="payModal<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-sm">
                          <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                              <h6 class="modal-title fw-bold">Record Payment</h6>
                              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST">
                              <?= csrf_field() ?>
                              <input type="hidden" name="action" value="record_payment">
                              <input type="hidden" name="fee_id" value="<?= $row['id'] ?>">
                              <div class="modal-body p-3">
                                <div class="mb-2">
                                  <small class="text-muted d-block">Student: <?= e($row['usn']) ?></small>
                                  <strong class="text-danger d-block">Due: <?= format_currency($row['balance_amount']) ?></strong>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label small fw-semibold">Amount to Pay *</label>
                                  <input type="number" step="100" name="payment_amount" class="form-control form-control-sm" value="<?= (float)$row['balance_amount'] ?>" max="<?= (float)$row['balance_amount'] ?>" min="1" required>
                                </div>
                                <div class="mb-2">
                                  <label class="form-label small fw-semibold">Payment Mode</label>
                                  <select name="payment_mode" class="form-select form-select-sm">
                                    <option value="Online NetBanking">Online NetBanking</option>
                                    <option value="UPI / QR">UPI / QR</option>
                                    <option value="Demand Draft">Demand Draft</option>
                                    <option value="Bank Challan">Bank Challan</option>
                                  </select>
                                </div>
                              </div>
                              <div class="modal-footer bg-light p-2">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-success fw-bold">Confirm</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    <?php else: ?>
                      <span class="text-success small fw-semibold"><i class="fa-solid fa-check"></i> Cleared</span>
                    <?php endif; ?>
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
