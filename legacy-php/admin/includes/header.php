<?php
/**
 * Admin Console - Common Header
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/auth.php';

require_admin_auth();
$currentAdmin = current_admin();
$adminPageTitle = $adminPageTitle ?? 'Admin Console';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($adminPageTitle) ?> - <?= APP_NAME ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/assets/images/crest.svg">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/dashboard.css">
  <style>
    .admin-sidebar {
      background-color: #0f172a;
      color: #94a3b8;
    }
    .admin-sidebar .portal-sidebar-heading {
      color: #64748b;
    }
    .admin-sidebar .portal-nav-link {
      color: #cbd5e1;
    }
    .admin-sidebar .portal-nav-link i {
      color: #94a3b8;
    }
    .admin-sidebar .portal-nav-link:hover {
      background-color: #1e293b;
      color: #ffffff;
    }
    .admin-sidebar .portal-nav-link.active {
      background-color: #0284c7;
      color: #ffffff;
    }
    .admin-sidebar .portal-nav-link.active i {
      color: #ffffff;
    }
    .badge-role {
      font-size: 11px;
      padding: 3px 8px;
      border-radius: 4px;
      text-transform: uppercase;
      font-weight: 700;
    }
  </style>
</head>
<body class="portal-body">
