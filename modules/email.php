<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth();
?>

<style>
.email-module { padding: 32px 40px; }
.module-header { margin-bottom: 28px; }
.module-header h2 { font-size: 1.8rem; font-weight: 800; margin-bottom: 6px; }
.module-header p  { color: #666; font-size: .9rem; }
.search-box { display: flex; gap: 12px; margin-bottom: 36px; }
.search-box input { flex: 1; padding: 16px 20px; background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.1); border-radius: 12px; color: #fff; font-size: 1rem; outline: none; transition: all .2s; }
.search-box input:focus { border-color: rgba(255,255,255,.3); background: rgba(255,255,255,.07); }
.search-box button { padding: 16px 32px; background: #fff; color: #000; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; transition: all .2s; white-space: nowrap; }
.search-box button:hover:not(:disabled) { background: #e0e0e0; transform: translateY(-1px); }
.search-box button:disabled { background: #333; color: #666; cursor: not-allowed; }
.results-header { display: none; margin-bottom: 24px; padding: 16px 20px; background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); border-radius: 10px; font-size: .9rem; color: #888; }
.results-header span { color: #fff; font-weight: 700; }
.loading-msg { display: none; text-align: center; padding: 60px 20px; color: #555; font-size: .95rem; }
.loading-msg i { margin-right: 8px; color: #fff; }
.error-msg { display: none; padding: 16px 20px; background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.25); border-radius: 10px; color: #f87171; font-size: .9rem; margin-bottom: 24px; }
.breach-list { display: flex; flex-direction: column; gap: 16px; }
.breach-card { background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.08); border-radius: 12px; overflow: hidden; }
.breach-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 18px; border-bottom: 1px solid rgba(255,255,255,.06); background: rgba(255,255,255,.02); }
.breach-source { font-weight: 700; font-size: .9rem; color: #fff; }
.breach-tag { font-size: .72rem; padding: 2px 10px; border-radius: 20px; background: rgba(255,255,255,.07); color: #888; font-family: monospace; }
.breach-table { width: 100%; border-collapse: collapse; }
.breach-table tr { border-bottom: 1px solid rgba(255,255,255,.04); }
.breach-table tr:last-child { border-bottom: none; }
.breach-table td { padding: 9px 18px; font-size: .85rem; vertical-align: top; }
.breach-table td:first-child { color: #555; width: 140px; font-size: .8rem; }
.breach-table td:last-child { color: #e0e0e0; word-break: break-all; }
.field-password td:last-child { color: #f87171; font-family: monospace; }
.field-email    td:last-child { color: #60a5fa; }
.field-ip       td:last-child { color: #a78bfa; }
.field-name     td:last-child { color: #4ade80; }
.no-results { text-align: center; padding: 60px 20px; color: #555; font-size: .95rem; }
.no-results i { font-size: 2rem; display: block; margin-bottom: 12px; color: #333; }
.raw-toggle-wrap { padding: 10px 18px; border-top: 1px solid rgba(255,255,255,.05); }
.raw-toggle { padding:6px 12px; background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.07); border-radius:6px; color:#555; font-size:.73rem; cursor:pointer; transition:all .2s; }
.raw-toggle:hover { background:rgba(255,255,255,.06); color:#fff; }
.raw-json { display:none; margin-top:8px; padding:10px; background:rgba(0,0,0,.4); border-radius:8px; font-size:.7rem; color:#6ee7b7; white-space:pre-wrap; word-break:break-all; max-height:300px; overflow-y:auto; font-family:monospace; }
</style>

<div class="email-module">
    <div class="module-header">
        <h2><i class="fas fa-envelope"></i> Email OSINT</h2>
        <p>Search 46 breach databases via BreachHub</p>
    </div>
    <div class="search-box">
        <input type="email" id="emailInput" placeholder="target@example.com" autocomplete="off">
        <button id="searchBtn" onclick="startSearch()">
            <i class="fas fa-search"></i> Search
        </button>
    </div>
    <div class="error-msg" id="errorMsg"></div>
    <div class="loading-msg" id="loadingMsg">
        <i class="fas fa-spinner fa-spin"></i> Searching 46 breach databases...
    </div>
    <div class="results-header" id="resultsHeader"></div>
    <div class="breach-list" id="breachList"></div>
</div>

<script>
function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function fieldClass(key) {
    const k = key.toLowerCase();
    if (k.includes('pass') || k.includes('hash')) return 'field-password';
    if (k.includes('email') || k.includes('mail')) return 'field-email';
    if (k === 'ip' || k.includes('lastip') || k === 'ip_address') return 'field-ip';
    if (['name','username','user','login','first_name','last_name'].includes(k)) return 'field-name';
    return '';
}
function flatten(obj, prefix) {
    prefix = prefix || '';
    const out = {};
    for (const [k, v] of Object.entries(obj || {})) {
        const key = prefix ? prefix + '.' + k : k;
        if (v === null || v === undefined || v === '') continue;
        if (typeof v === 'object' && !Array.isArray(v)) Object.assign(out, flatten(v, key));
        else if (Array.isArray(v)) { if (v.length) out[key] = v.map(x => typeof x === 'object' ? JSON.stringify(x) : x).join(', '); }
        else out[key] = v;
    }
    return out;
}
function renderBreach(sourceName, records) {
    const card = document.createElement('div');
    card.className = 'breach-card';
    let tablesHtml = '';
    records.forEach((rec, idx) => {
        const flat = (rec && typeof rec === 'object') ? flatten(rec) : { value: String(rec) };
        const entries = Object.entries(flat);
        if (!entries.length) return;
        tablesHtml +=
            '<table class="breach-table">' +
            entries.map(([k,v]) => '<tr class="' + fieldClass(k) + '"><td>' + esc(k) + '</td><td>' + esc(String(v)) + '</td></tr>').join('') +
            '</table>' +
            (idx < records.length - 1 ? '<hr style="border:none;border-top:1px solid rgba(255,255,255,.05);margin:0">' : '');
    });
    card.innerHTML =
        '<div class="breach-header">' +
            '<span class="breach-source">— ' + esc(sourceName) + '</span>' +
            '<span class="breach-tag">' + records.length.toLocaleString() + ' record' + (records.length !== 1 ? 's' : '') + '</span>' +
        '</div>' +
        tablesHtml;
    return card;
}
function toggleRaw(btn) {
    const raw = btn.nextElementSibling;
    const open = raw.style.display === 'block';
    raw.style.display = open ? 'none' : 'block';
    btn.innerHTML = open ? '<i class="fas fa-code"></i> Raw JSON' : '<i class="fas fa-code"></i> Hide JSON';
}

function extractRecords(name, v) {
    // Strutture verificate da test reali:
    // snusbase:        { results: [{email,name,_domain,...}] }
    // leakcheck:       { results: [{source_name,email,password,...}] }
    // breachbase:      { results: [{origin,username,password,email}] }
    // breachdirectory: { results: [{title,domain,email,username}] }
    // osintcat:        { results: [{domain,email,password}] }
    // seekbase:        { documents: [{filename,content}] }
    // hudsonrock:      { results: [{computer_name,...}] }
    // seon_email:      { data: {email,score,...} } — oggetto singolo
    // leaksight:       { data: [...] } o { results: [...] }
    // fetchbase:       { results: [...] }
    // leakosint:       { results: [...] }

    if (!v || typeof v !== 'object') return null;
    if (v.skipped) return null;

    // Skip errori puliti
    if (v.error && !v.results && !v.data && !v.documents) return null;
    if (v.success === false && !v.results && !v.data && !v.documents) return null;

    // Tutti i campi array possibili in ordine di priorità
    const ARRAY_FIELDS = [
        'results','result','data','documents','hits','records',
        'items','entries','leaks','breaches','credentials',
        'logs','matches','emails','users','accounts',
    ];

    for (const field of ARRAY_FIELDS) {
        const val = v[field];
        if (!Array.isArray(val) || val.length === 0) continue;
        if (typeof val[0] === 'object' && val[0] !== null) return val;
        if (typeof val[0] === 'string') return val.map(s => ({ value: s }));
    }

    // seon: { data: { email, score, ... } } — oggetto singolo con dati utili
    if (v.data && typeof v.data === 'object' && !Array.isArray(v.data)) {
        const keys = Object.keys(v.data);
        if (keys.length > 1) return [v.data];
    }

    // Snusbase style: { DB_NAME: [records] } dentro results o direttamente
    const target = (v.results && typeof v.results === 'object' && !Array.isArray(v.results)) ? v.results : v;
    const sources = [];
    for (const [k, val] of Object.entries(target)) {
        if (['success','error','credit','service','query','count','version','timestamp'].includes(k)) continue;
        if (Array.isArray(val) && val.length > 0 && typeof val[0] === 'object') {
            sources.push(...val);
        }
    }
    if (sources.length > 0) return sources;

    return null;
}

function extractSources(name, v) {
    const sn = name.toUpperCase().replace(/_/g, ' ');
    const records = extractRecords(name, v);
    if (!records || records.length === 0) return [];
    return [{ label: sn, records }];
}

async function startSearch() {
    const email = document.getElementById('emailInput').value.trim();
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert('Enter a valid email address');
        return;
    }
    const btn           = document.getElementById('searchBtn');
    const errorMsg      = document.getElementById('errorMsg');
    const loadingMsg    = document.getElementById('loadingMsg');
    const resultsHeader = document.getElementById('resultsHeader');
    const breachList    = document.getElementById('breachList');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
    errorMsg.style.display      = 'none';
    loadingMsg.style.display    = 'block';
    resultsHeader.style.display = 'none';
    breachList.innerHTML        = '';

    try {
        const formData = new FormData();
        formData.append('email', email);
        const resp = await fetch('/api_proxy.php', { method: 'POST', body: formData });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const res = await resp.json();
        loadingMsg.style.display = 'none';

        if (res.remaining !== undefined && res.remaining !== null) {
            const creditEl = document.querySelector('.credit-badge strong');
            if (creditEl) {
                const parts = creditEl.textContent.split('/');
                if (parts.length === 2) creditEl.textContent = res.remaining + '/' + parts[1].trim();
            }
        }

        if (!res.ok) {
            errorMsg.innerHTML = res.limit_reached
                ? '<i class="fas fa-exclamation-circle"></i> Daily search limit reached. <strong>Upgrade your plan</strong> for more searches.'
                : 'Error: ' + (res.err || 'Unknown error');
            errorMsg.style.display = 'block';
            return;
        }

        const data = res.data;
        let allSources = [];

        if (data && data.results && typeof data.results === 'object') {
            for (const [name, serviceData] of Object.entries(data.results)) {
                extractSources(name, serviceData).forEach(s => allSources.push(s));
            }
        }

        // Deduplicazione per label
        const seen = new Set();
        allSources = allSources.filter(s => {
            if (seen.has(s.label)) return false;
            seen.add(s.label);
            return true;
        });

        // Ordina per numero record decrescente
        allSources.sort((a, b) => b.records.length - a.records.length);

        const totalRecords = allSources.reduce((sum, s) => sum + s.records.length, 0);

        if (allSources.length === 0) {
            breachList.innerHTML = '<div class="no-results"><i class="fas fa-shield-alt"></i>No results found for <strong>' + esc(email) + '</strong></div>';
        } else {
            resultsHeader.style.display = 'block';
            resultsHeader.innerHTML =
                'Results for <span>"' + esc(email) + '"</span> &mdash; ' +
                '<span>' + totalRecords.toLocaleString() + '</span> records across ' +
                '<span>' + allSources.length + '</span> source' + (allSources.length !== 1 ? 's' : '');
            allSources.forEach(s => breachList.appendChild(renderBreach(s.label, s.records)));
        }

    } catch(e) {
        loadingMsg.style.display = 'none';
        errorMsg.textContent = 'Connection error: ' + e.message;
        errorMsg.style.display = 'block';
    }
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-search"></i> Search';
}
</script>