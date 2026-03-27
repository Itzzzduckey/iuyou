<?php
require_once __DIR__ . '/../includes/auth.php';
$canSearch = canSearch();
?>
<div class="module-card">
    <h2 style="font-size: 1.4rem; margin-bottom: 8px;">
        <i class="fas fa-building"></i> Company Search
    </h2>
    <p style="color: #888; margin-bottom: 24px;">Search company and enterprise data</p>
    
    <form class="dashboard-search-form" data-module="company" method="POST" action="">
        <div class="form-group">
            <label class="form-label"><i class="fas fa-building"></i> Enter company name</label>
            <input type="text" name="query" class="form-input" placeholder="company.com" required>
        </div>
        <button type="submit" class="btn-primary" <?php echo !$canSearch ? 'disabled' : ''; ?>>
            <i class="fas fa-search"></i> Search
        </button>
    </form>
</div>
<div id="results-company" class="module-results"></div>
