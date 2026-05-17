# Migration guide — v4.0.0 (forks & deployments)

For maintainers upgrading from the legacy monolithic PHP layout to the **v4.0.0** refactor line (`refactor/v3-security` / `develop_oss_v3.0`).

## Summary of breaking / behavioral changes

| Area | Before | After v4.0.0 |
|------|--------|----------------|
| Admin DB config | Inline `$servername` in each file | `require_once __DIR__ . '/../config.php'` |
| Admin login SQL | String concatenation | Prepared statement |
| Admin delete / parking | String-built SQL | Prepared statements |
| Login UI CSS | Duplicated inline `<style>` | `assets/css/login.css` |
| `login.html` links | `admin/`, `employee/`, `tenant/` | `Admin/`, `Employee/`, `Tenant/` |
| `Owner/*.php` | Standalone pages | **301 redirects** to Admin/Employee (see [OWNER_DEPRECATION.md](./OWNER_DEPRECATION.md)) |
| `statistics.php` | Queried `employees` table | Fixed to `employee` |

**Not changed in v4.0.0:** database schema, password hashing (still plaintext in seed), Employee/Tenant inline mysqli (see backlog).

---

## Upgrade steps

### 1. Backup

- Database dump  
- Copy of `config.php` and any local overrides  

### 2. Pull / merge

```bash
git fetch origin
git checkout refactor/v3-security   # or tag v4.0.0 when published
git pull
```

### 3. Configuration

Edit `config.php` at project root:

```php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "apartment_management";
```

Use environment-specific values; do not commit production secrets.

### 4. Database

No migration script is required for v4.0.0. Re-import only if starting fresh:

```bash
mysql -u root -p apartment_management < apartment_management\(1\).sql
```

### 5. Web server paths

- Document root must include project root (so `config.php`, `assets/`, `Admin/` resolve).
- On **Linux**, folder names are case-sensitive: use `Admin/`, not `admin/`.

### 6. Owner bookmarks

Update bookmarks or integrations that pointed to `Owner/*` — see [OWNER_DEPRECATION.md](./OWNER_DEPRECATION.md).

### 7. Validate

```bash
composer install
bash scripts/run-phase3-validation.sh
```

Complete [V3_PHASE4_SIGNOFF.md](./V3_PHASE4_SIGNOFF.md).

### 8. Optional dev tooling

| Tool | Command |
|------|---------|
| Refactor backlog | `php scripts/generate_refactor_backlog.php` |
| PHPStan level 3+ | `PHPSTAN_LEVEL=3 bash scripts/run-phpstan.sh` |

---

## Fork-specific checklist

- [ ] Update README clone URL / project name  
- [ ] Replace sample admin password in docs (do not use `12345` in production)  
- [ ] Configure PHPMailer if using `Employee/sendemail.php`  
- [ ] Remove or protect `Employee/style copy.css` if unused  
- [ ] Plan Employee/Tenant `config.php` migration (backlog)  

---

## Rollback

1. Check out previous tag or `main` commit before merge.  
2. Restore database backup if schema or data was changed manually.  
3. Restore previous `config.php`.

---

## Support

- Changes log: [V3_CHANGES.md](./V3_CHANGES.md)  
- Release notes: [RELEASE_V4.md](./RELEASE_V4.md)  
- Open issues on your fork with Phase 3 scan output attached (`docs/validation/`).
