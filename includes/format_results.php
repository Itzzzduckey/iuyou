<?php
/**
 * Format API response data as readable HTML. No raw JSON.
 * Used by all dashboard modules to display OsintDog (and similar) results.
 */

function _osint_field_class($key) {
    $k = strtolower($key);
    if (strpos($k, 'pass') !== false || strpos($k, 'hash') !== false) return 'cell-password';
    if (strpos($k, 'email') !== false || strpos($k, 'mail') !== false) return 'cell-email';
    if ($k === 'ip' || strpos($k, 'ip') !== false) return 'cell-ip';
    if (in_array($k, ['name', 'username', 'user', 'login', 'first_name', 'last_name'])) return 'cell-name';
    return '';
}

function _osint_label($key) {
    return htmlspecialchars(ucwords(trim(str_replace(['_', '-'], ' ', $key))));
}

/** Keys we never display (API branding / internal) */
function _osint_skip_key($key) {
    $k = strtolower(trim(str_replace(['_', ' ', '-'], '', $key)));
    return in_array($k, ['creditlookup', 'creditlookupmadeby'], true);
}

function _osint_value($val) {
    if ($val === null || $val === '') return '—';
    if (is_bool($val)) return $val ? 'Yes' : 'No';
    if (is_scalar($val)) return htmlspecialchars((string) $val);
    return ''; // nested: handled by render
}

/**
 * Render a single level: scalar → row, array of objects → table, object → section or grid.
 */
function _osint_render_value($val, $depth = 0) {
    if ($val === null || $val === '') return '';
    if (is_bool($val)) return '<span class="result-value">' . ($val ? 'Yes' : 'No') . '</span>';
    if (is_scalar($val)) return '<span class="result-value">' . htmlspecialchars((string) $val) . '</span>';

    if (isset($val['found']) && count($val) <= 2) {
        $out = '<span class="result-value">' . (int) $val['found'] . ' hit(s)</span>';
        foreach ($val as $k => $v) {
            if ($k === 'found' || _osint_skip_key($k) || !is_scalar($v)) continue;
            if (strpos((string) $v, 'osintdog.com') !== false && stripos((string) $v, 'made by') !== false) continue;
            $out .= ' <span class="result-muted">' . _osint_label($k) . ': ' . htmlspecialchars((string) $v) . '</span>';
        }
        return $out;
    }

    if (array_keys($val) === range(0, count($val) - 1)) {
        if (count($val) === 0) return '';
        if (is_array($val[0]) && !empty($val[0])) {
            return _osint_render_table($val);
        }
        $list = array_map(function ($v) { return is_scalar($v) ? htmlspecialchars((string) $v) : ''; }, $val);
        return '<span class="result-value">' . implode(', ', array_filter($list)) . '</span>';
    }

    $html = '<div class="result-nested">';
    foreach ($val as $k => $v) {
        if ($k === 'success' || $k === 'error') continue;
        if (_osint_skip_key($k)) continue;
        if (is_scalar($v) && (strpos((string) $v, 'osintdog.com') !== false && stripos((string) $v, 'made by') !== false)) continue;
        if (is_scalar($v)) {
            $cls = _osint_field_class($k);
            $html .= '<div class="result-kv ' . $cls . '"><span class="result-label">' . _osint_label($k) . '</span><span class="result-value">' . _osint_value($v) . '</span></div>';
        } else {
            $html .= '<div class="result-block"><span class="result-block-title">' . _osint_label($k) . '</span>' . _osint_render_value($v, $depth + 1) . '</div>';
        }
    }
    $html .= '</div>';
    return $html;
}

