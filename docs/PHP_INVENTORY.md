# PHP file inventory

Generated for **V3.0 Phase 0**. Update this file when adding or removing scripts.

**Legend**

- **DB:** `inline` = duplicates connection block in file; `config` = uses `require` of `config.php` (target state for Phase 1).
- **Phase 1:** Y = likely touched in security/config phase; 2 = Owner phase; 3 = UI phase.

| File | Role | Purpose | DB (current) | Phase |
|------|------|---------|--------------|-------|
| `config.php` | Shared | MySQL connection singleton | — | 1 |
| `enquire.php` | Shared | Public enquiry form → `enquiries` table | inline | 1 |
| `terminate.php` | Shared | Account/session termination (root) | inline | 1 |
| `Admin/admin_login.php` | Admin | Admin authentication | inline | 1 |
| `Admin/admin_dashboard.php` | Admin | Admin home / sidebar shell | — | 3 |
| `Admin/admin_disp.php` | Admin | Display admin-related data | inline | 1 |
| `Admin/allotparkingslot.php` | Admin | Assign parking to tenant | inline | 1 |
| `Admin/complaints.php` | Admin | View/manage complaints | inline | 1 |
| `Admin/count.php` | Admin | Counts / statistics fragment | inline | 1 |
| `Admin/createowner.php` | Admin | Create owner record | inline | 1 |
| `Admin/delete.php` | Admin | Delete records | inline | 1 |
| `Admin/deny.php` | Admin | Deny request/action | inline | 1 |
| `Admin/export.php` | Admin | Export data (CSV/etc.) | inline | 1 |
| `Admin/exportenq.php` | Admin | Export enquiries | inline | 1 |
| `Admin/fees.php` | Admin | Maintenance fees view/manage | inline | 1 |
| `Admin/get_employees.php` | Admin | API/list employees | inline | 1 |
| `Admin/manage_data.php` | Admin | Data management UI | inline | 1 |
| `Admin/parkingslot.php` | Admin | Parking slot management | inline | 1 |
| `Admin/statistics.php` | Admin | Statistics dashboard | inline | 1 |
| `Admin/viewenq.php` | Admin | View enquiries | inline | 1 |
| `Employee/employeelogin.php` | Employee | Employee authentication | inline | 1 |
| `Employee/empdashboard.php` | Employee | Employee home dashboard | — | 3 |
| `Employee/approve_request.php` | Employee | Approve service/salary request | inline | 1 |
| `Employee/createtenant.php` | Employee | Create tenant | inline | 1 |
| `Employee/delete_tenant.php` | Employee | Remove tenant | inline | 1 |
| `Employee/deny_request.php` | Employee | Deny request | inline | 1 |
| `Employee/edit_tenant.php` | Employee | Edit tenant details | inline | 1 |
| `Employee/export.php` | Employee | Export data | inline | 1 |
| `Employee/export_data.php` | Employee | Export handler | inline | 1 |
| `Employee/fees.php` | Employee | Fees view | inline | 1 |
| `Employee/parking.php` | Employee | Parking management | inline | 1 |
| `Employee/sendemail.php` | Employee | Email notifications (PHPMailer) | inline | 1 |
| `Employee/services.php` | Employee | Services management | inline | 1 |
| `Employee/terminate.php` | Employee | Terminate employee-related session | inline | 1 |
| `Employee/totalcomplaint.php` | Employee | Complaint totals | inline | 1 |
| `Employee/viewcomplaints.php` | Employee | List complaints | inline | 1 |
| `Employee/viewrequest.php` | Employee | View service requests | inline | 1 |
| `Employee/viewtenant.php` | Employee | List/view tenants | inline | 1 |
| `Tenant/tenant_login.php` | Tenant | Tenant authentication | inline | 1 |
| `Tenant/tenant_dashboard.php` | Tenant | Tenant home | — | 3 |
| `Tenant/signup.php` | Tenant | Registration form | inline | 1 |
| `Tenant/process_signup.php` | Tenant | Registration handler | inline | 1 |
| `Tenant/complaintraise.php` | Tenant | Raise complaint | inline | 1 |
| `Tenant/maintanencefee.php` | Tenant | Maintenance fee UI | inline | 1 |
| `Tenant/submit_maintenance_fee.php` | Tenant | Fee payment handler | inline | 1 |
| `Tenant/parkingslotallot.php` | Tenant | View allotted parking | inline | 1 |
| `Tenant/requestservice.php` | Tenant | Request service form | inline | 1 |
| `Tenant/submit_request.php` | Tenant | Service request handler | inline | 1 |
| `Tenant/terminate.php` | Tenant | Tenant logout/terminate | inline | 1 |
| `Owner/owner_login.php` | Owner | Owner authentication (legacy) | inline | 2 |
| `Owner/owner_dashboard.php` | Owner | Owner dashboard (legacy) | — | 2 |
| `Owner/complaintcount.php` | Owner | Complaint counts | inline | 2 |
| `Owner/createemployee.php` | Owner | Create employee | inline | 2 |
| `Owner/createroom.php` | Owner | Create room | inline | 2 |
| `Owner/feesrecieved.php` | Owner | Fees received view | inline | 2 |
| `Owner/roomdetails.php` | Owner | Room details | inline | 2 |
| `Owner/terminate.php` | Owner | Terminate session | inline | 2 |
| `Owner/viewcomplaints.php` | Owner | View complaints | inline | 2 |
| `Owner/viewempl.php` | Owner | View employees | inline | 2 |

**Counts:** Admin 18 · Employee 18 · Tenant 11 · Owner 11 · Shared 3 · **Total 59**
