# Software Requirements Specification (SRS) — Complete (Part 1 & Part 2)
**Project:** Face Recognition Attendance System  
**Document Version:** 1.1.0  
**Date:** October 2026  
**Target Environment:** PHP 8.2 (XAMPP), MySQL/MariaDB, Native HTML5/CSS3/Vanilla JS (`face-api.js`)  

---

## 1. Preface

### 1.1 Purpose of Document
This Software Requirements Specification (SRS) document details the functional and non-functional requirements for the **Face Recognition Attendance System**. It serves as the primary formal contract between stakeholders, system architects, software developers, UI/UX designers, and quality assurance engineers for the academic Software Engineering course project at Misr International University (MIU).

### 1.2 Intended Readers
- **Course Evaluators / Professors / TAs:** To assess system compliance against academic rubrics and Software Engineering principles.
- **Software Architects & Developers:** To guide architectural layout, database schema design, and backend/frontend implementation.
- **QA & Testing Engineers:** To formulate unit, integration, system, and manual verification test matrices.

### 1.3 Version History
| Version | Date | Author | Description |
|---|---|---|---|
| `1.0.0` | 2026-10-02 | Lead Engineering Team | Initial release of SRS Part 1 (Preface, Intro, Similar Systems, Glossary, Requirements, NFRs). |
| `1.1.0` | 2026-10-02 | Lead Engineering Team | Completed SRS Part 2 (Scenarios, Use Cases, Form-Based Specs, & Traceability Matrix). |

---

## 2. Introduction

### 2.1 Purpose & Scope
The **Face Recognition Attendance System** is an automated, web-based biometric attendance tracking platform designed for higher education and enterprise workforce environments. The system eliminates proxy attendance ("buddy punching"), reduces manual record-keeping overhead, provides server-verified biometric check-ins/check-outs, and offers HR-level reporting and automated administrative alerts.

### 2.2 System Definitions
- **Biometric Face Descriptor:** A 128-dimensional floating-point vector generated from facial landmark keypoints using local deep learning models (`face-api.js`).
- **Server-Side Verification:** A security model where Euclidean distance calculation between active descriptors and registered baseline descriptors is strictly executed on the server.
- **Liveness Challenge:** A head-turn yaw detection sequence requiring dynamic user movement before attendance verification is authorized.

### 2.3 Intended Users & Roles
1. **Administrator (Admin):** Manages user accounts, configures attendance schedules and camera parameters, reviews/approves correction requests, generates system-wide reports, and broadcasts alerts.
2. **Standard User (User / Student / Employee):** Registers baseline facial features, performs daily check-in and check-out via face scan, views personal attendance logs, and submits attendance correction requests.

### 2.4 Product Perspective
The application operates as a standalone web application hosted on Apache/XAMPP with a MariaDB database backend. The system utilizes client-side WebRTC camera streams and local `face-api.js` models for descriptor extraction, while maintaining server-enforced authentication, authorization, and data processing.

---

## 3. Similar Systems Comparison

To benchmark requirements and functionality, three commercial and open-source biometric attendance solutions were evaluated:

| Feature / Metric | **Face Recognition Attendance System** (Our System) | **Jibble Face Recognition** (Commercial Cloud) | **OpenCV Attendance System** (Python/Desktop) |
|---|---|---|---|
| **Architecture** | Web (PHP MVC + Local JS `face-api.js`) | Multi-tenant SaaS (Cloud / Mobile App) | Desktop GUI (Python Tkinter/Qt + OpenCV) |
| **Biometric Processing** | Server-verified 128-d descriptor comparison | Cloud API biometric matching | Local CPU OpenCV / dlib matching |
| **Buddy Punching Protection** | Server-enforced head-turn liveness challenge | AI Anti-Spoofing & Geofencing | Basic static photo capture (High spoof risk) |
| **Biometric Privacy** | Only 128-d descriptors stored (No raw images saved) | Encrypted cloud biometric storage | Raw image files saved to disk (Privacy risk) |
| **HR / Data Export** | CSV Export/Import with UTF-8 BOM | PDF/CSV Export + Payroll Sync | Basic CSV dump |
| **Offline Capability** | 100% Offline (Self-hosted assets/models) | Requires active internet connectivity | 100% Offline desktop application |
| **Role-Based Control** | Server-side RBAC with dynamic self-referencing menu | Enterprise RBAC with team hierarchies | Single-user admin interface |

