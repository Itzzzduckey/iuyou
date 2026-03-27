<?php
session_start();
require_once __DIR__ . '/includes/auth.php';

$keyauth = requireAuth();

// Get module from URL
$module = $_GET['m'] ?? 'home';
$module_file = __DIR__ . '/modules/' . basename($module) . '.php';
ob_start();
if (file_exists($module_file)) {
    include $module_file;
} else {
    include __DIR__ . "/modules/home.php";
}
$module_content = ob_get_clean();

$userInfo = [
    "username" => $_SESSION["username"] ?? "User",
    "subscription" => $_SESSION["subscription"] ?? "Free",
];
$remaining = getRemainingSearches();
$limit = getDailyLimit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - OsintX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #000;
            color: #fff;
            overflow-x: hidden;
        }
        
        /* Background gradients - STILE OSINTX.IT */
        .bg-gradients {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }
        
        .gradient-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(140px);
            opacity: 0.04;
            animation: float-smooth 25s infinite ease-in-out;
        }
        
        .gradient-blob:nth-child(1) {
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(255,255,255,0.5) 0%, rgba(255,255,255,0) 70%);
            top: -300px;
            left: -300px;
        }
        
        .gradient-blob:nth-child(2) {
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 70%);
            top: 50%;
            right: -250px;
            animation-delay: 8s;
        }
        
        .gradient-blob:nth-child(3) {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255,255,255,0.45) 0%, rgba(255,255,255,0) 70%);
            bottom: -200px;
            left: 20%;
            animation-delay: 15s;
        }
        
        @keyframes float-smooth {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(80px, -80px) scale(1.15); }
            66% { transform: translate(-60px, 60px) scale(0.95); }
        }
        
        /* Layout */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }
        
        /* Sidebar - STILE OSINTX.IT */
        .sidebar {
            width: 260px;
            background: rgba(10,10,10,0.8);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255,255,255,0.08);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { 
            background: rgba(255,255,255,0.1); 
            border-radius: 3px;
        }
        
        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        
        .logo-text {
            font-size: 1.6rem;
            font-weight: 900;
            letter-spacing: -0.5px;
            text-shadow: 0 0 20px rgba(255,255,255,0.3);
        }
        
        .logo-sub { color: #666; font-size: 0.8rem; }
        
        .nav-section {
            padding: 12px 0;
        }
        
        .nav-section-title {
            padding: 12px 20px 8px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555;
            font-weight: 600;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: #888;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.9rem;
            border-left: 3px solid transparent;
        }
        
        .nav-item:hover {
            background: rgba(255,255,255,0.05);
            color: #fff;
            box-shadow: 0 0 20px rgba(255,255,255,0.05);
        }
        
        .nav-item.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left-color: #fff;
            box-shadow: 0 0 30px rgba(255,255,255,0.15);
        }
        
        .nav-item i { width: 18px; text-align: center; }
        
        /* Main Content */
        .main-content {
            margin-left: 260px;
            flex: 1;
            min-height: 100vh;
        }
        
        .top-bar {
            padding: 20px 40px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            background: rgba(10,10,10,0.8);
            backdrop-filter: blur(20px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .top-bar-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .credit-badge {
            padding: 8px 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            font-size: 0.85rem;
            box-shadow: 0 4px 16px rgba(255,255,255,0.05);
        }
        
        .btn-logout {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #f87171;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .key-display { cursor: pointer; color: #555; font-family: monospace; font-size: 0.8rem; letter-spacing: 2px; transition: all 0.2s; }
        .key-display:hover { color: #fff; letter-spacing: normal; }
        .btn-logout:hover {
            background: rgba(239,68,68,0.2);
            box-shadow: 0 4px 16px rgba(239,68,68,0.3);
        }
        
        .content-area {
            padding: 40px;
            max-width: 1400px;
        }
        
        /* Module Cards - STILE OSINTX.IT */
        .module-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.01) 100%);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .module-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        }
        
        .module-card:hover {
            background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.03) 100%);
            border-color: rgba(255,255,255,0.2);
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(255,255,255,0.1);
        }
        
        /* Forms */
        .form-group { margin-bottom: 20px; }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            color: #aaa;
        }
        
        .form-input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            color: #fff;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: rgba(255,255,255,0.25);
            background: rgba(255,255,255,0.06);
            box-shadow: 0 0 20px rgba(255,255,255,0.1);
        }
        
        .btn-primary {
            background: white;
            color: black;
            padding: 14px 32px;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(255,255,255,0.15);
        }
        
        .btn-primary:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(255,255,255,0.25);
        }
        
        /* Results - STILE OSINTX.IT */
        .result-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.01) 100%);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 16px;
            transition: all 0.3s;
        }
        
        .result-card:hover {
            border-color: rgba(255,255,255,0.2);
            box-shadow: 0 8px 32px rgba(255,255,255,0.1);
        }
        
        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        
        .result-badge {
            padding: 4px 12px;
            background: rgba(34,197,94,0.15);
            color: #4ade80;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .result-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }
        
        .result-item {
            padding: 12px;
            background: rgba(255,255,255,0.02);
            border-radius: 8px;
        }
        
        .result-label {
            font-size: 0.75rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        
        .result-value {
            font-size: 0.95rem;
            color: #fff;
            font-weight: 500;
        }
        
        /* Alert */
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
        }
        
        .alert-success {
            background: rgba(34,197,94,0.1);
            border: 1px solid rgba(34,197,94,0.3);
            color: #86efac;
        }
        
        /* Formatted results (no raw JSON) */
        .result-meta {
            padding: 12px 16px;
            margin-bottom: 20px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 10px;
            font-size: 0.9rem;
            color: #aaa;
        }
        .result-meta strong { color: #fff; }
        .result-meta-item { margin-right: 16px; }
        .result-source-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            margin-bottom: 16px;
            overflow: hidden;
        }
        .result-source-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            background: rgba(255,255,255,0.04);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .result-source-name {
            font-weight: 700;
            font-size: 0.95rem;
            color: #fff;
        }
        .result-source-badge {
            font-size: 0.75rem;
            padding: 4px 10px;
            background: rgba(74,222,128,0.15);
            color: #4ade80;
            border-radius: 20px;
            font-weight: 600;
        }
        .result-source-body {
            padding: 18px;
        }
        .result-kv {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            gap: 16px;
        }
        .result-kv:last-child { border-bottom: none; }
        .result-kv .result-label {
            flex-shrink: 0;
            font-size: 0.8rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .result-kv .result-value {
            font-size: 0.9rem;
            color: #e0e0e0;
            word-break: break-all;
            text-align: right;
        }
        .result-kv.cell-password .result-value { color: #f87171; font-family: monospace; }
        .result-kv.cell-email .result-value { color: #60a5fa; }
        .result-kv.cell-ip .result-value { color: #a78bfa; }
        .result-kv.cell-name .result-value { color: #4ade80; }
        .result-nested { margin-top: 12px; }
        .result-block { margin-bottom: 16px; }
        .result-block-title {
            display: block;
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .result-muted { font-size: 0.85rem; color: #666; margin-left: 8px; }
        .result-table-wrap {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.06);
        }
        .result-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
        }
        .result-table th {
            text-align: left;
            padding: 10px 14px;
            background: rgba(255,255,255,0.04);
            color: #888;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .result-table td {
            padding: 10px 14px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            color: #e0e0e0;
            word-break: break-word;
        }
        .result-table tr:last-child td { border-bottom: none; }
        .result-table td.cell-password { color: #f87171; font-family: monospace; }
        .result-table td.cell-email { color: #60a5fa; }
        .result-table td.cell-ip { color: #a78bfa; }
        .result-table td.cell-name { color: #4ade80; }
        .result-empty {
            text-align: center;
            padding: 32px 20px;
            color: #555;
            font-size: 0.95rem;
        }
        /* TikTok profile (injected via AJAX) */
        .tiktok-profile-card { background: linear-gradient(135deg, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0.02) 100%); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 28px; margin-bottom: 24px; }
        .tiktok-profile-top { display: flex; align-items: flex-start; gap: 24px; flex-wrap: wrap; }
        .tiktok-avatar-wrap { flex-shrink: 0; }
        .tiktok-avatar { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.05); }
        .tiktok-avatar-placeholder { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #fe2c55 0%, #25f4ee 100%); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2.5rem; border: 3px solid rgba(255,255,255,0.15); }
        .tiktok-profile-info { flex: 1; min-width: 200px; }
        .tiktok-profile-name { font-size: 1.5rem; font-weight: 700; color: #fff; margin-bottom: 4px; }
        .tiktok-profile-handle { font-size: 1rem; color: #888; margin-bottom: 12px; }
        .tiktok-profile-about { font-size: 0.9rem; color: #aaa; margin-bottom: 20px; line-height: 1.5; }
        .tiktok-profile-meta { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 12px; margin-bottom: 0; }
        .tiktok-meta-item { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; padding: 12px; text-align: center; }
        .tiktok-meta-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #666; margin-bottom: 4px; }
        .tiktok-meta-value { font-size: 1.1rem; font-weight: 700; color: #fff; }
        .tiktok-results-section { margin-top: 8px; }
        .module-results-loading { padding: 48px 24px; text-align: center; color: #666; }
        .module-results-loading i { display: block; margin-bottom: 12px; font-size: 2rem; color: #fff; }
        /* Result enter animation – GPU-friendly, no layout thrash */
        @keyframes resultEnter {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .module-results > .result-card,
        .module-results > .alert {
            animation: resultEnter 0.28s ease-out;
        }
        .content-area { animation: contentEnter 0.22s ease-out; }
        @keyframes contentEnter {
            from { opacity: 0.6; }
            to   { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="bg-gradients">
        <div class="gradient-blob"></div>
        <div class="gradient-blob"></div>
        <div class="gradient-blob"></div>
    </div>
    
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="logo-text">OsintX<span class="logo-sub">.it</span></div>
            </div>
            
            <div class="nav-section">
                <a href="dashboard.php?m=home" class="nav-item nav-module-link <?php echo $module === 'home' ? 'active' : ''; ?>" data-module="home">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">OSINT TOOLS</div>
                <a href="dashboard.php?m=email" class="nav-item nav-module-link <?php echo $module === 'email' ? 'active' : ''; ?>" data-module="email">
                    <i class="fas fa-envelope"></i> Email OSINT
                </a>
                <a href="dashboard.php?m=username" class="nav-item nav-module-link <?php echo $module === 'username' ? 'active' : ''; ?>" data-module="username">
                    <i class="fas fa-user"></i> Username Search
                </a>
                <a href="dashboard.php?m=phone" class="nav-item nav-module-link <?php echo $module === 'phone' ? 'active' : ''; ?>" data-module="phone">
                    <i class="fas fa-phone"></i> Phone Lookup
                </a>
                <a href="dashboard.php?m=ip" class="nav-item nav-module-link <?php echo $module === 'ip' ? 'active' : ''; ?>" data-module="ip">
                    <i class="fas fa-globe"></i> IP Lookup
                </a>
                <a href="dashboard.php?m=domain" class="nav-item nav-module-link <?php echo $module === 'domain' ? 'active' : ''; ?>" data-module="domain">
                    <i class="fas fa-server"></i> Domain Lookup
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">SOCIAL MEDIA</div>
                <a href="dashboard.php?m=twitter" class="nav-item nav-module-link <?php echo $module === 'twitter' ? 'active' : ''; ?>" data-module="twitter">
                    <i class="fab fa-twitter"></i> Twitter
                </a>
                <a href="dashboard.php?m=instagram" class="nav-item nav-module-link <?php echo $module === 'instagram' ? 'active' : ''; ?>" data-module="instagram">
                    <i class="fab fa-instagram"></i> Instagram
                </a>
                <a href="dashboard.php?m=tiktok" class="nav-item nav-module-link <?php echo $module === 'tiktok' ? 'active' : ''; ?>" data-module="tiktok">
                    <i class="fab fa-tiktok"></i> TikTok
                </a>
                <a href="dashboard.php?m=github" class="nav-item nav-module-link <?php echo $module === 'github' ? 'active' : ''; ?>" data-module="github">
                    <i class="fab fa-github"></i> GitHub
                </a>
                <a href="dashboard.php?m=linkedin" class="nav-item nav-module-link <?php echo $module === 'linkedin' ? 'active' : ''; ?>" data-module="linkedin">
                    <i class="fab fa-linkedin"></i> LinkedIn
                </a>
                <a href="dashboard.php?m=facebook" class="nav-item nav-module-link <?php echo $module === 'facebook' ? 'active' : ''; ?>" data-module="facebook">
                    <i class="fab fa-facebook"></i> Facebook
                </a>
                <a href="dashboard.php?m=youtube" class="nav-item nav-module-link <?php echo $module === 'youtube' ? 'active' : ''; ?>" data-module="youtube">
                    <i class="fab fa-youtube"></i> YouTube
                </a>
                <a href="dashboard.php?m=reddit" class="nav-item nav-module-link <?php echo $module === 'reddit' ? 'active' : ''; ?>" data-module="reddit">
                    <i class="fab fa-reddit"></i> Reddit
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">MESSAGING</div>
                <a href="dashboard.php?m=discord" class="nav-item nav-module-link <?php echo $module === 'discord' ? 'active' : ''; ?>" data-module="discord">
                    <i class="fab fa-discord"></i> Discord
                </a>
                <a href="dashboard.php?m=telegram" class="nav-item nav-module-link <?php echo $module === 'telegram' ? 'active' : ''; ?>" data-module="telegram">
                    <i class="fab fa-telegram"></i> Telegram
                </a>
                <a href="dashboard.php?m=snapchat" class="nav-item nav-module-link <?php echo $module === 'snapchat' ? 'active' : ''; ?>" data-module="snapchat">
                    <i class="fab fa-snapchat"></i> Snapchat
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">GAMING</div>
                <a href="dashboard.php?m=twitch" class="nav-item nav-module-link <?php echo $module === 'twitch' ? 'active' : ''; ?>" data-module="twitch">
                    <i class="fab fa-twitch"></i> Twitch
                </a>
                <a href="dashboard.php?m=steam" class="nav-item nav-module-link <?php echo $module === 'steam' ? 'active' : ''; ?>" data-module="steam">
                    <i class="fab fa-steam"></i> Steam
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">VEHICLE & FINANCIAL</div>
                <a href="dashboard.php?m=vin" class="nav-item nav-module-link <?php echo $module === 'vin' ? 'active' : ''; ?>" data-module="vin">
                    <i class="fas fa-car"></i> VIN Lookup
                </a>
                <a href="dashboard.php?m=plate" class="nav-item nav-module-link <?php echo $module === 'plate' ? 'active' : ''; ?>" data-module="plate">
                    <i class="fas fa-id-card"></i> License Plate
                </a>
                <a href="dashboard.php?m=crypto" class="nav-item nav-module-link <?php echo $module === 'crypto' ? 'active' : ''; ?>" data-module="crypto">
                    <i class="fab fa-bitcoin"></i> Crypto Wallet
                </a>
                <a href="dashboard.php?m=bin" class="nav-item nav-module-link <?php echo $module === 'bin' ? 'active' : ''; ?>" data-module="bin">
                    <i class="fas fa-credit-card"></i> BIN Lookup
                </a>
                <a href="dashboard.php?m=iban" class="nav-item nav-module-link <?php echo $module === 'iban' ? 'active' : ''; ?>" data-module="iban">
                    <i class="fas fa-university"></i> IBAN Lookup
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">ADVANCED</div>
                <a href="dashboard.php?m=company" class="nav-item nav-module-link <?php echo $module === 'company' ? 'active' : ''; ?>" data-module="company">
                    <i class="fas fa-building"></i> Company Search
                </a>
                <a href="dashboard.php?m=reverse-image" class="nav-item nav-module-link <?php echo $module === 'reverse-image' ? 'active' : ''; ?>" data-module="reverse-image">
                    <i class="fas fa-image"></i> Reverse Image
                </a>
                <a href="dashboard.php?m=face" class="nav-item nav-module-link <?php echo $module === 'face' ? 'active' : ''; ?>" data-module="face">
                    <i class="fas fa-user-secret"></i> Face Recognition
                </a>
                <a href="dashboard.php?m=breach" class="nav-item nav-module-link <?php echo $module === 'breach' ? 'active' : ''; ?>" data-module="breach">
                    <i class="fas fa-shield-alt"></i> Breach Check
                </a>
                <a href="dashboard.php?m=paste" class="nav-item nav-module-link <?php echo $module === 'paste' ? 'active' : ''; ?>" data-module="paste">
                    <i class="fas fa-file-alt"></i> Paste Search
                </a>
            </div>
        </div>
        
        <div class="main-content">
            <div class="top-bar">
                <h1 class="page-title">Dashboard</h1>
                <div class="top-bar-right">
                    <div class="credit-badge">
                        Credits: <strong><?php echo $remaining; ?>/<?php echo $limit; ?></strong>
                    </div>
                    <span class="key-display" title="<?php echo htmlspecialchars($userInfo['username']); ?>">••••••••••••••••</span>
                    <a href="logout.php" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
            
            <div class="content-area">
                <?php echo $module_content; ?>
            </div>
        </div>
    </div>
</body>
</html>
<style>
.key-display {
    cursor: pointer;
    letter-spacing: 2px;
    color: #666;
    transition: all 0.2s;
    font-family: monospace;
    font-size: 0.85rem;
}
.key-display:hover {
    color: #fff;
    letter-spacing: normal;
}
</style>
<script>
document.querySelectorAll('.key-display').forEach(el => {
    const key = el.getAttribute('title');
    el.addEventListener('mouseenter', () => { el.textContent = key; });
    el.addEventListener('mouseleave', () => { el.textContent = '••••••••••••••••'; });
});

document.addEventListener('DOMContentLoaded', function() {
    var contentArea = document.querySelector('.content-area');
    if (!contentArea) return;

    function getModuleFromUrl() {
        var m = (window.location.search || '').replace(/^\?/, '').split('&').filter(function(p) { return p.indexOf('m=') === 0; })[0];
        return m ? decodeURIComponent(m.replace('m=', '')) : 'home';
    }

    function setActiveNav(module) {
        document.querySelectorAll('.sidebar .nav-module-link').forEach(function(a) {
            a.classList.toggle('active', a.getAttribute('data-module') === module);
        });
    }

    function loadModule(module, noPush) {
        fetch('get_module.php?m=' + encodeURIComponent(module))
            .then(function(r) {
                if (r.status === 401) { window.location.href = 'login.php'; return; }
                return r.text();
            })
            .then(function(html) {
                if (typeof html !== 'string') return;
                contentArea.innerHTML = html;
                setActiveNav(module);
                contentArea.style.animation = 'none';
                contentArea.offsetHeight;
                contentArea.style.animation = 'contentEnter 0.22s ease-out';
            })
            .catch(function() {});
    }

    document.querySelector('.sidebar').addEventListener('click', function(e) {
        var a = e.target.closest('a.nav-module-link');
        if (!a) return;
        e.preventDefault();
        var module = a.getAttribute('data-module');
        if (!module) return;
        var url = 'dashboard.php?m=' + encodeURIComponent(module);
        history.pushState({ module: module }, '', url);
        loadModule(module);
    });

    window.addEventListener('popstate', function(e) {
        var module = e.state && e.state.module ? e.state.module : getModuleFromUrl();
        loadModule(module, true);
    });

    contentArea.addEventListener('submit', function(e) {
        var form = e.target;
        if (!form || !form.classList.contains('dashboard-search-form')) return;
        e.preventDefault();
        var module = form.getAttribute('data-module');
        if (!module) return;
        var queryInput = form.querySelector('[name="query"]');
        var query = queryInput ? queryInput.value.trim() : '';
        if (!query) return;
        var resultsEl = document.getElementById('results-' + module);
        if (!resultsEl) return;
        var btn = form.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...'; }
        resultsEl.innerHTML = '<div class="module-results-loading"><i class="fas fa-spinner fa-spin"></i>Searching...</div>';
        resultsEl.style.display = 'block';

        var body = new FormData();
        body.append('query', query);
        fetch('dashboard_search.php?m=' + encodeURIComponent(module), { method: 'POST', body: body })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-search"></i> Search'; }
                if (data.ok) {
                    resultsEl.innerHTML = data.html;
                    if (data.remaining !== undefined) {
                        var badge = document.querySelector('.credit-badge strong');
                        if (badge) badge.textContent = data.remaining + '/' + (data.limit != null ? data.limit : '');
                    }
                } else {
                    resultsEl.innerHTML = '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> ' + (data.error || 'Error') + '</div>';
                }
            })
            .catch(function() {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-search"></i> Search'; }
                resultsEl.innerHTML = '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> Connection error</div>';
            });
    });
});
</script>
