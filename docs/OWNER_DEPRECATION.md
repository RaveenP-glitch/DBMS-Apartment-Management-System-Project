# Owner role deprecation

The **Owner** user type is deprecated. Admin workflows in `Admin/` cover management tasks previously split across Owner.

Direct access to `Owner/*.php` redirects to the mapped replacement below.

| Legacy file | Redirect target | Notes |
|-------------|-----------------|-------|
| `Owner/owner_login.php` | `Admin/admin_login.php` | Use Admin login |
| `Owner/owner_dashboard.php` | `Admin/admin_dashboard.php` | |
| `Owner/viewcomplaints.php` | `Admin/complaints.php` | |
| `Owner/complaintcount.php` | `Admin/count.php` | Totals / counts |
| `Owner/feesrecieved.php` | `Admin/fees.php` | Maintenance fees |
| `Owner/roomdetails.php` | `Admin/admin_disp.php` | Tenant / manager display |
| `Owner/viewempl.php` | `Admin/manage_data.php` | Employees & tenants |
| `Owner/createemployee.php` | `Employee/createtenant.php` | Closest employee CRUD |
| `Owner/createroom.php` | `Admin/manage_data.php` | No dedicated Admin room UI |
| `Owner/terminate.php` | `terminate.php` | Shared terminate script |

Removal of the `owner` database table is **not** part of this change; only HTTP entry points are redirected.
