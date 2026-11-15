<?php
/**
 * Main Landing / Parent Login Page
 * Contineo Platform Recreation - Demo Institute of Engineering
 */

declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

// If user is already logged in, redirect to dashboard
if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$activeTerm = get_active_term();
$termLinks = get_active_term_links();
$loginNotices = get_login_notices();

// Retrieve modal error if present from failed login redirect
$modalError = $_SESSION['modal_error'] ?? null;
unset($_SESSION['modal_error']);

// Generate years 1960 to current year
$currentYear = (int)date('Y');
$years = range(1960, $currentYear);
rsort($years);

$months = [
    '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
    '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug',
    '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Parent Portal - Demo Institute of Engineering</title>

  <!-- Favicon / Crest -->
  <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/assets/images/crest.svg">

  <!-- Bootstrap 5 CSS & Font Awesome 6 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Custom Contineo Landing Page Styles -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body id="page_bg">

  <!-- Error Modal (Failed Login with Live Attempts Count) -->
  <div id="myloginModal" class="modal" role="dialog" aria-modal="true" style="<?= $modalError ? 'display:block;' : 'display:none;' ?>">
    <div class="modal-content">
      <span class="close" aria-label="Close modal">&times;</span>
      <h3>Login Failed</h3>
      <p id="modalErrorMessage">
        <?= e($modalError['message'] ?? 'You have entered an invalid username or password.') ?>
      </p>
      <p id="modalAttemptsBadge" class="attempts-badge">
        <?php if (!empty($modalError['is_locked'])): ?>
          Account locked due to consecutive failed attempts.<br>
          <span style="font-size:14px;color:#64748b;">Please retry in <?= ceil(($modalError['retry_seconds'] ?? 900) / 60) ?> minute(s).</span>
        <?php elseif (isset($modalError['attempts_remaining'])): ?>
          <?= (int)$modalError['attempts_remaining'] ?> attempts remaining
        <?php else: ?>
          0 attempts remaining
        <?php endif; ?>
      </p>
    </div>
  </div>

  <!-- Landing Container -->
  <div class="cn-landing">
    <!-- Left Section: Institute Information & Notice Board -->
    <div class="cn-bgpart1">
      <div class="cn-web-logo">
        <div class="cn-logo">
          <a href="<?= BASE_URL ?>/index.php" title="Demo Institute of Engineering">
            <div class="cn-big-logo">
              <img src="<?= BASE_URL ?>/assets/images/crest.svg" alt="Demo Institute Crest">
            </div>
          </a>
          <div class="cn-landing-logo">
            <a class="cn-heading" href="<?= BASE_URL ?>/index.php">
              DEMO INSTITUTE OF ENGINEERING,<br><?= APP_PINCODE ?>
            </a>
          </div>
        </div>
      </div>

      <div class="cn-welcome">
        <h2>Welcome to Demo Institute of Engineering</h2>
        <p>
          Demo Institute of Engineering (DIE) is an autonomous institution approved by the
          All India Council for Technical Education (AICTE), New Delhi. DIE attained autonomous status from
          Visvesvaraya Technological University, Belagavi. It has been accredited with 'A+' Grade by NAAC.
        </p>

        <!-- Notice Board Box -->
        <div class="cn-cal-events">
          <h3>
            <i class="fa-solid fa-circle-info"></i> Notice Board
          </h3>
          <ul class="cn-notice-list">
            <?php if (!empty($loginNotices)): ?>
              <?php foreach ($loginNotices as $notice): ?>
                <li>
                  <i class="fa-solid fa-info-circle"></i>
                  <span><?= e($notice['content']) ?></span>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li>
                <i class="fa-solid fa-info-circle"></i>
                <span>Welcome to the preview of the new mobile friendly parent portal</span>
              </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>

    <!-- Right Section: Term Links & Login Card -->
    <div class="cn-bgpart2">
      <!-- Mobile Logo Header (shown on small screens) -->
      <div class="cn-mobile-logo">
        <img src="<?= BASE_URL ?>/assets/images/crest.svg" alt="Demo Institute Crest">
        <div class="cn-heading">
          DEMO INSTITUTE OF ENGINEERING,<br><?= APP_PINCODE ?>
        </div>
      </div>

      <!-- Term Sub-Portal Access Links -->
      <?php foreach ($termLinks as $link): ?>
        <div class="cn-links" style="margin-bottom: <?= (int)$link['margin_top'] ?>px;">
          <a href="<?= BASE_URL . e($link['route_slug']) ?>" title="<?= e($link['title']) ?>">
            <?= e($link['title']) ?>
          </a>
        </div>
      <?php endforeach; ?>

      <!-- Login Card -->
      <div class="cn-form-box">
        <form id="login-form" action="<?= BASE_URL ?>/login.php" method="POST" onsubmit="return submitLogin(event)">
          <?= csrf_field() ?>
          <input type="hidden" name="term" value="<?= e($activeTerm['code']) ?>">
          <input type="hidden" name="passwd" id="passwd">

          <h3>Login to Your Account</h3>

          <!-- Username (USN) -->
          <div class="form-group">
            <label class="clr1" for="username">Username</label>
            <div class="input-icon-wrap">
              <i class="fa-regular fa-envelope"></i>
              <input type="text" class="cn-input" name="username" id="username" 
                     placeholder="USN (e.g. 1DM24CS001)" required autocomplete="username" spellcheck="false">
            </div>
          </div>

          <!-- Password (Date of Birth dropdowns) -->
          <div class="form-group">
            <label class="clr1">Password (Date of Birth)</label>
            <div class="dob-select-group">
              <!-- Day -->
              <select name="dd" id="dd" class="cn-select" required aria-label="Day of Birth">
                <option value="" disabled selected>Day</option>
                <?php for ($d = 1; $d <= 31; $d++): $dStr = sprintf('%02d', $d); ?>
                  <option value="<?= $dStr ?>"><?= $dStr ?></option>
                <?php endfor; ?>
              </select>

              <!-- Month -->
              <select name="mm" id="mm" class="cn-select" required aria-label="Month of Birth">
                <option value="" disabled selected>Month</option>
                <?php foreach ($months as $num => $mon): ?>
                  <option value="<?= $num ?>"><?= $mon ?></option>
                <?php endforeach; ?>
              </select>

              <!-- Year -->
              <select name="yyyy" id="yyyy" class="cn-select" required aria-label="Year of Birth">
                <option value="" disabled selected>Year</option>
                <?php foreach ($years as $yr): ?>
                  <option value="<?= $yr ?>"><?= $yr ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <!-- Form Actions: Forgot Password & Submit -->
          <div class="form-action-row">
            <a class="cn-forgot-link" href="<?= BASE_URL ?>/forgot_password.php">Forgot Password?</a>
            <button type="submit" class="cn-login-btn">Login</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Footer with Safety Notice Badge -->
  <footer>
    <div class="cn-footer-container">
      <div class="cn-footer-text">
        <p class="mb-0">
          Copyright &copy; Powered By <a href="<?= VENDOR_URL ?>" target="_blank" rel="noopener noreferrer"><?= VENDOR_NAME ?></a>
        </p>
      </div>

      <!-- Non-negotiable visible DEMO badge -->
      <div class="text-center">
        <span class="demo-badge">
          <i class="fa-solid fa-triangle-exclamation"></i> <?= DEMO_BADGE_TEXT ?>
        </span>
      </div>

      <div class="cn-footer-links text-end">
        <a href="<?= BASE_URL ?>/terms_of_service.php" class="text-light text-decoration-none me-2">Terms of Service</a> |
        <a href="<?= BASE_URL ?>/privacy_policy.php" class="text-light text-decoration-none ms-2 me-2">Privacy Policy</a> |
        <a href="<?= BASE_URL ?>/admin/login.php" class="text-warning text-decoration-none ms-2" title="Faculty & Administrator Portal">
          <i class="fa-solid fa-lock"></i> Admin
        </a>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
