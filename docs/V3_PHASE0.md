# V3.0 Phase 0 — Prepare (human-led)

**Status:** Complete  
**Branch for Phase 1:** `refactor/v3-security` (create from `develop_oss_v3.0` or current default)  
**Last updated:** 2026-05-17  
**Change log:** [V3_CHANGES.md](./V3_CHANGES.md) — updated as refactors land

Phase 0 does not change application behavior. It freezes scope, records baselines, inventories files, and defines rules for humans and AI during semi-automated refactoring.

---

## 1. Frozen scope

Work is split into phases. **Do not start a later phase until the current phase exit criteria are met.**

| Phase | Name | In scope | Out of scope |
|-------|------|----------|--------------|
| **1** | Security + config | Single `config.php` for all DB access; prepared statements for queries touched in Phase 1; session checks on protected pages; `htmlspecialchars` on echoed user input; env-based credentials (optional `.env.example`) | UI redesign, framework migration |
| **2** | Owner cleanup | Decide fate of `Owner/` (remove, redirect to Admin, or document as deprecated); align README with Admin/Employee/Tenant only in `login.html` | Database normalization |
| **3** | UI / structure | Shared layout (`sidebar.html`), dedupe CSS, fix path casing (`Admin` vs `admin`), dashboard main content areas | New features |
| **4** | Database (future) | Normalization, merge `complaint` / `complaints`, migrations | — |

### Phase 1 exit criteria (next milestone)

- [ ] Every `*.php` file includes DB via `config.php` (no inline `$servername` blocks).
- [ ] Login and registration flows use prepared statements.
- [ ] Protected dashboards redirect to login when session is missing.
- [ ] Manual baseline checklist (section 3) passes on local XAMPP/MAMP.

### Explicit non-goals (all phases until re-scoped)

- Rewriting in Laravel/Slim without a separate decision.
- Changing fee amounts, complaint workflows, or role permissions without product sign-off.
- Committing real passwords or production credentials to the repo.

---

## 2. Branch strategy

| Branch | Purpose |
|--------|---------|
| `main` / `master` | Stable releases |
| `develop_oss_v3.0` | Integration branch for V3 effort (current working branch) |
| `refactor/v3-security` | **Phase 1 only** — config + security (recommended PR target) |
| `refactor/v3-owner` | Phase 2 (create when Phase 1 merges) |
| `refactor/v3-ui` | Phase 3 (create when Phase 2 merges) |

### Commands

```bash
# From repo root, with a clean or intentionally staged working tree:
git checkout develop_oss_v3.0
git pull   # if remote exists
git checkout -b refactor/v3-security

# Phase 1 work happens here; open PR back to develop_oss_v3.0 when exit criteria pass.
```

**Note:** If you already have uncommitted Phase 0 / config edits on `develop_oss_v3.0`, commit or stash them before switching, or create `refactor/v3-security` from the current HEAD so nothing is lost.

---

## 3. Baseline — environment and flows

### 3.1 Environment setup

1. Install **XAMPP**, **WAMP**, or **MAMP** (PHP 8.x + MySQL/MariaDB).
2. Clone or copy project into the web root, e.g.  
   `htdocs/DBMS-Apartment-Management-System-Project/`
3. Import database:
   - Open phpMyAdmin → create database `apartment_management` (if not exists).
   - Import `apartment_management(1).sql`.
4. Edit `config.php` if needed:

   ```php
   $servername = "127.0.0.1";
   $username = "root";
   $password = "";
   $database = "apartment_management";
   ```

5. Start Apache + MySQL; open  
   `http://localhost/DBMS-Apartment-Management-System-Project/index.html`

### 3.2 Known baseline issues (do not “fix” in Phase 0)

- `login.html` redirects to `admin/`, `employee/`, `tenant/` (lowercase) but folders are `Admin/`, `Employee/`, `Tenant/` — **breaks on Linux**; tracked for Phase 3.
- Owner role exists in `Owner/` but is **not** in `login.html` selector; README notes Owner merged with Admin in intent only.
- Plaintext passwords in DB sample data (`admin.password = '12345'`).
- Duplicate complaint tables: `complaint` and `complaints`.
- `config.php` exists but **no PHP file requires it yet** (inline `mysqli` in each script).

### 3.3 Manual regression checklist

Run before and after each phase. Mark **Pass / Fail / N/A** and date.

