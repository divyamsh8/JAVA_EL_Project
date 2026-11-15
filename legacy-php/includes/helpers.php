<?php
/**
 * Global Helper Functions
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function sanitize_input(?string $input): string {
    return trim($input ?? '');
}

function flash(string $key, ?string $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function get_client_ip(): string {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

function get_active_term(): array {
    $db = get_db();
    
    // Check if passed via GET parameter
    $termCode = sanitize_input($_GET['term'] ?? '');
    
    // Check if session has term
    if (empty($termCode) && !empty($_SESSION['term_code'])) {
        $termCode = $_SESSION['term_code'];
    }

    if (!empty($termCode)) {
        $stmt = $db->prepare("SELECT * FROM terms WHERE code = ? LIMIT 1");
        $stmt->execute([$termCode]);
        $term = $stmt->fetch();
        if ($term) {
            $_SESSION['term_id'] = $term['id'];
            $_SESSION['term_code'] = $term['code'];
            $_SESSION['term_name'] = $term['name'];
            return $term;
        }
    }

    // Default to active Even term 2026 (id=1 or first active)
    $stmt = $db->query("SELECT * FROM terms WHERE is_active = 1 ORDER BY id ASC LIMIT 1");
    $term = $stmt->fetch();
    if ($term) {
        $_SESSION['term_id'] = $term['id'];
        $_SESSION['term_code'] = $term['code'];
        $_SESSION['term_name'] = $term['name'];
        return $term;
    }

    return [
        'id' => 1,
        'code' => 'even_2026',
        'name' => 'Even term 2026',
        'academic_year' => '2025-2026'
    ];
}

function get_all_terms(): array {
    $db = get_db();
    $stmt = $db->query("SELECT * FROM terms ORDER BY id ASC");
    return $stmt->fetchAll();
}

function get_active_term_links(): array {
    $db = get_db();
    $stmt = $db->query("SELECT * FROM term_links WHERE is_active = 1 ORDER BY display_order ASC");
    return $stmt->fetchAll();
}

function get_login_notices(): array {
    $db = get_db();
    $stmt = $db->query("SELECT * FROM notices WHERE is_login_notice = 1 AND is_active = 1 ORDER BY priority ASC, id DESC");
    return $stmt->fetchAll();
}

function format_date(?string $date): string {
    if (empty($date)) return '-';
    $time = strtotime($date);
    return $time ? date('d M Y', $time) : $date;
}

function format_currency($amount): string {
    return '₹' . number_format((float)$amount, 2);
}
