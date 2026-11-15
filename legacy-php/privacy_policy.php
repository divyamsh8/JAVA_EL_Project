<?php
/**
 * Privacy Policy - Demo Institute of Engineering
 */

declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/helpers.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Privacy Policy - <?= APP_NAME ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/assets/images/crest.svg">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="bg-light">
  <div class="container py-5 my-3" style="max-width: 900px;">
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-body p-4 p-md-5">
        <div class="d-flex align-items-center mb-4 pb-3 border-bottom gap-3">
          <img src="<?= BASE_URL ?>/assets/images/crest.svg" alt="Crest" width="70" height="70">
          <div>
            <h3 class="mb-0 fw-bold text-dark"><?= APP_NAME ?></h3>
            <small class="text-muted">Parent Information Portal &bull; Contineo Academic Management System</small>
          </div>
        </div>

        <h2 class="h4 fw-bold text-primary mb-3">Privacy & Data Handling Policy</h2>
        
        <div class="alert alert-info border-info d-flex align-items-center gap-2 mb-4">
          <i class="fa-solid fa-shield-halved fs-4"></i>
          <div>
            <strong>Educational Data Privacy:</strong> This portal processes fictional academic evaluations. In compliance with student data privacy guidelines, zero real-world personally identifiable information (PII) is gathered or retained.
          </div>
        </div>

        <h5 class="fw-bold mt-4">1. Data Storage & Password Hashing</h5>
        <p class="text-secondary">
          In strict adherence to secure application development standards, no plain-text passwords or dates of birth used for authentication are stored in the database. Authentication credentials utilize industry-standard bcrypt adaptive hashing (<code>password_hash()</code>) with salt rounds.
        </p>

        <h5 class="fw-bold mt-4">2. Session Cookies & Security Headers</h5>
        <p class="text-secondary">
          Sessions are governed using HttpOnly and SameSite=Lax flags, preventing cross-site scripting (XSS) extraction. Inactive sessions automatically expire after 30 minutes.
        </p>

        <h5 class="fw-bold mt-4">3. Audit Logging</h5>
        <p class="text-secondary">
          Administrative login logs track timestamp, IP address, user-agent, and status for security audit purposes and brute-force mitigation.
        </p>

        <div class="mt-5 pt-3 border-top d-flex justify-content-between align-items-center">
          <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-primary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Login
          </a>
          <span class="text-muted small">Effective: Academic Year 2025-2026</span>
        </div>
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
