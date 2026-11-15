# Demo Institute of Engineering — Institutional Parent Portal

> **Contineo Platform Recreation** migrated to **Java 21 / Spring Boot 3.3.x** and **React 18 / TypeScript SPA**.  
> **DISCLAIMER:** This project is a demonstration learning recreation using purely fictional data and placeholder branding. It carries a visible `DEMO – fictional data` badge in the footer of every page. No real institutional credentials or private student data are collected or reproduced.

---

## Architecture Overview

```
                                      ┌──────────────────────────────────────────────┐
                                      │  React 18 + TypeScript SPA (Vite / Port 5173)│
                                      │  - Pixel-matched Contineo platform styling   │
                                      │  - USN + Day/Month/Year dropdown login       │
                                      │  - Term sub-portals (/parentseven/, etc.)    │
                                      │  - Failed Login modal with live attempts     │
                                      │  - Recharts attendance & CIE scaling         │
                                      └──────────────────────┬───────────────────────┘
                                                             │ REST API Requests
                                                             ▼
┌────────────────────────────────────────────────────────────────────────────────────┐
│ Spring Boot 3.3.5 Backend (Java 21 / Port 8080)                                    │
│ - Spring Security 6 with stateless JWT authentication & CORS                       │
│ - BCrypt password hashing & real calendar Date of Birth validation                │
│ - Bucket4j / concurrent rate limiting (5 failed attempts per username+IP per 15m)  │
│ - Spring Data JPA + Flyway migrations (V1 schema, V2 seed)                         │
│ - Cross-student access control (returns HTTP 403 Forbidden on mismatch)            │
│ - Serves REST API (/api/v1) and embedded production SPA (/index.html)              │
└────────────────────────────────────────────┬───────────────────────────────────────┘
                                             │
                      ┌──────────────────────┴──────────────────────┐
                      ▼                                             ▼
           H2 Embedded (MySQL Mode)                         MySQL 8.0 (Docker)
        (Zero-config local development)               (Optional containerized DB)
```

---

## Technology Stack

- **Backend**:
  - Java 21 (OpenJDK 21)
  - Spring Boot 3.3.5
  - Spring Security 6 (Stateless JWT, BCrypt, Cross-student authorization)
  - Spring Data JPA & Hibernate 6
  - Flyway 10 (Automated SQL migrations)
  - H2 Database (MySQL compatibility mode) / MySQL 8
  - Springdoc OpenAPI 2.6 (Swagger UI at `/swagger-ui.html`)
  - JUnit 5 & Mockito test suite
- **Frontend**:
  - React 18.3 & React DOM
  - TypeScript 5.6 (with strict type-checking enabled)
  - Vite 5.4
  - React Router v6
  - Bootstrap 5.3 & react-bootstrap
  - Recharts (Interactive attendance visualization)
  - Lucide React icons
  - Axios typed client
- **Reference Legacy Code**:
  - Archived in `/legacy-php` for parity auditing.

---

## Quick Start

### 1. Prerequisites
- **Java 21**: installed at `/opt/homebrew/opt/openjdk@21` (or in `$JAVA_HOME`).
- **Node.js**: v18+ and npm.
- **Maven**: 3.8+.

### 2. Run Both Backend & Frontend
Simply execute the launcher script:
```bash
./run.sh
```

This will:
1. Free ports 8080 and 5173 if busy.
2. Start the Spring Boot backend on `http://localhost:8080`.
3. Start the Vite dev server with proxy on `http://localhost:5173`.

Alternatively, run separately:
```bash
# Terminal 1 - Backend
cd backend
export JAVA_HOME=/opt/homebrew/opt/openjdk@21
export PATH="$JAVA_HOME/bin:$PATH"
mvn spring-boot:run

# Terminal 2 - Frontend
cd frontend
npm run dev
```

---

## Demo Credentials

### Parent / Student Login

#### Tenant 1: Demo Institute of Engineering (`die`) — Contineo Classic Blue Theme
- **Login Schema**: USN + Date of Birth (Day / Month / Year dropdowns)
- **Primary Demo Account** (Multi-child sibling family):
  - **Username (USN)**: `1DM24CS001`
  - **DOB**: Day: `15`, Month: `Apr`, Year: `2005`
  - **Ward**: Aarav Sharma (4th Sem CSE)
  - **Linked Sibling**: Ananya Sharma (2nd Sem ECE, USN: `1DM24EC002`) — switchable instantaneously via top navbar dropdown!
- **Attendance Shortage Demo Account**:
  - **Username (USN)**: `1DM24EC001`
  - **DOB**: Day: `10`, Month: `Jan`, Year: `2005` (Triggers critical shortage banner `<75%`)

