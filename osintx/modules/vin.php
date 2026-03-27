<?php
require_once __DIR__ . '/../includes/auth.php';
$canSearch = canSearch();
?>
<div class="module-card">
    <h2 style="font-size: 1.4rem; margin-bottom: 8px;">
        <i class="fas fa-car"></i> VIN Lookup
    </h2>
    <p style="color: #888; margin-bottom: 24px;">Vehicle identification number lookup</p>
    
    <form class="dashboard-search-form" data-module="vin" method="POST" action="">
        <div class="form-group">
            <label class="form-label"><i class="fas fa-car"></i> Enter VIN</label>
            <input type="text" name="query" class="form-input" placeholder="17-character VIN" required>
        </div>
        <button type="submit" class="btn-primary" <?php echo !$canSearch ? 'disabled' : ''; ?>>
            <i class="fas fa-search"></i> Search
        </button>
    </form>
</div>
<div id="results-vin" class="module-results"></div>
