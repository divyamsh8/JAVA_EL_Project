<?php
/**
 * Shared Student Header Card Component
 */

declare(strict_types=1);

$user = current_user();
$activeTerm = get_active_term();
?>
<div class="student-header-card">
  <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4">
    <!-- Student Avatar -->
    <div class="student-avatar-wrap flex-shrink-0">
      <img src="<?= BASE_URL ?>/<?= e($user['photo_url'] ?? 'assets/images/student_placeholder.svg') ?>" alt="Student Avatar">
    </div>

    <!-- Student Details -->
    <div class="student-details text-center text-md-start flex-grow-1">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <div>
          <h4 class="mb-1 text-white fw-bold">
            <?= e(($user['first_name'] ?? 'Demo') . ' ' . ($user['last_name'] ?? 'Student')) ?>
          </h4>
          <div class="text-light opacity-75 small">
            <?= e($user['course_name'] ?? 'Bachelor of Engineering') ?> &bull; <?= e($user['dept_name'] ?? 'Engineering Department') ?>
          </div>
        </div>

        <!-- Term Badge -->
        <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 rounded-pill fw-bold">
          <i class="fa-solid fa-graduation-cap me-1"></i> Term: <?= e($activeTerm['name']) ?>
        </span>
      </div>

      <!-- Quick Info Badges -->
      <div class="student-badges justify-content-center justify-content-md-start">
        <span class="student-badge-item">
          <strong>USN:</strong> <?= e($user['usn'] ?? $user['username']) ?>
        </span>
        <span class="student-badge-item">
          <strong>Roll No:</strong> <?= e($user['roll_no'] ?? '01') ?>
        </span>
        <span class="student-badge-item">
          <strong>Semester:</strong> <?= (int)($user['current_sem'] ?? 4) ?>th Semester
        </span>
        <span class="student-badge-item">
          <strong>Academic Year:</strong> <?= e($activeTerm['academic_year'] ?? '2025-2026') ?>
        </span>
        <span class="student-badge-item">
          <strong>Blood Group:</strong> <?= e($user['blood_group'] ?? 'O+') ?>
        </span>
        <span class="student-badge-item">
          <strong>Admission:</strong> <?= e((string)($user['admission_year'] ?? 2024)) ?>
        </span>
      </div>
    </div>
  </div>
</div>
