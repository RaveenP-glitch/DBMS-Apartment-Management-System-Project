# Phase 3 — Mechanical validation (automated)

Automated checks before human sign-off (Phase 4) and release (Phase 5).

## Quick run

```bash
# All Phase 3 checks
bash scripts/run-phase3-validation.sh

# Or via Composer (after composer install)
composer validate
```

Reports are written under `docs/validation/`.

| Check | Script | Output |
|-------|--------|--------|
| PHP syntax | `scripts/validate-php-syntax.sh` | `docs/validation/syntax-report.txt` |
| PHPStan | `scripts/run-phpstan.sh` | `docs/validation/phpstan-report.txt` |
| SQL security | `scripts/security-scan-sql.php` | `docs/validation/security-sql-report.json` |
| Summary | `scripts/run-phase3-validation.sh` | `docs/validation/phase3-summary.json` |

## 3.1 Syntax

Equivalent to:

```bash
find . -name '*.php' ! -path './vendor/*' -exec php -l {} \;
```

Wrapper (aggregates failures):

```bash
bash scripts/validate-php-syntax.sh
```

Exit code `0` = all files pass.

## 3.2 PHPStan (levels 2 → 5)

Install once:

```bash
composer install
```

Run at default level 2:

```bash
vendor/bin/phpstan analyse -c phpstan.neon.dist
# or
bash scripts/run-phpstan.sh
```

Increase level as codebase hardens:

| Level | When to use |
|-------|-------------|
| 2 | Current default — basic unknowns |
| 3 | After obvious null/type fixes |
| 4 | Stricter return types |
| 5 | Maximum strictness (long-term) |

```bash
PHPSTAN_LEVEL=3 bash scripts/run-phpstan.sh
```

Update baseline after intentional suppressions:

```bash
vendor/bin/phpstan analyse -c phpstan.neon.dist --generate-baseline=phpstan-baseline.neon
```

## 3.3 SQL security scan

Detects likely **string-built SQL** (e.g. `"SELECT ... $var"`) — not a substitute for code review.

```bash
php scripts/security-scan-sql.php
```

Optional manual grep:

```bash
grep -rEn '(SELECT|INSERT|UPDATE|DELETE).*\$' --include='*.php' . | grep -v vendor
```

Exit code `1` if any **critical** findings remain.

## CI suggestion

```yaml
- run: bash scripts/validate-php-syntax.sh
- run: composer install --no-interaction
- run: PHPSTAN_LEVEL=2 bash scripts/run-phpstan.sh
- run: php scripts/security-scan-sql.php
```

Phase 3 **pass** = syntax OK + PHPStan OK at agreed level + zero critical SQL scan findings (or documented exceptions in baseline).
