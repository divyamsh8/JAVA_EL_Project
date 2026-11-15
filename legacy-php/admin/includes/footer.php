<?php
/**
 * Admin Console - Common Footer
 */

declare(strict_types=1);
?>
  <!-- Admin Console Footer with Safety Badge -->
  <footer class="bg-dark text-white-50 border-top border-secondary py-3">
    <div class="cn-footer-container">
      <div class="cn-footer-text text-white-50">
        <p class="mb-0">
          <?= APP_NAME ?> &bull; Administrative Panel &bull; Powered by <?= VENDOR_NAME ?>
        </p>
      </div>

      <!-- Non-negotiable visible DEMO badge -->
      <div class="text-center">
        <span class="demo-badge">
          <i class="fa-solid fa-triangle-exclamation"></i> <?= DEMO_BADGE_TEXT ?>
        </span>
      </div>

      <div class="cn-footer-links text-end">
        <span class="small text-white-50">Authorized Personnel Only &bull; Role: <?= e($_SESSION['admin_role_name'] ?? 'Admin') ?></span>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var toggle = document.getElementById('sidebarToggle');
      var sidebar = document.getElementById('portalSidebar');
      if (toggle && sidebar) {
        toggle.addEventListener('click', function() {
          sidebar.classList.toggle('show');
        });
      }
    });
  </script>
</body>
</html>
