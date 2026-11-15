<?php
/**
 * Portal Top Navigation Bar
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

$user = current_user();
$activeTerm = get_active_term();
$allTerms = get_all_terms();
?>
<header class="portal-navbar">
  <div class="d-flex align-items-center gap-3">
    <!-- Mobile Sidebar Toggle -->
    <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" id="sidebarToggle" aria-label="Toggle Navigation">
      <i class="fa-solid fa-bars"></i>
    </button>

    <!-- Institution Brand -->
    <a href="<?= BASE_URL ?>/dashboard.php" class="portal-brand">
      <img src="<?= BASE_URL ?>/assets/images/crest.svg" alt="Crest">
      <div class="portal-brand-text d-none d-sm-block">
        <?= APP_NAME ?>
        <small>Autonomous &bull; Accredited NAAC A+</small>
      </div>
    </a>
  </div>

  <div class="d-flex align-items-center gap-3">
    <!-- Academic Term Scoping Switcher -->
    <div class="dropdown">
      <button class="btn btn-sm btn-outline-primary dropdown-toggle d-flex align-items-center gap-2 fw-semibold" type="button" id="termDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-calendar-alt text-primary"></i>
        <span><?= e($activeTerm['name']) ?></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="termDropdown">
        <li class="dropdown-header small text-muted">Select Academic Term</li>
        <?php foreach ($allTerms as $t): ?>
          <li>
            <a class="dropdown-item d-flex justify-content-between align-items-center <?= ($t['id'] == $activeTerm['id']) ? 'active fw-bold' : '' ?>" 
               href="?term=<?= urlencode($t['code']) ?>">
              <span><?= e($t['name']) ?></span>
              <?php if ($t['id'] == $activeTerm['id']): ?>
                <i class="fa-solid fa-check small ms-2"></i>
              <?php endif; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Student / Parent Profile Chip -->
    <div class="dropdown">
      <button class="btn btn-sm btn-light border dropdown-toggle d-flex align-items-center gap-2" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="<?= BASE_URL ?>/<?= e($user['photo_url'] ?? 'assets/images/student_placeholder.svg') ?>" alt="User" width="28" height="28" class="rounded-circle">
        <span class="d-none d-md-inline fw-semibold small text-dark">
          <?= e($user['first_name'] ?? $user['username']) ?>
        </span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userMenu">
        <li class="px-3 py-2 border-bottom">
          <div class="fw-bold text-dark"><?= e(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></div>
          <div class="text-muted small">USN: <?= e($user['usn'] ?? $user['username']) ?></div>
          <div class="text-muted small"><?= e($user['dept_code'] ?? 'Engineering') ?> &bull; Sem <?= (int)($user['current_sem'] ?? 4) ?></div>
        </li>
        <li>
          <a class="dropdown-item py-2" href="<?= BASE_URL ?>/dashboard.php?page=profile">
            <i class="fa-regular fa-user me-2 text-primary"></i> My Profile
          </a>
        </li>
        <li>
          <a class="dropdown-item py-2" href="<?= BASE_URL ?>/dashboard.php?page=change_password">
            <i class="fa-solid fa-key me-2 text-warning"></i> Change Password
          </a>
        </li>
        <li><hr class="dropdown-divider my-1"></li>
        <li>
          <a class="dropdown-item py-2 text-danger fw-semibold" href="<?= BASE_URL ?>/logout.php">
            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</header>
