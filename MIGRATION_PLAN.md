
# MIGRATION_PLAN.md — PHP Parent Portal to Config-Driven, Multi-Tenant Spring Boot 3 + React TypeScript

Comprehensive architecture audit, database mapping, business rule catalog, and implementation blueprint for migrating the Demo Institute of Engineering Parent Portal (Contineo platform recreation) to **Java 21 / Spring Boot 3.3.x** and **React 18 / TypeScript SPA** with **Multi-Tenancy, Dynamic Config, Mobile-First UX, and Advanced Parent Features**.

---


## 1. Inventory of Legacy PHP Assets & Modern Equivalents

### 1.1 Public & Parent Pages


| Legacy PHP Page | Legacy Route | Target Spring Boot Controller | Target React TSX Page / Component | Key Dynamic Enhancements |
| :--- | :--- | :--- | :--- | :--- |
| `index.php` | `/`, `/?term=...` | `PublicController`, `TenantController` | `LoginPage.tsx` | Schema-driven login (DOB dropdowns or Password), live theme tokens, active notices |
| `parentseven/index.php` | `/parentseven/` | `TenantController.getTenantConfig()` | Dynamic term route | Generated from DB terms, not hardcoded |
| `parentssupply/index.php` | `/parentssupply/` | `TenantController.getTenantConfig()` | Dynamic term route | Term slug scoping in request context |
| `parentssupplypart2/index.php` | `/parentssupplypart2/` | `TenantController.getTenantConfig()` | Dynamic term route | Term slug scoping in request context |
| `login.php` | `POST /login.php` | `AuthController.login()` | `LoginPage.tsx` | BCrypt verification, real calendar check, Bucket4j rate limiting, JWT cookies |
| `dashboard.php` | `/dashboard.php` | `ParentPortalController.getDashboard()` | `DashboardPage.tsx` | Mobile-first cards, sibling switcher, class average comparison, SSE alerts |
| `modules/profile.php` | `?module=profile` | `ParentPortalController.getProfile()` | `ProfilePage.tsx` | Student biodata, parents' contact details, emergency phone |
| `modules/attendance.php` | `?module=attendance` | `ParentPortalController.getAttendance()` | `AttendancePage.tsx` | Monthly breakdown, Recharts bar chart, <75% shortage alert badge, class average |
| `modules/cie.php` | `?module=cie` | `ParentPortalController.getCieMarks()` | `CieMarksPage.tsx` | Continuous Internal Assessment scaling formula, 20-mark eligibility check |
| `modules/results.php` | `?module=results` | `ParentPortalController.getResults()` | `ResultsPage.tsx` | Semester exam grades, SGPA/CGPA evaluation, backlog tracker, PDF export |
| `modules/timetable.php` | `?module=timetable` | `ParentPortalController.getTimetable()` | `TimetablePage.tsx` | Weekly period schedule grid Monday–Saturday with room & faculty |
| `modules/announcements.php` | `?module=announcements` | `ParentPortalController.getAnnouncements()` | `AnnouncementsPage.tsx` | Circulars with category filters, attachments, and read receipts |
| `modules/fees.php` | `?module=fees` | `ParentPortalController.getFees()` | `FeesPage.tsx` | Tuition/lab breakdown, sandbox payment simulation, downloadable PDF receipts |
| `modules/change_password.php` | `?module=change_password` | `ParentPortalController.changePassword()` | `ChangePasswordPage.tsx` | Current password verification, new password submission with confirmation |
| `forgot_password.php` | `/forgot_password.php` | `AuthController.forgotPassword()` | `ForgotPasswordPage.tsx` | 3-step OTP recovery flow with on-screen demo OTP |
| `logout.php` | `/logout.php` | `AuthController.logout()` | Top navbar logout | Invalidate JWT, clear local state |
| *New* | N/A | `ParentPortalController.getCalendar()` | `CalendarPage.tsx` | Academic calendar with exams, holidays, and `.ics` export |
| *New* | N/A | `MessagingController` | `MessagingPage.tsx` | Parent-mentor direct consultation & appointment booking |
| *New* | N/A | `LeaveController` | `LeaveRequestPage.tsx` | Online absence intimation and medical leave submission |
| *New* | N/A | `FeedbackController` | `FeedbackPage.tsx` | Parent grievance and institutional feedback form |
| *New* | N/A | `PdfReportController` | Downloadable PDF | PDF Report Card (`/api/v1/parent/reports/report-card.pdf`) |

### 1.2 Administrative Console (`/admin`)

| Administrative Function | Legacy PHP File | Target Spring Controller | Target React Page |
| :--- | :--- | :--- | :--- |
| Overview & Shortage Dashboard | `admin/index.php` | `AdminController.getStats()` | `AdminDashboardPage.tsx` |
| Student & Parent Directory | `admin/students.php`, `parents.php` | `AdminController.listStudents()` | `AdminStudentsPage.tsx` |
| Notice Board & Announcements | `admin/notices.php`, `announcements.php` | `AdminController.updateNotice()` | `AdminNoticesPage.tsx` |
| Security Audit Logs | `admin/logs.php` | `AdminController.getLogs()` | `AdminLogsPage.tsx` |
| *New* Branding & Theme Customizer | N/A | `TenantAdminController.updateTheme()` | `AdminThemePage.tsx` (Live visual preview) |
| *New* Dynamic Module Registry | N/A | `TenantAdminController.updateModules()` | `AdminModulesPage.tsx` |
| *New* Rules Engine Configurator | N/A | `TenantAdminController.updateRules()` | `AdminRulesPage.tsx` |