---

## 4. Glossary of Technical Terms

| Term | Definition |
|---|---|
| **128-d Descriptor** | A vector of 128 numerical float values representing unique geometric biometric facial characteristics. |
| **Buddy Punching** | Fraudulent practice where an employee/student logs attendance on behalf of an absent colleague. |
| **CSRF** | Cross-Site Request Forgery: an attack forcing authenticated users to submit unintended HTTP requests. |
| **Euclidean Distance** | Standard straight-line distance measure between two points in Euclidean space: $d(p,q) = \sqrt{\sum_{i=1}^n (q_i - p_i)^2}$. |
| **Liveness Detection** | Algorithmic test to confirm that the input stream originates from a live human rather than a photograph or video replay. |
| **MVC** | Model-View-Controller architectural pattern isolating data access, user interface, and application workflow logic. |
| **PDO** | PHP Data Objects: database abstraction layer providing secure prepared statement interfaces. |
| **RBAC** | Role-Based Access Control: restricting system access permissions strictly based on user roles (`admin` / `user`). |
| **XSS** | Cross-Site Scripting: vulnerability allowing injection of malicious scripts into trusted web pages. |

---

## 5. Functional User Requirements (UR)

### 5.1 User Role: Administrator (Admin)
- **UR-ADMIN-01:** The Admin shall be able to log in securely using administrative credentials.
- **UR-ADMIN-02:** The Admin shall be able to view a dynamic dashboard showing real-time attendance statistics and interactive charts.
- **UR-ADMIN-03:** The Admin shall be able to create, read, update, deactivate, and soft-delete user accounts.
- **UR-ADMIN-04:** The Admin shall be able to import batch user records via CSV file with line-by-line validation reports.
- **UR-ADMIN-05:** The Admin shall be able to configure system settings (working start/end times, late grace period, recognition distance threshold, default camera settings, active weekend days, system timezone).
- **UR-ADMIN-06:** The Admin shall be able to manage academic/official holidays (create, list, delete).
- **UR-ADMIN-07:** The Admin shall be able to view, search, filter, and export all user attendance records.
- **UR-ADMIN-08:** The Admin shall be able to review pending attendance correction requests and approve or reject them with mandatory administrative notes.
- **UR-ADMIN-09:** The Admin shall be able to generate daily, weekly, monthly, department, late, and absent attendance reports, with CSV export functionality.
- **UR-ADMIN-10:** The Admin shall be able to manually trigger automated absence generation for any past working date.
- **UR-ADMIN-11:** The Admin shall be able to reset facial baseline data for users needing re-enrollment.
- **UR-ADMIN-12:** The Admin shall be able to send system-wide notifications or targeted user alerts.

### 5.2 User Role: Standard User (User)
- **UR-USER-01:** The User shall be able to register a new user account (assigned strictly role `user`).
- **UR-USER-02:** The User shall be able to log in and out securely with automated session management.
- **UR-USER-03:** The User shall be able to register their facial baseline by capturing multiple sample frames via camera stream.
- **UR-USER-04:** The User shall be able to mark attendance (check-in and check-out) via real-time camera recognition and liveness confirmation.
- **UR-USER-05:** The User shall be able to view their personal attendance history with search and status filtering options.
- **UR-USER-06:** The User shall be able to submit attendance correction requests specifying requested check-in/out timestamps and mandatory justification reasons.
- **UR-USER-07:** The User shall be able to view their user profile, update personal information, and monitor face registration status.
- **UR-USER-08:** The User shall be able to change their account password after providing their current password.
- **UR-USER-09:** The User shall be able to view internal notifications and unread alert indicators.

---

## 6. Functional System Requirements (FR)

### 6.1 Authentication & Access Control
- **FR-01 (Traceable to UR-ADMIN-01, UR-USER-01, UR-USER-02):** Password hashing via `password_hash()` and `password_verify()`.
- **FR-02 (Traceable to UR-USER-01):** Public user registration strictly forces role `user`.
- **FR-03 (Traceable to UR-ADMIN-01, UR-USER-02):** Rate-limiting login attempts (5 consecutive failures lock IP).
- **FR-04 (Traceable to UR-ADMIN-01, UR-USER-02):** Secure sessions (`httponly`, `samesite=Lax`, idle timeout).
- **FR-05 (Traceable to UR-ADMIN-01..12, UR-USER-01..09):** Server-side RBAC middleware returning HTTP 403 on authorization failure.
- **FR-06 (Traceable to UR-ADMIN-02, UR-USER-02):** Dynamic recursive navigation menus generated from `menu_items`.

