<?php
/**
 * OsintDog API client – uses service-specific endpoints per docs (https://osintdog.com/docs).
 * Each search type calls the required APIs (LeakCheck, HackCheck, Snusbase, SEON, etc.)
 * and merges results. KeyScore is not used (deprecated).
 */
require_once __DIR__ . '/../config.php';

class OsintDog {
    private $api_key;
    private $base_url;

    public function __construct() {
        $this->api_key = OSINTDOG_API_KEY;
        $this->base_url = rtrim(OSINTDOG_BASE_URL, '/');
    }

    /** GET request */
    private function get($path, $query = []) {
        $url = $this->base_url . $path;
        if (!empty($query)) $url .= (strpos($path, '?') !== false ? '&' : '?') . http_build_query($query);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_HTTPHEADER => [
                'X-API-Key: ' . $this->api_key,
                'Content-Type: application/json',
            ],
        ]);
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) return null;
        if ($code !== 200) return null;
        $data = json_decode($response, true);
        return is_array($data) ? $data : null;
    }

    /** POST request */
    private function post($path, $body) {
        $url = $this->base_url . $path;
        $payload = is_string($body) ? $body : json_encode($body);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_HTTPHEADER => [
                'X-API-Key: ' . $this->api_key,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload),
            ],
        ]);
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) return null;
        if ($code !== 200) return null;
        $data = json_decode($response, true);
        return is_array($data) ? $data : null;
    }

    /** Merge multiple source responses into one result set for the formatter */
    private function merged($search_term, $search_type, array $sources) {
        $results = [];
        foreach ($sources as $name => $data) {
            if ($data !== null) $results[$name] = $data;
        }
        return [
            'success' => true,
            'data' => [
                'search_term' => $search_term,
                'search_type' => $search_type,
                'results' => $results,
            ],
        ];
    }

    /** Universal search (LeakCheck, HackCheck) – used as base for email/username/phone/domain/ip */
    private function universalSearch($type, $value) {
        $body = ['field' => [[$type => $value]]];
        return $this->post('/api/search', $body);
    }

    // ---------- Email ----------
    public function email($email) {
        $sources = [
            'Universal (LeakCheck, HackCheck)' => $this->universalSearch('email', $email),
            'LeakCheck v2' => $this->post('/api/leakcheck/v2', ['term' => $email, 'search_type' => 'email', 'limit' => 500]),
            'HackCheck' => $this->post('/api/hackcheck', ['term' => $email, 'search_type' => 'email']),
            'BreachBase' => $this->post('/api/breachbase', ['term' => $email, 'search_type' => 'email']),
            'OathNet Holehe (120+ platforms)' => $this->post('/api/oathnet/holehe', ['field' => [['email' => $email]]]),
            'Snusbase' => $this->post('/api/snusbase/search', ['terms' => [$email], 'types' => ['email'], 'wildcard' => false]),
            'LeakSight' => $this->post('/api/leaksight', ['term' => $email, 'search_type' => 'email']),
            'Akula' => $this->post('/api/akula', ['searchTerm' => $email, 'search_type' => 'email']),
            'LeakOSINT' => $this->post('/api/leakosint/search', ['query' => $email, 'type' => 'email']),
            'IntelVault' => $this->post('/api/intelvault', ['field' => [['email' => $email]]]),
        ];
        $seon = $this->get('/api/seon/' . rawurlencode($email));
        if ($seon !== null) $sources['SEON Email'] = $seon;
        return $this->merged($email, 'email', $sources);
    }

    // ---------- Username ----------
    public function username($username) {
        $sources = [
            'Universal (LeakCheck, HackCheck)' => $this->universalSearch('username', $username),
            'LeakCheck v2' => $this->post('/api/leakcheck/v2', ['term' => $username, 'search_type' => 'username', 'limit' => 500]),
            'HackCheck' => $this->post('/api/hackcheck', ['term' => $username, 'search_type' => 'username']),
            'BreachBase' => $this->post('/api/breachbase', ['term' => $username, 'search_type' => 'username']),
            'Snusbase' => $this->post('/api/snusbase/search', ['terms' => [$username], 'types' => ['username'], 'wildcard' => false]),
            'LeakSight' => $this->post('/api/leaksight', ['term' => $username, 'search_type' => 'username']),
            'Akula' => $this->post('/api/akula', ['searchTerm' => $username, 'search_type' => 'username']),
            'INF0SEC Username' => $this->get('/api/inf0sec/username', ['q' => $username]),
        ];
        return $this->merged($username, 'username', $sources);
    }

    // ---------- Phone ----------
    public function phone($phone) {
        $sources = [
            'Universal (LeakCheck, HackCheck)' => $this->universalSearch('phone', $phone),
            'LeakCheck v2' => $this->post('/api/leakcheck/v2', ['term' => $phone, 'search_type' => 'phone', 'limit' => 500]),
            'LeakSight' => $this->post('/api/leaksight', ['term' => $phone, 'search_type' => 'phone']),
            'LeakOSINT' => $this->post('/api/leakosint/search', ['query' => $phone, 'type' => 'phone']),
            'INF0SEC HLR (carrier)' => $this->get('/api/inf0sec/hlr', ['q' => $phone]),
        ];
        $seon = $this->get('/api/seon/phone', ['phone' => $phone]);
        if ($seon !== null) $sources['SEON Phone'] = $seon;
        return $this->merged($phone, 'phone', $sources);
    }

    // ---------- IP ----------
    public function ip($ip) {
        $sources = [
            'Universal (LeakCheck, HackCheck)' => $this->universalSearch('ip', $ip),
            'LeakCheck v2' => $this->post('/api/leakcheck/v2', ['term' => $ip, 'search_type' => 'ip', 'limit' => 500]),
            'HackCheck (lastip)' => $this->post('/api/hackcheck', ['term' => $ip, 'search_type' => 'lastip']),
            'BreachBase (lastip)' => $this->post('/api/breachbase', ['term' => $ip, 'search_type' => 'lastip']),
            'LeakSight' => $this->post('/api/leaksight', ['term' => $ip, 'search_type' => 'ip']),
            'LeakSight IPGeo' => $this->post('/api/leaksight', ['term' => $ip, 'search_type' => 'ipgeo']),
            'Shodan Host' => $this->post('/api/shodan/host', ['ip' => $ip, 'history' => false, 'minify' => false]),
            'IntelFetch IP Lookup' => $this->post('/api/intelfetch/ip-lookup', ['ip' => $ip]),
            'Snusbase IP WHOIS' => $this->post('/api/snusbase/ip-whois', ['ip' => $ip]),
        ];
        return $this->merged($ip, 'ip', $sources);
    }

    // ---------- Domain ----------
    public function domain($domain) {
        $sources = [
            'Universal (LeakCheck, HackCheck)' => $this->universalSearch('domain', $domain),
            'LeakCheck v2' => $this->post('/api/leakcheck/v2', ['term' => $domain, 'search_type' => 'domain', 'limit' => 500]),
            'Snusbase' => $this->post('/api/snusbase/search', ['terms' => [$domain], 'types' => ['domain'], 'wildcard' => false]),
            'LeakSight' => $this->post('/api/leaksight', ['term' => $domain, 'search_type' => 'domain']),
            'Akula' => $this->post('/api/akula', ['searchTerm' => $domain, 'search_type' => 'domain']),
            'Shodan DNS' => $this->post('/api/shodan/dns', ['domain' => $domain, 'history' => false]),
            'INF0SEC Domain' => $this->get('/api/inf0sec/domain', ['q' => $domain]),
            'IntelFetch Domain' => $this->get('/api/intelfetch/domain', ['domain' => $domain]),
        ];
        return $this->merged($domain, 'domain', $sources);
    }

    // ---------- Breach (email / username / IP depending on input) ----------
    public function breach($query) {
        if (strpos($query, '@') !== false) return $this->email($query);
        if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', trim($query))) return $this->ip($query);
        return $this->username($query);
    }

    // ---------- Social / platform-specific ----------
    public function tiktok($username) {
        $basic = $this->post('/api/tiktokrecon', ['username' => $username, 'type' => 'basic']);
        $full = $this->post('/api/tiktokrecon', ['username' => $username, 'type' => 'full']);
        $sources = [
            'TikTok Recon (basic)' => $basic,
            'TikTok Recon (full)' => $full,
            'Universal username' => $this->universalSearch('username', $username),
        ];
        return $this->merged($username, 'tiktok', $sources);
    }

    public function reddit($username) {
        $sources = [
            'Room 101 Analyze' => $this->get('/api/room101/analyze/' . rawurlencode($username)),
            'Room 101 User' => $this->get('/api/room101/user/' . rawurlencode($username)),
            'Universal username' => $this->universalSearch('username', $username),
        ];
        return $this->merged($username, 'reddit', $sources);
    }

    public function discord($user_id) {
        $sources = [
            'Genesis Discord' => $this->get('/api/genesis/discord', ['id' => $user_id]),
            'Discord Stalker' => $this->get('/api/discord-stalker', ['query' => $user_id]),
            'INF0SEC Discord' => $this->get('/api/inf0sec/discord', ['q' => $user_id]),
        ];
        return $this->merged($user_id, 'discord', $sources);
    }

    public function steam($steam_id) {
        $sources = [
            'OathNet Steam' => $this->post('/api/oathnet/steam', ['steam_id' => $steam_id]),
            'Genesis Steam' => $this->get('/api/genesis/steam', ['id' => $steam_id]),
        ];
        return $this->merged($steam_id, 'steam', $sources);
    }

    public function github($username) {
        $sources = [
            'IntelFetch GitHub' => $this->get('/api/intelfetch/github', ['username' => $username, 'extensive' => 'true']),
            'Universal username' => $this->universalSearch('username', $username),
        ];
        return $this->merged($username, 'github', $sources);
    }

    /** YouTube: OathNet GHunt (Google account lookup) + universal username */
    public function youtube($channel_id) {
        $ghunt = $this->post('/api/oathnet/ghunt', ['query' => $channel_id]);
        $sources = [
            'OathNet GHunt (Google)' => $ghunt,
            'Universal username' => $this->universalSearch('username', $channel_id),
        ];
        return $this->merged($channel_id, 'youtube', $sources);
    }

    /** Twitter, Instagram, LinkedIn, Facebook, Telegram, Snapchat, Twitch – username-based sources */
    public function twitter($username) { return $this->username($username); }
    public function instagram($username) { return $this->username($username); }
    public function linkedin($username) { return $this->username($username); }
    public function facebook($username) { return $this->username($username); }
    public function telegram($username) { return $this->username($username); }
    public function snapchat($username) { return $this->username($username); }
    public function twitch($username) { return $this->username($username); }

    // ---------- Crypto, Company (from docs) ----------
    public function crypto($address, $coin = 'btc') {
        $type = strtoupper($coin);
        if ($type === 'ETH') $type = 'ETH'; elseif ($type === 'LTC') $type = 'LTC'; else $type = 'BTC';
        $data = $this->post('/api/intelfetch/crypto', ['crypto_type' => $type, 'address' => $address]);
        $sources = ['IntelFetch Crypto' => $data];
        return $this->merged($address, 'crypto', $sources);
    }

    public function company($name, $country = '') {
        $sources = [
            'IntelFetch Enterprise' => $this->get('/api/intelfetch/enterprise', ['query' => $name, 'size' => 50]),
        ];
        return $this->merged($name, 'company', $sources);
    }

    // ---------- Paste / other ----------
    public function paste($query) {
        $sources = [
            'Universal username' => $this->universalSearch('username', $query),
            'INF0SEC Username' => $this->get('/api/inf0sec/username', ['q' => $query]),
        ];
        return $this->merged($query, 'paste', $sources);
    }

    // ---------- Optional: License plate (Rutify – Chilean), VIN, BIN, IBAN, Reverse image, Face ----------
    public function plate($plate, $state = '') {
        $data = $this->post('/api/rutify/car', ['plate' => $plate]);
        return [
            'success' => true,
            'data' => [
                'search_term' => $plate,
                'search_type' => 'plate',
                'note' => 'Rutify plate lookup (Chilean data).',
                'results' => $data !== null ? ['Rutify Car' => $data] : [],
            ],
        ];
    }

    private function notSupported($name, $detail = '') {
        return [
            'success' => true,
            'data' => [
                'note' => $detail ?: 'No dedicated OsintDog endpoint for this type.',
                'requested' => $name,
                'results' => [],
            ],
        ];
    }

    public function vin($vin) { return $this->notSupported('VIN'); }
    public function bin($bin) { return $this->notSupported('BIN'); }
    public function iban($iban) { return $this->notSupported('IBAN'); }
    public function reverseImage($url) { return $this->notSupported('Reverse image'); }
    public function face($url) { return $this->notSupported('Face recognition'); }
}
