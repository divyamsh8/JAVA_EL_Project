<?php
/**
 * Admin Console - Sidebar Navigation
 */

declare(strict_types=1);

$currentAdminPage = $currentAdminPage ?? 'dashboard';
?>
<aside class="portal-sidebar admin-sidebar" id="portalSidebar">
  <ul class="portal-sidebar-nav">
    <li class="portal-sidebar-heading">Administrative</li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/admin/index.php" class="portal-nav-link <?= ($currentAdminPage === 'dashboard') ? 'active' : '' ?>">
        <i class="fa-solid fa-gauge-high"></i>
        <span>Admin Dashboard</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/admin/students.php" class="portal-nav-link <?= ($currentAdminPage === 'students') ? 'active' : '' ?>">
        <i class="fa-solid fa-user-graduate"></i>
        <span>Students &amp; Parents</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/admin/subjects.php" class="portal-nav-link <?= ($currentAdminPage === 'subjects') ? 'active' : '' ?>">
        <i class="fa-solid fa-book-bookmark"></i>
        <span>Subjects &amp; Courses</span>
      </a>
    </li>

    <li class="portal-sidebar-heading">Academics &amp; Evaluations</li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/admin/attendance.php" class="portal-nav-link <?= ($currentAdminPage === 'attendance') ? 'active' : '' ?>">
        <i class="fa-solid fa-calendar-check"></i>
        <span>Attendance Records</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/admin/marks.php" class="portal-nav-link <?= ($currentAdminPage === 'marks') ? 'active' : '' ?>">
        <i class="fa-solid fa-clipboard-list"></i>
        <span>Internal Marks (CIE)</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/admin/results.php" class="portal-nav-link <?= ($currentAdminPage === 'results') ? 'active' : '' ?>">
        <i class="fa-solid fa-award"></i>
        <span>Exam Results</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/admin/timetable.php" class="portal-nav-link <?= ($currentAdminPage === 'timetable') ? 'active' : '' ?>">
        <i class="fa-solid fa-calendar-days"></i>
        <span>Weekly Timetable</span>
      </a>
    </li>

    <li class="portal-sidebar-heading">Communications &amp; Finance</li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/admin/notices.php" class="portal-nav-link <?= ($currentAdminPage === 'notices') ? 'active' : '' ?>">
        <i class="fa-solid fa-chalkboard"></i>
        <span>Login Notice Board</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/admin/announcements.php" class="portal-nav-link <?= ($currentAdminPage === 'announcements') ? 'active' : '' ?>">
        <i class="fa-solid fa-bullhorn"></i>
        <span>Circulars &amp; News</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/admin/fees.php" class="portal-nav-link <?= ($currentAdminPage === 'fees') ? 'active' : '' ?>">
        <i class="fa-solid fa-file-invoice-dollar"></i>
        <span>Fee Accounts</span>
      </a>
    </li>

    <li class="portal-sidebar-heading">System &amp; Security</li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/admin/terms.php" class="portal-nav-link <?= ($currentAdminPage === 'terms') ? 'active' : '' ?>">
        <i class="fa-solid fa-layer-group"></i>
        <span>Term Sub-Portals</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/admin/users.php" class="portal-nav-link <?= ($currentAdminPage === 'users') ? 'active' : '' ?>">
        <i class="fa-solid fa-users-gear"></i>
        <span>User Accounts</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/admin/logs.php" class="portal-nav-link <?= ($currentAdminPage === 'logs') ? 'active' : '' ?>">
        <i class="fa-solid fa-shield-halved"></i>
        <span>Login Audit Trail</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/admin/logout.php" class="portal-nav-link text-danger">
        <i class="fa-solid fa-right-from-bracket text-danger"></i>
        <span>Logout Admin</span>
      </a>
    </li>
  </ul>
</aside>
