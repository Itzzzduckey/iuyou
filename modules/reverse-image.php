<?php
require_once __DIR__ . '/../includes/auth.php';
$canSearch = canSearch();
?>
<div class="module-card">
    <h2 style="font-size: 1.4rem; margin-bottom: 8px;">
        <i class="fas fa-image"></i> Reverse Image
    </h2>
    <p style="color: #888; margin-bottom: 24px;">Search by image URL</p>
    
    <form class="dashboard-search-form" data-module="reverse-image" method="POST" action="">
        <div class="form-group">
            <label class="form-label"><i class="fas fa-image"></i> Enter image URL</label>
            <input type="text" name="query" class="form-input" placeholder="https://example.com/image.jpg" required>
        </div>
        <button type="submit" class="btn-primary" <?php echo !$canSearch ? 'disabled' : ''; ?>>
            <i class="fas fa-search"></i> Search
        </button>
    </form>
</div>
<div id="results-reverse-image" class="module-results"></div>
