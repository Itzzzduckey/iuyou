<?php
require_once __DIR__ . '/../includes/auth.php';
$canSearch = canSearch();
?>
<div class="module-card">
    <h2 style="font-size: 1.4rem; margin-bottom: 8px;">
        <i class="fab fa-bitcoin"></i> Crypto Wallet
    </h2>
    <p style="color: #888; margin-bottom: 24px;">Search cryptocurrency address</p>
    
    <form class="dashboard-search-form" data-module="crypto" method="POST" action="">
        <div class="form-group">
            <label class="form-label"><i class="fab fa-bitcoin"></i> Enter address</label>
            <input type="text" name="query" class="form-input" placeholder="1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa" required>
        </div>
        <button type="submit" class="btn-primary" <?php echo !$canSearch ? 'disabled' : ''; ?>>
            <i class="fas fa-search"></i> Search
        </button>
    </form>
</div>
<div id="results-crypto" class="module-results"></div>
