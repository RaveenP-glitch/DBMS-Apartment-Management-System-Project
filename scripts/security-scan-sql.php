#!/usr/bin/env php
<?php
/**
 * Phase 3 — Security scan: SQL strings with variable interpolation (not prepared statements).
 * Output: docs/validation/security-sql-report.json
 */
$root = dirname(__DIR__);
$outFile = $root . '/docs/validation/security-sql-report.json';
$findings = [];

$skipDirs = ['vendor', '.git', 'node_modules'];
$skipFiles = ['security-scan-sql.php', 'generate_refactor_backlog.php'];

$patterns = [
    'query_string_interp' => '/->query\s*\(\s*["\'][^"\']*\$[a-zA-Z_]/',
    'sql_string_concat' => '/["\']\s*(SELECT|INSERT|UPDATE|DELETE)[^"\']*["\']\s*\.\s*\$/i',
    'sql_var_inside_quotes' => '/(SELECT|INSERT|UPDATE|DELETE)[^;]*["\'][^"\']*\{?\$[a-zA-Z_]/i',
    'mysqli_query_interp' => '/mysqli_query\s*\([^,]+,\s*["\'][^"\']*\$/',
];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $path = $file->getPathname();
    foreach ($skipDirs as $dir) {
        if (str_contains($path, DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR)) {
            continue 2;
        }
    }
    if (in_array(basename($path), $skipFiles, true)) {
        continue;
    }

    $rel = ltrim(str_replace($root, '', $path), '/\\');
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        continue;
    }

    $usesPrepare = (bool) preg_match('/->prepare\s*\(/', implode("\n", $lines));

    foreach ($lines as $num => $line) {
        if (preg_match('/->prepare\s*\(/', $line)) {
            continue;
        }
        if (preg_match('/\$_(POST|GET|REQUEST|COOKIE)/', $line) === 0
            && !preg_match('/\$[a-zA-Z_][a-zA-Z0-9_]*/', $line)) {
            continue;
        }
        foreach ($patterns as $type => $regex) {
            if (preg_match($regex, $line)) {
                // Ignore safe casts like (int) in other lines; flag for human review
                $findings[] = [
                    'file' => $rel,
                    'line' => $num + 1,
                    'type' => $type,
                    'snippet' => trim($line),
                    'uses_prepare_elsewhere' => $usesPrepare,
                    'priority' => str_contains($line, '$_') ? 'critical' : 'high',
                ];
            }
        }
        // Classic: SELECT ... '$var'
        if (preg_match('/(SELECT|INSERT|UPDATE|DELETE).*["\'][^"\']*\$[a-zA-Z_]/i', $line)
            && !preg_match('/->prepare/', $line)) {
            $findings[] = [
                'file' => $rel,
                'line' => $num + 1,
                'type' => 'sql_quote_interpolation',
                'snippet' => trim($line),
                'uses_prepare_elsewhere' => $usesPrepare,
                'priority' => 'critical',
            ];
        }
    }
}

// Dedupe file+line
$unique = [];
foreach ($findings as $f) {
    $key = $f['file'] . ':' . $f['line'] . ':' . $f['type'];
    $unique[$key] = $f;
}
$findings = array_values($unique);

usort($findings, fn ($a, $b) => strcmp($a['file'], $b['file']) ?: $a['line'] <=> $b['line']);

$report = [
    'generated_at' => date('c'),
    'total_findings' => count($findings),
    'critical' => count(array_filter($findings, fn ($f) => $f['priority'] === 'critical')),
    'findings' => $findings,
];

@mkdir(dirname($outFile), 0775, true);
file_put_contents($outFile, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

echo "Security SQL scan: {$report['total_findings']} finding(s) → $outFile\n";
exit($report['critical'] > 0 ? 1 : 0);