### 6.2 Facial Baseline Registration & Biometric Processing
- **FR-07 (Traceable to UR-USER-03):** Client-side landmark detection and 128-d vector extraction using local `face-api.js`.
- **FR-08 (Traceable to UR-USER-03):** Baseline enrollment averaging 3–5 facial samples into one descriptor.
- **FR-09 (Traceable to UR-USER-03, UR-ADMIN-11):** Biometric descriptor stored as JSON text in `face_data` with unique `user_id` FK.
- **FR-10 (Traceable to UR-USER-03):** Zero biometric images saved; optional profile photos handled as separate file uploads.
- **FR-11 (Traceable to UR-USER-03, UR-USER-04):** Descriptors kept strictly server-private.

### 6.3 Face Verification & Attendance Marking Logic
- **FR-12 (Traceable to UR-USER-04):** Client head-turn yaw challenge (68 landmarks) before descriptor transmission.
- **FR-13 (Traceable to UR-USER-04):** Server Euclidean distance calculation between candidate and stored baseline.
- **FR-14 (Traceable to UR-USER-04):** Server match authorization when $D \le \text{recognition\_threshold}$ (default `0.6`).
- **FR-15 (Traceable to UR-USER-04):** Single check-in and check-out restriction per user per calendar date.
- **FR-16 (Traceable to UR-USER-04):** Status assignment (`Present` vs `Late`) based on DB settings rules.

### 6.4 Absence Generation & Cron Execution
- **FR-17 (Traceable to UR-ADMIN-10):** CLI `cron/generate_absences.php` script & Admin UI trigger for target dates.
- **FR-18 (Traceable to UR-ADMIN-10):** Automatic skip of configured weekends and official `holidays`.
- **FR-19 (Traceable to UR-ADMIN-10):** Idempotent creation of `Absent` records and alert notifications.
- **FR-20 (Traceable to UR-USER-04, UR-ADMIN-10):** Flagging unclosed daily check-ins as `Incomplete`.

### 6.5 Correction Requests & Workflow
- **FR-21 (Traceable to UR-USER-06):** User submission of correction requests with requested times and required reasons.
- **FR-22 (Traceable to UR-USER-06):** Strict authorization check enforcing user ownership of attendance record.
- **FR-23 (Traceable to UR-ADMIN-08):** Transactional Admin approval updating attendance, status, and generating user alerts.

### 6.6 Reporting & HR CSV Management
- **FR-24 (Traceable to UR-ADMIN-07, UR-ADMIN-09):** Real-time SQL aggregations for daily, weekly, monthly reports.
- **FR-25 (Traceable to UR-ADMIN-09):** CSV exports formatted with UTF-8 BOM (`\xEF\xBB\xBF`).
- **FR-26 (Traceable to UR-ADMIN-04):** Row-by-row CSV user import validation reporting detailed error lines.

---

## 7. Non-Functional Requirements (NFR)

- **NFR-PERF-01:** Descriptor distance computation $< 50\text{ ms}$.
- **NFR-PERF-02:** End-to-end recognition execution $< 3.0\text{ seconds}$.
- **NFR-PERF-03:** Dashboard render latency $< 500\text{ ms}$.
- **NFR-SEC-01:** PDO prepared statements only (100% SQLi protection).
- **NFR-SEC-02:** HTML escaping (`htmlspecialchars`) on all rendered output (XSS protection).
- **NFR-SEC-03:** CSRF tokens required on all state-changing POST forms.
- **NFR-SEC-04:** Zero raw biometric facial images/frames transmitted or stored on server.
- **NFR-SEC-05:** Passwords hashed with `password_hash()`.
- **NFR-USA-01:** Strict 4-color palette (`#222831`, `#393E46`, `#948979`, `#DFD0B8`) with light sand background.
- **NFR-USA-02:** Typography: self-hosted `Bricolage Grotesque` + `Figtree`.
- **NFR-USA-03:** Fully responsive from 360px to 1440px+.
- **NFR-USA-04:** 60fps micro-animations via `transform` and `opacity`.
- **NFR-REL-01:** Raw PHP errors logged to `storage/logs/` (friendly error pages to user).
- **NFR-REL-02:** DB foreign key integrity enforced with constraints.
- **NFR-MAINT-01:** Strict MVC + Repository design pattern structure.

