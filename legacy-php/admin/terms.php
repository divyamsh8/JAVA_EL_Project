<?php
/**
 * Admin Console - Academic Terms & Login Term Access Links Management
 */

declare(strict_types=1);

$adminPageTitle = 'Manage Term Portals';
$currentAdminPage = 'terms';

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
    } elseif ($action === 'update_link') {
        $linkId = (int)$_POST['link_id'];
        $title = sanitize_input($_POST['title'] ?? '');
        $margin = (int)$_POST['margin_top'];
        $order = (int)$_POST['display_order'];
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $stmt = $db->prepare("UPDATE term_links SET title = ?, margin_top = ?, display_order = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$title, $margin, $order, $isActive, $linkId]);
        $message = 'Term access link updated.';
    }
}

$terms = $db->query("SELECT * FROM terms ORDER BY id ASC")->fetchAll();
$links = $db->query("SELECT * FROM term_links ORDER BY display_order ASC")->fetchAll();
?>

<div class="portal-wrapper">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <main class="portal-main">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <div>
        <h4 class="fw-bold text-dark mb-1">Academic Terms &amp; Sub-Portal Access Links</h4>
        <small class="text-muted">Control the terms accessible by students and parents on the Contineo login page</small>
      </div>
    </div>

    <?php if ($message): ?>
      <div class="alert alert-success alert-dismissible fade show small" role="alert">
        <?= e($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- Sub-Portal Links on Login Page -->
    <div class="content-card mb-4">
      <div class="content-card-header">
        <h5><i class="fa-solid fa-link text-primary"></i> Contineo Login Page Term Buttons</h5>
      </div>
      <div class="content-card-body p-0">
        <div class="table-responsive">
          <table class="table table-institutional mb-0">
            <thead>
              <tr>
                <th>Button Display Title</th>
                <th>Route Path</th>
                <th>Target Term</th>
                <th class="text-center">Spacing (Margin Top)</th>
                <th class="text-center">Order</th>
                <th class="text-center">Status</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($links as $link): ?>
                <tr>
                  <form method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="update_link">
                    <input type="hidden" name="link_id" value="<?= $link['id'] ?>">
                    <td>
                      <input type="text" name="title" class="form-control form-control-sm fw-bold" value="<?= e($link['title']) ?>" required>
                    </td>
                    <td><code><?= e($link['route_slug']) ?></code></td>
                    <td><span class="badge bg-light text-dark border"><?= e($link['term_code']) ?></span></td>
                    <td class="text-center" style="width: 100px;">
                      <input type="number" name="margin_top" class="form-control form-control-sm text-center" value="<?= (int)$link['margin_top'] ?>">
                    </td>
                    <td class="text-center" style="width: 80px;">
                      <input type="number" name="display_order" class="form-control form-control-sm text-center" value="<?= (int)$link['display_order'] ?>">
                    </td>
                    <td class="text-center">
                      <div class="form-check form-switch d-inline-block">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= $link['is_active'] ? 'checked' : '' ?>>
                      </div>
                    </td>
                    <td class="text-center">
                      <button type="submit" class="btn btn-sm btn-outline-primary py-0 px-2">
                        <i class="fa-solid fa-floppy-disk"></i> Save
                      </button>
                    </td>
                  </form>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Active Academic Terms -->
    <div class="content-card">
      <div class="content-card-header">
        <h5><i class="fa-solid fa-calendar-alt text-success"></i> Configured Academic Terms</h5>
      </div>
      <div class="content-card-body p-0">
        <div class="table-responsive">
          <table class="table table-institutional mb-0">
            <thead>
              <tr>
                <th>Code</th>
                <th>Term Name</th>
                <th>Academic Year</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($terms as $t): ?>
                <tr>
                  <td class="fw-bold"><code><?= e($t['code']) ?></code></td>
                  <td class="fw-semibold text-dark"><?= e($t['name']) ?></td>
                  <td><?= e($t['academic_year']) ?></td>
                  <td class="small text-muted"><?= format_date($t['start_date']) ?></td>
                  <td class="small text-muted"><?= format_date($t['end_date']) ?></td>
                  <td class="text-center">
                    <span class="badge bg-success-subtle text-success">Active</span>
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
