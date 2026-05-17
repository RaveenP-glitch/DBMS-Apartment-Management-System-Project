# Phase 4 — Human spot-check (sign-off)

Minimal manual testing before tagging **v4.0.0**. Use a **staging** database (copy of production or seed from `apartment_management(1).sql` with test rows).

**Tester:** _______________  
**Date:** _______________  
**Environment:** XAMPP / MAMP / WAMP — PHP _____ MySQL _____  
**Branch / commit:** _______________

---

## Staging database setup

1. Create database `apartment_management_staging` (or use separate MySQL instance).
2. Import `apartment_management(1).sql`.
3. Point `config.php` at staging credentials (do not commit production passwords).
4. Document test accounts used below.

| Role | Username | Password (staging only) |
|------|----------|-------------------------|
| Admin | | |
| Employee | | |
| Tenant | | |

---

## Sign-off checklist

Mark **Pass / Fail / N/A** and add notes for any failure.

### Authentication

| ID | Test | Steps | Result | Notes |
|----|------|-------|--------|-------|
| S4-01 | Landing | Open `index.html` | ☐ P ☐ F | |
| S4-02 | Role picker | `login.html` → Admin / Employee / Tenant | ☐ P ☐ F | Paths `Admin/`, `Employee/`, `Tenant/` |
| S4-03 | Admin login | Valid credentials → `admin_dashboard.php` | ☐ P ☐ F | |
| S4-04 | Admin login fail | Wrong password shows error, no redirect | ☐ P ☐ F | |
| S4-05 | Employee login | Valid → `empdashboard.php` | ☐ P ☐ F | |
| S4-06 | Tenant login | Valid → `tenant_dashboard.php` | ☐ P ☐ F | |
| S4-07 | Owner redirect | `Owner/owner_login.php` → Admin login | ☐ P ☐ F | 301 deprecated |

### Payments & fees

| ID | Test | Steps | Result | Notes |
|----|------|-------|--------|-------|
| S4-08 | Admin fees view | `Admin/fees.php` loads table | ☐ P ☐ F | |
| S4-09 | Tenant maintenance | `Tenant/maintanencefee.php` → submit flow | ☐ P ☐ F | |
| S4-10 | Fee record | Confirm row in `maintenance_payment` after submit | ☐ P ☐ F | |

### Delete & terminate

| ID | Test | Steps | Result | Notes |
|----|------|-------|--------|-------|
| S4-11 | Admin delete | `manage_data.php` → delete test row (employee/tenant) | ☐ P ☐ F | Use disposable test ID |
| S4-12 | Tenant terminate | `Tenant/terminate.php` behavior unchanged | ☐ P ☐ F | |
| S4-13 | Root terminate | `terminate.php` if used by your deployment | ☐ P ☐ N/A | |

### Complaints & enquiries

| ID | Test | Steps | Result | Notes |
|----|------|-------|--------|-------|
| S4-14 | Tenant complaint | `Tenant/complaintraise.php` submit | ☐ P ☐ F | |
| S4-15 | Admin complaints | `Admin/complaints.php` lists new complaint | ☐ P ☐ F | |
| S4-16 | Public enquiry | `enquire.php` submit → `Admin/viewenq.php` | ☐ P ☐ F | |

### Email (if enabled)

| ID | Test | Steps | Result | Notes |
|----|------|-------|--------|-------|
| S4-17 | PHPMailer config | `Employee/sendemail.php` or configured path | ☐ P ☐ F ☐ N/A | Requires SMTP setup |
| S4-18 | Send test mail | Trigger one notification | ☐ P ☐ F ☐ N/A | |

### Admin refactors (V3)

| ID | Test | Steps | Result | Notes |
|----|------|-------|--------|-------|
| S4-19 | Parking slots | `Admin/parkingslot.php` shows 20 slots | ☐ P ☐ F | |
| S4-20 | Export | `Admin/export.php` downloads CSV | ☐ P ☐ F | |

---

## Phase 3 gate (must pass before sign-off)

```bash
bash scripts/run-phase3-validation.sh
```

| Check | Pass? |
|-------|-------|
| Syntax (`syntax-report.txt`) | ☐ |
| PHPStan level ___ (default 2) | ☐ |
| SQL security scan (no unresolved critical) | ☐ |

Attach or link: `docs/validation/phase3-summary.json`

---

## Sign-off decision

| Decision | Sign |
|----------|------|
| **Approved for v4.0.0 tag** | _______________ |
| **Blocked** — issues: | |

Blocked items → file GitHub issues or add to `docs/refactor_backlog.json` via `php scripts/generate_refactor_backlog.php`.

---

## Related

- Baseline (Phase 0): [V3_PHASE0.md](./V3_PHASE0.md) §3.3  
- Changelog: [V3_CHANGES.md](./V3_CHANGES.md)  
- Release: [RELEASE_V4.md](./RELEASE_V4.md)