---

## 8. Operational Scenarios (Narrative Flow)

### Scenario 1: User Face Baseline Registration
1. User logs in, navigates to "Register Face", and consents to camera access.
2. Interface displays live video inside a framed scanner container and loads `face-api.js` local models.
3. System prompts user to look directly at the camera.
4. Client detects exactly one face, extracts 3 samples, computes the average 128-d descriptor, and sends a CSRF-protected POST to `/face/store`.
5. Server validates descriptor array (128 floats), saves to `face_data` table, updates user status, and renders a success toast notification.

### Scenario 2: Standard Daily Check-In
1. User navigates to "Mark Attendance" at 08:45 AM.
2. System initializes camera stream and prompts for head-turn liveness ("Turn head LEFT").
3. User turns head left; system validates landmark yaw transition and prompts to face center.
4. Client extracts live face descriptor and POSTs to `/attendance/verify`.
5. Server fetches stored descriptor for user ID, computes Euclidean distance ($D = 0.28 \le 0.60$), matches identity, computes status (`Present` for 08:45 AM check-in), inserts attendance record, and renders green verification screen.

### Scenario 3: Daily Check-Out
1. User returns to "Mark Attendance" at 05:05 PM.
2. Live scanner confirms liveness, extracts descriptor, and posts to `/attendance/verify`.
3. Server calculates Euclidean distance ($D = 0.31 \le 0.60$), locates today's existing check-in record, updates `check_out` timestamp to 17:05:00, and displays "Check-Out Successful".

### Scenario 4: Late Arrival Processing
1. User checks in at 09:35 AM (working start 09:00 AM, late threshold 15 mins $\to$ limit 09:15 AM).
2. Server verifies face match, detects check-in time exceeds limit, sets attendance status to `Late`, creates attendance record, and dispatches a `Late` alert notification to the user.

### Scenario 5: Duplicate Check-In Attempt
1. User attempts to check in again at 10:15 AM after already checking in at 08:45 AM.
2. Server verifies face match, checks `attendance` table for `(user_id, TODAY)`, detects existing check-in record without check-out, blocks duplicate entry, and displays alert: "You have already checked in today."

### Scenario 6: Failed Biometric Recognition
1. Unauthorized individual (or unregistered face) attempts attendance using an enrolled user's account.
2. System completes liveness check and posts descriptor to server.
3. Server computes Euclidean distance ($D = 0.88 > 0.60$), rejects verification match, logs security event, and displays clear error message: "Facial verification failed. Distance threshold exceeded."

### Scenario 7: Correction Request & Admin Approval
1. User notices their check-out was missed on a previous day.
2. User submits Correction Request with requested check-out time `17:00` and reason "Camera failure during departure".
3. System inserts request with status `Pending` and alerts Admin.
4. Admin reviews request in Admin Panel, inputs review note "Verified with CCTV", and clicks "Approve".
5. Server executes DB transaction: updates attendance `check_out` time, recalculates status to `Present`, sets request status to `Approved`, and notifies user via alert.

### Scenario 8: Automated Absence Generation
1. Admin triggers "Generate Absences for Date" (or CLI script runs automatically at midnight).
2. System checks if target date is a configured weekend day or official holiday; if yes, skips execution.
3. System identifies active users with no attendance row for target working date.
4. System executes bulk insert of `Absent` attendance rows and dispatches `Absent` alert notifications to affected users.

### Scenario 9: Administrative User Creation & HR Import
1. Admin opens User Management, clicks "Add User" or "Import CSV".
2. Admin uploads `employees.csv`.
3. Server processes file row by row, validates email formats, unique identifiers, hashes default passwords, creates user entries in DB, and outputs an import summary report (e.g. "25 created, 1 skipped due to duplicate email").

### Scenario 10: Unauthorized Access Attempt
1. Authenticated user with role `user` manually types `/admin/settings` into browser address bar.
2. Server RBAC middleware intercepts request, verifies active user role is `user` (not `admin`), logs security incident, and immediately renders HTTP 403 Access Denied page.

