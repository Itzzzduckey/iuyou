<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/keyauth.php';

$error = '';

// Dev bypass: accept any key when running on localhost (disable for production)
$is_local = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1']) || (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['license_key'])) {
    $license_key = trim($_POST['license_key']);
    
    if (empty($license_key)) {
        $error = 'Please enter your license key';
    } elseif ($is_local) {
        // Local dev: skip KeyAuth, log in with fake session
        $_SESSION['authenticated'] = true;
        $_SESSION['license_key'] = $license_key;
        $_SESSION['username'] = 'Dev User';
        $_SESSION['subscription'] = 'Lifetime';
        $_SESSION['expiry'] = 'Never';
        $_SESSION['login_time'] = time();
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        header('Location: dashboard.php');
        exit;
    } else {
        $keyauth = new KeyAuth();
        $result = $keyauth->login($license_key);
        
        if ($result['success']) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

$keyauth = new KeyAuth();
if ($keyauth->isAuthenticated()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OsintX - Secure Access Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', sans-serif;
            background: #000000;
            color: #e0e0e0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }

        .bg-gradient {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.08;
            pointer-events: none;
            z-index: 0;
        }

        .gradient-1 {
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 70%);
            top: -250px; left: -250px;
        }
        .gradient-2 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 70%);
            top: 50%; right: -200px;
        }
        .gradient-3 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.35) 0%, rgba(255,255,255,0) 70%);
            bottom: -150px; left: 15%;
        }
        .gradient-4 {
            width: 650px; height: 650px;
            background: radial-gradient(circle, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0) 70%);
            top: 35%; left: 45%;
        }

        .particles-container {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            animation: float-particle linear infinite;
            opacity: 0;
        }

        @keyframes float-particle {
            0%   { transform: translateY(100vh) translateX(0) scale(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { transform: translateY(-100vh) translateX(var(--drift)) scale(1); opacity: 0; }
        }

        .particle.small  { width: 3px; height: 3px; background: rgba(255,255,255,0.4); box-shadow: 0 0 6px rgba(255,255,255,0.3); }
        .particle.medium { width: 5px; height: 5px; background: rgba(200,200,200,0.5); box-shadow: 0 0 8px rgba(200,200,200,0.4); }
        .particle.large  { width: 7px; height: 7px; background: rgba(255,255,255,0.6); box-shadow: 0 0 12px rgba(255,255,255,0.5); }
        .particle.grey   { background: rgba(150,150,150,0.4); box-shadow: 0 0 8px rgba(150,150,150,0.3); }

        .header {
            background: rgba(10,10,10,0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            padding: 20px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 10;
        }

        .brand-container {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            transition: opacity 0.2s ease;
        }

        .brand-container:hover { opacity: 0.85; }

        .logo-img {
            height: 42px;
            width: auto;
            filter: drop-shadow(0 0 10px rgba(255,255,255,0.3));
        }

        .brand-name {
            font-size: 26px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .back-btn {
            color: #999;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .back-btn:hover {
            color: #fff;
            background: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.12);
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px 20px;
            position: relative;
            z-index: 1;
        }

        .container {
            width: 100%;
            max-width: 480px;
        }

        .login-box-wrapper {
            position: relative;
            padding: 2px;
            background: linear-gradient(135deg,
                rgba(255,255,255,0.15) 0%,
                rgba(255,255,255,0.05) 50%,
                rgba(255,255,255,0.15) 100%);
            border-radius: 18px;
            box-shadow:
                0 0 40px rgba(255,255,255,0.1),
                0 0 80px rgba(255,255,255,0.05),
                0 20px 60px rgba(0,0,0,0.5);
        }

        .login-box {
            background: #0a0a0a;
            border-radius: 16px;
            padding: 48px 40px;
            position: relative;
        }

        .login-header { margin-bottom: 36px; }

        .login-title {
            font-size: 20px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .login-title i { color: #fff; font-size: 18px; }

        .login-subtitle {
            font-size: 13px;
            color: #888;
            line-height: 1.5;
        }

        .form-group { margin-bottom: 28px; }

        label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #bbb;
            margin-bottom: 10px;
            font-weight: 500;
        }

        label i { font-size: 12px; color: #777; }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 15px 18px;
            background: #000000;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            color: #fff;
            font-size: 14px;
            font-family: 'Courier New', monospace;
            transition: all 0.2s ease;
            outline: none;
            letter-spacing: 1px;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: rgba(255,255,255,0.3);
            background: #050505;
            box-shadow: 0 0 0 3px rgba(255,255,255,0.06);
        }

        input::placeholder { color: #444; letter-spacing: normal; }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: #ffffff;
            color: #000;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 16px rgba(255,255,255,0.15);
        }

        .btn-login:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(255,255,255,0.25);
        }

        .btn-login:active { transform: translateY(0); }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 24px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .alert-error {
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.3);
            color: #ef4444;
        }

        .footer-section {
            margin-top: 40px;
            padding-top: 32px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 16px;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px; height: 44px;
            color: #888;
            text-decoration: none;
            font-size: 18px;
            transition: all 0.2s ease;
            border-radius: 10px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
        }

        .social-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.15);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255,255,255,0.1);
        }

        .payment-footer {
            background: rgba(10,10,10,0.95);
            border-top: 1px solid rgba(255,255,255,0.06);
            padding: 32px 24px;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .payment-title {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .payment-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
            max-width: 900px;
            margin: 0 auto;
        }

        .payment-logo {
            height: 36px;
            width: auto;
            opacity: 0.7;
            transition: all 0.2s ease;
        }

        .payment-logo:hover {
            opacity: 1;
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        .footer-links {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-top: 24px;
            font-size: 13px;
        }

        .footer-links a { color: #888; text-decoration: none; transition: color 0.2s ease; }
        .footer-links a:hover { color: #fff; }
        .footer-links .separator { color: #444; }

        .copyright {
            text-align: center;
            margin-top: 16px;
            font-size: 12px;
            color: #555;
        }

        @media (max-width: 768px) {
            .header { padding: 16px 20px; }
            .brand-name { font-size: 22px; }
            .logo-img { height: 36px; }
            .login-box { padding: 36px 28px; }
            .main-content { padding: 40px 16px; }
        }

        @media (max-width: 480px) {
            .login-box { padding: 32px 24px; }
            .brand-name { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="particles-container" id="particlesContainer"></div>

    <div class="bg-gradient gradient-1"></div>
    <div class="bg-gradient gradient-2"></div>
    <div class="bg-gradient gradient-3"></div>
    <div class="bg-gradient gradient-4"></div>

    <div class="header">
        <a href="/" class="brand-container">
            <img src="/images/732374ad-e8c2-4b4e-9af8-eb8364b297ae-removebg-preview.png" alt="OsintX Logo" class="logo-img">
            <span class="brand-name">OsintX.it</span>
        </a>
        <a href="/" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Back
        </a>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="login-box-wrapper">
                <div class="login-box">
                    <div class="login-header">
                        <div class="login-title">
                            <i class="fas fa-shield-alt"></i>
                            Login to OsintX.it
                        </div>
                        <p class="login-subtitle">Access your threat intelligence dashboard</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="license_key">
                                <i class="fas fa-key"></i>
                                License Key
                            </label>
                            <input
                                type="text"
                                id="license_key"
                                name="license_key"
                                placeholder="XXXXX-XXXXX-XXXXX-XXXXX"
                                autocomplete="off"
                                required
                            >
                        </div>

                        <button type="submit" class="btn-login">
                            Sign In
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>

                    <div class="footer-section">
                        <div class="social-links">
                            <a href="https://dsc.gg/osintx" class="social-link" target="_blank" title="Discord">
                                <i class="fab fa-discord"></i>
                            </a>
                            <a href="https://t.me/osintx" class="social-link" target="_blank" title="Telegram">
                                <i class="fab fa-telegram"></i>
                            </a>
                            <a href="mailto:info@osintx.it" class="social-link" title="Email">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="payment-footer">
        <div class="payment-title">Accepted Payment Methods</div>
        <div class="payment-logos">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" class="payment-logo" style="height: 20px;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" class="payment-logo" style="height: 18px;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" class="payment-logo" style="height: 26px;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/4/46/Bitcoin.svg" alt="Bitcoin" class="payment-logo" style="height: 26px;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/0/05/Ethereum_logo_2014.svg" alt="Ethereum" class="payment-logo" style="height: 26px;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/f/f8/LTC-400.png" alt="Litecoin" class="payment-logo" style="height: 26px;">
            <img src="https://upload.wikimedia.org/wikipedia/en/b/b9/Solana_logo.png" alt="Solana" class="payment-logo" style="height: 26px;">
        </div>
        <div class="footer-links">
            <a href="/pages/tos.html">Terms of Service</a>
            <span class="separator">•</span>
            <a href="/pages/contact.html">Contact</a>
        </div>
        <div class="copyright">© 2025 OsintX.it - All rights reserved</div>
    </div>

    <script>
        function createParticles() {
            const container = document.getElementById('particlesContainer');
            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                const sizes = ['small', 'medium', 'large'];
                particle.classList.add(sizes[Math.floor(Math.random() * sizes.length)]);
                if (Math.random() > 0.5) particle.classList.add('grey');
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDuration = (15 + Math.random() * 25) + 's';
                particle.style.animationDelay = Math.random() * 10 + 's';
                particle.style.setProperty('--drift', ((Math.random() - 0.5) * 200) + 'px');
                container.appendChild(particle);
            }
        }
        createParticles();
    </script>
</body>
</html>