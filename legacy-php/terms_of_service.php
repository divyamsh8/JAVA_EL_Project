<?php
/**
 * Terms of Service - Demo Institute of Engineering
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
  <title>Terms of Service - <?= APP_NAME ?></title>
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

        <h2 class="h4 fw-bold text-primary mb-3">Terms of Service (Demonstration & Educational Use)</h2>
        
        <div class="alert alert-warning border-warning d-flex align-items-center gap-2 mb-4">
          <i class="fa-solid fa-triangle-exclamation fs-4"></i>
          <div>
            <strong>Demonstration Notice:</strong> This web application is a sandbox replica built solely for educational and training purposes. All student records, parents, grades, and notices are entirely fictional.
          </div>
        </div>

        <h5 class="fw-bold mt-4">1. Authorized Access & Privacy</h5>
        <p class="text-secondary">
          Access to this parent portal demonstration is governed by standard institutional usage principles. Authorized users include enrolled fictional students, parents, faculty, and educational reviewers.
        </p>

        <h5 class="fw-bold mt-4">2. Protection of Credentials</h5>
        <p class="text-secondary">
          Do not input real banking, national identity (Aadhaar/SSN), or sensitive personal information into this portal. The system uses fictional student USNs and birthdates for credential simulation.
        </p>

        <h5 class="fw-bold mt-4">3. Rate Limiting & System Integrity</h5>
        <p class="text-secondary">
          For educational simulation of production grade security, login attempts are strictly capped at 5 failed attempts per 15-minute rolling window per IP address and username to simulate brute-force prevention.
        </p>

        <div class="mt-5 pt-3 border-top d-flex justify-content-between align-items-center">
          <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-primary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Login
          </a>
          <span class="text-muted small">Last updated: February 2026</span>
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
