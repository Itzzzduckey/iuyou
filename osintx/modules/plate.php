<?php
require_once __DIR__ . '/../includes/auth.php';
$canSearch = canSearch();
?>
<div class="module-card">
    <h2 style="font-size: 1.4rem; margin-bottom: 8px;">
        <i class="fas fa-id-card"></i> License Plate
    </h2>
    <p style="color: #888; margin-bottom: 24px;">License plate lookup (e.g. Rutify Chilean data)</p>
    
    <form class="dashboard-search-form" data-module="plate" method="POST" action="">
        <div class="form-group">
            <label class="form-label"><i class="fas fa-id-card"></i> Enter plate</label>
            <input type="text" name="query" class="form-input" placeholder="ABCD12" required>
        </div>
        <button type="submit" class="btn-primary" <?php echo !$canSearch ? 'disabled' : ''; ?>>
            <i class="fas fa-search"></i> Search
        </button>
    </form>
</div>
<div id="results-plate" class="module-results"></div>
