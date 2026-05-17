#!/usr/bin/env bash
# Phase 3 — Run all mechanical validation (syntax, PHPStan, SQL security scan).
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

SUMMARY="$ROOT/docs/validation/phase3-summary.json"
mkdir -p "$ROOT/docs/validation"

SYNTAX_EXIT=0
PHPSTAN_EXIT=0
SECURITY_EXIT=0

echo "=== Phase 3.1: PHP syntax ==="
bash "$ROOT/scripts/validate-php-syntax.sh" || SYNTAX_EXIT=$?

echo ""
echo "=== Phase 3.2: PHPStan (level ${PHPSTAN_LEVEL:-2}) ==="
bash "$ROOT/scripts/run-phpstan.sh" || PHPSTAN_EXIT=$?

echo ""
echo "=== Phase 3.3: SQL security scan ==="
php "$ROOT/scripts/security-scan-sql.php" || SECURITY_EXIT=$?

# Optional grep fallback (documented in V3_PHASE3_VALIDATION.md)
GREP_HITS=0
if command -v rg >/dev/null 2>&1; then
  GREP_HITS=$(rg -n '(SELECT|INSERT|UPDATE|DELETE).*\\\$' --glob '*.php' --glob '!vendor/**' . 2>/dev/null | wc -l | tr -d ' ')
elif command -v grep >/dev/null 2>&1; then
  GREP_HITS=$(grep -rEn '(SELECT|INSERT|UPDATE|DELETE).*\\$' --include='*.php' . 2>/dev/null | grep -v vendor | wc -l | tr -d ' ' || true)
fi

OVERALL=0
[[ $SYNTAX_EXIT -eq 0 && $PHPSTAN_EXIT -eq 0 && $SECURITY_EXIT -eq 0 ]] || OVERALL=1

cat >"$SUMMARY" <<EOF
{
  "generated_at": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
  "syntax": { "exit_code": $SYNTAX_EXIT, "report": "docs/validation/syntax-report.txt" },
  "phpstan": { "exit_code": $PHPSTAN_EXIT, "level": ${PHPSTAN_LEVEL:-2}, "report": "docs/validation/phpstan-report.txt" },
  "security_sql": { "exit_code": $SECURITY_EXIT, "report": "docs/validation/security-sql-report.json" },
  "grep_fallback_hits": $GREP_HITS,
  "overall_pass": $([ $OVERALL -eq 0 ] && echo true || echo false)
}
EOF

echo ""
echo "Summary: $SUMMARY"
echo "Overall: $([ $OVERALL -eq 0 ] && echo PASS || echo FAIL)"
exit $OVERALL
