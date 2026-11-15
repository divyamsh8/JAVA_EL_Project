# Bug & Logic Audit Report

**Date**: 2026-11-15T23:12:00+05:30  
**Project**: Contineo Parent Portal (Java Spring Boot 3 + React TSX)  
**Safety Branch**: `cleanup/fitmate-removal`  

---

## 1. Summary of Audit Scope

An exhaustive static and runtime logic audit was performed across all backend controllers, services, repositories, JPA entities, security configuration, and frontend pages, components, and contexts.

| Category | Checked Items | Passed | Resolved / Patched |
| :--- | :---: | :---: | :---: |
| **Backend Core & Compile** | 106 Java classes, 30 entities, 30 repos | 106 | 0 |
| **JPA & Entity Serialization** | Lazy loading proxies, Jackson bytebuddy reflection | 30 | 4 (`@JsonIgnore` added to Payment, Leave, Feedback, Mentors) |
| **Business Logic** | Attendance div/0, CIE formula, SGPA/CGPA, Shortage | 4 | 0 (All algorithms verified) |
| **Security & Auth** | Rate limiting, BCrypt, DOB validation, IDOR 403 checks | 5 | 0 (All verified) |
| **Error Handling** | Uncaught exceptions, `@RestControllerAdvice` | 1 | 1 (Added `GlobalExceptionHandler.java`) |
| **Frontend Strict TypeScript** | 35 TSX files, Vite build | 35 | 4 (Fixed unused vars & modal props) |

---

## 2. Issues Identified, Root Causes & Fixes

### Issue 1: Lazy Hibernate Entity Serialization
- **ID**: `BUG-001`
- **Severity**: High
- **File & Line**: `Payment.java`, `FeedbackTicket.java`, `LeaveRequest.java`, `MentorAppointment.java`
- **Symptom**: `500 Internal Server Error` during serialization of newly persisted entities containing lazy relationships (`Student`, `Parent`, `FeeRecord`), with Jackson error `No serializer found for class org.hibernate.proxy.pojo.bytebuddy.ByteBuddyInterceptor`.
- **Root Cause**: Jackson's ObjectMapper attempted to serialize uninitialized Hibernate lazy-loading proxy references.
- **Fix**:
  1. Added `@JsonIgnore` to `student`, `parent`, and `feeRecord` references in corresponding entity classes.
  2. Configured `spring.jackson.serialization.fail-on-empty-beans: false` in `application.yml`.
- **Status**: **RESOLVED & VERIFIED** (Both sandbox fee payment and leave request submission serialize clean JSON with `200 OK`).

---

### Issue 2: Missing Parent Reference on Leave and Feedback Submission
- **ID**: `BUG-002`
- **Severity**: High
- **File & Line**: `ParentPortalService.java:390` & `ParentPortalService.java:426`
- **Symptom**: `500 Internal Server Error` when submitting absence notes or grievance feedback tickets.
- **Root Cause**: Database tables `leave_requests` and `feedback_tickets` have a foreign key constraint `parent_id BIGINT NOT NULL`. The builder in `ParentPortalService` set `.student(student)` but omitted `.parent(student.getParent())`.
- **Fix**: Added `.parent(student.getParent())` when constructing `LeaveRequest` and `FeedbackTicket` entities.
- **Status**: **RESOLVED & VERIFIED** (Both `POST /api/v1/parent/leave` and `POST /api/v1/parent/feedback` return `200 OK`).

---

### Issue 3: Missing Centralized Exception Handling (`@RestControllerAdvice`)
- **ID**: `BUG-003`
- **Severity**: Medium
- **File & Line**: `backend/src/main/java/com/demo/parentportal/config/GlobalExceptionHandler.java`
- **Symptom**: Unhandled backend exceptions (`IllegalArgumentException`, `SecurityException`, `AccessDeniedException`) returned non-standard Tomcat HTML/JSON errors rather than consistent `ApiResponse<T>` JSON wrappers.
- **Root Cause**: Absence of a `@RestControllerAdvice` component.
- **Fix**: Implemented `GlobalExceptionHandler` intercepting `IllegalArgumentException` (400), `SecurityException` (403), `AccessDeniedException` (403), `MethodArgumentNotValidException` (400 with field details), and general unhandled exceptions (500).
- **Status**: **RESOLVED & VERIFIED** (Tested: IDOR attempts return clean `{"success":false,"message":"Unauthorized access..."}`).

---

### Issue 4: Dual Request Support for Fee Payments (Query Params & JSON Body)
- **ID**: `BUG-004`
- **Severity**: Low
- **File & Line**: `ParentPortalController.java:234` & `dto/PaymentRequest.java`
- **Symptom**: If an API consumer sent fee payment attributes via JSON request body rather than query parameters, the endpoint rejected with missing parameter errors.
- **Root Cause**: `@RequestParam Long feeId` was mandatory on the method signature.
- **Fix**: Created `PaymentRequest` DTO and updated `payFees` to gracefully accept parameters either as URL query params or as a JSON request body.
- **Status**: **RESOLVED & VERIFIED** (Tested with both query parameters and JSON body payloads).

---

### Issue 5: Strict TypeScript Compiler Warnings & Prop Mismatches
- **ID**: `BUG-005`
- **Severity**: Low
- **File & Line**: `LoginPage.tsx`, `AdminThemePage.tsx`, `FeesPage.tsx`, `ResultsPage.tsx`
- **Symptom**: `tsc && vite build` failed during strict type-check due to unused imports (`tenantApi`, `tenantSlug`, `Clock`, `Globe`, `token`) and prop name mismatch in `FailedLoginModal` (`isOpen`/`onClose` instead of `show`/`onHide`).
- **Root Cause**: Legacy component prop signatures and strict TS flag TS6133/TS2322.
- **Fix**: Aligned `FailedLoginModal` props with `show` & `onHide`, cleaned unused imports, utilized `tenantSlug` in branding header.
- **Status**: **RESOLVED & VERIFIED** (`npm run build` succeeds with 0 errors).

---

## 3. Verified Security & Business Invariants

1. **Division by Zero Protection**: In `ParentPortalService.getAttendance()`, attendance calculation checks `totalConducted > 0` before division, returning `BigDecimal.ZERO` when no classes have occurred.
2. **CIE Scaling Invariant**: Aggregate Continuous Internal Assessment marks formula $\frac{\text{CIE1} + \text{CIE2}}{2} + \text{Assign1} + \text{Assign2}$ is capped at 50.0 marks. Eligibility flag requires $\ge 20.0$ marks.
3. **DOB Calendar Accuracy**: Invalid dates (e.g., February 31st, February 29th on non-leap years) are rejected by `LocalDate.of()` before password comparison.
4. **IDOR & Cross-Student Security**: Wards belonging to another parent account return `HTTP 403 Forbidden` (`Unauthorized access to another student's records`).
5. **Rate Limiting**: 5 failed login attempts per username + IP locks access for 15 minutes, with remaining attempts displayed in the failed login modal.