| ID | Flow | Entry URL | Expected result |
|----|------|-----------|-----------------|
| P0-01 | Landing | `index.html` | Page loads; Login and Enquire links work |
| P0-02 | Enquiry | `enquire.php` | Form submits; row in `enquiries` (or success message) |
| P0-03 | Admin login | `Admin/admin_login.php` | Valid admin → `admin_dashboard.php` |
| P0-04 | Admin dashboard nav | `Admin/admin_dashboard.php` | Sidebar links load (complaints, fees, parking, etc.) |
| P0-05 | Employee login | `Employee/employeelogin.php` | Valid employee → `empdashboard.php` |
| P0-06 | Employee complaints | `Employee/viewcomplaints.php` | Lists complaints |
| P0-07 | Tenant login | `Tenant/tenant_login.php` | Valid tenant → `tenant_dashboard.php` |
| P0-08 | Tenant complaint | `Tenant/complaintraise.php` | Can submit complaint |
| P0-09 | Tenant maintenance | `Tenant/maintanencefee.php` | Payment flow reachable |
| P0-10 | Tenant signup | `Tenant/signup.php` → `process_signup.php` | Registration completes |
| P0-11 | Terminate (shared) | `terminate.php` | Behaves as before (role-specific variants in subfolders) |
| P0-12 | Owner login (legacy) | `Owner/owner_login.php` | Document Pass/Fail — may be unmaintained |

### 3.4 Test credentials (from SQL seed)

| Role | Username / identifier | Password (sample DB) |
|------|------------------------|----------------------|
| Admin | `Raveen Panditha` | `12345` |
| Employee | Set in `employee` table | Per DB |
| Tenant | Per `tenant` table | Per DB |

---

## 4. PHP file inventory

**Total:** 59 PHP files (`config.php` + 58 application scripts)

See [PHP_INVENTORY.md](./PHP_INVENTORY.md) for the full table (path, role, purpose, DB inline?, notes).

### Summary by role

| Role | Folder | Count | Notes |
|------|--------|-------|-------|
| Admin | `Admin/` | 18 | Primary operator; includes dashboard, CRUD, exports |
| Employee | `Employee/` | 18 | Complaints, tenants, parking, requests, exports |
| Tenant | `Tenant/` | 11 | Login, signup, fees, complaints, services |
| Owner (legacy) | `Owner/` | 11 | Not linked from `login.html`; Phase 2 decision |
| Shared | repo root | 3 | `config.php`, `enquire.php`, `terminate.php` |

### Static / front-end assets (reference)

| File | Role |
|------|------|
| `index.html` | Public landing |
| `login.html` | Role picker → login PHP |
| `sidebar.html`, `sidebar.css`, `sidebar.js` | Shared nav fragment (partial adoption) |
| `Admin/style.css`, `Admin/styles.css` | Admin styling |
| `Employee/style.css` | Employee styling |

### Database tables (from `apartment_management(1).sql`)

`admin`, `apartment`, `complaint`, `complaints`, `employee`, `enquiries`, `hotel`, `maintenance_payment`, `owner`, `parking`, `parking_slot`, `room`, `salary_requests`, `services`, `service_requests`, `tenant`

---

## 5. Rules for AI and contributors

Canonical copy for Cursor: [.cursor/rules/v3-refactor-php.mdc](../.cursor/rules/v3-refactor-php.mdc)

### Quick rules

1. **Config:** Use `require_once __DIR__ . '/../config.php';` from role folders; `__DIR__ . '/config.php'` from repo root. Never duplicate `$servername` / `new mysqli` blocks.
2. **SQL:** Use prepared statements (`$stmt = $conn->prepare(...)`). No string concatenation of `$_POST` / `$_GET` into queries.
3. **Behavior:** Do not change business rules, fee logic, or DELETE/terminate semantics unless the task explicitly says so.
4. **Scope:** Stay within the active phase (Phase 1 = config + security only).
5. **Owner:** Do not add Owner to `login.html` or expand `Owner/` until Phase 2.
6. **Style:** Match surrounding file style (procedural PHP, inline HTML). No framework intro in Phase 1.
7. **Output:** Escape with `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` when echoing DB/user data.
8. **Sessions:** Use `session_start()` at top of protected pages; store role + user id in `$_SESSION`; redirect if missing.
9. **Paths:** Preserve folder names `Admin`, `Employee`, `Tenant` (Pascal case).
10. **PRs:** Small, reviewable diffs; one concern per commit where possible.

---

## Phase 0 completion checklist

- [x] Scope document (this file, §1)
- [x] Branch naming documented (§2); create `refactor/v3-security` locally
- [x] Baseline environment + regression checklist (§3)
- [x] File inventory (§4 + `PHP_INVENTORY.md`)
- [x] AI rules (§5 + `.cursor/rules/v3-refactor-php.mdc`)

**Next step:** Check out `refactor/v3-security` and begin **Phase 1** (config.php everywhere, then prepared statements on auth flows).

**Later phases (done as tooling/docs):** [V3_PHASE3_VALIDATION.md](./V3_PHASE3_VALIDATION.md) · [V3_PHASE4_SIGNOFF.md](./V3_PHASE4_SIGNOFF.md) · [RELEASE_V4.md](./RELEASE_V4.md) — tracked in [V3_CHANGES.md](./V3_CHANGES.md).
