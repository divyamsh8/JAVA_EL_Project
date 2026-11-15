# Recovery, Cleanliness, and Bug Audit Plan

**Target Branch**: `cleanup/fitmate-removal`  
**Base Repository**: `Contineo_JAVA_EL`  
**Canonical Package Name**: `com.demo.parentportal` (backend) / `contineo-parent-portal-frontend` (frontend)  
**Safety Protocol**: No destructive deletes; isolated branch work; backup created in `/_backup/`.

---

## 1. Action Items & Risk Assessment Matrix

| Step | Action Item | Description | Risk Level | Mitigation Strategy |
| :---: | :--- | :--- | :---: | :--- |
| **01** | **Safety Branch & Backup** | Branch `cleanup/fitmate-removal` created. Back up database and migration files in `_backup/`. | **Zero** | Completed prior to execution. |
| **02** | **Quarantine Management** | Create `_quarantine/`. Move any suspected non-portal files. | **Low** | No active foreign files were detected; folder remains initialized as a safety sink. |
| **03** | **Global Canonical Naming Check** | Verify all artifacts, packages, database names, and titles use canonical `parentportal` nomenclature. | **Low** | Verified: Maven artifact `parent-portal-backend`, base package `com.demo.parentportal`, package.json `contineo-parent-portal-frontend`. |
| **04** | **Backend Logic & Security Audit** | Audit Spring Security 6 config, BCrypt DOB validation, cross-student authorization, rate limiting, and JPA relationship serializability. | **Medium** | Run full test suite (`mvn clean test`) after any audit patch. |
| **05** | **Frontend Logic & Accessibility Audit** | Check TypeScript strict mode, Zod/form validations, responsive mobile layouts, PWA precaching, and language switcher. | **Low** | Execute `npx tsc --noEmit` and `npm run build`. |
| **06** | **Full Regression & End-to-End Verification** | Execute automated integration tests for both DIE and AIT tenants, PDF generation, SSE notifications, and IDOR access checks. | **Low** | Verify test assertions and command exit codes. |
| **07** | **Documentation & Final Deliverables** | Produce `BUG_REPORT.md`, `CLEANUP_SUMMARY.md`, and update `README.md` with demo credentials. | **Zero** | Accurate documentation reflecting working state. |

---

## 2. Recovery Plan Execution Phases

### Phase 2: Restore and Rename
1. **Quarantine Audit**: Confirm `_quarantine/` is ready. No foreign fitness files detected in source tree.
2. **Canonical Identifiers**:
   - Maven Group/Artifact: `com.demo.parentportal` / `parent-portal-backend`
   - Application Name: `parent-portal-backend`
   - Database Name: `portal_demo`
   - Docker Container: `portal-mysql`
   - Frontend Package: `contineo-parent-portal-frontend`
3. **Database Baseline**:
   - `V1__schema.sql`: 30 clean tables for educational portal.
   - `V2__seed.sql`: Dual tenant seed data.
   - Zero FITMATE tables or columns exist.

### Phase 3: Bug and Logic Audit
Audit the codebase against the specific checks requested:
1. **Backend Checks**:
   - Spring Security 6 configuration, CORS origins, and CSRF settings.
   - Rate limiting: Bucket4j / Concurrent attempts tracker (5 attempts per USN+IP).
   - DOB validation: Real calendar date verification (rejecting non-existent dates like 31-Feb before BCrypt check).
   - Cross-student access control: Parent restricted to their own wards (returns HTTP 403 on IDOR attempts).
   - Entity serialization: Ensure lazy Hibernate proxies use `@JsonIgnore` to prevent serialization exceptions.
   - OpenPDF downloads: Valid binary streams with `Content-Disposition` headers.
   - Calendar `.ics`: Valid RFC 5545 format with `text/calendar` media type.
2. **Frontend Checks**:
   - Strict TypeScript (`tsc --noEmit` with zero errors).
   - Dynamic tenant styling via CSS variables (`--tenant-primary`, etc.).
   - Multi-child ward switcher in top header.
   - Responsive design (360px to 1366px) with mobile bottom navigation.
   - Multilingual translations (`en`, `kn`, `hi`).

### Phase 4: Fix & Commit
- Document any findings in `BUG_REPORT.md`.
- Ensure regression tests cover any identified bugs.

### Phase 5: Verification & Deliverables
- Clean compilation and test pass on backend (`mvn -o clean test`).
- Clean build on frontend (`npm run build`).
- Output `BUG_REPORT.md` and `CLEANUP_SUMMARY.md`.