#### Tenant 2: Apex Institute of Technology (`ait`) — Modern Emerald Theme
- **Login Schema**: USN + Password
- **Student Account**:
  - **Username (USN)**: `2AT24AI001`
  - **Password**: `Student@123`
  - **Student**: Dev Malhotra (6th Sem AI & ML)

### Institutional Administrator Login
- **DIE Super Admin**:
  - **Username**: `admin`
  - **Password**: `Admin@123`
- **AIT Super Admin**:
  - **Username**: `apex_admin`
  - **Password**: `Admin@123`

---

## Preserved Business Rules & Algorithms

1. **Date of Birth Calendar Validation**:
   - Rejects non-existent calendar dates (such as February 31st or February 29th on non-leap years) before hashing.
   - Normalizes valid dates to standard ISO `YYYY-MM-DD` and verifies against the stored BCrypt hash.
2. **Attendance Percentage & Shortage Alert Threshold**:
   - $\text{Attendance } \% = \frac{\sum \text{attended}}{\sum \text{conducted}} \times 100$
   - Any subject or overall aggregate $< 75.0\%$ triggers an institutional shortage warning badge.
3. **CIE Scaling Formula**:
   - $\text{CIE Total} = \frac{\text{CIE-1} + \text{CIE-2}}{2} + \text{Assignment-1} + \text{Assignment-2}$ (capped at 50 marks).
   - Minimum 20.0 marks required for semester exam eligibility.
4. **Rate Limiting**:
   - Maximum 5 failed attempts per USN + IP address per 15-minute window.
   - Live attempts remaining count returned to client on failure.
   - 5 failed attempts locks the account temporarily.

---

## REST API Endpoints

| Method | Endpoint | Description | Access |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/v1/auth/login` | Authenticate with USN + DOB or password | Public |
| `POST` | `/api/v1/auth/forgot-password`| 3-step OTP password reset | Public |
| `GET` | `/api/v1/auth/me` | Fetch authenticated user summary | Authenticated |
| `GET` | `/api/v1/public/terms` | List active academic terms | Public |
| `GET` | `/api/v1/public/term-links`| List login page term buttons | Public |
| `GET` | `/api/v1/public/notices/login`| Notice board active message | Public |
| `GET` | `/api/v1/parent/dashboard`| Aggregate dashboard metrics | Parent / Admin |
| `GET` | `/api/v1/parent/profile` | Student biodata and parent details | Parent / Admin |
| `GET` | `/api/v1/parent/attendance`| Class attendance breakdown | Parent / Admin |
| `GET` | `/api/v1/parent/cie` | Continuous Internal Assessment scores | Parent / Admin |
| `GET` | `/api/v1/parent/results` | Semester exam grades, SGPA, CGPA | Parent / Admin |
| `GET` | `/api/v1/parent/timetable` | Weekly period schedule | Parent / Admin |
| `GET` | `/api/v1/parent/fees` | Fee account balances and dues | Parent / Admin |
| `POST` | `/api/v1/parent/fees/pay` | Instant sandbox fee settlement | Parent / Admin |
| `GET` | `/api/v1/parent/fees/receipt/{id}.pdf` | PDF receipt generation (OpenPDF) | Parent / Admin |
| `GET` | `/api/v1/parent/reports/report-card.pdf`| PDF student report card & transcript | Parent / Admin |
| `GET` | `/api/v1/calendar/events` | Academic calendar schedule | Public |
| `GET` | `/api/v1/calendar/export.ics` | RFC 5545 iCalendar calendar file | Public |
| `GET` | `/api/v1/parent/leave` | Student absence requests | Parent / Admin |
| `POST` | `/api/v1/parent/leave` | Submit leave intimation | Parent / Admin |
| `GET` | `/api/v1/parent/feedback` | Parent feedback & grievance tickets | Parent / Admin |
| `POST` | `/api/v1/parent/feedback` | Submit feedback ticket | Parent / Admin |
| `GET` | `/api/v1/parent/mentors/appointments` | Faculty mentor meetings | Parent / Admin |
| `POST` | `/api/v1/parent/mentors/appointments` | Book faculty consultation | Parent / Admin |
| `GET` | `/api/v1/notifications/stream` | Server-Sent Events (SSE) notification stream | Public |
| `GET` | `/api/v1/tenant/config` | Config-driven tenant styling, rules & modules | Public |
| `PUT` | `/api/v1/admin/tenant/{id}/settings` | Live theme and copy customizer | Admin |
| `PUT` | `/api/v1/admin/tenant/{id}/module` | Enable/disable modules & custom labels | Admin |
| `PUT` | `/api/v1/admin/tenant/{id}/rules` | Custom academic shortage & CIE rules | Admin |
| `GET` | `/api/v1/admin/audit-logs` | Security audit trail of login activities | Admin |
