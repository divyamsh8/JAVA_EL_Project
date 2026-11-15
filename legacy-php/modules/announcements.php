<?php
/**
 * Parent Portal - Announcements & Circulars Module
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

$activeTerm = get_active_term();
$termId = (int)$activeTerm['id'];
$db = get_db();

// Handle simulated file download
if (isset($_GET['download'])) {
    $filename = basename(sanitize_input($_GET['download']));
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo "%PDF-1.4\n% Demo Institute of Engineering - Institutional Circular Preview: " . $filename . "\n%%EOF";
    exit;
}

$stmt = $db->prepare("
    SELECT * FROM announcements 
    WHERE (term_id = ? OR term_id IS NULL) AND is_published = 1 
    ORDER BY published_date DESC, id DESC
");
$stmt->execute([$termId]);
$announcements = $stmt->fetchAll();
?>

<div class="content-card mb-4">
  <div class="content-card-header">
    <h5>
      <i class="fa-solid fa-bullhorn text-warning"></i> 
      Institutional Circulars &amp; Official Notifications &bull; Term: <?= e($activeTerm['name']) ?>
    </h5>
    <span class="badge bg-light text-muted border"><?= count($announcements) ?> Circulars Published</span>
  </div>

  <div class="content-card-body p-4">
    <?php if (!empty($announcements)): ?>
      <div class="row g-4">
        <?php foreach ($announcements as $ann): ?>
          <div class="col-md-6">
            <div class="card h-100 border rounded-3 shadow-none p-3 bg-light-subtle">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1">
                  <?= e($ann['category']) ?>
                </span>
                <small class="text-muted">
                  <i class="fa-regular fa-calendar me-1"></i><?= format_date($ann['published_date']) ?>
                </small>
              </div>

              <h5 class="fw-bold text-dark mb-2"><?= e($ann['title']) ?></h5>
              <p class="text-secondary small mb-3 flex-grow-1" style="line-height: 1.6;">
                <?= nl2br(e($ann['description'])) ?>
              </p>

              <?php if (!empty($ann['attachment_file'])): ?>
                <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                  <span class="text-muted small">
                    <i class="fa-solid fa-paperclip me-1"></i><?= e($ann['attachment_file']) ?>
                  </span>
                  <a href="<?= BASE_URL ?>/dashboard.php?page=announcements&download=<?= urlencode($ann['attachment_file']) ?>" 
                     class="btn btn-sm btn-outline-danger">
                    <i class="fa-solid fa-file-pdf me-1"></i> Download PDF
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="text-center py-5 text-muted">
        <i class="fa-regular fa-newspaper fa-3x mb-3 text-secondary"></i>
        <h5>No Active Circulars</h5>
        <p class="small">There are currently no active notifications for this academic term.</p>
      </div>
    <?php endif; ?>
  </div>
</div>
