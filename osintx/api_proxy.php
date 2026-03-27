<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json');

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    echo json_encode(['ok' => false, 'err' => 'Not authenticated']);
    exit;
}

if (!canSearch()) {
    echo json_encode([
        'ok'            => false,
        'err'           => 'Daily search limit reached. Upgrade your plan for more searches.',
        'limit_reached' => true,
    ]);
    exit;
}

$email = trim($_POST['email'] ?? '');
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'err' => 'Invalid email']);
    exit;
}

incrementSearchCount();

define('PYTHON_SERVICE_URL', 'http://127.0.0.1:5555');
define('PYTHON_TIMEOUT', 180);

function pythonServiceAvailable(): bool {
    $ch = curl_init(PYTHON_SERVICE_URL . '/health');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 3,
        CURLOPT_CONNECTTIMEOUT => 2,
    ]);
    curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $http === 200;
}

function callPythonService(string $email): array {
    $payload = json_encode(['email' => $email, 'timeout' => PYTHON_TIMEOUT - 10]);
    $ch = curl_init(PYTHON_SERVICE_URL . '/search');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_TIMEOUT        => PYTHON_TIMEOUT,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
        ],
    ]);
    $raw   = curl_exec($ch);
    $errno = curl_errno($ch);
    $http  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($errno) return ['ok' => false, 'err' => 'Service error #' . $errno];
    if ($http !== 200) return ['ok' => false, 'err' => 'Service HTTP ' . $http];

    $data = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) return ['ok' => false, 'err' => 'Invalid JSON from service'];

    return ['ok' => true, 'data' => $data];
}

if (pythonServiceAvailable()) {
    $result = callPythonService($email);
} else {
    $result = ['ok' => false, 'err' => 'Search service offline. Please try again in a moment.'];
}

$remaining = getRemainingSearches();
$result['remaining'] = ($remaining === 999999) ? null : $remaining;

echo json_encode($result);
