# Changelog

All notable changes to this project are documented here.

Format based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [v4.0.0] - 2026-05-17

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

Approximately **120** open backlog items. Top issue types:

- `mysqli_without_config`: 27
- `inline_mysqli_config`: 26
- `uses_superglobal_input`: 24
- `select_star`: 12
- `missing_output_escaping`: 11
- `legacy_owner_role`: 10
- `sql_interpolation_risk`: 8
- `wrong_folder_case_in_links`: 1


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
