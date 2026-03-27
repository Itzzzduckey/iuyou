<?php
/**
 * AJAX search endpoint: runs module search and returns result HTML (no page reload).
 * POST: query=...  GET: m=module
 */
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    echo json_encode(['ok' => false, 'error' => 'Not authenticated']);
    exit;
}

$module = isset($_GET['m']) ? trim($_GET['m']) : '';
$query = isset($_POST['query']) ? trim($_POST['query']) : '';

if ($module === '' || $query === '') {
    echo json_encode(['ok' => false, 'error' => 'Missing module or query']);
    exit;
}

$allowed = [
    'ip','username','phone','domain','breach','twitter','instagram','tiktok','github','linkedin',
    'facebook','youtube','reddit','discord','telegram','snapchat','twitch','steam','vin','paste',
    'company','face','reverse-image','crypto','bin','iban','plate'
];
if (!in_array($module, $allowed, true)) {
    echo json_encode(['ok' => false, 'error' => 'Invalid module']);
    exit;
}

if (!canSearch()) {
    echo json_encode(['ok' => false, 'error' => 'Daily search limit reached. Upgrade your plan for more searches.']);
    exit;
}

require_once __DIR__ . '/includes/osintdog.php';
require_once __DIR__ . '/includes/format_results.php';

$api = new OsintDog();
$method = $module;
if ($module === 'reverse-image') $method = 'reverseImage';

if (!method_exists($api, $method)) {
    echo json_encode(['ok' => false, 'error' => 'Search not available']);
    exit;
}

$response = $api->$method($query);

if (empty($response['success'])) {
    echo json_encode(['ok' => false, 'error' => $response['error'] ?? 'Search failed']);
    exit;
}

incrementSearchCount();
$html = render_module_result_html($module, $response['data'], $query);
$remaining = getRemainingSearches();
$limit = getDailyLimit();

echo json_encode([
    'ok' => true,
    'html' => $html,
    'remaining' => $remaining,
    'limit' => $limit,
]);
