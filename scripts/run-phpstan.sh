#!/usr/bin/env bash
# Phase 3 — PHPStan (levels 2→5). Set PHPSTAN_LEVEL=3 etc. to override default.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

LEVEL="${PHPSTAN_LEVEL:-2}"
CONFIG="${PHPSTAN_CONFIG:-phpstan.neon.dist}"
REPORT="${1:-docs/validation/phpstan-report.txt}"
mkdir -p "$(dirname "$REPORT")"

if [[ -x "$ROOT/vendor/bin/phpstan" ]]; then
  PHPSTAN="$ROOT/vendor/bin/phpstan"
elif command -v phpstan >/dev/null 2>&1; then
  PHPSTAN="phpstan"
else
  {
    echo "PHPStan not installed (skipped)."
    echo "Install: composer install --working-dir=$ROOT"
  } | tee "$REPORT"
  exit 0
fi

{
  echo "PHPStan level: $LEVEL"
  echo "Config: $CONFIG"
  echo "Started: $(date -u +%Y-%m-%dT%H:%M:%SZ)"
  echo "---"
} >"$REPORT"

if "$PHPSTAN" analyse -c "$CONFIG" --level="$LEVEL" --memory-limit=512M --no-progress >>"$REPORT" 2>&1; then
  echo "---" >>"$REPORT"
  echo "Result: PASS" >>"$REPORT"
  echo "PHPStan level $LEVEL: PASS → $REPORT"
  exit 0
fi

echo "---" >>"$REPORT"
echo "Result: FAIL (see above)" >>"$REPORT"
echo "PHPStan level $LEVEL: FAIL → $REPORT"
exit 1
