# V3.0 Changes Log

Living document for the **semi-automated V3 refactor** of the Apartment Management System.  
Update this file whenever you complete a V3 task, merge a PR, or finish an AI-assisted batch.

| Meta | Value |
|------|--------|
| **Branch** | `refactor/v3-security` (from `develop_oss_v3.0`) |
| **Approach** | V3.0 — human review + AI/IDE assistance |
| **Last updated** | 2026-05-17 |
| **Related docs** | [V3_PHASE0.md](./V3_PHASE0.md) · [PHP_INVENTORY.md](./PHP_INVENTORY.md) · [OWNER_DEPRECATION.md](./OWNER_DEPRECATION.md) · [refactor_backlog.json](./refactor_backlog.json) |

---

## How to update this doc

1. Add a dated entry under [Changelog](#changelog).
2. Move items from [Pending](#pending) to [Completed](#completed) when done.
3. Refresh backlog count: `php scripts/generate_refactor_backlog.php`
4. Note any behavior changes, migrations, or manual test results.

---

## Progress overview

| Phase | Description | Status |
|-------|-------------|--------|
| **0** | Scope, branch, baseline, inventory, AI rules | Done |
| **1a** | Automated refactor backlog (JSON) | Done |
| **1b** | `Admin/` → `config.php` + prepared statements (input SQL) | Done |
| **2a** | Shared login CSS (`assets/css/login.css`) | Done |
| **2b** | Deprecate `Owner/` (301 redirects) | Done |
| **1c** | `Employee/` → `config.php` + security | Pending |
| **1d** | `Tenant/` + shared root PHP → `config.php` | Pending |
| **3** | UI / path fixes, dashboard content, CSS dedupe | Pending |
| **4** | Database normalization | Pending |

**Backlog items (auto-generated):** ~120 — run `php scripts/generate_refactor_backlog.php` for current count.

---

## Changelog

### 2026-05-17 — Phase 0 (prepare)

**Goal:** Freeze scope and enable safe semi-automated refactors.

| Deliverable | Location |
|-------------|----------|
| Scope & phased plan | `docs/V3_PHASE0.md` |
| PHP file inventory (59 files) | `docs/PHP_INVENTORY.md` |
| Cursor AI rules for PHP refactors | `.cursor/rules/v3-refactor-php.mdc` |
| Working branch | `refactor/v3-security` |
| README pointer to V3 docs | `README.md` |

**Decisions recorded**

- Phase 1 scope: security + shared `config.php` (not full framework migration).
- Owner role treated as legacy; formal deprecation in a later batch.
- `login.html` folder casing (`Admin/` vs `admin/`) flagged for fix.

---

### 2026-05-17 — Automated inventory (V4-style Phase 1)

**Goal:** Machine-readable refactor backlog for AI batches and humans.

| Deliverable | Location |
|-------------|----------|
| Inventory generator | `scripts/generate_refactor_backlog.php` |
| Backlog output | `docs/refactor_backlog.json` |

**Detects:** inline `mysqli` config, SQL interpolation risk, `SELECT *`, duplicate login CSS, legacy `Owner/`, path casing issues.

```bash
php scripts/generate_refactor_backlog.php
```

---

### 2026-05-17 — Batch: `Admin/` security & config

**Goal:** All Admin database scripts use `config.php`; user input uses prepared statements where applicable.

**Scripts added**

| Script | Purpose |
|--------|---------|
| `scripts/refactor_admin_config.php` | One-off bulk replace inline DB blocks in `Admin/` |

**Files updated (16 with DB access)**

| File | Changes |
|------|---------|
| `Admin/admin_login.php` | `config.php`; prepared login; shared CSS link; escaped error output |
| `Admin/delete.php` | `config.php`; prepared `DELETE` by type + id |
| `Admin/allotparkingslot.php` | `config.php`; prepared insert (unchanged logic) |
| `Admin/parkingslot.php` | `config.php`; prepared slot lookup by number |
| `Admin/statistics.php` | `config.php`; fixed table name `employee` (was `employees`) |
| `Admin/admin_disp.php` | `config.php` |
| `Admin/complaints.php` | `config.php` |
| `Admin/count.php` | `config.php` |
| `Admin/createowner.php` | `config.php` (insert already prepared) |
| `Admin/export.php` | `config.php` |
| `Admin/exportenq.php` | `config.php` |
| `Admin/fees.php` | `config.php` |
| `Admin/get_employees.php` | `config.php` |
| `Admin/manage_data.php` | `config.php` |
| `Admin/viewenq.php` | `config.php` |

**Unchanged (no DB or empty)**

- `Admin/admin_dashboard.php` — HTML/CSS only  
- `Admin/deny.php` — empty file  

**Validation:** `php -l Admin/*.php` — no syntax errors.

---

### 2026-05-17 — Batch: shared login CSS

**Goal:** Remove duplicated inline login styles; single stylesheet.

| Deliverable | Location |
|-------------|----------|
| Shared stylesheet | `assets/css/login.css` |
| Strip inline `<style>` helper | `scripts/strip_login_inline_css.php` |

**Files updated**

| File | Changes |
|------|---------|
| `Admin/admin_login.php` | Links `../assets/css/login.css` |
| `Employee/employeelogin.php` | Inline styles removed; links shared CSS |
| `Tenant/tenant_login.php` | Inline styles removed; links shared CSS |
| `login.html` | Inline styles removed; links `./assets/css/login.css`; paths `Admin/`, `Employee/`, `Tenant/` |

**Note:** `Employee/employeelogin.php` and `Tenant/tenant_login.php` still use inline `mysqli` — backlog item for next batch.

---

### 2026-05-17 — Batch: deprecate `Owner/`

**Goal:** Legacy Owner HTTP endpoints redirect to Admin/Employee equivalents; no removal of DB `owner` table.

| Deliverable | Location |
|-------------|----------|
| Redirect map | `docs/OWNER_DEPRECATION.md` |

**All `Owner/*.php` replaced with 301 redirects**

| Legacy | Redirects to |
|--------|----------------|
| `owner_login.php` | `Admin/admin_login.php` |
| `owner_dashboard.php` | `Admin/admin_dashboard.php` |
| `viewcomplaints.php` | `Admin/complaints.php` |
| `complaintcount.php` | `Admin/count.php` |
| `feesrecieved.php` | `Admin/fees.php` |
| `roomdetails.php` | `Admin/admin_disp.php` |
| `viewempl.php` | `Admin/manage_data.php` |
| `createemployee.php` | `Employee/createtenant.php` |
| `createroom.php` | `Admin/manage_data.php` |
| `terminate.php` | `terminate.php` (root) |

---

## Completed

- [x] Phase 0 planning docs and AI rules  
- [x] Branch `refactor/v3-security`  
- [x] Refactor backlog generator + JSON output  
- [x] **Admin/** centralized DB config  
- [x] **Admin/** prepared statements on auth, delete, parking allot/slot lookup  
- [x] **Admin/** `statistics.php` table name fix  
- [x] Shared **login CSS** + `login.html` path casing  
- [x] **Owner/** deprecated via redirects + mapping doc  

---

## Pending

- [ ] **Employee/** — `config.php`, prepared statements, session checks (see backlog)  
- [ ] **Tenant/** — same as Employee  
- [ ] **Root** — `enquire.php`, `terminate.php` → `config.php`  
- [ ] Password hashing (`password_hash` / `password_verify`) + DB migration  
- [ ] Session guards on protected dashboards  
- [ ] `htmlspecialchars` on remaining echo loops (Admin tables, etc.)  
- [ ] Phase 3 UI: dashboard main sections, dedupe `Admin/style.css` vs `Employee/style.css`  
- [ ] Phase 4: DB normalization (`complaint` vs `complaints`, etc.)  
- [ ] Manual baseline checklist (§3.3 in `V3_PHASE0.md`) — record Pass/Fail  

---

## File tree (V3-added / heavily modified)

```
docs/
  V3_PHASE0.md          # Phase 0 plan
  V3_CHANGES.md         # This file
  PHP_INVENTORY.md      # File list by role
  OWNER_DEPRECATION.md  # Owner redirect map
  refactor_backlog.json # Auto-generated issues

scripts/
  generate_refactor_backlog.php
  refactor_admin_config.php
  strip_login_inline_css.php

assets/css/
  login.css

.cursor/rules/
  v3-refactor-php.mdc

Admin/                  # 16 PHP files → config.php
Owner/                  # 10 redirect stubs
login.html              # Shared CSS + folder paths
README.md               # Links to V3 docs
```

---

## Known issues (unchanged or noted during V3)

| Issue | Notes |
|-------|--------|
| Plaintext passwords in DB seed | Not migrated yet |
| `Employee/employeelogin.php` queries `owner` table | Pre-existing; verify intended behavior before changing |
| `complaint` + `complaints` tables | Phase 4 |
| `Admin/deny.php` empty | No behavior |
| Backlog false positives | `sql_interpolation_risk` may flag files that use `$_POST` and SQL in same file but already use prepares |

---

## Next suggested batch

1. Run `php scripts/generate_refactor_backlog.php`  
2. Refactor `Employee/*.php` (mirror Admin pattern)  
3. Refactor `Tenant/*.php` and root `enquire.php` / `terminate.php`  
4. Update this doc + move Pending → Completed  

---

*Maintainers: append new dated sections above the oldest entry in [Changelog](#changelog); do not delete history unless consolidating releases.*
