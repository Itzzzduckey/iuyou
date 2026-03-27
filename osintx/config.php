<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

define('KEYAUTH_APP_NAME', 'OsintX');
define('KEYAUTH_OWNER_ID', 'qicC3tq57e');
define('KEYAUTH_APP_SECRET', '1813b3f52020170630ddc73488f0e09f271955c36c52d4d2265d9bf998ddeed0');
define('KEYAUTH_VERSION', '1.0');

define('OSINTDOG_API_KEY', 'EwUatNBRnyJKluS76YscGB3-hoExGXxa_BHW6xAHoTE');
define('OSINTDOG_BASE_URL', 'https://osintdog.com');

define('SESSION_LIFETIME', 86400);
define('SESSION_NAME', 'osintx_session');

// Daily search limits per plan
define('DAILY_PREMIUM_SEARCHES', 70);            // Premium €3,99/mo
define('DAILY_PREMIUM_ADVANCED_SEARCHES', 500);  // Premium Advanced €9,99/mo
define('DAILY_LIFETIME_SEARCHES', 999999);       // Lifetime — unlimited

define('SITE_URL', 'https://osintx.it');

date_default_timezone_set('Europe/Rome');
?>