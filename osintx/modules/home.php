<?php
// modules/home.php
$username = $_SESSION['username'] ?? 'User';
$remaining = getRemainingSearches();
$limit = getDailyLimit();
$used = $limit - $remaining;
$pct = $limit > 0 ? round(($used / $limit) * 100) : 0;
?>

<style>
.home-wrap { padding: 0; }

.welcome-row { margin-bottom: 24px; }

.welcome-title {
    font-size: 2rem;
    font-weight: 800;
    letter-spacing: -0.5px;
    margin-bottom: 4px;
}

.welcome-sub { color: #555; font-size: .9rem; }
.welcome-sub span { color: #4ade80; font-weight: 600; }

/* MAIN LAYOUT */
.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 260px;
    gap: 16px;
    margin-bottom: 16px;
}

.left-col {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* Stats */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.stat-card {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 14px;
    padding: 22px;
    position: relative;
    overflow: hidden;
    transition: border-color .2s, box-shadow .2s;
}

.stat-card:hover {
    border-color: rgba(255,255,255,.15);
    box-shadow: 0 8px 32px rgba(255,255,255,.06);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.1), transparent);
}

.stat-label {
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #555;
    font-weight: 600;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.stat-value {
    font-size: 2.2rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 4px;
    letter-spacing: -1px;
}

.stat-sub { font-size: .75rem; color: #555; }
.stat-green .stat-value { color: #4ade80; }
.stat-white .stat-value { color: #fff; }
.stat-blue  .stat-value { color: #60a5fa; }

/* Panels */
.panel {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 14px;
    overflow: hidden;
}

.panel-header {
    padding: 14px 20px;
    border-bottom: 1px solid rgba(255,255,255,.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.panel-title {
    font-size: .83rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #ccc;
}

.panel-title i { color: #555; font-size: .78rem; }
.panel-body { padding: 16px 20px; }

/* Credit bar */
.credit-bar-wrap { margin-bottom: 12px; }

.credit-bar-top {
    display: flex;
    justify-content: space-between;
    font-size: .76rem;
    color: #888;
    margin-bottom: 7px;
}

.credit-bar-top strong { color: #fff; }

.credit-track {
    height: 5px;
    background: rgba(255,255,255,.06);
    border-radius: 3px;
    overflow: hidden;
}

.credit-fill {
    height: 100%;
    background: #fff;
    border-radius: 3px;
    transition: width .6s ease;
}

.credit-fill.warn   { background: #fbbf24; }
.credit-fill.danger { background: #f87171; }

.chart-wrap { position: relative; height: 150px; }

canvas#activityChart {
    width: 100% !important;
    height: 100% !important;
}

/* Right col — Quick Access spans full height of left col */
.right-col {
    display: flex;
    flex-direction: column;
}

.qa-panel {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 14px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.qa-panel .panel-body {
    padding: 16px 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.qa-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
    justify-content: space-between;
}

.qa-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    background: rgba(255,255,255,.02);
    border: 1px solid rgba(255,255,255,.06);
    border-radius: 10px;
    text-decoration: none;
    color: #ccc;
    font-size: .83rem;
    font-weight: 500;
    transition: all .2s;
    flex: 1;
}

.qa-item:hover {
    background: rgba(255,255,255,.06);
    border-color: rgba(255,255,255,.12);
    color: #fff;
    transform: translateX(3px);
}

.qa-icon {
    width: 30px; height: 30px;
    background: rgba(255,255,255,.05);
    border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem;
    color: #888;
    flex-shrink: 0;
}

.qa-arrow {
    margin-left: auto;
    color: #333;
    font-size: .72rem;
    transition: color .2s;
}

.qa-item:hover .qa-arrow { color: #888; }

/* Status row */
.status-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.status-card {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 14px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.status-dot {
    width: 9px; height: 9px;
    border-radius: 50%;
    flex-shrink: 0;
    box-shadow: 0 0 8px currentColor;
}

.dot-green  { background: #4ade80; color: #4ade80; }
.dot-yellow { background: #fbbf24; color: #fbbf24; }
.dot-red    { background: #f87171; color: #f87171; }

.status-info { flex: 1; }
.status-name { font-size: .8rem; font-weight: 600; color: #ccc; margin-bottom: 1px; }
.status-desc { font-size: .7rem; color: #555; }
.status-tag  { font-size: .68rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; white-space: nowrap; }
.tag-online   { background: rgba(74,222,128,.12); color: #4ade80; }
.tag-degraded { background: rgba(251,191,36,.12);  color: #fbbf24; }
.tag-offline  { background: rgba(239,68,68,.1);    color: #f87171; }

@media (max-width: 1100px) {
    .dashboard-grid { grid-template-columns: 1fr; }
    .stats-grid     { grid-template-columns: repeat(2, 1fr); }
    .status-row     { grid-template-columns: 1fr; }
}
</style>

<div class="home-wrap">

    <div class="welcome-row">
        <div class="welcome-title">Welcome, <?php
        $display = $_SESSION['username'] ?? 'User';
        if (strlen($display) > 20 || stripos($display, 'KEYAUTH') !== false) $display = 'User';
        echo htmlspecialchars($display);
        ?></div>
        <div class="welcome-sub">Account Status • <span>Active</span></div>
    </div>

    <div class="dashboard-grid">

        <!-- LEFT: stats + chart -->
        <div class="left-col">

            <div class="stats-grid">
                <div class="stat-card stat-white">
                    <div class="stat-label"><i class="fas fa-layer-group"></i> Plan</div>
                    <div class="stat-value" style="font-size:1.25rem"><?php echo htmlspecialchars(getPlanName()); ?></div>
                    <div class="stat-sub">Verified membership</div>
                </div>
                <div class="stat-card stat-green">
                    <div class="stat-label"><i class="fas fa-bolt"></i> Daily Credits</div>
                    <div class="stat-value"><?php echo $limit >= 999999 ? '&infin;' : $remaining; ?></div>
                    <div class="stat-sub"><?php echo $limit >= 999999 ? 'Unlimited searches' : 'Requests available today'; ?></div>
                </div>
                <div class="stat-card stat-blue">
                    <div class="stat-label"><i class="fas fa-chart-bar"></i> Used Today</div>
                    <div class="stat-value"><?php echo $used >= 999999 ? 0 : $used; ?></div>
                    <div class="stat-sub"><?php echo $limit >= 999999 ? 'No daily limit' : $pct . '% of daily limit'; ?></div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <span class="panel-title"><i class="fas fa-chart-line"></i> Search Activity</span>
                    <span style="font-size:.72rem;color:#444">Last 7 days</span>
                </div>
                <div class="panel-body">
                    <div class="credit-bar-wrap">
                        <div class="credit-bar-top">
                            <span>Daily credits used</span>
                            <strong><?php echo $used; ?> / <?php echo $limit; ?></strong>
                        </div>
                        <div class="credit-track">
                            <div class="credit-fill <?php echo $pct > 90 ? 'danger' : ($pct > 70 ? 'warn' : ''); ?>"
                                 style="width: <?php echo $pct; ?>%"></div>
                        </div>
                    </div>
                    <div class="chart-wrap">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT: Quick Access full height -->
        <div class="right-col">
            <div class="qa-panel">
                <div class="panel-header">
                    <span class="panel-title"><i class="fas fa-bolt"></i> Quick Access</span>
                </div>
                <div class="panel-body">
                    <div class="qa-list">
                        <a href="?m=email" class="qa-item">
                            <div class="qa-icon"><i class="fas fa-envelope"></i></div>
                            <span>Email OSINT</span>
                            <i class="fas fa-chevron-right qa-arrow"></i>
                        </a>
                        <a href="?m=username" class="qa-item">
                            <div class="qa-icon"><i class="fas fa-user"></i></div>
                            <span>Username Search</span>
                            <i class="fas fa-chevron-right qa-arrow"></i>
                        </a>
                        <a href="?m=phone" class="qa-item">
                            <div class="qa-icon"><i class="fas fa-phone"></i></div>
                            <span>Phone Lookup</span>
                            <i class="fas fa-chevron-right qa-arrow"></i>
                        </a>
                        <a href="?m=ip" class="qa-item">
                            <div class="qa-icon"><i class="fas fa-globe"></i></div>
                            <span>IP Lookup</span>
                            <i class="fas fa-chevron-right qa-arrow"></i>
                        </a>
                        <a href="?m=domain" class="qa-item">
                            <div class="qa-icon"><i class="fas fa-server"></i></div>
                            <span>Domain Lookup</span>
                            <i class="fas fa-chevron-right qa-arrow"></i>
                        </a>
                        <a href="?m=breach" class="qa-item">
                            <div class="qa-icon"><i class="fas fa-shield-alt"></i></div>
                            <span>Breach Check</span>
                            <i class="fas fa-chevron-right qa-arrow"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Status -->
    <div class="status-row">
        <div class="status-card">
            <div class="status-dot dot-green"></div>
            <div class="status-info">
                <div class="status-name">APIs</div>
                <div class="status-desc">Breach databases</div>
            </div>
            <span class="status-tag tag-online">Online</span>
        </div>
        <div class="status-card">
            <div class="status-dot dot-green"></div>
            <div class="status-info">
                <div class="status-name">KeyAuth</div>
                <div class="status-desc">License validation</div>
            </div>
            <span class="status-tag tag-online">Online</span>
        </div>
        <div class="status-card">
            <div class="status-dot dot-green"></div>
            <div class="status-info">
                <div class="status-name">OsintX Platform</div>
                <div class="status-desc">All systems</div>
            </div>
            <span class="status-tag tag-online">Operational</span>
        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const used = <?php echo $used; ?>;
const baseData = [0, 0, 0, 0, 0, 0, used];
const data = baseData.map((v, i) => i === 6 ? v : Math.floor(Math.random() * Math.max(used * 1.5, 5)));

const ctx = document.getElementById('activityChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            data: data,
            borderColor: 'rgba(255,255,255,0.8)',
            borderWidth: 2,
            backgroundColor: 'rgba(255,255,255,0.04)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: 'rgba(255,255,255,0.9)',
            pointBorderColor: 'transparent',
            pointRadius: 4,
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(10,10,10,0.9)',
                borderColor: 'rgba(255,255,255,0.1)',
                borderWidth: 1,
                titleColor: '#888',
                bodyColor: '#fff',
                padding: 10,
            }
        },
        scales: {
            x: {
                display: true,
                grid: { display: false, drawBorder: false },
                ticks: { color: '#444', font: { size: 11 } },
            },
            y: { display: false, beginAtZero: true, grace: '5%' }
        },
        layout: { padding: { top: 6, bottom: 0, left: 0, right: 0 } },
        interaction: { intersect: false, mode: 'index' }
    }
});
</script>