---

## 2. Target Database Schema (Multi-Tenant & Config-Driven)

### Core Entities & Relationships

1. **`tenants`**: `id`, `slug` (`die`, `ait`), `name`, `code`, `is_active`, `created_at`.
2. **`tenant_settings`**: `id`, `tenant_id`, `theme_json` (primary color, secondary color, background colors, fonts, border-radius, logo crest), `welcome_text`, `notice_text`, `footer_text`, `contact_email`, `contact_phone`.
3. **`login_schemas`**: `id`, `tenant_id`, `identifier_field` (`USN`, `EMAIL`, `PHONE`), `credential_type` (`DOB_DROPDOWN`, `PASSWORD`, `OTP`), `rate_limit_max` (5), `lockout_minutes` (15).
4. **`modules` & `tenant_modules`**: Dynamic registry mapping portal features (`attendance`, `cie`, `results`, `timetable`, `fees`, `calendar`, `announcements`, `messaging`, `leave`, `feedback`) to tenants with custom labels, order, and enabled status.
5. **`rules_config`**: `id`, `tenant_id`, `shortage_threshold` (e.g. 75.0), `cie_max_marks` (50.0), `cie_min_eligibility` (20.0), `grading_scale_json`.
6. **Operational Tables (all scoped with `tenant_id`)**:
   - `roles`, `departments`, `courses`, `terms`, `users`, `parents`, `students`, `subjects`, `attendance`, `internal_marks`, `examination_results`, `timetable`, `announcements`, `fee_records`.
7. **Advanced Feature Tables**:
   - `calendar_events`: `id`, `tenant_id`, `title`, `event_type` (`EXAM`, `HOLIDAY`, `ACADEMIC`, `FEE_DUE`), `start_date`, `end_date`, `description`.
   - `leave_requests`: `id`, `tenant_id`, `student_id`, `parent_id`, `start_date`, `end_date`, `reason`, `status` (`PENDING`, `APPROVED`, `REJECTED`).
   - `feedback_tickets`: `id`, `tenant_id`, `student_id`, `parent_id`, `category`, `subject`, `message`, `status` (`OPEN`, `RESOLVED`).
   - `mentor_appointments`: `id`, `tenant_id`, `student_id`, `faculty_name`, `appointment_time`, `mode` (`IN_PERSON`, `ONLINE`), `status`.
   - `payments` & `receipts`: `id`, `tenant_id`, `fee_id`, `student_id`, `transaction_ref`, `amount`, `payment_method`, `pdf_url`.
   - `notifications`: `id`, `tenant_id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`.
   - `login_logs` & `audit_log`: `id`, `tenant_id`, `username`, `ip_address`, `status`, `failure_reason`, `created_at`.

---

## 3. Preserved Institutional Business Rules

1. **Date of Birth Authentication**:
   - DOB normalized to ISO `YYYY-MM-DD`. Validated using real calendar calculations (e.g., rejecting February 31st and Feb 29 on non-leap years).
2. **Attendance Percentage & Shortage Warning**:
   - $\text{Attendance } \% = \frac{\sum \text{classes\_attended}}{\sum \text{classes\_conducted}} \times 100$
   - If $\% < \text{shortage\_threshold}$ (default 75.0%), trigger `<75% Shortage Alert` (badge: Red/Warning).
3. **CIE Scaling Formula**:
   - $\text{CIE Total} = \frac{\text{CIE-1} + \text{CIE-2}}{2} + \text{Assign-1} + \text{Assign-2}$ (Capped at 50 marks).
   - Minimum 20.0 marks required for semester exam eligibility.
6. **Multi-Child Sibling Resolution**:
   - Parent accounts linked to multiple students resolve current active student from request context or header (`X-Student-Id`).

---

## 4. Multi-Tenant Seed Design

### Tenant 1: Demo Institute of Engineering (`slug: die`)

- **Theme**: Institutional Navy (`#242466`) & Cyan (`#b3dff8`), classic traditional crest.
- **Login Schema**: Student USN + Date of Birth dropdowns (Day, Month, Year).
- **Sub-portals**: Even Term 2026, Supply Term 2025-2026, Supply Part 2.
- **Students**: 30 students across CSE, ECE, MECH.
- **Demo Parent with Siblings**: Parent Rajesh Sharma has two enrolled children:
  - Aarav Sharma (`1DM24CS001`, 4th Sem CSE, DOB: 15-Apr-2005)
  - Ananya Sharma (`1DM24EC002`, 2nd Sem ECE, DOB: 22-Aug-2005)

### Tenant 2: Apex Institute of Technology (`slug: ait`)

- **Theme**: Modern Emerald (`#065f46`) & Amber (`#d97706`), modern crest.
- **Login Schema**: Student Roll No / USN + Password.
- **Sub-portals**: Spring Term 2026, Summer Fast-Track 2026.
- **Students**: 30 students across AI, Data Science, Robotics.