---

## 9. Tabular Use Cases

### UC-01: User Registration
- **ID & Name:** `UC-01` — Register User Account
- **Actor:** Public Visitor
- **Pre-Conditions:** Visitor is not logged in.
- **Main Flow:**
  1. Visitor opens `/register`.
  2. Visitor submits name, email, password, phone, department, unique identifier, and optional photo.
  3. Server validates fields, enforces role `user`, hashes password, creates user record in DB.
  4. Server redirects user to login page with success flash message.
- **Alternative Flows:**
  - *A1 Validation Error:* Input fields invalid or email/identifier already exists; server re-renders form with specific error highlights.
  - *A2 Role Tampering:* POST payload contains `role=admin`; server ignores payload parameter and forces `user`.
- **Post-Conditions:** New user account created with status `active` and role `user`.

### UC-02: User Login
- **ID & Name:** `UC-02` — Authenticate User & Establish Session
- **Actor:** User / Admin
- **Pre-Conditions:** User account exists and status is `active`.
- **Main Flow:**
  1. User submits email and password on `/login`.
  2. Server verifies rate-limiting limits in `login_attempts`.
  3. Server fetches user by email, executes `password_verify()`.
  4. Server regenerates session ID, sets user session data, clears failed login attempts.
  5. Server redirects Admin to `/admin/dashboard` or User to `/user/dashboard`.
- **Alternative Flows:**
  - *A1 Invalid Credentials:* Incorrect password; system logs failure IP, increments `login_attempts`, returns error message.
  - *A2 Account Locked:* 5 consecutive failures recorded; system blocks login attempt for lockout window.
- **Post-Conditions:** Authenticated HTTP session established; dynamic menu rendered according to role.

### UC-03: Register Face Baseline
- **ID & Name:** `UC-03` — Register Facial Biometric Baseline
- **Actor:** Authenticated User
- **Pre-Conditions:** User logged in; camera accessible; local `face-api.js` models loaded.
- **Main Flow:**
  1. User navigates to `/face/register`.
  2. Video stream initializes inside scanning container.
  3. Client captures 3 valid face sample frames, extracts 128-d vectors, and averages them.
  4. Client submits descriptor payload to `/face/store` via POST.
  5. Server validates array format, inserts or updates record in `face_data` table.
- **Alternative Flows:**
  - *A1 Multiple / No Faces:* Scanner detects 0 or >1 face; UI shows visual warning banner and delays capture.
  - *A2 Camera Permission Denied:* Browser blocks WebRTC stream; UI renders explicit instructions to grant permission.
- **Post-Conditions:** 128-d descriptor stored in `face_data` table linked to user.

### UC-04: Mark Attendance via Face Scan
- **ID & Name:** `UC-04` — Mark Daily Attendance (Check-In / Check-Out)
- **Actor:** Authenticated User
- **Pre-Conditions:** User logged in; face descriptor registered; camera active.
- **Main Flow:**
  1. User opens `/attendance/scan`.
  2. UI displays head-turn liveness challenge ("Turn Head Left").
  3. User completes yaw challenge; UI confirms liveness and extracts live 128-d face descriptor.
  4. Client posts descriptor to `/attendance/verify` (the server determines check-in or check-out).
  5. Server verifies Euclidean distance ($D \le 0.60$) vs baseline descriptor.
  6. Server applies `AttendanceRules`, inserts/updates `attendance` table row, and sends back result JSON.
  7. UI renders confirmation modal with user photo, status badge, and check-in time.
- **Alternative Flows:**
  - *A1 Biometric Distance Exceeded:* $D > 0.60$; server rejects match, increments failure count, returns error.
  - *A2 Duplicate Check-In:* User already has active check-in today; system prompts for check-out instead.
  - *A3 Liveness Timeout:* User fails head-turn challenge within 10s; UI resets scanner state.
- **Post-Conditions:** Attendance record inserted/updated in database; alert created if status is `Late`.

