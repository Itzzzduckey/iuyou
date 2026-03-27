<?php
require_once __DIR__ . '/../config.php';

class KeyAuth {
    private $app_name;
    private $owner_id;
    private $app_secret;
    private $session_id;
    
    public function __construct() {
        $this->app_name = KEYAUTH_APP_NAME;
        $this->owner_id = KEYAUTH_OWNER_ID;
        $this->app_secret = KEYAUTH_APP_SECRET;
        
        if (!session_id()) {
            session_name(SESSION_NAME);
            session_start();
        }
    }
    
    public function init() {
        $data = [
            'type' => 'init',
            'name' => $this->app_name,
            'ownerid' => $this->owner_id
        ];
        
        $response = $this->makeRequest($data);
        
        if ($response && isset($response['success']) && $response['success']) {
            $this->session_id = $response['sessionid'];
            return true;
        }
        
        return false;
    }
    
    public function login($license_key) {
        if (!$this->init()) {
            return ['success' => false, 'message' => 'Failed to initialize'];
        }
        
        $data = [
            'type' => 'license',
            'key' => $license_key,
            'sessionid' => $this->session_id,
            'name' => $this->app_name,
            'ownerid' => $this->owner_id
        ];
        
        $response = $this->makeRequest($data);
        
        if ($response && isset($response['success']) && $response['success']) {
            $_SESSION['authenticated'] = true;
            $_SESSION['license_key'] = $license_key;
            $_SESSION['username'] = $response['info']['username'] ?? 'User';
            $_SESSION['subscription'] = $response['info']['subscriptions'][0]['subscription'] ?? 'Free';
            $_SESSION['expiry'] = $response['info']['subscriptions'][0]['expiry'] ?? 'Never';
            $_SESSION['login_time'] = time();
            $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
            
            return ['success' => true, 'message' => 'Login successful'];
        }
        
        return ['success' => false, 'message' => $response['message'] ?? 'Invalid license'];
    }
    
    public function isAuthenticated() {
        if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
            return false;
        }
        
        if (isset($_SESSION['login_time'])) {
            $elapsed = time() - $_SESSION['login_time'];
            if ($elapsed > SESSION_LIFETIME) {
                $this->logout();
                return false;
            }
        }
        
        return true;
    }
    
    public function logout() {
        session_destroy();
        return true;
    }
    
    private function makeRequest($data) {
        if (!function_exists('curl_init')) {
            die('ERROR: cURL not enabled');
        }
        
        $ch = curl_init('https://keyauth.win/api/1.2/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        
        // Properly close before returning to avoid deprecated warning in PHP 8.0+
        $handle = $ch;
        unset($ch);
        curl_close($handle);
        
        if ($errno) {
            return ['success' => false, 'message' => 'Connection error'];
        }
        
        return json_decode($response, true);
    }
}
?>