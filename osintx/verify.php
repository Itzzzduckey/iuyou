<?php
session_start();

// Configurazione
define('TURNSTILE_SECRET_KEY', '0x4AAAAAACbNLAStq4phwavloCx3AFshUdk');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ottieni il token dal form
    $token = isset($_POST['cf-turnstile-response']) ? $_POST['cf-turnstile-response'] : '';
    
    if (empty($token)) {
        // Token mancante
        header('Location: verify.html?error=1');
        exit;
    }
    
    // Prepara i dati per la verifica
    $data = [
        'secret' => TURNSTILE_SECRET_KEY,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    
    // Invia richiesta a Cloudflare per verificare il token
    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        // Errore nella richiesta
        header('Location: verify.html?error=1');
        exit;
    }
    
    $result = json_decode($response, true);
    
    // Verifica se la risposta è valida
    if (isset($result['success']) && $result['success'] === true) {
        // Verifica superata!
        $_SESSION['verified'] = true;
        $_SESSION['verified_time'] = time();
        
        // Reindirizza al sito principale
        header('Location: /');
        exit;
    } else {
        // Verifica fallita
        error_log('Turnstile verification failed: ' . json_encode($result));
        header('Location: verify.html?error=1');
        exit;
    }
} else {
    // Richiesta non POST, reindirizza alla pagina di verifica
    header('Location: verify.html');
    exit;
}
?>
