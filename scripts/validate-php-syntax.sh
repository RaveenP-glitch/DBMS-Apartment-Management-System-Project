#!/usr/bin/env bash
# Phase 3 — PHP syntax check for all project PHP files.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

REPORT="${1:-docs/validation/syntax-report.txt}"
mkdir -p "$(dirname "$REPORT")"

: > "$REPORT"
FAIL=0
COUNT=0

while IFS= read -r -d '' file; do
  COUNT=$((COUNT + 1))
  if php -l "$file" >>"$REPORT" 2>&1; then
    echo "OK  $file" >>"$REPORT"
  else
    echo "FAIL $file" >>"$REPORT"
    FAIL=$((FAIL + 1))
  fi
done < <(find . -name '*.php' \
  ! -path './vendor/*' \
  ! -path './.git/*' \
  -print0)

{
  echo "---"
  echo "Files checked: $COUNT"
  echo "Failures: $FAIL"
  echo "Generated: $(date -u +%Y-%m-%dT%H:%M:%SZ)"
} >>"$REPORT"

echo "Syntax report: $REPORT ($COUNT files, $FAIL failures)"
exit "$FAIL"
