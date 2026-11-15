<?php
/**
 * Parent Portal - Student & Parent Profile Module
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

$user = current_user();
?>

<div class="row g-4">
  <!-- Student Information Card -->
  <div class="col-lg-6">
    <div class="content-card h-100">
      <div class="content-card-header">
        <h5>
          <i class="fa-solid fa-user-graduate text-primary"></i> 
          Student Personal & Academic Profile
        </h5>
      </div>
      <div class="content-card-body p-4">
        <div class="table-responsive">
          <table class="table table-borderless mb-0">
            <tbody>
              <tr>
                <td class="text-muted fw-semibold" style="width: 38%;">University Seat No (USN)</td>
                <td class="fw-bold text-dark">: <?= e($user['usn']) ?></td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Student Name</td>
                <td class="fw-bold text-dark">: <?= e($user['first_name'] . ' ' . $user['last_name']) ?></td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Class Roll Number</td>
                <td class="fw-bold text-dark">: <?= e($user['roll_no'] ?? '01') ?></td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Department</td>
                <td class="text-dark">: <?= e($user['dept_name']) ?> (<?= e($user['dept_code']) ?>)</td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Enrolled Course</td>
                <td class="text-dark">: <?= e($user['course_name']) ?></td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Current Semester</td>
                <td class="text-dark">: <?= (int)$user['current_sem'] ?>th Semester</td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Admission Year</td>
                <td class="text-dark">: <?= (int)$user['admission_year'] ?></td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Date of Birth</td>
                <td class="text-dark">: <?= format_date($user['dob']) ?></td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Gender</td>
                <td class="text-dark">: <?= e(ucfirst(strtolower($user['gender'] ?? 'Male'))) ?></td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Blood Group</td>
                <td class="text-dark">: <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1"><?= e($user['blood_group'] ?? 'O+') ?></span></td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Student Institutional Email</td>
                <td class="text-dark">: <?= e($user['user_email']) ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Parent / Guardian Information Card -->
  <div class="col-lg-6">
    <div class="content-card h-100">
      <div class="content-card-header">
        <h5>
          <i class="fa-solid fa-users text-success"></i> 
          Parent & Guardian Details
        </h5>
      </div>
      <div class="content-card-body p-4">
        <div class="table-responsive">
          <table class="table table-borderless mb-0">
            <tbody>
              <tr>
                <td class="text-muted fw-semibold" style="width: 38%;">Father's Name</td>
                <td class="fw-bold text-dark">: <?= e($user['father_name'] ?? 'Not Recorded') ?></td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Mother's Name</td>
                <td class="fw-bold text-dark">: <?= e($user['mother_name'] ?? 'Not Recorded') ?></td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Occupation</td>
                <td class="text-dark">: <?= e($user['occupation'] ?? 'Service / Business') ?></td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Primary Contact Mobile</td>
                <td class="text-dark">: <a href="tel:<?= e($user['primary_mobile']) ?>" class="text-decoration-none fw-semibold"><?= e($user['primary_mobile']) ?></a></td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Alternate Mobile</td>
                <td class="text-dark">: <?= e($user['alternate_mobile'] ?? '-') ?></td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Parent Email</td>
                <td class="text-dark">: <a href="mailto:<?= e($user['user_email']) ?>" class="text-decoration-none"><?= e($user['user_email']) ?></a></td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Residential Address</td>
                <td class="text-dark">: <?= nl2br(e($user['address'] ?? 'Mysuru, Karnataka')) ?></td>
              </tr>
              <tr>
                <td class="text-muted fw-semibold">Portal Account Status</td>
                <td class="text-dark">: <span class="badge bg-success-subtle text-success border border-success">Verified Parent Account</span></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="alert alert-light border mt-4 mb-0 small text-muted">
          <i class="fa-solid fa-shield-halved text-primary me-1"></i>
          <strong>Note:</strong> To modify contact details or address records, please contact the Academic Registrar Office with formal proof of identity.
        </div>
      </div>
    </div>
  </div>
</div>
