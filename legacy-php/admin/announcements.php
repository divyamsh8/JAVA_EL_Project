<?php
/**
 * Admin Console - Announcements & Circulars Management
 */

declare(strict_types=1);

$adminPageTitle = 'Manage Circulars';
$currentAdminPage = 'announcements';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$db = get_db();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($token)) {
        $error = 'Security token invalid.';
    } elseif ($action === 'create_announcement') {
        $title = sanitize_input($_POST['title'] ?? '');
        $category = sanitize_input($_POST['category'] ?? 'Academic');
        $desc = sanitize_input($_POST['description'] ?? '');
        $filename = sanitize_input($_POST['attachment_file'] ?? '');
        $date = sanitize_input($_POST['published_date'] ?? date('Y-m-d'));

        if (empty($title) || empty($desc)) {
            $error = 'Title and Description are required.';
        } else {
            $stmt = $db->prepare("INSERT INTO announcements (term_id, title, category, description, attachment_file, is_published, published_date) VALUES (1, ?, ?, ?, ?, 1, ?)");
            $stmt->execute([$title, $category, $desc, $filename, $date]);
            $message = 'Circular published successfully.';
        }
    } elseif ($action === 'delete_announcement') {
        $id = (int)$_POST['announcement_id'];
        $db->prepare("DELETE FROM announcements WHERE id = ?")->execute([$id]);
        $message = 'Circular removed.';
    }
}

$announcements = $db->query("SELECT * FROM announcements ORDER BY published_date DESC, id DESC")->fetchAll();
?>

<div class="portal-wrapper">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="portal-main">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <div>
        <h4 class="fw-bold text-dark mb-1">Institutional Circulars &amp; Notifications</h4>
        <small class="text-muted">Broadcast official announcements to students and parents</small>
      </div>
      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newCircularModal">
        <i class="fa-solid fa-plus me-1"></i> New Circular
      </button>
    </div>

    <?php if ($message): ?>
      <div class="alert alert-success alert-dismissible fade show small" role="alert">
        <?= e($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert alert-danger alert-dismissible fade show small" role="alert">
        <?= e($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="content-card">
      <div class="content-card-body p-0">
        <div class="table-responsive">
          <table class="table table-institutional mb-0">
            <thead>
              <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Title</th>
                <th>Attachment</th>
                <th class="text-center">Status</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($announcements as $a): ?>
                <tr>
                  <td class="small text-muted"><?= format_date($a['published_date']) ?></td>
                  <td><span class="badge bg-primary-subtle text-primary"><?= e($a['category']) ?></span></td>
                  <td>
                    <div class="fw-bold text-dark"><?= e($a['title']) ?></div>
                    <small class="text-muted text-truncate d-block" style="max-width: 350px;"><?= e($a['description']) ?></small>
                  </td>
                  <td class="small"><?= e($a['attachment_file'] ?: 'None') ?></td>
                  <td class="text-center">
                    <span class="badge bg-success-subtle text-success">Published</span>
                  </td>
                  <td class="text-center">
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this announcement?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete_announcement">
                      <input type="hidden" name="announcement_id" value="<?= $a['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2">
                        <i class="fa-solid fa-trash-can"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Add Circular Modal -->
<div class="modal fade" id="newCircularModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold">Publish Circular</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASE_URL ?>/admin/announcements.php" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="create_announcement">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Circular Title *</label>
            <input type="text" name="title" class="form-control form-control-sm" required>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-semibold">Category</label>
              <select name="category" class="form-select form-select-sm">
                <option value="Examinations">Examinations</option>
                <option value="Academic">Academic</option>
                <option value="Scholarship">Scholarship</option>
                <option value="Events">Events</option>
                <option value="Department">Department</option>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Publish Date</label>
              <input type="date" name="published_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Description / Circular Body *</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Attachment File Name (Simulated PDF)</label>
            <input type="text" name="attachment_file" class="form-control form-control-sm" placeholder="e.g. Schedule_Notice.pdf">
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-primary fw-bold">Publish Circular</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
