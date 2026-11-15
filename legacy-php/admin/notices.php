<?php
/**
 * Admin Console - Login Notice Board Management
 */

declare(strict_types=1);

$adminPageTitle = 'Manage Notice Board';
$currentAdminPage = 'notices';

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
    } elseif ($action === 'create_notice') {
        $title = sanitize_input($_POST['title'] ?? '');
        $content = sanitize_input($_POST['content'] ?? '');
        $isLogin = isset($_POST['is_login_notice']) ? 1 : 0;
        $priority = (int)($_POST['priority'] ?? 1);

        if (empty($content)) {
            $error = 'Notice content is required.';
        } else {
            $stmt = $db->prepare("INSERT INTO notices (title, content, is_login_notice, priority, is_active) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$title, $content, $isLogin, $priority]);
            $message = 'Notice posted successfully!';
        }
    } elseif ($action === 'toggle_notice') {
        $id = (int)$_POST['notice_id'];
        $db->prepare("UPDATE notices SET is_active = (1 - is_active) WHERE id = ?")->execute([$id]);
        $message = 'Notice status updated.';
    } elseif ($action === 'delete_notice') {
        $id = (int)$_POST['notice_id'];
        $db->prepare("DELETE FROM notices WHERE id = ?")->execute([$id]);
        $message = 'Notice deleted.';
    }
}

$notices = $db->query("SELECT * FROM notices ORDER BY priority ASC, id DESC")->fetchAll();
?>

<div class="portal-wrapper">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="portal-main">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <div>
        <h4 class="fw-bold text-dark mb-1">Notice Board &amp; Login Page Announcements</h4>
        <small class="text-muted">Notices marked "Login Page" appear directly in the Contineo Notice Board card</small>
      </div>
      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newNoticeModal">
        <i class="fa-solid fa-plus me-1"></i> Add Notice
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
                <th>Title</th>
                <th>Content Text</th>
                <th class="text-center">Login Page</th>
                <th class="text-center">Priority</th>
                <th class="text-center">Status</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($notices as $n): ?>
                <tr>
                  <td class="fw-bold text-dark"><?= e($n['title']) ?></td>
                  <td class="small" style="max-width: 400px;"><?= e($n['content']) ?></td>
                  <td class="text-center">
                    <?php if ($n['is_login_notice']): ?>
                      <span class="badge bg-primary px-2 py-1"><i class="fa-solid fa-eye me-1"></i> Visible on Login</span>
                    <?php else: ?>
                      <span class="badge bg-light text-muted border">Portal Only</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center fw-bold">#<?= (int)$n['priority'] ?></td>
                  <td class="text-center">
                    <form method="POST" style="display:inline;">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="toggle_notice">
                      <input type="hidden" name="notice_id" value="<?= $n['id'] ?>">
                      <button type="submit" class="btn btn-sm <?= $n['is_active'] ? 'btn-success' : 'btn-secondary' ?> py-0 px-2">
                        <?= $n['is_active'] ? 'Active' : 'Draft' ?>
                      </button>
                    </form>
                  </td>
                  <td class="text-center">
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this notice?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete_notice">
                      <input type="hidden" name="notice_id" value="<?= $n['id'] ?>">
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

<!-- Add Notice Modal -->
<div class="modal fade" id="newNoticeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold">Post New Notice</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASE_URL ?>/admin/notices.php" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="create_notice">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Notice Title</label>
            <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. Welcome Notice" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Notice Content *</label>
            <textarea name="content" class="form-control" rows="3" placeholder="Enter notice text to be shown on login page..." required></textarea>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-semibold">Display Priority</label>
              <input type="number" name="priority" class="form-control form-control-sm" value="1" min="1">
            </div>
            <div class="col-6 d-flex align-items-center mt-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_login_notice" value="1" id="loginCheck" checked>
                <label class="form-check-label small fw-semibold" for="loginCheck">
                  Show on Login Page Notice Board
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-primary fw-bold">Publish Notice</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
