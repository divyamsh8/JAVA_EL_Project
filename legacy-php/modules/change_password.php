<?php
/**
 * Parent Portal - Change Password Module
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

$user = current_user();
$userId = (int)$user['user_id'];
$db = get_db();

$error = '';
$success = '';

// Generate years
$currentYear = (int)date('Y');
$years = range(1960, $currentYear);
rsort($years);

$months = [
    '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
    '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug',
    '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $error = 'Security token invalid. Please refresh the page.';
    } else {
        $currDd = sanitize_input($_POST['curr_dd'] ?? '');
        $currMm = sanitize_input($_POST['curr_mm'] ?? '');
        $currYyyy = sanitize_input($_POST['curr_yyyy'] ?? '');

        $newDd = sanitize_input($_POST['new_dd'] ?? '');
        $newMm = sanitize_input($_POST['new_mm'] ?? '');
        $newYyyy = sanitize_input($_POST['new_yyyy'] ?? '');

        $confirmDd = sanitize_input($_POST['confirm_dd'] ?? '');
        $confirmMm = sanitize_input($_POST['confirm_mm'] ?? '');
        $confirmYyyy = sanitize_input($_POST['confirm_yyyy'] ?? '');

        if (empty($currDd) || empty($currMm) || empty($currYyyy) ||
            empty($newDd) || empty($newMm) || empty($newYyyy) ||
            empty($confirmDd) || empty($confirmMm) || empty($confirmYyyy)) {
            $error = 'Please fill out all fields.';
        } elseif (!checkdate((int)$currMm, (int)$currDd, (int)$currYyyy)) {
            $error = 'Current Date of Birth is invalid.';
        } elseif (!checkdate((int)$newMm, (int)$newDd, (int)$newYyyy)) {
            $error = 'New Date of Birth is invalid.';
        } elseif (($newDd !== $confirmDd) || ($newMm !== $confirmMm) || ($newYyyy !== $confirmYyyy)) {
            $error = 'New Date of Birth and Confirmation Date of Birth do not match.';
        } else {
            $currDob = sprintf('%04d-%02d-%02d', (int)$currYyyy, (int)$currMm, (int)$currDd);
            $newDob = sprintf('%04d-%02d-%02d', (int)$newYyyy, (int)$newMm, (int)$newDd);

            // Fetch current password hash from database
            $uStmt = $db->prepare("SELECT password_hash FROM users WHERE id = ?");
            $uStmt->execute([$userId]);
            $storedHash = $uStmt->fetchColumn();

            if (!password_verify($currDob, $storedHash)) {
                $error = 'Current Date of Birth (Password) is incorrect.';
            } else {
                $newHash = password_hash($newDob, PASSWORD_DEFAULT);
                $upStmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $upStmt->execute([$newHash, $userId]);

                // Also update student profile DOB
                $upStmt2 = $db->prepare("UPDATE students SET dob = ? WHERE user_id = ?");
                $upStmt2->execute([$newDob, $userId]);

                $success = 'Password (Date of Birth) updated successfully! Please use this updated DOB for subsequent logins.';
            }
        }
    }
}
?>

<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="content-card">
      <div class="content-card-header">
        <h5>
          <i class="fa-solid fa-lock text-warning"></i> 
          Change Account Password (Date of Birth)
        </h5>
      </div>
      <div class="content-card-body p-4">
        <?php if ($error): ?>
          <div class="alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-3">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div><?= e($error) ?></div>
          </div>
        <?php endif; ?>

        <?php if ($success): ?>
          <div class="alert alert-success py-2 px-3 small d-flex align-items-center gap-2 mb-3">
            <i class="fa-solid fa-circle-check"></i>
            <div><?= e($success) ?></div>
          </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/dashboard.php?page=change_password" method="POST">
          <?= csrf_field() ?>

          <!-- Current Password (DOB) -->
          <div class="mb-4">
            <label class="form-label fw-bold text-dark small">Current Password (Date of Birth)</label>
            <div class="row g-2">
              <div class="col-4">
                <select name="curr_dd" class="form-select form-select-sm" required>
                  <option value="" disabled selected>Day</option>
                  <?php for ($d = 1; $d <= 31; $d++): $dStr = sprintf('%02d', $d); ?>
                    <option value="<?= $dStr ?>"><?= $dStr ?></option>
                  <?php endfor; ?>
                </select>
              </div>
              <div class="col-4">
                <select name="curr_mm" class="form-select form-select-sm" required>
                  <option value="" disabled selected>Month</option>
                  <?php foreach ($months as $k => $v): ?>
                    <option value="<?= $k ?>"><?= $v ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-4">
                <select name="curr_yyyy" class="form-select form-select-sm" required>
                  <option value="" disabled selected>Year</option>
                  <?php foreach ($years as $yr): ?>
                    <option value="<?= $yr ?>"><?= $yr ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <hr class="my-4">

          <!-- New Password (DOB) -->
          <div class="mb-3">
            <label class="form-label fw-bold text-dark small">New Password (Date of Birth)</label>
            <div class="row g-2">
              <div class="col-4">
                <select name="new_dd" class="form-select form-select-sm" required>
                  <option value="" disabled selected>Day</option>
                  <?php for ($d = 1; $d <= 31; $d++): $dStr = sprintf('%02d', $d); ?>
                    <option value="<?= $dStr ?>"><?= $dStr ?></option>
                  <?php endfor; ?>
                </select>
              </div>
              <div class="col-4">
                <select name="new_mm" class="form-select form-select-sm" required>
                  <option value="" disabled selected>Month</option>
                  <?php foreach ($months as $k => $v): ?>
                    <option value="<?= $k ?>"><?= $v ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-4">
                <select name="new_yyyy" class="form-select form-select-sm" required>
                  <option value="" disabled selected>Year</option>
                  <?php foreach ($years as $yr): ?>
                    <option value="<?= $yr ?>"><?= $yr ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <!-- Confirm New Password (DOB) -->
          <div class="mb-4">
            <label class="form-label fw-bold text-dark small">Confirm New Password (Date of Birth)</label>
            <div class="row g-2">
              <div class="col-4">
                <select name="confirm_dd" class="form-select form-select-sm" required>
                  <option value="" disabled selected>Day</option>
                  <?php for ($d = 1; $d <= 31; $d++): $dStr = sprintf('%02d', $d); ?>
                    <option value="<?= $dStr ?>"><?= $dStr ?></option>
                  <?php endfor; ?>
                </select>
              </div>
              <div class="col-4">
                <select name="confirm_mm" class="form-select form-select-sm" required>
                  <option value="" disabled selected>Month</option>
                  <?php foreach ($months as $k => $v): ?>
                    <option value="<?= $k ?>"><?= $v ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-4">
                <select name="confirm_yyyy" class="form-select form-select-sm" required>
                  <option value="" disabled selected>Year</option>
                  <?php foreach ($years as $yr): ?>
                    <option value="<?= $yr ?>"><?= $yr ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4 fw-bold">
              <i class="fa-solid fa-floppy-disk me-1"></i> Update Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
