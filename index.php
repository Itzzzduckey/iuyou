<?php
session_start();

// Verify/Turnstile disabled for local dev - re-enable for production
// if (!isset($_SESSION['verified']) || $_SESSION['verified'] !== true) {
//     header('Location: verify.html');
//     exit;
// }
// $sessionLifetime = 86400;
// if (isset($_SESSION['verified_time']) && (time() - $_SESSION['verified_time'] > $sessionLifetime)) {
//     session_destroy();
//     header('Location: verify.html');
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OsintX.it - Professional OSINT Platform</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <meta property="og:type" content="website">
  <meta property="og:url" content="https://osintx.it/">
  <meta property="og:title" content="OsintX - OSINT & API Provider">
  <meta property="og:description" content="Professional OSINT Intelligence Platform - Access comprehensive breach intelligence through our unified API. +0 Intelligence Sources, zero data retention, lightning-fast results for security professionals worldwide.">
  
  <meta name="twitter:card" content="summary">
  <meta name="twitter:url" content="https://osintx.it/">
  <meta name="twitter:title" content="OsintX - OSINT & API Provider">
  <meta name="twitter:description" content="Professional OSINT Intelligence Platform - Access comprehensive breach intelligence through our unified API. +0 Intelligence Sources, zero data retention, lightning-fast results for security professionals worldwide.">
  
  <meta name="theme-color" content="#000000">
  
  <style>
    :root {
      --bg: #000000;
      --bg-card: #0a0a0a;
      --text: #ffffff;
      --text-muted: #888888;
      --border: rgba(255,255,255,0.08);
      --glow: rgba(255,255,255,0.15);
      --accent: #ffffff;
    }

    * { 
      margin: 0; 
      padding: 0; 
      box-sizing: border-box; 
    }
    
    html { 
      scroll-behavior: smooth; 
    }
    
    body { 
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', sans-serif;
      background: var(--bg);
      color: var(--text);
      overflow-x: hidden;
      line-height: 1.6;
    }

    /* ============================================
       SCROLL ANIMATIONS - stile stolen.tax
    ============================================ */

    /* Stato iniziale: invisibile e spostato in basso */
    .animate-on-scroll {
      opacity: 0;
      transform: translateY(32px);
      transition: opacity 0.6s ease, transform 0.6s ease;
    }

    /* Quando diventa visibile */
    .animate-on-scroll.visible {
      opacity: 1;
      transform: translateY(0);
    }

    /* Variante: entra da sinistra */
    .animate-from-left {
      opacity: 0;
      transform: translateX(-32px);
      transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .animate-from-left.visible {
      opacity: 1;
      transform: translateX(0);
    }

    /* Variante: entra da destra (es. privacy-card hover slideX) */
    .animate-from-right {
      opacity: 0;
      transform: translateX(32px);
      transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .animate-from-right.visible {
      opacity: 1;
      transform: translateX(0);
    }

    /* Scale-in per le stat cards */
    .animate-scale {
      opacity: 0;
      transform: scale(0.9);
      transition: opacity 0.5s ease, transform 0.5s ease;
    }
    .animate-scale.visible {
      opacity: 1;
      transform: scale(1);
    }

    /* Hero content: appare con leggero salto dal basso */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(24px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Hero animazioni iniziali (non scroll, subito al caricamento) */
    .hero-animate {
      animation: fadeInUp 0.7s ease forwards;
      opacity: 0;
    }
    .hero-animate:nth-child(1) { animation-delay: 0.1s; }
    .hero-animate:nth-child(2) { animation-delay: 0.25s; }
    .hero-animate:nth-child(3) { animation-delay: 0.4s; }
    .hero-animate:nth-child(4) { animation-delay: 0.55s; }

    /* ============================================
       DECORATIVE GRADIENTS
    ============================================ */
    .bg-gradients {
      position: fixed;
      inset: 0;
      pointer-events: none;
      z-index: 0;
      overflow: hidden;
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

    /* Header */
    header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      background: rgba(0, 0, 0, 0.85);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border);
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 40px;
    }

    .header-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 18px 0;
      height: 80px;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 14px;
      text-decoration: none;
      color: white;
      transition: opacity 0.2s;
    }

    .logo:hover { opacity: 0.8; }

    .logo img {
      width: 48px;
      height: 48px;
      filter: drop-shadow(0 0 12px rgba(255, 255, 255, 0.3));
    }

    .logo-text {
      font-size: 1.6rem;
      font-weight: 700;
      letter-spacing: -0.5px;
    }

    .logo-sub {
      font-size: 0.8rem;
      color: #666;
      margin-left: 2px;
    }

    .header-nav {
      display: flex;
      align-items: center;
      gap: 36px;
    }

    .nav-link {
      color: var(--text-muted);
      text-decoration: none;
      font-weight: 500;
      font-size: 0.95rem;
      transition: color 0.2s;
      position: relative;
    }

    .nav-link:hover { color: var(--text); }

    .btn-primary {
      background: white;
      color: black;
      padding: 11px 24px;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s;
      font-size: 0.95rem;
      border: none;
      cursor: pointer;
      box-shadow: 0 4px 16px rgba(255, 255, 255, 0.15);
    }

    .btn-primary:hover {
      background: #f0f0f0;
      transform: translateY(-2px);
      box-shadow: 0 6px 24px rgba(255, 255, 255, 0.25);
    }

    .btn-secondary {
      background: rgba(255,255,255,0.05);
      border: 1px solid var(--border);
      color: white;
      padding: 13px 28px;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s;
      font-size: 0.95rem;
      cursor: pointer;
    }

    .btn-secondary:hover {
      background: rgba(255,255,255,0.1);
      border-color: rgba(255,255,255,0.2);
      transform: translateY(-1px);
    }

    .btn-lg {
      padding: 16px 36px;
      font-size: 1rem;
      border-radius: 8px;
    }

    /* Main */
    main {
      padding-top: 80px;
      position: relative;
      z-index: 1;
    }

    /* Hero */
    .hero-new {
      min-height: calc(100vh - 80px);
      display: flex;
      align-items: center;
      padding: 80px 0;
    }

    .hero-grid {
      display: grid;
      grid-template-columns: 1fr 450px;
      gap: 80px;
      align-items: center;
    }

    .hero-content {
      max-width: 700px;
    }

    .platform-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(255,255,255,0.05);
      border: 1px solid var(--border);
      padding: 8px 18px;
      border-radius: 20px;
      font-size: 0.8rem;
      margin-bottom: 32px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--text-muted);
    }

    .hero-title {
      font-size: 4rem;
      font-weight: 900;
      line-height: 1.1;
      margin-bottom: 24px;
      letter-spacing: -2px;
    }

    .hero-title-line { display: block; }

    .title-accent {
      color: var(--accent);
      position: relative;
      display: inline-block;
    }

    .hero-subtitle {
      font-size: 1.15rem;
      color: var(--text-muted);
      line-height: 1.7;
      margin-bottom: 40px;
      max-width: 600px;
    }

    .hero-cta {
      display: flex;
      gap: 16px;
      align-items: center;
    }

    /* Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .stat-card {
      background: rgba(255,255,255,0.03);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 28px 24px;
      transition: all 0.3s;
      backdrop-filter: blur(10px);
    }

    .stat-card:hover {
      background: rgba(255,255,255,0.06);
      border-color: rgba(255,255,255,0.15);
      transform: translateY(-4px);
    }

    .stat-label {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: #505663;
      margin-bottom: 12px;
      font-weight: 600;
    }

    .stat-icon {
      font-size: 0.85rem;
      color: #adadad;
    }

    .stat-value {
      font-size: 2.5rem;
      font-weight: 900;
      color: white;
      letter-spacing: -1px;
      margin-bottom: 8px;
    }

    .stat-desc {
      font-size: 0.85rem;
      color: var(--text-muted);
      line-height: 1.4;
    }

    .stat-card-full {
      grid-column: 1 / -1;
      padding: 24px;
    }

    .uptime-bar {
      width: 100%;
      height: 6px;
      background: rgba(255,255,255,0.1);
      border-radius: 10px;
      overflow: hidden;
      margin-top: 12px;
      position: relative;
    }

    .uptime-fill {
      height: 100%;
      background: linear-gradient(90deg, #ffffff 0%, #dddddd 100%);
      width: 0%;
      border-radius: 10px;
      box-shadow: 0 0 12px rgba(255,255,255,0.4);
      transition: width 1.2s ease 0.3s;
    }

    /* Quando la stat card uptime diventa visibile, anima la barra */
    .stat-card-full.visible .uptime-fill {
      width: 99%;
    }

    .uptime-value {
      position: absolute;
      right: 12px;
      top: -28px;
      font-size: 1.1rem;
      font-weight: 700;
      color: white;
    }

    /* Features */
    .features {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
      gap: 28px;
      margin: 180px 0;
    }

    .card {
      background: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.01) 100%);
      backdrop-filter: blur(12px);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 40px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    }

    .card:hover {
      background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.03) 100%);
      border-color: rgba(255,255,255,0.2);
      transform: translateY(-6px);
      box-shadow: 0 12px 40px rgba(255,255,255,0.1);
    }

    .card-icon {
      width: 56px;
      height: 56px;
      background: rgba(255,255,255,0.08);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 24px;
      font-size: 1.6rem;
      transition: all 0.3s;
    }

    .card:hover .card-icon {
      background: rgba(255,255,255,0.12);
      transform: scale(1.1);
    }

    .card h3 { margin-bottom: 14px; font-size: 1.35rem; }
    .card p { color: var(--text-muted); font-size: 0.98rem; line-height: 1.75; }

    h2 {
      font-size: 2.8rem;
      font-weight: 900;
      line-height: 1.2;
      letter-spacing: -0.8px;
    }

    h3 { font-size: 1.3rem; font-weight: 700; }

    /* Partners */
    .partners-section {
      margin: 180px 0;
      text-align: center;
    }

    .partners-section > div:first-child { margin-bottom: 70px; }
    .partners-section p { font-size: 1.2rem; color: var(--text-muted); max-width: 800px; margin: 20px auto 0; }

    .partners-carousel {
      overflow: hidden;
      position: relative;
      padding: 50px 0;
      max-width: 900px;
      margin: 0 auto;
      mask-image: linear-gradient(to right, transparent 0%, black 10%, black 90%, transparent 100%);
      -webkit-mask-image: linear-gradient(to right, transparent 0%, black 10%, black 90%, transparent 100%);
    }

    .partners-track {
      display: flex;
      gap: 48px;
      width: max-content;
      animation: partners-scroll 30s linear infinite;
    }

    @keyframes partners-scroll {
      from { transform: translateX(0); }
      to { transform: translateX(-50%); }
    }

    .partner-badge {
      background: rgba(255,255,255,0.05);
      border: 1px solid var(--border);
      padding: 16px 42px;
      border-radius: 50px;
      font-size: 1.05rem;
      font-weight: 600;
      color: #ffffff;
      white-space: nowrap;
      flex-shrink: 0;
      transition: all 0.3s ease;
      backdrop-filter: blur(12px);
      text-decoration: none;
    }

    .partner-badge:hover {
      background: rgba(255,255,255,0.1);
      border-color: rgba(255,255,255,0.25);
      transform: translateY(-3px);
      box-shadow: 0 8px 24px rgba(255,255,255,0.12);
    }

    /* Privacy */
    .privacy-section { margin: 180px 0; }

    .privacy-header { text-align: center; margin-bottom: 80px; }
    .privacy-header p { font-size: 1.2rem; color: var(--text-muted); max-width: 800px; margin: 20px auto 0; }

    .privacy-cards {
      display: flex;
      flex-direction: column;
      gap: 24px;
      max-width: 1000px;
      margin: 0 auto;
    }

    .privacy-card {
      background: linear-gradient(135deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.01) 100%);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 40px;
      transition: all 0.3s;
      display: flex;
      gap: 28px;
      backdrop-filter: blur(12px);
    }

    .privacy-card:hover {
      background: linear-gradient(135deg, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0.02) 100%);
      border-color: rgba(255,255,255,0.2);
      transform: translateX(6px);
    }

    .privacy-icon {
      width: 56px;
      height: 56px;
      min-width: 56px;
      background: rgba(255,255,255,0.12);
      border: 1px solid rgba(255,255,255,0.25);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.6rem;
      color: white;
    }

    .privacy-content h3 { margin-bottom: 14px; font-size: 1.35rem; }
    .privacy-content p { color: var(--text-muted); font-size: 0.98rem; line-height: 1.75; }

    /* Testimonials */
    .testimonials-section { margin: 180px 0; overflow: hidden; }

    .testimonials-carousel { overflow: hidden; padding: 30px 0; }

    .carousel-track {
      display: flex;
      gap: 36px;
      animation: scroll-testimonials 25s linear infinite;
      width: max-content;
    }

    .carousel-track:hover { animation-play-state: paused; }

    @keyframes scroll-testimonials {
      from { transform: translateX(0); }
      to { transform: translateX(-50%); }
    }

    .testimonial-card {
      background: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.01) 100%);
      backdrop-filter: blur(12px);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 36px;
      min-width: 420px;
      flex: 0 0 auto;
      transition: all 0.3s ease;
    }

    .testimonial-card:hover {
      background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.03) 100%);
      border-color: rgba(255,255,255,0.25);
      transform: translateY(-8px);
      box-shadow: 0 16px 48px rgba(255,255,255,0.12);
    }

    .testimonial-stars { color: #ffffff; margin-bottom: 20px; font-size: 1.25rem; letter-spacing: 4px; }
    .testimonial-text { font-size: 1.02rem; line-height: 1.85; margin-bottom: 32px; white-space: normal; color: #cccccc; }

    .testimonial-author { display: flex; align-items: center; gap: 16px; }
    .author-name { font-weight: 600; font-size: 1.05rem; }
    .author-role { font-size: 0.9rem; color: var(--text-muted); }

    /* FAQ */
    .faq-section { margin: 180px 0; }

    .faq-header { text-align: center; margin-bottom: 80px; }
    .faq-header p { font-size: 1.2rem; color: var(--text-muted); margin-top: 16px; }

    .faq-list {
      max-width: 1000px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .faq-item {
      background: rgba(255,255,255,0.02);
      border: 1px solid var(--border);
      border-radius: 12px;
      overflow: hidden;
      transition: all 0.3s;
      backdrop-filter: blur(10px);
    }

    .faq-item.active {
      background: rgba(255,255,255,0.06);
      border-color: rgba(255,255,255,0.25);
    }

    .faq-question {
      padding: 26px 36px;
      font-size: 1.08rem;
      font-weight: 600;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 20px;
      cursor: pointer;
      user-select: none;
      transition: all 0.2s;
    }

    .faq-item:hover .faq-question { color: white; }

    .faq-answer {
      padding: 0 36px 28px 36px;
      color: var(--text-muted);
      font-size: 0.98rem;
      line-height: 1.8;
      display: none;
    }

    .faq-item.active .faq-answer { display: block; }

    .faq-icon {
      color: #666;
      font-size: 1.25rem;
      transition: transform 0.3s;
    }

    .faq-item.active .faq-icon { transform: rotate(180deg); color: var(--text); }

    /* CTA */
    .cta {
      background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.02) 100%);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 90px 40px;
      text-align: center;
      margin-top: 180px;
      backdrop-filter: blur(12px);
      box-shadow: 0 20px 60px rgba(255,255,255,0.05);
    }

    .cta h2 { margin-bottom: 20px; }
    .cta p { font-size: 1.2rem; color: var(--text-muted); max-width: 700px; margin: 0 auto 48px; }

    /* Footer */
    footer {
      border-top: 1px solid var(--border);
      padding: 70px 0;
      position: relative;
      z-index: 10;
      margin-top: 120px;
      background: rgba(0,0,0,0.5);
      backdrop-filter: blur(10px);
    }

    .footer-inner {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      gap: 40px;
    }

    @media (min-width: 768px) { .footer-inner { flex-direction: row; } }

    .footer-links {
      display: flex;
      align-items: center;
      gap: 0;
    }

    .footer-links a {
      color: var(--text-muted);
      text-decoration: none;
      padding: 0 20px;
      font-size: 0.95rem;
      transition: color 0.2s;
      border-right: 1px solid var(--border);
    }

    .footer-links a:last-child { border-right: none; }
    .footer-links a:hover { color: white; }
    .footer-copyright { color: #555; font-size: 0.9rem; }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 10px; }
    ::-webkit-scrollbar-track { background: #000; }
    ::-webkit-scrollbar-thumb { background: #1a1a1a; border-radius: 5px; }
    ::-webkit-scrollbar-thumb:hover { background: #2a2a2a; }

    /* Responsive */
    @media (max-width: 968px) {
      .hero-grid { grid-template-columns: 1fr; gap: 60px; }
      .stats-grid { max-width: 500px; margin: 0 auto; }
      .hero-title { font-size: 3.5rem; }
    }

    @media (max-width: 768px) {
      .hero-title { font-size: 2.8rem; }
      h2 { font-size: 2rem; }
      .container { padding: 0 24px; }
      .header-nav { gap: 20px; }
      .hero-cta { flex-direction: column; align-items: stretch; }
      .features { grid-template-columns: 1fr; }
      .privacy-card { flex-direction: column; }
      .testimonial-card { min-width: 340px; }
      .stats-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <div class="bg-gradients">
    <div class="gradient-blob"></div>
    <div class="gradient-blob"></div>
    <div class="gradient-blob"></div>
  </div>

  <header>
    <div class="container">
      <div class="header-inner">
        <a href="index.php" class="logo">
          <img src="./images/732374ad-e8c2-4b4e-9af8-eb8364b297ae-removebg-preview.png" alt="OsintX">
          <div>
            <span class="logo-text">OsintX<span class="logo-sub">.it</span></span>
          </div>
        </a>
        <div class="header-nav">
          <a href="./pages/update.html" class="nav-link">Updates</a>
          <a href="./pages/tos.html" class="nav-link">Terms</a>
          <a href="./pages/shop.html" class="nav-link">Pricing</a>
          <a href="./pages/privacy.html" class="nav-link">Privacy</a>
          <a href="./pages/contact.html" class="nav-link">Contact</a>
          <a href="login.php" class="btn-primary">Dashboard</a>
        </div>
      </div>
    </div>
  </header>

  <main class="container">

    <!-- Hero: animazione al caricamento con fadeInUp scalato -->
    <section class="hero-new">
      <div class="hero-grid">
        <div class="hero-content">
          <div class="platform-badge hero-animate">
            <span>OSINT PLATFORM</span>
          </div>
          
          <h1 class="hero-title hero-animate">
            <span class="hero-title-line">STEALTH ACCESS</span>
            <span class="hero-title-line title-accent">TO 450B+ DATA</span>
            <span class="hero-title-line">NO LOGS , NO TRACES</span>
          </h1>

          <p class="hero-subtitle hero-animate">
            Uncover digital intelligence through the most comprehensive OSINT platform with access to 450B+ records from premium sources.
          </p>

          <div class="hero-cta hero-animate">
            <a href="./pages/shop.html" class="btn-primary btn-lg">BUY NOW</a>
            <a href="login.php" class="btn-secondary btn-lg">DASHBOARD</a>
          </div>
        </div>

        <!-- Stats: appaiono in sequenza con scroll -->
        <div class="stats-grid">
          <div class="stat-card animate-scale">
            <div class="stat-label">
              <i class="fas fa-broadcast-tower stat-icon"></i>
              <span>SOURCES</span>
            </div>
            <div class="stat-value">39+</div>
            <div class="stat-desc">Active Intelligence APIs</div>
          </div>

          <div class="stat-card animate-scale">
            <div class="stat-label">
              <i class="fas fa-database stat-icon"></i>
              <span>RECORDS</span>
            </div>
            <div class="stat-value">450B+</div>
            <div class="stat-desc">Searchable Data Points</div>
          </div>

          <div class="stat-card animate-scale">
            <div class="stat-label">
              <i class="fas fa-shield-alt stat-icon"></i>
              <span>PRIVACY</span>
            </div>
            <div class="stat-value">LOGS</div>
            <div class="stat-desc">User Agents & IPs Only</div>
          </div>

          <div class="stat-card animate-scale">
            <div class="stat-label">
              <i class="fas fa-bolt stat-icon"></i>
              <span>SPEED</span>
            </div>
            <div class="stat-value">&lt;7s</div>
            <div class="stat-desc">Average Response</div>
          </div>

          <div class="stat-card stat-card-full animate-scale">
            <div class="stat-label">
              <span>UPTIME</span>
              <span class="uptime-value"></span>
            </div>
            <div class="uptime-bar">
              <div class="uptime-fill"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features: ogni card appare con delay scalato -->
    <section class="features">
      <div class="card animate-on-scroll">
        <div class="card-icon"><i class="fas fa-bolt"></i></div>
        <h3>Lightning Fast</h3>
        <p>Sub-second response times with 99.9% uptime. Built on enterprise infrastructure for maximum reliability and performance.</p>
      </div>
      <div class="card animate-on-scroll">
        <div class="card-icon"><i class="fas fa-shield-alt"></i></div>
        <h3>Minimal Logging</h3>
        <p>Your searches are private. Minimal logging, minimal tracking. All queries processed in real-time with maximum privacy.</p>
      </div>
      <div class="card animate-on-scroll">
        <div class="card-icon"><i class="fas fa-database"></i></div>
        <h3>39+ Sources</h3>
        <p>Access comprehensive intelligence from premium sources including LeakCheck, Snusbase, BreachBase, IntelVault and more.</p>
      </div>
      <div class="card animate-on-scroll">
        <div class="card-icon"><i class="fas fa-code"></i></div>
        <h3>Simple API</h3>
        <p>Complete documentation with cURL examples and integration guides. Start querying in under 5 minutes.</p>
      </div>
      <div class="card animate-on-scroll">
        <div class="card-icon"><i class="fas fa-clock"></i></div>
        <h3>Real-time Data</h3>
        <p>Access the latest breach intelligence with regular database updates from verified sources worldwide.</p>
      </div>
      <div class="card animate-on-scroll">
        <div class="card-icon"><i class="fas fa-gem"></i></div>
        <h3>Simple Pricing</h3>
        <p>€6,99/month or €15/month or €50/lifetime for unlimited access. No hidden costs, no per-query fees. Cancel anytime.</p>
      </div>
    </section>

    <!-- Partners: titolo con scroll, carousel sempre visibile -->
    <section class="partners-section">
      <div class="animate-on-scroll">
        <h2>OsintX Platforms</h2>
        <p>OsintX uses multiple platforms worldwide.</p>
      </div>

      <div class="partners-carousel">
        <div class="partners-track">
          <a href="https://leakcheck.io" class="partner-badge">LeakCheck</a>
          <a href="https://hackcheck.io" class="partner-badge">HackCheck</a>
          <a href="" class="partner-badge">KeyScore</a>
          <a href="https://snusbase.org" class="partner-badge">Snusbase</a>
          <a href="https://breachbase.com/" class="partner-badge">BreachBase</a>
          <a href="https://intelvault.it" class="partner-badge">IntelVault</a>
          <a href="https://breach.vip" class="partner-badge">BreachVIP</a>
          <a href="" class="partner-badge">Rutify</a>
          <a href="" class="partner-badge">Akula</a>
          <a href="" class="partner-badge">LeakSight</a>
          <a href="" class="partner-badge">Room101</a>
          <a href="" class="partner-badge">OathNet</a>
          <a href="" class="partner-badge">SEON</a>
          <a href="" class="partner-badge">Shodan</a>
          <a href="" class="partner-badge">Genesis</a>
          <a href="" class="partner-badge">OsintDog</a>
          <!-- duplicati per loop -->
          <a href="https://leakcheck.io" class="partner-badge">LeakCheck</a>
          <a href="https://hackcheck.io" class="partner-badge">HackCheck</a>
          <a href="" class="partner-badge">KeyScore</a>
          <a href="https://snusbase.org" class="partner-badge">Snusbase</a>
          <a href="https://breachbase.com/" class="partner-badge">BreachBase</a>
          <a href="https://intelvault.it" class="partner-badge">IntelVault</a>
          <a href="https://breach.vip" class="partner-badge">BreachVIP</a>
          <a href="" class="partner-badge">Rutify</a>
          <a href="" class="partner-badge">Akula</a>
          <a href="" class="partner-badge">LeakSight</a>
          <a href="" class="partner-badge">Room101</a>
          <a href="" class="partner-badge">OathNet</a>
          <a href="" class="partner-badge">SEON</a>
          <a href="" class="partner-badge">Shodan</a>
          <a href="" class="partner-badge">Genesis</a>
          <a href="" class="partner-badge">OsintDog</a>
        </div>
      </div>
    </section>

    <!-- Privacy: titolo + cards con scroll -->
    <section class="privacy-section">
      <div class="privacy-header animate-on-scroll">
        <h2>Concerned About Your Privacy?</h2>
        <p>We understand privacy matters. Here's what you need to know about data removal and protecting yourself online.</p>
      </div>
      <div class="privacy-cards">
        <div class="privacy-card animate-from-left">
          <div class="privacy-icon">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="privacy-content">
            <h3>Found Your Data?</h3>
            <p>If you found your personal information in our search results, don't panic. Here are some general tips to protect yourself: reset your passwords regularly, use two-factor authentication, change your IP address, and update your usernames. These steps can help enhance your online security and privacy.</p>
          </div>
        </div>
        <div class="privacy-card animate-from-right">
          <div class="privacy-icon info">
            <i class="fas fa-user-shield"></i>
          </div>
          <div class="privacy-content">
            <h3>Your Privacy Matters</h3>
            <p>We encrypt all searches and never sell your data, ensuring your investigative privacy. Our searches only use publicly available information and previously leaked data to help you understand your digital footprint.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials: titolo con scroll, carousel sempre attivo -->
    <section class="testimonials-section">
      <div class="container">
        <div class="animate-on-scroll" style="text-align:center; margin-bottom:70px;">
          <h2>OsintX Users</h2>
          <p style="font-size:1.2rem; color:var(--text-muted); max-width:800px; margin:20px auto 0;">
            Feedback, reviews, and insights from people who rely on the platform every day for investigations, analysis, and intelligence work.
          </p>
        </div>

        <div class="testimonials-carousel">
          <div class="carousel-track">
            <div class="testimonial-card">
              <div class="testimonial-stars">★★★★★</div>
              <p class="testimonial-text">"<br><br>"</p>
              <div class="testimonial-author">
                <div>
                  <div class="author-name">Anonymous</div>
                  <div class="author-role">Verified Member</div>
                </div>
              </div>
            </div>
            <div class="testimonial-card">
              <div class="testimonial-stars">★★★★★</div>
              <p class="testimonial-text">"<br><br>"</p>
              <div class="testimonial-author">
                <div>
                  <div class="author-name">Anonymous</div>
                  <div class="author-role">Verified Member</div>
                </div>
              </div>
            </div>
            <div class="testimonial-card">
              <div class="testimonial-stars">★★★★★</div>
              <p class="testimonial-text">"<br><br>"</p>
              <div class="testimonial-author">
                <div>
                  <div class="author-name">Anonymous</div>
                  <div class="author-role">Verified Member</div>
                </div>
              </div>
            </div>
            <div class="testimonial-card">
              <div class="testimonial-stars">★★★★★</div>
              <p class="testimonial-text">""</p>
              <div class="testimonial-author">
                <div>
                  <div class="author-name">Anonymous</div>
                  <div class="author-role">Verified Member</div>
                </div>
              </div>
            </div>
            <div class="testimonial-card">
              <div class="testimonial-stars">★★★★★</div>
              <p class="testimonial-text">""</p>
              <div class="testimonial-author">
                <div>
                  <div class="author-name">Anonymous</div>
                  <div class="author-role">Verified Member</div>
                </div>
              </div>
            </div>
            <div class="testimonial-card">
              <div class="testimonial-stars">★★★★★</div>
              <p class="testimonial-text">""</p>
              <div class="testimonial-author">
                <div>
                  <div class="author-name">Anonymous</div>
                  <div class="author-role">Verified Member</div>
                </div>
              </div>
            </div>
            <div class="testimonial-card">
              <div class="testimonial-stars">★★★★★</div>
              <p class="testimonial-text">"<br><br>"</p>
              <div class="testimonial-author">
                <div>
                  <div class="author-name">Anonymous</div>
                  <div class="author-role">Verified Member</div>
                </div>
              </div>
            </div>
            <!-- duplicati per loop infinito -->
            <div class="testimonial-card">
              <div class="testimonial-stars">★★★★★</div>
              <p class="testimonial-text">""</p>
              <div class="testimonial-author">
                <div>
                  <div class="author-name">Anonymous</div>
                  <div class="author-role">Verified Member</div>
                </div>
              </div>
            </div>
            <div class="testimonial-card">
              <div class="testimonial-stars">★★★★★</div>
              <p class="testimonial-text">""</p>
              <div class="testimonial-author">
                <div>
                  <div class="author-name">Anonymous</div>
                  <div class="author-role">Verified Member</div>
                </div>
              </div>
            </div>
            <div class="testimonial-card">
              <div class="testimonial-stars">★★★★★</div>
              <p class="testimonial-text">""</p>
              <div class="testimonial-author">
                <div>
                  <div class="author-name">Anonymous</div>
                  <div class="author-role">Verified Member</div>
                </div>
              </div>
            </div>
            <div class="testimonial-card">
              <div class="testimonial-stars">★★★★★</div>
              <p class="testimonial-text">""</p>
              <div class="testimonial-author">
                <div>
                  <div class="author-name">Anonymous</div>
                  <div class="author-role">Verified Member</div>
                </div>
              </div>
            </div>
            <div class="testimonial-card">
              <div class="testimonial-stars">★★★★★</div>
              <p class="testimonial-text">""</p>
              <div class="testimonial-author">
                <div>
                  <div class="author-name">Anonymous</div>
                  <div class="author-role">Verified Member</div>
                </div>
              </div>
            </div>
            <div class="testimonial-card">
              <div class="testimonial-stars">★★★★★</div>
              <p class="testimonial-text">""</p>
              <div class="testimonial-author">
                <div>
                  <div class="author-name">Anonymous</div>
                  <div class="author-role">Verified Member</div>
                </div>
              </div>
            </div>
            <div class="testimonial-card">
              <div class="testimonial-stars">★★★★★</div>
              <p class="testimonial-text">""</p>
              <div class="testimonial-author">
                <div>
                  <div class="author-name">Anonymous</div>
                  <div class="author-role">Verified Member</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ: ogni item appare con scroll -->
    <section class="faq-section">
      <div class="faq-header animate-on-scroll">
        <h2>Frequently Asked Questions</h2>
        <p>Everything you need to know about our search platform</p>
      </div>
      <div class="faq-list">
        <div class="faq-item animate-on-scroll">
          <div class="faq-question">
            <span>How does OsintX work?</span>
            <span class="faq-icon"><i class="fas fa-chevron-right"></i></span>
          </div>
          <div class="faq-answer">
            OsintX is powered by a centralized API that queries over 39 intelligence sources at the same time. Each search compiles the most relevant data into one unified result.
          </div>
        </div>
        <div class="faq-item animate-on-scroll">
          <div class="faq-question">
            <span>Are my data safe?</span>
            <span class="faq-icon"><i class="fas fa-chevron-right"></i></span>
          </div>
          <div class="faq-answer">
            Yes. All communications are encrypted and protected, and we follow strict security standards to ensure user privacy.
          </div>
        </div>
        <div class="faq-item animate-on-scroll">
          <div class="faq-question">
            <span>Can I change my plan at any time?</span>
            <span class="faq-icon"><i class="fas fa-chevron-right"></i></span>
          </div>
          <div class="faq-answer">
            Yes. You can upgrade or switch plans whenever you want, and changes take effect instantly.
          </div>
        </div>
        <div class="faq-item animate-on-scroll">
          <div class="faq-question">
            <span>What is API access?</span>
            <span class="faq-icon"><i class="fas fa-chevron-right"></i></span>
          </div>
          <div class="faq-answer">
            API access allows you to integrate OsintX directly into your own tools, scripts, or applications with the same performance and data sources as our platform.
          </div>
        </div>
        <div class="faq-item animate-on-scroll">
          <div class="faq-question">
            <span>How can I get help?</span>
            <span class="faq-icon"><i class="fas fa-chevron-right"></i></span>
          </div>
          <div class="faq-answer">
            Our team is available 24/7 through Discord and email, with priority support for Premium users.
          </div>
        </div>
      </div>
    </section>

    <!-- CTA finale: appare con scroll -->
    <section class="cta animate-on-scroll">
      <h2>Ready to get started?</h2>
      <p>Join security professionals, researchers, and teams worldwide using OsintX for comprehensive OSINT intelligence.</p>
      <a href="./pages/shop.html" class="btn-primary btn-lg">View Plans <i class="fas fa-external-link-alt"></i></a>
    </section>

  </main>

  <footer>
    <div class="container">
      <div class="footer-inner">
        <a href="index.php" class="logo">
          <img src="./images/732374ad-e8c2-4b4e-9af8-eb8364b297ae-removebg-preview.png" alt="OsintX">
          <div>
            <span class="logo-text">OsintX<span class="logo-sub">.it</span></span>
          </div>
        </a>
        <div class="footer-links">
          <a href="./pages/privacy.html"> Privacy </a>
          <a href="./pages/tos.html"> Terms </a>
          <a href="./pages/update.html"> Update </a>
          <span class="footer-copyright">⠀⠀© 2026 OsintX.it</span>
        </div>
      </div>
    </div>
  </footer>

  <script>
    // ============================================
    // FAQ toggle
    // ============================================
    document.querySelectorAll('.faq-item .faq-question').forEach(q => {
      q.addEventListener('click', () => q.parentElement.classList.toggle('active'));
    });

    // ============================================
    // SCROLL ANIMATIONS - stile stolen.tax
    // IntersectionObserver con delay scalato per ogni gruppo
    // ============================================
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
          // Calcola delay in base alla posizione dell'elemento nel DOM
          // tra gli elementi animate-on-scroll visibili
          const siblings = Array.from(
            entry.target.parentElement.querySelectorAll(
              '.animate-on-scroll, .animate-from-left, .animate-from-right, .animate-scale'
            )
          );
          const position = siblings.indexOf(entry.target);
          const delay = position >= 0 ? position * 50 : 0;

          setTimeout(() => {
            entry.target.classList.add('visible');
          }, delay);

          // Smette di osservare dopo che è apparso
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    // Osserva tutti gli elementi animabili
    document.querySelectorAll(
      '.animate-on-scroll, .animate-from-left, .animate-from-right, .animate-scale'
    ).forEach(el => observer.observe(el));
  </script>

</body>
</html>