<?php
/**
 * Portal Responsive Sidebar Navigation
 */

declare(strict_types=1);

$currentPage = $currentPage ?? 'dashboard';
?>
<aside class="portal-sidebar" id="portalSidebar">
  <ul class="portal-sidebar-nav">
    <li class="portal-sidebar-heading">Main Navigation</li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/dashboard.php?page=dashboard" class="portal-nav-link <?= ($currentPage === 'dashboard') ? 'active' : '' ?>">
        <i class="fa-solid fa-gauge-high"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/dashboard.php?page=profile" class="portal-nav-link <?= ($currentPage === 'profile') ? 'active' : '' ?>">
        <i class="fa-regular fa-id-card"></i>
        <span>Student Profile</span>
      </a>
    </li>

    <li class="portal-sidebar-heading">Academic Information</li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/dashboard.php?page=attendance" class="portal-nav-link <?= ($currentPage === 'attendance') ? 'active' : '' ?>">
        <i class="fa-solid fa-chart-pie"></i>
        <span>Attendance Status</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/dashboard.php?page=cie" class="portal-nav-link <?= ($currentPage === 'cie') ? 'active' : '' ?>">
        <i class="fa-solid fa-clipboard-check"></i>
        <span>Internal Marks (CIE)</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/dashboard.php?page=results" class="portal-nav-link <?= ($currentPage === 'results') ? 'active' : '' ?>">
        <i class="fa-solid fa-graduation-cap"></i>
        <span>Examination Results</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/dashboard.php?page=timetable" class="portal-nav-link <?= ($currentPage === 'timetable') ? 'active' : '' ?>">
        <i class="fa-solid fa-calendar-days"></i>
        <span>Class Timetable</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/dashboard.php?page=announcements" class="portal-nav-link <?= ($currentPage === 'announcements') ? 'active' : '' ?>">
        <i class="fa-solid fa-bullhorn"></i>
        <span>Announcements</span>
      </a>
    </li>

    <li class="portal-sidebar-heading">Finance & Services</li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/dashboard.php?page=fees" class="portal-nav-link <?= ($currentPage === 'fees') ? 'active' : '' ?>">
        <i class="fa-solid fa-receipt"></i>
        <span>Fee Records & Receipts</span>
      </a>
    </li>

    <li class="portal-sidebar-heading">Security</li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/dashboard.php?page=change_password" class="portal-nav-link <?= ($currentPage === 'change_password') ? 'active' : '' ?>">
        <i class="fa-solid fa-lock"></i>
        <span>Change Password</span>
      </a>
    </li>
    <li class="portal-nav-item">
      <a href="<?= BASE_URL ?>/logout.php" class="portal-nav-link text-danger">
        <i class="fa-solid fa-right-from-bracket text-danger"></i>
        <span>Logout</span>
      </a>
    </li>
  </ul>
</aside>
