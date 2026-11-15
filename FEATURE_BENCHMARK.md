# FEATURE_BENCHMARK.md — Institutional Parent Portal & ERP Benchmark

Comprehensive competitive analysis, feature matrix, prioritization scoring, and architectural implementation selection for the **Config-Driven, Mobile-First Institutional Parent Portal (Contineo Platform Recreation)**.

---

## 1. Competitive Landscape Overview

To inform the target feature set, public documentation, case studies, feature listings, and demonstration portals of **12 leading higher-education and K-12 parent portals and ERP systems** were analyzed:

1. **Contineo (Institutional Higher Education Portal)** — Deployed across premier autonomous engineering colleges (e.g., NIE Mysore, RVCE, BMSCE). Focuses on USN/DOB login, term-based academic records, continuous internal evaluations (CIE), attendance shortage warnings (<75%), and semester examination results.
2. **Camu (Digital Campus / Student & Parent Portal)** — Widely used in Indian universities. Offers native mobile apps, real-time timetable alerts, fee payment gateways, and student mentorship tracking.
3. **Fedena (Open & Enterprise SIS)** — Open-source-derived institutional ERP. Strengths: fee structure management, custom grading profiles, SMS/email notifications, and multi-child parent accounts.
4. **MyClassboard (MCB)** — Leading Indian K-12/College ERP. Strengths: Parent-teacher communication, transport GPS tracking, fee receipt downloads, and circular read acknowledgments.
5. **Entab (CampusCare)** — Established Indian school/college platform. Strengths: Online leave requests, grievance submission, event calendar, and student health records.
6. **Teachmint (Teachmint for Institutions)** — Modern SaaS ERP. Strengths: Instant announcement push notifications, classroom recordings, automated fee reminders, and mobile-first responsive dashboards.
7. **Classter (Cloud School & University Management)** — European higher-ed ERP. Strengths: Modular architecture, dynamic grading rules, multi-language support (i18n), and self-service document requests.
8. **PowerSchool Parent Portal** — US standard SIS. Strengths: Multi-student family accounts, standards-based grade books, attendance intervention flags, and mobile push notifications.
9. **Blackbaud Education Management** — Higher-ed & prep portal. Strengths: Financial aid ledger, alumni/parent directory, institutional donation portals, and role-based access security.
10. **Gradelink** — Streamlined SIS. Strengths: At-a-glance GPA/academic standing, conduct/discipline tracking, and automated weekly parent progress emails.
11. **Skyward Family Access** — US K-12/Vocational. Strengths: Food service balance tracking, graduation credit progress trackers, and granular emergency contact editors.
12. **Google Classroom (Guardian Summaries)** — Minimalist parent engagement model. Strengths: Automated weekly digest emails, missing assignment flags, and zero-learning-curve UX.

---

## 2. Feature Comparison Matrix

| Feature Domain | Feature Capability | Contineo | Camu | Fedena | MCB | Entab | Teachmint | Classter | PowerSchool | Parent Value (1–5) | Build Effort (S/M/L) | Decision |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :--- |
| **Authentication & Tenancy** | Multi-tenant dynamic branding & CSS tokens | ❌ | ✅ | ✅ | ❌ | ❌ | ✅ | ✅ | ✅ | **5** | **M** | **YES (Core)** |
| | Configurable login schema (DOB dropdowns vs Password vs OTP) | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ | ❌ | **5** | **M** | **YES (Core)** |
| | Multi-child / Sibling switcher | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | **5** | **S** | **YES** |
| | Term / Sub-portal scoping (`/parentseven`, etc.) | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | **4** | **S** | **YES** |
| **Academic Tracking** | Attendance shortage alerts (<75%) & class-average comparison | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | **5** | **S** | **YES** |
| | Subject-wise session hours & monthly trend chart | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ✅ | **5** | **M** | **YES** |
| | Internal marks (CIE) scaling formula & eligibility status | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ | ✅ | ❌ | **5** | **S** | **YES** |
| | Semester exam results, SGPA/CGPA & backlog/arrear tracker | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | **5** | **M** | **YES** |
| | PDF Report Card & Transcript generation | ❌ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | **5** | **M** | **YES** |
| **Schedule & Calendar** | Weekly period timetable grid with room & faculty | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | **4** | **S** | **YES** |
| | Interactive Academic Calendar (Holidays, Exams) with `.ics` export | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | **4** | **M** | **YES** |
| **Financial Management** | Fee ledger, tuition/lab installment breakdown | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | **5** | **S** | **YES** |
| | Sandbox online fee payment gateway & instant PDF receipt | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | **5** | **M** | **YES** |
| **Communication & Community**| Circulars & announcements with read receipts acknowledgment | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | **4** | **M** | **YES** |
| | Real-time Server-Sent Events (SSE) notification center | ❌ | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ | **5** | **M** | **YES** |
| | Parent–mentor direct messaging & office hour appointment booking | ❌ | ✅ | ❌ | ✅ | ✅ | ✅ | ✅ | ❌ | **4** | **M** | **YES** |
| | Online Leave / Absence intimation form | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | **4** | **S** | **YES** |
| | Parent Grievance & Institutional Feedback ticketing | ❌ | ❌ | ✅ | ✅ | ✅ | ❌ | ✅ | ❌ | **4** | **S** | **YES** |
| **Mobile & Accessibility** | Mobile-first 360px layout with bottom nav & touch targets | ❌ | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ | **5** | **M** | **YES** |
| | PWA (Installable, offline cache with 'last updated' stale badge) | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | **5** | **M** | **YES** |
| | Multilingual UI (English, Kannada, Hindi) | ❌ | ❌ | ✅ | ❌ | ❌ | ✅ | ✅ | ❌ | **4** | **M** | **YES** |
| | Dark Mode toggle & WCAG 2.1 AA accessibility contrast | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | **4** | **S** | **YES** |
| **Admin & Compliance** | Live Theme & Branding visual customizer with instant preview | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | **5** | **M** | **YES** |
| | Dynamic module registry & tenant feature-flag management | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | **5** | **M** | **YES** |
| | Security audit logs (login attempts, IP, timestamps, locked status) | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ | ✅ | ✅ | **5** | **S** | **YES** |
| | Live Bus / GPS Tracking | ❌ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ | **2** | **L** | **NO (Hardware dependent)** |
| | Cafeteria / Meal debit balances | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | **1** | **M** | **NO (Irrelevant to demo)** |
| | Biometric face-recognition attendance | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | **1** | **L** | **NO (Privacy/Demo safety)** |

