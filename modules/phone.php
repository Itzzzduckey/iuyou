<?php
require_once __DIR__ . '/../includes/auth.php';
$canSearch = canSearch();
?>
<div class="module-card">
    <h2 style="font-size: 1.4rem; margin-bottom: 8px;">
        <i class="fas fa-phone"></i> Phone Lookup
    </h2>
    <p style="color: #888; margin-bottom: 24px;">Search and analyze phone using OsintDog API</p>
    
    <form class="dashboard-search-form" data-module="phone" method="POST" action="">
        <div class="form-group">
            <label class="form-label"><i class="fas fa-phone"></i> Enter phone</label>
            <input type="text" name="query" class="form-input" placeholder="+1234567890" required>
        </div>
        <button type="submit" class="btn-primary" <?php echo !$canSearch ? 'disabled' : ''; ?>>
            <i class="fas fa-search"></i> Search
        </button>
    </form>
</div>
<div id="results-phone" class="module-results"></div>
