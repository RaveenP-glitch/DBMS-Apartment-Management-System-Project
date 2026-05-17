#!/usr/bin/env php
<?php
/**
 * V4 Phase 1 — Automated inventory.
 * Scans the repo and writes docs/refactor_backlog.json.
 *
 * Usage: php scripts/generate_refactor_backlog.php
 */

$root = dirname(__DIR__);
$backlog = [
    'generated_at' => date('c'),
    'project' => 'DBMS-Apartment-Management-System-Project',
    'items' => [],
];

function add_item(array &$backlog, string $file, string $issue, string $priority, array $extra = []): void
{
    $backlog['items'][] = array_merge([
        'file' => $file,
        'issue' => $issue,
        'priority' => $priority,
    ], $extra);
}

function rel_path(string $root, string $path): string
{
    return ltrim(str_replace($root, '', $path), '/\\');
}

// --- PHP files ---
$phpFiles = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($phpFiles as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }
    $path = $file->getPathname();
    if (str_contains($path, DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR)) {
        continue;
    }

    $rel = rel_path($root, $path);
    $content = file_get_contents($path);

    if ($rel !== 'config.php' && preg_match('/\$servername\s*=/', $content)) {
        add_item($backlog, $rel, 'inline_mysqli_config', 'high', ['grep' => '$servername']);
    }

    if (preg_match('/new\s+mysqli\s*\(/', $content) && $rel !== 'config.php') {
        if (!preg_match("/require.*config\.php/", $content)) {
            add_item($backlog, $rel, 'mysqli_without_config', 'high');
        }
    }

    if (preg_match('/\$_(POST|GET|REQUEST)\s*\[[^\]]+\]\s*[^;]*(\.|concat).*?(SELECT|INSERT|UPDATE|DELETE)/is', $content)
        || preg_match('/(SELECT|INSERT|UPDATE|DELETE)[^;]*\$_(POST|GET)/i', $content)
        || preg_match('/["\'].*\$_(POST|GET|REQUEST)/', $content)) {
        add_item($backlog, $rel, 'sql_interpolation_risk', 'critical');
    }

    if (preg_match('/\$_(POST|GET|REQUEST)/', $content)) {
        add_item($backlog, $rel, 'uses_superglobal_input', 'medium');
    }

    if (preg_match('/SELECT\s+\*\s+FROM/i', $content)) {
        add_item($backlog, $rel, 'select_star', 'low');
    }

    if (!preg_match('/htmlspecialchars\s*\(/', $content) && preg_match('/echo\s+.*\$row/', $content)) {
        add_item($backlog, $rel, 'missing_output_escaping', 'medium');
    }
}

// --- CSS / HTML duplicate login styles ---
$loginStyleFiles = [];
foreach (['login.html', 'Admin/admin_login.php', 'Employee/employeelogin.php', 'Tenant/tenant_login.php'] as $candidate) {
    $full = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $candidate);
    if (is_file($full)) {
        $c = file_get_contents($full);
        if (preg_match('/\.login__form/', $c) && preg_match('/<style>/', $c)) {
            $loginStyleFiles[] = $candidate;
        }
    }
}
if (count($loginStyleFiles) > 1) {
    foreach ($loginStyleFiles as $f) {
        add_item($backlog, $f, 'duplicate_inline_login_css', 'medium', [
            'related' => array_values(array_diff($loginStyleFiles, [$f])),
        ]);
    }
}

// --- Admin vs Employee style.css ---
$adminCss = $root . '/Admin/style.css';
$empCss = $root . '/Employee/style.css';
if (is_file($adminCss) && is_file($empCss)) {
    $a = filesize($adminCss);
    $e = filesize($empCss);
    if (abs($a - $e) < 500) {
        add_item($backlog, 'Admin/style.css', 'duplicate_dashboard_css', 'low', [
            'related' => ['Employee/style.css'],
        ]);
    }
}

if (is_file($root . '/Employee/style copy.css')) {
    add_item($backlog, 'Employee/style copy.css', 'duplicate_css_copy', 'low');
}

// --- Owner legacy ---
$ownerDir = $root . '/Owner';
if (is_dir($ownerDir)) {
    foreach (glob($ownerDir . '/*.php') as $ownerFile) {
        add_item($backlog, rel_path($root, $ownerFile), 'legacy_owner_role', 'medium', [
            'phase' => 'owner_deprecation',
        ]);
    }
}

// --- login.html path casing ---
$loginHtml = $root . '/login.html';
if (is_file($loginHtml) && preg_match('/href\s*=\s*["\']admin\//', file_get_contents($loginHtml))) {
    add_item($backlog, 'login.html', 'wrong_folder_case_in_links', 'high');
}

// Sort: critical > high > medium > low
$order = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
usort($backlog['items'], function ($a, $b) use ($order) {
    return ($order[$a['priority']] ?? 9) <=> ($order[$b['priority']] ?? 9);
});

$out = $root . '/docs/refactor_backlog.json';
file_put_contents($out, json_encode($backlog, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

echo "Wrote " . count($backlog['items']) . " backlog items to docs/refactor_backlog.json\n";