function _osint_render_table($rows) {
    if (empty($rows) || !is_array($rows[0])) return '';
    $flat = [];
    foreach ($rows as $row) {
        $flat[] = _osint_flatten_row($row);
    }
    $allKeys = [];
    foreach ($flat as $r) { $allKeys = array_merge($allKeys, array_keys($r)); }
    $allKeys = array_unique($allKeys);
    $skip = ['success', 'error', 'raw', 'json', 'creditlookup', 'credit_lookup'];
    $allKeys = array_filter($allKeys, function ($k) use ($skip) {
        if (in_array(strtolower($k), $skip)) return false;
        if (_osint_skip_key($k)) return false;
        return true;
    });

    $html = '<div class="result-table-wrap"><table class="result-table">';
    $html .= '<thead><tr>';
    foreach ($allKeys as $key) {
        $html .= '<th>' . _osint_label($key) . '</th>';
    }
    $html .= '</tr></thead><tbody>';
    foreach ($flat as $row) {
        $html .= '<tr>';
        foreach ($allKeys as $key) {
            $v = isset($row[$key]) ? $row[$key] : '';
            $cls = _osint_field_class($key);
            if (is_array($v) || is_object($v)) $v = ''; // avoid JSON dump
            $html .= '<td class="' . $cls . '">' . (is_scalar($v) ? htmlspecialchars((string) $v) : '—') . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table></div>';
    return $html;
}

function _osint_flatten_row($arr, $prefix = '') {
    $out = [];
    foreach ($arr as $k => $v) {
        if ($v === null || $v === '') continue;
        if (_osint_skip_key($k)) continue;
        $key = $prefix ? $prefix . '.' . $k : $k;
        if (is_array($v) && !empty($v) && isset($v[0]) && is_array($v[0])) continue;
        if (is_array($v) && array_keys($v) !== range(0, count($v) - 1)) {
            $out = array_merge($out, _osint_flatten_row($v, $key));
        } elseif (is_scalar($v)) {
            $out[$key] = $v;
        }
    }
    return $out;
}

/**
 * Main entry: render full API response as nice HTML (no raw JSON).
 * Handles structure: { success, search_term, search_type, results: { sourceName: data } }
 */
function render_formatted_results($data, $search_term = '') {
    if (!is_array($data)) return '<p class="result-empty">No data to display.</p>';

    $html = '';
    $meta = [];
    if (!empty($data['search_term'])) $meta[] = 'Search: <strong>' . htmlspecialchars($data['search_term']) . '</strong>';
    if (!empty($data['search_type'])) $meta[] = 'Type: ' . htmlspecialchars($data['search_type']);
    if (!empty($search_term) && empty($data['search_term'])) $meta[] = 'Query: <strong>' . htmlspecialchars($search_term) . '</strong>';
    if (!empty($meta)) {
        $html .= '<div class="result-meta">' . implode(' &middot; ', $meta) . '</div>';
    }

    if (!empty($data['results']) && is_array($data['results'])) {
        foreach ($data['results'] as $sourceName => $sourceData) {
            if (strtolower(trim($sourceName)) === 'keyscore') continue;
            $html .= '<div class="result-source-card">';
            $html .= '<div class="result-source-header">';
            $html .= '<span class="result-source-name">' . _osint_label($sourceName) . '</span>';
            $count = '';
            if (is_array($sourceData) && isset($sourceData['found'])) $count = (int) $sourceData['found'] . ' hit(s)';
            elseif (is_array($sourceData) && isset($sourceData[0]) && is_array($sourceData[0])) $count = count($sourceData) . ' record(s)';
            if ($count) $html .= '<span class="result-source-badge">' . $count . '</span>';
            $html .= '</div>';
            $html .= '<div class="result-source-body">';
            $html .= _osint_render_value($sourceData);
            $html .= '</div></div>';
        }
    } else {
        $skip = ['success', 'search_term', 'search_type', 'results'];
        foreach (['note', 'requested', 'message', 'error'] as $k) {
            if (isset($data[$k]) && is_scalar($data[$k])) $html .= '<div class="result-meta"><span class="result-meta-item">' . _osint_label($k) . ': ' . htmlspecialchars((string) $data[$k]) . '</span></div>';
        }
        $reduced = array_diff_key($data, array_flip($skip));
        if (!empty($reduced)) {
            $html .= '<div class="result-source-card"><div class="result-source-header"><span class="result-source-name">Results</span></div><div class="result-source-body">' . _osint_render_value($reduced) . '</div></div>';
        }
    }

    if (trim($html) === '') $html = '<p class="result-empty">No results to display.</p>';
    return $html;
}

/**
 * Build TikTok profile card + results HTML (for AJAX / API).
 */
function render_tiktok_result_html($results, $query) {
    $profile = null;
    $profileSource = null;
    if (!empty($results['results'])) {
        foreach (['TikTok Recon (full)', 'TikTok Recon (basic)'] as $key) {
            if (isset($results['results'][$key]) && is_array($results['results'][$key])) {
                $profile = $results['results'][$key];
                $profileSource = $key;
                break;
            }
        }
    }
    $flat = function ($arr, $prefix = '') use (&$flat) {
        if (!is_array($arr)) return [];
        $out = [];
        foreach ($arr as $k => $v) {
            $key = $prefix ? $prefix . '.' . $k : $k;
            if (is_array($v) && isset($v[0])) continue;
            if (is_array($v) && !isset($v[0])) $out = array_merge($out, $flat($v, $key));
            else $out[$key] = $v;
        }
        return $out;
    };
    $get = function ($data, $keys) use ($flat) {
        if (!is_array($data)) return '';
        $f = $flat($data);
        foreach ((array) $keys as $key) {
            foreach ($f as $k => $v) {
                if (stripos($k, $key) !== false || str_replace(['_',' '], '', strtolower($k)) === str_replace(['_',' '], '', strtolower($key))) return is_scalar($v) ? (string) $v : '';
            }
        }
        return '';
    };
    if ($profile !== null && $profileSource !== null) unset($results['results'][$profileSource]);
    $avatarUrl = $profile ? $get($profile, ['Avatar URL', 'AvatarURL', 'avatar_url', 'Avatar']) : '';
    $nickname = $profile ? $get($profile, ['Nickname', 'nickname']) : '';
    $username = $profile ? $get($profile, ['Username', 'username']) : ($query ?? '');
    $about = $profile ? $get($profile, ['About', 'about', 'Bio', 'bio']) : '';
    $country = $profile ? $get($profile, ['Country', 'country']) : '';
    $language = $profile ? $get($profile, ['Language', 'language']) : '';
    $created = $profile ? $get($profile, ['Account Created', 'account_created', 'AccountCreated']) : '';
    $followers = $profile ? $get($profile, ['Followers', 'followers']) : '';
    $following = $profile ? $get($profile, ['Following', 'following']) : '';
    $hearts = $profile ? $get($profile, ['Hearts', 'hearts', 'Likes']) : '';
    $videos = $profile ? $get($profile, ['Videos', 'videos']) : '';
    if ($created && preg_match('/^(\d{4}-\d{2}-\d{2})/', $created, $m)) $created = $m[1];

    $html = '<div class="result-card"><div class="result-header"><h3 style="font-size:1.1rem;font-weight:600;">Search Results</h3><span class="result-badge"><i class="fas fa-check-circle"></i> Found</span></div>';
    if ($profile !== null) {
        $html .= '<div class="tiktok-profile-card"><div class="tiktok-profile-top"><div class="tiktok-avatar-wrap">';
        $html .= $avatarUrl ? '<img src="' . htmlspecialchars($avatarUrl) . '" alt="Avatar" class="tiktok-avatar" loading="lazy" referrerpolicy="no-referrer">' : '<div class="tiktok-avatar-placeholder"><i class="fab fa-tiktok"></i></div>';
        $html .= '</div><div class="tiktok-profile-info"><div class="tiktok-profile-name">' . ($nickname ? htmlspecialchars($nickname) : '—') . '</div>';
        $html .= '<div class="tiktok-profile-handle">' . ($username ? '@' . htmlspecialchars($username) : '') . '</div>';
        $html .= '<div class="tiktok-profile-about">' . ($about ? htmlspecialchars($about) : 'User has no about') . '</div><div class="tiktok-profile-meta">';
        if ($country) $html .= '<div class="tiktok-meta-item"><span class="tiktok-meta-label">Country</span><div class="tiktok-meta-value">' . htmlspecialchars($country) . '</div></div>';
        if ($language) $html .= '<div class="tiktok-meta-item"><span class="tiktok-meta-label">Language</span><div class="tiktok-meta-value">' . htmlspecialchars($language) . '</div></div>';
        if ($created) $html .= '<div class="tiktok-meta-item"><span class="tiktok-meta-label">Joined</span><div class="tiktok-meta-value">' . htmlspecialchars($created) . '</div></div>';
        if ($followers !== '') $html .= '<div class="tiktok-meta-item"><span class="tiktok-meta-label">Followers</span><div class="tiktok-meta-value">' . htmlspecialchars($followers) . '</div></div>';
        if ($following !== '') $html .= '<div class="tiktok-meta-item"><span class="tiktok-meta-label">Following</span><div class="tiktok-meta-value">' . htmlspecialchars($following) . '</div></div>';
        if ($hearts !== '') $html .= '<div class="tiktok-meta-item"><span class="tiktok-meta-label">Hearts</span><div class="tiktok-meta-value">' . htmlspecialchars($hearts) . '</div></div>';
        if ($videos !== '') $html .= '<div class="tiktok-meta-item"><span class="tiktok-meta-label">Videos</span><div class="tiktok-meta-value">' . htmlspecialchars($videos) . '</div></div>';
        $html .= '</div></div></div></div>';
    }
    $html .= '<div class="tiktok-results-section result-formatted">' . render_formatted_results($results, $query ?? '') . '</div></div>';
    return $html;
}

/**
 * Build full result card HTML for a module (for AJAX response). Returns HTML only.
 */
function render_module_result_html($module, $results, $query) {
    if ($module === 'tiktok') return render_tiktok_result_html($results, $query);
    $inner = render_formatted_results($results, $query);
    return '<div class="result-card"><div class="result-header"><h3 style="font-size:1.1rem;font-weight:600;">Search Results</h3><span class="result-badge"><i class="fas fa-check-circle"></i> Found</span></div><div class="result-formatted">' . $inner . '</div></div>';
}
