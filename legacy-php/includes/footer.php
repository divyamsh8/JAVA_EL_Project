<?php
/**
 * Common Portal Footer
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
?>
  <!-- Footer with Safety Badge -->
  <footer>
    <div class="cn-footer-container">
      <div class="cn-footer-text">
        <p class="mb-0">
          Copyright &copy; Powered By <a href="<?= VENDOR_URL ?>" target="_blank" rel="noopener noreferrer"><?= VENDOR_NAME ?></a>
        </p>
      </div>

      <!-- Non-negotiable visible DEMO badge -->
      <div class="text-center">
        <span class="demo-badge">
          <i class="fa-solid fa-triangle-exclamation"></i> <?= DEMO_BADGE_TEXT ?>
        </span>
      </div>

      <div class="cn-footer-links text-end">
        <a href="<?= BASE_URL ?>/terms_of_service.php" class="text-light text-decoration-none me-2">Terms of Service</a> |
        <a href="<?= BASE_URL ?>/privacy_policy.php" class="text-light text-decoration-none ms-2">Privacy Policy</a>
      </div>
    </div>
  </footer>

  <!-- Bootstrap 5 Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Sidebar Toggle Script for Mobile -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var sidebarToggle = document.getElementById('sidebarToggle');
      var sidebar = document.getElementById('portalSidebar');
      if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
          sidebar.classList.toggle('show');
        });
      }
    });
  </script>
</body>
</html>
