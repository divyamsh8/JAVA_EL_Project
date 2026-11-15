<?php
/**
 * Application Configuration
 * Demo Institute of Engineering - Parent Portal (Contineo Recreation)
 */

declare(strict_types=1);

// Prevent direct access if needed, or define constants
define('APP_NAME', 'Demo Institute of Engineering');
define('APP_TAGLINE', 'Autonomous Institute Affiliated to VTU, Belagavi | Approved by AICTE, New Delhi | Accredited by NAAC');
define('APP_PINCODE', '570008');
define('VENDOR_NAME', 'Contineo');
define('VENDOR_URL', 'http://www.contineo.in/');
define('DEMO_BADGE_TEXT', 'DEMO – Fictional Data Only. For educational & demonstration purposes.');

// Session Security Configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    // If running over HTTPS, enable secure cookies
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

// Session timeout: 30 minutes of inactivity
define('SESSION_TIMEOUT_SECONDS', 1800);

// Rate Limiting Settings
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_MINUTES', 15);

// Helper function for dynamic base URL detection
function get_base_url(): string {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    
    // Check if script is inside a subfolder or root
    $dir = str_replace('\\', '/', dirname($script));
    
    // Clean sub-routes like /parentseven, /parentssupply, /modules, /admin
    $subfoldersToRemove = ['/parentseven', '/parentssupply', '/parentssupplypart2', '/modules', '/admin', '/api'];
    foreach ($subfoldersToRemove as $sub) {
        if (str_ends_with($dir, $sub)) {
            $dir = substr($dir, 0, -strlen($sub));
        }
    }
    
    $cleanPath = rtrim($dir, '/');
    return $protocol . $host . ($cleanPath === '' ? '' : $cleanPath);
}

define('BASE_URL', get_base_url());
