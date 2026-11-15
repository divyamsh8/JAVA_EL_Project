<?php
/**
 * Forgot Password - Demo Flow with Simulated OTP
 * Contineo Parent Portal - Demo Institute of Engineering
 */

declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/helpers.php';

$error = '';
$success = '';
$step = (int)($_SESSION['reset_step'] ?? 1);

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
    $action = $_POST['action'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($token)) {
        $error = 'Security token invalid. Please try again.';
    } elseif ($action === 'verify_details') {
        $usn = strtoupper(sanitize_input($_POST['username'] ?? ''));
        $dd = sanitize_input($_POST['dd'] ?? '');
        $mm = sanitize_input($_POST['mm'] ?? '');
        $yyyy = sanitize_input($_POST['yyyy'] ?? '');
        $contact = sanitize_input($_POST['contact'] ?? '');

        if (empty($usn) || empty($dd) || empty($mm) || empty($yyyy) || empty($contact)) {
            $error = 'Please fill in all verification fields.';
        } elseif (!checkdate((int)$mm, (int)$dd, (int)$yyyy)) {
            $error = 'Invalid date of birth selected.';
        } else {
            $inputDob = sprintf('%04d-%02d-%02d', (int)$yyyy, (int)$mm, (int)$dd);
            $db = get_db();

            $stmt = $db->prepare("
                SELECT u.id as user_id, u.username, s.id as student_id, s.first_name, s.dob, p.email as parent_email, p.primary_mobile
                FROM users u
                JOIN students s ON s.user_id = u.id
                JOIN parents p ON s.parent_id = p.id
                WHERE u.username = ? AND s.dob = ?
                LIMIT 1
            ");
            $stmt->execute([$usn, $inputDob]);
            $record = $stmt->fetch();

            if ($record && ($record['parent_email'] === $contact || $record['primary_mobile'] === $contact)) {
                // Generate 6 digit simulated OTP
                $otp = (string)random_int(100000, 999999);
                $_SESSION['reset_user_id'] = $record['user_id'];
                $_SESSION['reset_usn'] = $record['username'];
                $_SESSION['reset_otp'] = $otp;
                $_SESSION['reset_student_name'] = $record['first_name'];
                $_SESSION['reset_contact_masked'] = substr($contact, 0, 3) . '****' . substr($contact, -3);
                $_SESSION['reset_step'] = 2;
                $step = 2;
            } else {
                $error = 'No matching student record found with the supplied USN, Date of Birth, and registered contact.';
            }
        }
    } elseif ($action === 'verify_otp_and_reset') {
        $enteredOtp = sanitize_input($_POST['otp'] ?? '');
        $newDd = sanitize_input($_POST['new_dd'] ?? '');
        $newMm = sanitize_input($_POST['new_mm'] ?? '');
        $newYyyy = sanitize_input($_POST['new_yyyy'] ?? '');

        if ($enteredOtp !== ($_SESSION['reset_otp'] ?? '')) {
            $error = 'Invalid OTP entered. Please use the simulated OTP shown in the demo banner.';
        } elseif (empty($newDd) || empty($newMm) || empty($newYyyy) || !checkdate((int)$newMm, (int)$newDd, (int)$newYyyy)) {
            $error = 'Please select a valid new Date of Birth.';
        } else {
            $newDob = sprintf('%04d-%02d-%02d', (int)$newYyyy, (int)$newMm, (int)$newDd);
            $newHash = password_hash($newDob, PASSWORD_DEFAULT);
            $userId = (int)$_SESSION['reset_user_id'];

            $db = get_db();
            // Update user password and student DOB
            $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$newHash, $userId]);

            $stmt2 = $db->prepare("UPDATE students SET dob = ? WHERE user_id = ?");
            $stmt2->execute([$newDob, $userId]);

            // Clear reset session
            unset($_SESSION['reset_step'], $_SESSION['reset_otp'], $_SESSION['reset_user_id'], $_SESSION['reset_usn']);
            $step = 3;
            $success = 'Your password (Date of Birth) has been reset successfully! You can now log in with your updated credentials.';
        }
    }
}

