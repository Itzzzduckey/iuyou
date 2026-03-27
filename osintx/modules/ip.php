<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/osintdog.php';
$canSearch = canSearch();
?>
<div class="module-card">
    <h2 style="font-size: 1.4rem; margin-bottom: 8px;">
        <i class="fas fa-globe"></i> IP Lookup
    </h2>
    <p style="color: #888; margin-bottom: 24px;">Search and analyze ip using OsintDog API</p>
    
    <form class="dashboard-search-form" data-module="ip" method="POST" action="" id="form-ip">
        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-globe"></i> Enter ip
            </label>
            <input 
                type="text" 
                name="query" 
                class="form-input" 
                placeholder="8.8.8.8"
                required
            >
        </div>
        
        <button type="submit" class="btn-primary" <?php echo !$canSearch ? 'disabled' : ''; ?>>
            <i class="fas fa-search"></i> Search
        </button>
    </form>
</div>
<div id="results-ip" class="module-results"></div>