### UC-05: Review & Approve Correction Request
- **ID & Name:** `UC-05` — Process Attendance Correction Request
- **Actor:** Administrator
- **Pre-Conditions:** Pending correction request exists in DB; Admin logged in.
- **Main Flow:**
  1. Admin opens `/admin/corrections`.
  2. Admin views pending requests table, opens review modal for a request.
  3. Admin enters decision note and clicks "Approve".
  4. Server opens DB transaction: updates attendance record check-in/out times, re-evaluates status via `AttendanceRules`, updates request status to `Approved`, records reviewer ID and review timestamp, dispatches notification alert to requesting user.
  5. Server commits transaction and returns success toast.
- **Alternative Flows:**
  - *A1 Rejection:* Admin clicks "Reject" with note; request marked `Rejected`, attendance record remains unchanged, alert dispatched.
- **Post-Conditions:** Attendance record updated inside transaction; request resolved; user notified.

### UC-06: System Absence Generation
- **ID & Name:** `UC-06` — Generate Daily Absence Records
- **Actor:** Admin / Automated Cron
- **Pre-Conditions:** Working date has concluded.
- **Main Flow:**
  1. Trigger executes `/cron/generate_absences.php?date=YYYY-MM-DD`.
  2. System checks if target date is in `holidays` or configured weekend days. If yes, terminates gracefully.
  3. System queries all active users missing an `attendance` record for target date.
  4. System inserts `Absent` attendance rows and creates `Absent` alerts for affected users.
- **Alternative Flows:**
  - *A1 Re-execution (Idempotency):* Script run a second time for same date; `INSERT IGNORE` or unique constraint prevents duplicate rows.
- **Post-Conditions:** All active unrecorded users for working date have `Absent` records.

---

## 10. Form-Based & Tabular Specifications

### 10.1 Key Function: Check-In (Mark Attendance)

| Attribute | Specification |
|---|---|
| **Function Name** | `AttendanceController::checkIn()` |
| **Description** | Processes real-time live face descriptor, performs server biometric verification, applies attendance business rules, and records check-in. |
| **Inputs** | `csrf_token` (string), `live_descriptor` (JSON array of 128 floats), `liveness_verified` (boolean). |
| **Source** | Client scanning interface (`public/assets/js/scanner.js`) via HTTPS/HTTP POST. |
| **Outputs** | JSON response: `{ success: bool, message: string, data: { status: string, check_in_time: string, user_name: string } }`. |
| **Destination** | User browser DOM result modal / Toast UI. |
| **Action** | 1. Verify CSRF token and user session.<br>2. Fetch user baseline descriptor from `face_data`.<br>3. Compute Euclidean distance $D$.<br>4. If $D > \text{threshold}$, return error response.<br>5. Check for existing attendance row today; if check-in exists, reject.<br>6. Compare current time with `working_start` + `late_threshold`; determine `Present` or `Late`.<br>7. Insert `attendance` row.<br>8. If status `Late`, insert `alerts` row.<br>9. Return JSON success response. |
| **Requires** | Active session, valid CSRF token, registered face descriptor, PDO DB connection. |
| **Pre-Conditions** | User is authenticated; user has registered face data; user has not already checked in today. |
| **Post-Conditions** | New row created in `attendance` table; optional alert created in `alerts` table. |
| **Side Effects** | Updates user dashboard status widget; increments daily present/late counters in Admin dashboard. |

### 10.2 Key Function: Register Face Baseline

| Attribute | Specification |
|---|---|
| **Function Name** | `FaceRegistrationController::store()` |
| **Description** | Validates and stores user's averaged baseline 128-dimensional facial descriptor vector. |
| **Inputs** | `csrf_token` (string), `descriptor` (JSON string containing array of 128 floats). |
| **Source** | User Face Registration UI (`app/views/user/register_face.php`) via POST. |
| **Outputs** | HTTP 302 Redirect to `/user/profile` with flash success message, or JSON error structure. |
| **Destination** | User Browser View. |
| **Action** | 1. Authenticate user session and CSRF token.<br>2. Decode and validate descriptor JSON (must be array of exactly 128 floats).<br>3. Execute PDO upsert (`INSERT ... ON DUPLICATE KEY UPDATE`) on `face_data`.<br>4. Log success event.<br>5. Redirect to profile with status confirmation. |
| **Requires** | Valid user session, CSRF token, valid 128-float payload. |
| **Pre-Conditions** | User authenticated; WebRTC camera successfully captured baseline samples. |
| **Post-Conditions** | Baseline descriptor saved in `face_data`; user profile reflects face status "Registered". |
| **Side Effects** | Enables user access to Mark Attendance features. |