---

## 3. Prioritized Implementation Blueprint

### Tier 1: Core Foundation & Architecture (Phase 1 & 2)
1. **Multi-Tenancy Engine**: `tenants` table with subdomain/path slug resolution, isolated tenant data filtering, and dual seed tenants (`die` and `ait`).
2. **Config-Driven Theme & Branding**: Runtime CSS variables injection (`--tenant-primary`, `--tenant-bg-left`, `--tenant-bg-right`, `--tenant-radius`).
3. **Dynamic Login Form Builder**: Configurable login schema rendering:
   - Tenant 1: Student USN + Day/Month/Year DOB dropdowns.
   - Tenant 2: Student USN + Password.
4. **Preserved Business Rules**: BCrypt password verification, real calendar date validation, 5-attempt/15-min lockout rate limiter, and cross-student/cross-tenant 403 access control.

### Tier 2: Dynamic Parent Experience & Mobile-First Shell (Phase 3)
1. **Dynamic Navigation & Module Registry**: Sidebar for desktop, 5-item bottom tab bar for mobile (`Dashboard`, `Attendance`, `CIE`, `Fees`, `More` modal).
2. **Multi-Child / Sibling Switcher**: Top navbar dropdown for parents with multiple enrolled wards.
3. **Attendance Intelligence**: Bar chart, monthly breakdown, <75% shortage alert badge, and class-average baseline comparison.
4. **CIE Scaling Grid**: Internal test calculations with formula transparency and minimum 20-mark eligibility check.
5. **Semester Results & Backlog Tracker**: Grade sheet with SGPA/CGPA and clear backlog indicator.

### Tier 3: Benchmark-Selected Engagement Features (Phase 4)
1. **Real-time Notifications via Server-Sent Events (SSE)**: Live toast alerts for newly posted announcements, attendance flags, and fee receipts without page reload.
2. **Report Card & Hall-Ticket PDF Export**: Generated dynamically via OpenPDF on `/api/v1/parent/reports/report-card.pdf`.
3. **Academic Calendar with `.ics` Sync**: Institutional events, exam schedules, and holiday dates with one-click export to Google/Apple Calendar.
4. **Sandbox Fee Payment Gateway**: Mock payment simulation with instant invoice generation, balance update, and downloadable PDF receipt.
5. **Parent–Mentor Messaging & Appointment Booking**: Contact details, faculty office hours, and consultation booking.
6. **Leave Intimation & Grievance Forms**: Online parent absence note and feedback ticket submission.
7. **PWA & Offline Cache**: Workbox service worker caching student academic records for offline consultation.
8. **Multilingual (i18n)**: Instant language toggle between English, Kannada (ಕನ್ನಡ), and Hindi (हिन्दी).
9. **Dark Mode**: High-contrast dark theme adhering to WCAG 2.1 AA.

### Tier 4: Self-Service Administration Console (Phase 5)
1. **Branding & Theme Editor with Live Preview**: Real-time institutional palette and crest editor.
2. **Module Registry & Feature Flags**: Enable/disable portal features per tenant with drag-and-drop ordering.
3. **Rules Engine Editor**: Customize attendance shortage threshold (e.g. 75% vs 80%), CIE weights, and fee grace periods.
4. **Broadcast Announcement Composer**: Schedule announcements with target audience filters.
5. **Security Audit Log Viewer**: Detailed view of all authentication attempts, client IPs, user agents, and lockout events.

---

## 4. Features Excluded & Rationale

- **Live GPS Bus Tracking**: Requires physical IoT telemetry hardware and external mapping quotas, not applicable to a self-contained demonstration portal.
- **Biometric Facial Recognition**: Poses biometric privacy safety concerns and is inappropriate for an institutional demo parent portal.
- **Cafeteria Meal Plan Debit**: Irrelevant to Indian autonomous collegiate parent workflows; priority given to tuition and laboratory fee ledgers.
