<?php
/**
 * Master Parent Portal Router & Controller
 * Contineo Platform Recreation - Demo Institute of Engineering
 */

declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

// Require authenticated session
require_login();

// Allowed modules
$allowedPages = [
    'dashboard'       => ['title' => 'Dashboard Overview', 'file' => 'dashboard.php'],
    'profile'         => ['title' => 'Student Profile', 'file' => 'profile.php'],
    'attendance'      => ['title' => 'Attendance Status', 'file' => 'attendance.php'],
    'cie'             => ['title' => 'Internal Marks (CIE)', 'file' => 'cie.php'],
    'results'         => ['title' => 'Examination Results', 'file' => 'results.php'],
    'timetable'       => ['title' => 'Class Timetable', 'file' => 'timetable.php'],
    'announcements'   => ['title' => 'Circulars & Announcements', 'file' => 'announcements.php'],
    'fees'            => ['title' => 'Fee Records & Receipts', 'file' => 'fees.php'],
    'change_password' => ['title' => 'Change Password', 'file' => 'change_password.php']
];

$currentPage = sanitize_input($_GET['page'] ?? 'dashboard');
if (!array_key_exists($currentPage, $allowedPages)) {
    $currentPage = 'dashboard';
}

$pageConfig = $allowedPages[$currentPage];
$pageTitle = $pageConfig['title'];

// Header and top navigation
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="portal-wrapper">
  <!-- Responsive Sidebar -->
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <!-- Main Content Body -->
  <main class="portal-main">
    <!-- Student Header Summary Card -->
    <?php require_once __DIR__ . '/includes/student_header_card.php'; ?>

    <!-- Dynamic Module Content -->
    <?php 
      $moduleFilePath = __DIR__ . '/modules/' . $pageConfig['file'];
      if (file_exists($moduleFilePath)) {
          require $moduleFilePath;
      } else {
          echo '<div class="alert alert-danger">Module file not found: ' . e($pageConfig['file']) . '</div>';
      }
    ?>
  </main>
</div>

<?php 
// Portal Footer with non-negotiable safety badge
require_once __DIR__ . '/includes/footer.php'; 
?>
