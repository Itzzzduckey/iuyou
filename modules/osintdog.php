<?php
/**
 * OSINTDog - Modulo PHP
 * File: modules/osintdog.php
 *
 * Chiama il microservizio Python locale sulla porta 5555
 * e restituisce i risultati aggregati da tutti gli endpoint.
 *
 * Utilizzo da dashboard.php o da qualsiasi altra pagina:
 *   require_once __DIR__ . '/modules/osintdog.php';
 *   $results = osintdog_search_email('target@example.com');
 */

define('OSINTDOG_SERVICE_URL', 'http://127.0.0.1:5555');
define('OSINTDOG_TIMEOUT', 90); // secondi

/**
 * Esegue la ricerca email chiamando il microservizio Python.
 *
 * @param string $email  Indirizzo email da ricercare
 * @return array         Array con 'success', 'email', 'results' o 'error'
 */
function osintdog_search_email(string $email): array
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Email non valida'];
    }

    $payload = json_encode(['email' => $email, 'timeout' => OSINTDOG_TIMEOUT - 5]);

    $ch = curl_init(OSINTDOG_SERVICE_URL . '/search');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_TIMEOUT        => OSINTDOG_TIMEOUT,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
        ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr) {
        return ['success' => false, 'error' => 'Servizio non raggiungibile: ' . $curlErr];
    }

    if ($httpCode !== 200) {
        return ['success' => false, 'error' => "HTTP $httpCode dal microservizio"];
    }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['success' => false, 'error' => 'Risposta non valida dal microservizio'];
    }

    return ['success' => true, 'email' => $email, 'results' => $data['results'] ?? []];
}

/**
 * Controlla se il microservizio Python è attivo.
 *
 * @return bool
 */
function osintdog_service_healthy(): bool
{
    $ch = curl_init(OSINTDOG_SERVICE_URL . '/health');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 3,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode === 200;
}