// Reset button to start over
if (isset($_GET['restart'])) {
    unset($_SESSION['reset_step'], $_SESSION['reset_otp'], $_SESSION['reset_user_id']);
    header('Location: ' . BASE_URL . '/forgot_password.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password - <?= APP_NAME ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/assets/images/crest.svg">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="bg-light">

  <div class="container py-5 my-2" style="max-width: 580px;">
    <div class="card shadow border-0 rounded-3">
      <div class="card-header bg-white border-bottom-0 text-center pt-4 pb-2">
        <a href="<?= BASE_URL ?>/index.php">
          <img src="<?= BASE_URL ?>/assets/images/crest.svg" alt="Crest" width="75" height="75">
        </a>
        <h4 class="mt-2 fw-bold text-dark mb-0"><?= APP_NAME ?></h4>
        <small class="text-muted">Parent Portal Account Recovery</small>
      </div>

      <div class="card-body p-4 p-md-4">
        <?php if ($error): ?>
          <div class="alert alert-danger py-2 px-3 small d-flex align-items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div><?= e($error) ?></div>
          </div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
          <h5 class="fw-bold mb-3 border-bottom pb-2">Step 1: Verify Student & Parent Record</h5>
          <p class="text-muted small mb-4">
            Provide the student's USN, current registered Date of Birth, and the registered parent email or phone number.
          </p>

          <form action="<?= BASE_URL ?>/forgot_password.php" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="verify_details">

            <div class="mb-3">
              <label class="form-label fw-semibold small">Student USN</label>
              <input type="text" name="username" class="form-control" placeholder="e.g. 1DM24CS001" required value="<?= e($_POST['username'] ?? '') ?>">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold small">Registered Date of Birth</label>
              <div class="row g-2">
                <div class="col-4">
                  <select name="dd" class="form-select form-select-sm" required>
                    <option value="" disabled selected>Day</option>
                    <?php for ($d = 1; $d <= 31; $d++): $dStr = sprintf('%02d', $d); ?>
                      <option value="<?= $dStr ?>" <?= (isset($_POST['dd']) && $_POST['dd'] === $dStr) ? 'selected' : '' ?>><?= $dStr ?></option>
                    <?php endfor; ?>
                  </select>
                </div>
                <div class="col-4">
                  <select name="mm" class="form-select form-select-sm" required>
                    <option value="" disabled selected>Month</option>
                    <?php foreach ($months as $k => $v): ?>
                      <option value="<?= $k ?>" <?= (isset($_POST['mm']) && $_POST['mm'] === $k) ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-4">
                  <select name="yyyy" class="form-select form-select-sm" required>
                    <option value="" disabled selected>Year</option>
                    <?php foreach ($years as $yr): ?>
                      <option value="<?= $yr ?>" <?= (isset($_POST['yyyy']) && (int)$_POST['yyyy'] === $yr) ? 'selected' : '' ?>><?= $yr ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold small">Registered Email or Mobile</label>
              <input type="text" name="contact" class="form-control" placeholder="e.g. 9845012345 or rajesh.sharma@example.com" required value="<?= e($_POST['contact'] ?? '') ?>">
              <small class="text-muted d-block mt-1">Hint for demo: USN 1DM24CS001 uses DOB 15-Apr-2005 & phone 9845012345.</small>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <a href="<?= BASE_URL ?>/index.php" class="text-decoration-none small text-muted">
                <i class="fa-solid fa-arrow-left"></i> Cancel
              </a>
              <button type="submit" class="btn btn-primary px-4 fw-bold">Send OTP</button>
            </div>
          </form>

        <?php elseif ($step === 2): ?>
          <div class="alert alert-success border-success py-2 px-3 small mb-3">
            <div class="fw-bold mb-1"><i class="fa-solid fa-satellite-dish"></i> [DEMO SIMULATION SCREEN]</div>
            A simulated 6-digit verification code has been dispatched to <strong><?= e($_SESSION['reset_contact_masked']) ?></strong>.
            <div class="fs-5 fw-bold text-success mt-1 letter-spacing-2">Demo OTP: <mark class="px-2 py-1 bg-warning rounded"><?= e($_SESSION['reset_otp']) ?></mark></div>
          </div>

          <h5 class="fw-bold mb-3 border-bottom pb-2">Step 2: Enter Verification Code & Set New Password</h5>

          <form action="<?= BASE_URL ?>/forgot_password.php" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="verify_otp_and_reset">

            <div class="mb-3">
              <label class="form-label fw-semibold small">Enter 6-Digit OTP</label>
              <input type="text" name="otp" class="form-control text-center fs-4 fw-bold letter-spacing-2" maxlength="6" placeholder="______" required autofocus>
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold small">Select New Password (Date of Birth)</label>
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

            <div class="d-flex justify-content-between align-items-center">
              <a href="<?= BASE_URL ?>/forgot_password.php?restart=1" class="text-decoration-none small text-muted">
                <i class="fa-solid fa-rotate-left"></i> Start Over
              </a>
              <button type="submit" class="btn btn-success px-4 fw-bold">Update Password</button>
            </div>
          </form>

        <?php elseif ($step === 3): ?>
          <div class="text-center py-4">
            <div class="text-success mb-3">
              <i class="fa-regular fa-circle-check fa-4x"></i>
            </div>
            <h4 class="fw-bold text-success mb-2">Password Reset Successful!</h4>
            <p class="text-muted mb-4"><?= e($success) ?></p>
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary px-4 py-2 fw-bold">
              <i class="fa-solid fa-right-to-bracket me-2"></i> Go to Login
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <footer>
    <div class="cn-footer-container">
      <div class="cn-footer-text">
        <p class="mb-0">
          Copyright &copy; Powered By <a href="<?= VENDOR_URL ?>" target="_blank"><?= VENDOR_NAME ?></a>
        </p>
      </div>
      <div class="text-center">
        <span class="demo-badge">
          <i class="fa-solid fa-triangle-exclamation"></i> <?= DEMO_BADGE_TEXT ?>
        </span>
      </div>
      <div class="cn-footer-links text-end">
        <a href="<?= BASE_URL ?>/terms_of_service.php" class="text-light text-decoration-none me-2">Terms of Service</a> |
        <a href="<?= BASE_URL ?>/privacy_policy.php" class="text-light text-decoration-none ms-2">Privacy Policy</a>
      </div>
    </div>
  </footer>

</body>
</html>