### 10.3 Key Function: Approve Correction Request

| Attribute | Specification |
|---|---|
| **Function Name** | `CorrectionController::approve()` |
| **Description** | Transactionally approves user correction request, updates attendance logs, recalculates status, and dispatches user alerts. |
| **Inputs** | `request_id` (integer), `admin_note` (string), `csrf_token` (string). |
| **Source** | Admin Correction Review Modal (`app/views/admin/corrections.php`). |
| **Outputs** | Redirect to `/admin/corrections` with success flash alert. |
| **Destination** | Admin Browser View. |
| **Action** | 1. Verify Admin session and CSRF token.<br>2. Begin PDO transaction.<br>3. Fetch correction request and lock row.<br>4. Update `attendance` record with requested check-in/out timestamps.<br>5. Execute `AttendanceRules::evaluateStatus()` to update status (`Present`/`Late`).<br>6. Set `correction_requests` status = `Approved`, `reviewed_by` = Admin ID, `reviewed_at` = NOW(), `admin_note` = input.<br>7. Insert user alert notification in `alerts` table.<br>8. Commit PDO transaction. |
| **Requires** | Admin role authorization, valid request ID, database transaction support. |
| **Pre-Conditions** | Request status must be `Pending`. |
| **Post-Conditions** | Attendance row updated; request marked `Approved`; user alert dispathed. |
| **Side Effects** | Updates user's personal attendance history and monthly summary percentages. |

---

## 11. Traceability Matrix (UR $\to$ FR $\to$ Use Case)

| User Requirement (UR) | System Requirement (FR) | Use Case (UC) | Key Module / Class |
|---|---|---|---|
| **UR-ADMIN-01** | FR-01, FR-03, FR-04, FR-05 | `UC-02` | `AuthController`, `AuthService` |
| **UR-ADMIN-02** | FR-05, FR-06 | `UC-02` | `AdminController`, `MenuBuilder` |
| **UR-ADMIN-03** | FR-05, FR-26 | `UC-07` | `UserController`, `UserRepository` |
| **UR-ADMIN-04** | FR-26 | `UC-07` | `UserController`, `CsvImportService` |
| **UR-ADMIN-05** | FR-05 | `UC-08` | `SettingController`, `SettingsService` |
| **UR-ADMIN-06** | FR-05 | `UC-08` | `SettingController`, `HolidayRepository` |
| **UR-ADMIN-07** | FR-24, FR-25 | `UC-10` | `AttendanceController`, `ReportService` |
| **UR-ADMIN-08** | FR-21, FR-23 | `UC-05` | `CorrectionController`, `AttendanceService` |
| **UR-ADMIN-09** | FR-24, FR-25 | `UC-10` | `ReportController`, `ReportService` |
| **UR-ADMIN-10** | FR-17, FR-18, FR-19 | `UC-06` | `cron/generate_absences.php`, `AttendanceService` |
| **UR-ADMIN-11** | FR-09, FR-11 | `UC-03`, `UC-07` | `UserController`, `FaceDataRepository` |
| **UR-USER-01** | FR-01, FR-02 | `UC-01` | `AuthController`, `UserService` |
| **UR-USER-02** | FR-01, FR-04 | `UC-02` | `AuthController`, `AuthService` |
| **UR-USER-03** | FR-07, FR-08, FR-09, FR-10, FR-11 | `UC-03` | `FaceRegistrationController`, `face-api.js` |
| **UR-USER-04** | FR-12, FR-13, FR-14, FR-15, FR-16 | `UC-04` | `AttendanceController`, `FaceRecognitionService` |
| **UR-USER-05** | FR-05 | `UC-04` | `AttendanceController`, `AttendanceRepository` |
| **UR-USER-06** | FR-21, FR-22 | `UC-05` | `CorrectionController`, `CorrectionRepository` |
| **UR-USER-07** | FR-05 | `UC-01`, `UC-03` | `ProfileController`, `UserRepository` |
| **UR-USER-08** | FR-01, FR-05 | `UC-02` | `ProfileController`, `UserService` |
| **UR-USER-09** | FR-05 | `UC-04`, `UC-05`, `UC-06` | `AlertController`, `AlertRepository` |
