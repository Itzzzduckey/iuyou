<?php
require_once __DIR__ . '/../includes/auth.php';
$canSearch = canSearch();
?>
<div class="module-card">
    <h2 style="font-size: 1.4rem; margin-bottom: 8px;">
        <i class="fas fa-university"></i> IBAN Lookup
    </h2>
    <p style="color: #888; margin-bottom: 24px;">International Bank Account Number lookup</p>
    
    <form class="dashboard-search-form" data-module="iban" method="POST" action="">
        <div class="form-group">
            <label class="form-label"><i class="fas fa-university"></i> Enter IBAN</label>
            <input type="text" name="query" class="form-input" placeholder="GB82WEST12345698765432" required>
        </div>
        <button type="submit" class="btn-primary" <?php echo !$canSearch ? 'disabled' : ''; ?>>
            <i class="fas fa-search"></i> Search
        </button>
    </form>
</div>
<div id="results-iban" class="module-results"></div>
