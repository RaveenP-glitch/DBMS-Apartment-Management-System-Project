# Release v4.0.0

**Apartment Management System** — security and structure refactor (V3 semi-automated process, v4.0.0 release tag).

| Field | Value |
|-------|--------|
| **Tag** | `v4.0.0` |
| **Branch** | `refactor/v3-security` → merge to `develop_oss_v3.0` / `main` |
| **Date** | 2026-05-17 (set at tag time) |

---

## Highlights

- Centralized **Admin** database access via `config.php`
- **Prepared statements** on Admin auth, delete, and parking flows
- Shared **login stylesheet** (`assets/css/login.css`)
- **Owner role deprecated** — HTTP redirects to Admin/Employee equivalents
- **Automated validation** scripts (syntax, PHPStan, SQL scan)
- **Migration guide** for forks: [MIGRATION_V4.md](./MIGRATION_V4.md)

Full history: [CHANGELOG.md](../CHANGELOG.md) · [V3_CHANGES.md](./V3_CHANGES.md)

---

## Pre-release checklist

- [ ] `bash scripts/run-phase3-validation.sh` passes (or failures documented)
- [ ] [V3_PHASE4_SIGNOFF.md](./V3_PHASE4_SIGNOFF.md) signed off
- [ ] `php scripts/generate_refactor_backlog.php` — backlog reviewed
- [ ] `php scripts/prepare-release.php` — regenerates CHANGELOG section
- [ ] README and docs links verified

---

## Tagging (maintainers)

After merge and sign-off:

```bash
git checkout refactor/v3-security   # or main after merge
git pull

# Regenerate release artifacts
php scripts/generate_refactor_backlog.php
php scripts/prepare-release.php
bash scripts/run-phase3-validation.sh

git add CHANGELOG.md docs/ scripts/ composer.json phpstan.neon.dist phpstan-baseline.neon .gitignore
git commit -m "chore: release v4.0.0 validation, changelog, and migration docs"

git tag -a v4.0.0 -m "v4.0.0: Admin config/security, shared login CSS, Owner redirects, validation tooling"
git push origin v4.0.0
```

GitHub release body: paste **## v4.0.0** section from `CHANGELOG.md`.

---

## Post-release

- Continue Employee/Tenant `config.php` migration (see backlog)
- Raise PHPStan: `PHPSTAN_LEVEL=3 bash scripts/run-phpstan.sh`
- Plan DB normalization (Phase 4 in [V3_PHASE0.md](./V3_PHASE0.md))
