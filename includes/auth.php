<?php
require_once __DIR__ . '/keyauth.php';

function requireAuth() {
    $keyauth = new KeyAuth();
    if (!$keyauth->isAuthenticated()) {
        header('Location: /login.php');
        exit;
    }
    return $keyauth;
}

function getPlanName() {
    // KeyAuth returns subscription name exactly as set in dashboard
    // Names: "Premium" (level 1), "Premium Advanced" (level 2), "Lifetime" (level 3)
    $sub = trim($_SESSION['subscription'] ?? '');
    if (empty($sub)) return 'Unknown';

    // Match exactly first, then fuzzy
    if ($sub === 'Lifetime')         return 'Lifetime';
    if ($sub === 'Premium Advanced') return 'Premium Advanced';
    if ($sub === 'Premium')          return 'Premium';

    // Fuzzy fallback
    $sub_lower = strtolower($sub);
    if (strpos($sub_lower, 'lifetime') !== false)         return 'Lifetime';
    if (strpos($sub_lower, 'advanced') !== false)         return 'Premium Advanced';
    if (strpos($sub_lower, 'premium') !== false)          return 'Premium';

    // Return raw value so we can debug
    return $sub;
}

function getDailyLimit() {
    $plan = getPlanName();
    switch ($plan) {
        case 'Lifetime':         return DAILY_LIFETIME_SEARCHES;
        case 'Premium Advanced': return DAILY_PREMIUM_ADVANCED_SEARCHES;
        case 'Premium':          return DAILY_PREMIUM_SEARCHES;
        default:                 return DAILY_PREMIUM_SEARCHES;
    }
}

function getRemainingSearches() {
    if (!isset($_SESSION['search_count'])) {
        $_SESSION['search_count'] = 0;
        $_SESSION['search_date'] = date('Y-m-d');
    }

    if ($_SESSION['search_date'] !== date('Y-m-d')) {
        $_SESSION['search_count'] = 0;
        $_SESSION['search_date'] = date('Y-m-d');
    }

    $limit = getDailyLimit();
    if ($limit >= 999999) return 999999;
    return max(0, $limit - $_SESSION['search_count']);
}

function incrementSearchCount() {
    if (!isset($_SESSION['search_count'])) {
        $_SESSION['search_count'] = 0;
        $_SESSION['search_date'] = date('Y-m-d');
    }
    $_SESSION['search_count']++;
}

function canSearch() {
    return getRemainingSearches() > 0;
}
?>