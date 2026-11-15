<?php
/**
 * Admin Console - Top Navigation
 */

declare(strict_types=1);

$currentAdmin = current_admin();
?>
<header class="portal-navbar bg-dark text-white border-0 shadow-sm">
  <div class="d-flex align-items-center gap-3">
    <button class="btn btn-sm btn-outline-light d-lg-none" type="button" id="sidebarToggle" aria-label="Toggle Navigation">
      <i class="fa-solid fa-bars"></i>
    </button>
    <a href="<?= BASE_URL ?>/admin/index.php" class="portal-brand text-white">
      <img src="<?= BASE_URL ?>/assets/images/crest.svg" alt="Crest">
      <div class="portal-brand-text text-white">
        <?= APP_NAME ?>
        <small class="text-white-50">Admin &amp; Academic Management Portal</small>
      </div>
    </a>
  </div>

  <div class="d-flex align-items-center gap-3">
    <a href="<?= BASE_URL ?>/index.php" target="_blank" class="btn btn-sm btn-outline-light d-none d-md-flex align-items-center gap-1">
      <i class="fa-solid fa-arrow-up-right-from-square"></i>
      <span>Parent Portal</span>
    </a>

    <div class="dropdown">
      <button class="btn btn-sm btn-outline-light dropdown-toggle d-flex align-items-center gap-2" type="button" id="adminMenu" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-user-shield text-info"></i>
        <span class="fw-semibold small"><?= e($currentAdmin['username']) ?></span>
        <span class="badge bg-info text-dark badge-role"><?= e($currentAdmin['role_name']) ?></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="adminMenu">
        <li class="px-3 py-2 border-bottom">
          <div class="fw-bold text-dark"><?= e($currentAdmin['username']) ?></div>
          <small class="text-muted"><?= e($currentAdmin['email']) ?></small>
        </li>
        <li>
          <a class="dropdown-item py-2" href="<?= BASE_URL ?>/admin/users.php">
            <i class="fa-solid fa-users-gear me-2 text-primary"></i> User Management
          </a>
        </li>
        <li>
          <a class="dropdown-item py-2" href="<?= BASE_URL ?>/admin/logs.php">
            <i class="fa-solid fa-list-check me-2 text-secondary"></i> Login Audit Logs
          </a>
        </li>
        <li><hr class="dropdown-divider my-1"></li>
        <li>
          <a class="dropdown-item py-2 text-danger fw-semibold" href="<?= BASE_URL ?>/admin/logout.php">
            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</header>
