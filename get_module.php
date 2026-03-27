<?php
/**
 * Returns only the module HTML for dashboard (no full page).
 * Used for client-side tab switching without refresh.
 */
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header('HTTP/1.1 401 Unauthorized');
    echo '<p>Session expired. <a href="login.php">Log in</a>.</p>';
    exit;
}

$module = isset($_GET['m']) ? trim($_GET['m']) : 'home';
$allowed = [
    'home', 'ip', 'email', 'username', 'phone', 'domain', 'breach', 'twitter', 'instagram', 'tiktok',
    'github', 'linkedin', 'facebook', 'youtube', 'reddit', 'discord', 'telegram', 'snapchat',
    'twitch', 'steam', 'vin', 'paste', 'company', 'face', 'reverse-image', 'crypto', 'bin', 'iban', 'plate'
];
if (!in_array($module, $allowed, true)) {
    $module = 'home';
}

$module_file = __DIR__ . '/modules/' . basename($module) . '.php';
if (!file_exists($module_file)) {
    $module_file = __DIR__ . '/modules/home.php';
    $module = 'home';
}

ob_start();
include $module_file;
echo ob_get_clean();
