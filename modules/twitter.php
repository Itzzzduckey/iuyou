<?php
require_once __DIR__ . '/../includes/auth.php';
$canSearch = canSearch();
?>
<div class="module-card">
    <h2 style="font-size: 1.4rem; margin-bottom: 8px;">
        <i class="fab fa-twitter"></i> Twitter OSINT
    </h2>
    <p style="color: #888; margin-bottom: 24px;">Search and analyze username using OsintDog API</p>
    
    <form class="dashboard-search-form" data-module="twitter" method="POST" action="">
        <div class="form-group">
            <label class="form-label"><i class="fab fa-twitter"></i> Enter username</label>
            <input type="text" name="query" class="form-input" placeholder="elonmusk" required>
        </div>
        <button type="submit" class="btn-primary" <?php echo !$canSearch ? 'disabled' : ''; ?>>
            <i class="fas fa-search"></i> Search
        </button>
    </form>
</div>
<div id="results-twitter" class="module-results"></div>
