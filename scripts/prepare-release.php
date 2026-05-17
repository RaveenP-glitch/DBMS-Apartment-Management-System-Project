#!/usr/bin/env php
<?php
/**
 * Phase 5 — Build CHANGELOG.md from V3 work + refactor backlog summary.
 */
$root = dirname(__DIR__);
$changelogPath = $root . '/CHANGELOG.md';
$backlogPath = $root . '/docs/refactor_backlog.json';
$v3ChangesPath = $root . '/docs/V3_CHANGES.md';

$backlog = is_file($backlogPath)
    ? json_decode(file_get_contents($backlogPath), true)
    : ['items' => []];

$byPriority = ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0];
$byIssue = [];
foreach ($backlog['items'] ?? [] as $item) {
    $p = $item['priority'] ?? 'low';
    $byPriority[$p] = ($byPriority[$p] ?? 0) + 1;
    $issue = $item['issue'] ?? 'unknown';
    $byIssue[$issue] = ($byIssue[$issue] ?? 0) + 1;
}
arsort($byIssue);

$topIssues = array_slice($byIssue, 0, 8, true);
$issueLines = '';
foreach ($topIssues as $issue => $count) {
    $issueLines .= "- `$issue`: $count\n";
}

$remaining = array_sum($byPriority);
$generated = date('Y-m-d');

$content = <<<MD
# Changelog

All notable changes to this project are documented here.

Format based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [v4.0.0] - $generated

### Added

- `config.php` adoption across **Admin/** PHP endpoints
- Shared login styles: `assets/css/login.css`
- Refactor tooling: `scripts/generate_refactor_backlog.php`, `scripts/run-phase3-validation.sh`
- Phase 3 validation: syntax, PHPStan (level 2+), SQL security scan
- Docs: `docs/V3_CHANGES.md`, `docs/MIGRATION_V4.md`, `docs/V3_PHASE4_SIGNOFF.md`, `docs/OWNER_DEPRECATION.md`
- `composer.json` with PHPStan for static analysis

### Changed

- **Admin** login and delete/parking flows use prepared statements
- `login.html` role links use correct folder casing (`Admin/`, `Employee/`, `Tenant/`)
- `Admin/statistics.php` queries table `employee` (was invalid `employees`)

### Deprecated

- **Owner/** HTTP endpoints — 301 redirects (see `docs/OWNER_DEPRECATION.md`)

### Security

- Reduced SQL injection surface on refactored Admin scripts
- Automated SQL interpolation scan (`scripts/security-scan-sql.php`)

### Remaining (post-v4.0.0 backlog)

Approximately **$remaining** open backlog items. Top issue types:

$issueLines

Regenerate: `php scripts/generate_refactor_backlog.php`

---

## [Unreleased]

### Planned

- Employee/Tenant → `config.php` and prepared statements
- Password hashing (`password_hash` / `password_verify`)
- Session guards on protected dashboards
- Database normalization (`complaint` vs `complaints`)

---

## Earlier versions

Pre-v4.0.0 history: see git log and [docs/V3_CHANGES.md](docs/V3_CHANGES.md).

MD;

file_put_contents($changelogPath, $content);
echo "Wrote $changelogPath\n";
echo "Backlog items summarized: $remaining\n";
