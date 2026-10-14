# System Design Document (SDD)
**Project:** Face Recognition Attendance System  
**Document Version:** 1.0.0  
**Date:** October 2026  
**Target Environment:** PHP 8.2 (XAMPP), MySQL/MariaDB, Native HTML5/CSS3/Vanilla JS (`face-api.js`)  

---

## 1. System Architecture & Viewpoints

### 1.1 High-Level Architecture Overview
The system follows a strict **Model-View-Controller (MVC)** architectural pattern enhanced by the **Repository Pattern** for database abstraction. Front-end biometric feature extraction occurs entirely in the user's web browser using local `face-api.js` models, while security enforcement, Euclidean distance computation, and business rules remain strictly on the server.

```mermaid
graph TD
    subgraph ClientLayer["Client Layer (Browser)"]
        UI["HTML5 / CSS3 / Vanilla JS Views"]
        FaceApi["face-api.js (Local ML Models)"]
        Landmarks["68 Landmark Yaw Liveness Detector"]
        WebRTC["WebRTC Camera Stream"]
    end

    subgraph ServerLayer["Server Architecture (PHP 8.2 MVC)"]
        FC["public/index.php (Single Front Controller)"]
        Router["Core Router & Auth/Role Middleware"]
        Controllers["Controller Layer (Auth, User, Admin, Attendance, Reports, Corrections)"]
        Services["Service Business Logic (AuthService, AttendanceService, FaceRecognitionService, ReportService)"]
        Repos["Repository SQL Layer (UserRepository, AttendanceRepository, FaceDataRepository, SettingRepository)"]
        Models["Entity Models (User, Attendance, FaceData, CorrectionRequest, Alert, Setting, MenuItem)"]
    end

    subgraph StorageLayer["Data & Assets Layer (XAMPP / Local Disk)"]
        DB[("MariaDB Database (face_attendance)")]
        ModelsStorage["Local Models (assets/models/*.json, *.bin)"]
        UploadsStorage["File Uploads (storage/uploads/)"]
        LogsStorage["System Logs (storage/logs/)"]
    end

    WebRTC --> Landmarks
    Landmarks --> FaceApi
    FaceApi -->|"128-d Vector (POST JSON)"| FC
    UI -->|"HTTP Request / Form POST"| FC

    FC --> Router
    Router --> Controllers
    Controllers --> Services
    Services --> Repos
    Repos <--> Models
    Repos <-->|"PDO Prepared Statements"| DB

    Controllers -.->|"Logging Errors"| LogsStorage
    Controllers -.->|"Save User Photos"| UploadsStorage
    FaceApi -.->|"Load Weight Binaries"| ModelsStorage
```

### 1.2 System Context Diagram

```mermaid
graph TD
    User["Standard User (Student / Employee)"]
    Admin["System Administrator"]
    Camera["WebRTC Camera Device"]
    Cron["Windows Task Scheduler / CLI Cron"]
    System["Face Recognition Attendance System (XAMPP / PHP MVC)"]
    DB[("MariaDB Database (face_attendance)")]

    User -->|"Web Camera Stream & Live Face Scanning"| System
    User -->|"Submit Correction Requests & View History"| System
    Admin -->|"Manage Users, Configure Settings & Review Requests"| System
    Admin -->|"Generate Reports & Export HR CSV"| System
    Camera -->|"Video Stream Frames (Local Browser Memory)"| System
    Cron -->|"Trigger Daily Absence Processing"| System
    System <-->|"PDO Prepared SQL Statements"| DB
```

---

## 2. Use Case Viewpoint

```mermaid
graph LR
    subgraph SystemBoundary["Face Recognition Attendance System"]
        UC1["UC-01: Register User Account"]
        UC2["UC-02: Authenticate User & Session"]
        UC3["UC-03: Register Face Baseline"]
        UC4["UC-04: Mark Attendance (Check-In / Out)"]
        UC5["UC-05: View Attendance Records"]
        UC6["UC-06: Submit Correction Request"]
        UC7["UC-07: Admin User Management & CSV Import"]
        UC8["UC-08: Admin System Settings & Holidays"]
        UC9["UC-09: Review & Approve Correction Request"]
        UC10["UC-10: Admin Reports & CSV Export"]
        UC11["UC-11: Automated Absence Generation"]
        UC12["UC-12: System Alerts & Notifications"]
    end

    Guest["Public Visitor"] --> UC1
    Guest --> UC2

    User["Standard User"] --> UC2
    User --> UC3
    User --> UC4
    User --> UC5
    User --> UC6
    User --> UC12

    Admin["Administrator"] --> UC2
    Admin --> UC7
    Admin --> UC8
    Admin --> UC9
    Admin --> UC10
    Admin --> UC11
    Admin --> UC12
```

---

## 3. Class Design & Structure Viewpoint

```mermaid
classDiagram
    class Router {
        -array routes
        +add(method, path, handler, middlewares)
        +dispatch(uri, method)
    }

    class Controller {
        #render(view, data)
        #json(data, statusCode)
        #redirect(url)
    }

    class AuthController {
        -AuthService authService
        +login()
        +processLogin()
        +register()
        +processRegister()
        +logout()
    }

    class AttendanceController {
        -AttendanceService attendanceService
        -FaceRecognitionService faceService
        +index()
        +markView()
        +checkIn()
        +checkOut()
    }

    class AdminController {
        -UserService userService
        -ReportService reportService
        +dashboard()
    }

    class CorrectionController {
        -AttendanceService attendanceService
        +index()
        +store()
        +approve()
        +reject()
    }

    class AuthService {
        -UserRepository userRepo
        +authenticate(email, password)
        +registerUser(data)
    }

    class AttendanceService {
        -AttendanceRepository attendanceRepo
        -UserRepository userRepo
        -AlertRepository alertRepo
        +processCheckIn(userId, status)
        +processCheckOut(userId)
        +generateAbsences(date)
        +approveCorrection(requestId, note, adminId)
    }

    class FaceRecognitionService {
        -FaceDataRepository faceDataRepo
        -SettingsService settingsService
        +verifyDistance(userId, liveDescriptor) float
    }

    class UserRepository {
        +find(id) User
        +findByEmail(email) User
        +create(data) int
        +update(id, data) bool
        +delete(id) bool
    }

    class AttendanceRepository {
        +findByUserAndDate(userId, date) Attendance
        +create(data) int
        +update(id, data) bool
        +getUnclosedRecords(date) array
    }

    class FaceDataRepository {
        +findByUserId(userId) FaceData
        +saveDescriptor(userId, descriptorJson) bool
    }

    class User {
        +int id
        +string name
        +string email
        +string password
        +string role
        +string status
    }

    class Attendance {
        +int id
        +int userId
        +string attendanceDate
        +string checkIn
        +string checkOut
        +string status
    }

    class FaceData {
        +int id
        +int userId
        +string faceDescriptor
    }

    Controller <|-- AuthController
    Controller <|-- AttendanceController
    Controller <|-- AdminController
    Controller <|-- CorrectionController

    AuthController --> AuthService
    AttendanceController --> AttendanceService
    AttendanceController --> FaceRecognitionService
    AdminController --> AuthService
    CorrectionController --> AttendanceService

    AuthService --> UserRepository
    AttendanceService --> AttendanceRepository
    AttendanceService --> UserRepository
    FaceRecognitionService --> FaceDataRepository

    UserRepository ..> User
    AttendanceRepository ..> Attendance
    FaceDataRepository ..> FaceData
```

---

## 4. Dynamic Viewpoint (Sequence Diagrams)

### 4.1 Login Sequence

```mermaid
sequenceDiagram
    actor User as User / Admin
    participant View as Login View
    participant AuthCtrl as AuthController
    participant AuthSvc as AuthService
    participant UserRepo as UserRepository
    participant DB as MariaDB Database

    User->>View: Input email & password
    View->>AuthCtrl: POST /login (email, password, csrf_token)
    AuthCtrl->>AuthSvc: authenticate(email, password)
    AuthSvc->>UserRepo: findByEmail(email)
    UserRepo->>DB: SELECT * FROM users WHERE email = ? AND deleted_at IS NULL
    DB-->>UserRepo: User Record Data
    UserRepo-->>AuthSvc: User Entity
    AuthSvc->>AuthSvc: password_verify(password, user.password)
    alt Invalid Password or User Not Found
        AuthSvc-->>AuthCtrl: return false
        AuthCtrl-->>View: Render error message & increment rate-limit
    else Password Match & Active Status
        AuthSvc-->>AuthCtrl: return User Entity
        AuthCtrl->>AuthCtrl: session_regenerate_id(true), store user_id & role
        alt Role == 'admin'
            AuthCtrl-->>User: Redirect to /admin/dashboard
        else Role == 'user'
            AuthCtrl-->>User: Redirect to /user/dashboard
        end
    end
```

### 4.2 Face Registration Sequence

```mermaid
sequenceDiagram
    actor User as User
    participant Browser as Browser UI (face-api.js)
    participant RegCtrl as FaceRegistrationController
    participant FaceRepo as FaceDataRepository
    participant DB as MariaDB Database

    User->>Browser: Open /face/register
    Browser->>Browser: Request Camera Stream & Load Models
    Browser-->>User: Display Live Video Scanning Frame
    Browser->>Browser: Capture 3-5 Face Sample Vectors (128-d)
    Browser->>Browser: Compute Average Descriptor Array
    Browser->>RegCtrl: POST /face/register (descriptor_json, csrf_token)
    RegCtrl->>RegCtrl: Validate CSRF & Session Auth
    RegCtrl->>RegCtrl: Validate 128 Floats Format
    RegCtrl->>FaceRepo: saveDescriptor(user_id, descriptor_json)
    FaceRepo->>DB: INSERT INTO face_data (user_id, face_descriptor) VALUES (?, ?) ON DUPLICATE KEY UPDATE
    DB-->>FaceRepo: Success OK
    FaceRepo-->>RegCtrl: true
    RegCtrl-->>Browser: Redirect to /user/profile with Success Flash
    Browser-->>User: Display "Face Baseline Registered Successfully"
```

### 4.3 Check-In Verification Sequence

```mermaid
sequenceDiagram
    actor User as User
    participant Browser as Browser UI (Scanner & Landmark Yaw)
    participant AttCtrl as AttendanceController
    participant FaceSvc as FaceRecognitionService
    participant AttSvc as AttendanceService
    participant FaceRepo as FaceDataRepository
    participant AttRepo as AttendanceRepository
    participant DB as MariaDB Database

    User->>Browser: Open /attendance/scan
    Browser-->>User: Prompt Head-Turn Liveness Challenge ("Turn Left")
    User->>Browser: Turn Head Left
    Browser->>Browser: Detect Yaw Landmarks Transition -> Confirmed
    Browser->>Browser: Extract Live 128-d Descriptor
    Browser->>AttCtrl: POST /attendance/verify (live_descriptor, csrf_token)
    AttCtrl->>FaceSvc: verifyDistance(user_id, live_descriptor)
    FaceSvc->>FaceRepo: findByUserId(user_id)
    FaceRepo->>DB: SELECT face_descriptor FROM face_data WHERE user_id = ?
    DB-->>FaceRepo: Descriptor JSON
    FaceRepo-->>FaceSvc: Baseline Vector
    FaceSvc->>FaceSvc: Calculate Euclidean Distance (D) vs Threshold (0.60)
    alt Distance > 0.60
        FaceSvc-->>AttCtrl: Match Failed (Distance too high)
        AttCtrl-->>Browser: JSON { success: false, error: "Facial Verification Failed" }
    else Distance <= 0.60
        FaceSvc-->>AttCtrl: Match Verified Success
        AttCtrl->>AttSvc: processCheckIn(user_id, current_time)
        AttSvc->>AttRepo: findByUserAndDate(user_id, today)
        AttRepo->>DB: SELECT * FROM attendance WHERE user_id = ? AND attendance_date = TODAY
        DB-->>AttRepo: Result Record
        alt Record Already Exists
            AttRepo-->>AttSvc: Existing Record
            AttSvc-->>AttCtrl: Duplicate Check-In Blocked
            AttCtrl-->>Browser: JSON { success: false, error: "Already checked in today" }
        else No Existing Record
            AttSvc->>AttSvc: Compare time vs rules (Present vs Late)
            AttSvc->>AttRepo: create(user_id, date, time, status)
            AttRepo->>DB: INSERT INTO attendance (user_id, attendance_date, check_in, status)
            DB-->>AttRepo: Insert ID
            AttRepo-->>AttSvc: Success
            AttSvc-->>AttCtrl: Check-In Confirmed
            AttCtrl-->>Browser: JSON { success: true, status: "Present", time: "08:45 AM" }
            Browser-->>User: Display Verification Confirmation Screen
        end
    end
```

### 4.4 Correction Request Approval Sequence

```mermaid
sequenceDiagram
    actor Admin as Admin
    participant View as Corrections View
    participant CorrCtrl as CorrectionController
    participant AttSvc as AttendanceService
    participant DB as MariaDB Database
    actor User as Target User

    Admin->>View: Select Request & Input Admin Note -> Click Approve
    View->>CorrCtrl: POST /admin/corrections/approve (request_id, admin_note, csrf_token)
    CorrCtrl->>AttSvc: approveCorrection(request_id, admin_note, admin_id)
    AttSvc->>DB: BEGIN TRANSACTION
    AttSvc->>DB: SELECT * FROM correction_requests WHERE id = ? FOR UPDATE
    DB-->>AttSvc: Request Record
    AttSvc->>DB: UPDATE attendance SET check_in=?, check_out=?, status=? WHERE id=?
    AttSvc->>DB: UPDATE correction_requests SET status='Approved', admin_note=?, reviewed_by=? WHERE id=?
    AttSvc->>DB: INSERT INTO alerts (user_id, type, title, message) VALUES (?, 'Correction Approved', ...)
    AttSvc->>DB: COMMIT TRANSACTION
    DB-->>AttSvc: Transaction Committed OK
    AttSvc-->>CorrCtrl: Success true
    CorrCtrl-->>View: Redirect with Flash "Request Approved Successfully"
    View-->>Admin: Render Updated Table
    DB-->>User: User receives Alert Notification on next poll/page load
```

### 4.5 Automated Absence Generation Sequence

```mermaid
sequenceDiagram
    actor Trigger as CLI Cron / Admin Button
    participant Script as generate_absences.php
    participant AttSvc as AttendanceService
    participant DB as MariaDB Database

    Trigger->>Script: Run script with date parameter (YYYY-MM-DD)
    Script->>AttSvc: processDailyAbsences(target_date)
    AttSvc->>DB: SELECT date FROM holidays WHERE date = ?
    alt Target Date is Official Holiday
        DB-->>AttSvc: Holiday Found
        AttSvc-->>Script: Skip processing (Holiday)
    else Working Day
        AttSvc->>DB: Check if Target Date matches configured weekend days
        alt Target Date is Weekend
            AttSvc-->>Script: Skip processing (Weekend)
        else Active Working Day
            AttSvc->>DB: SELECT id FROM users WHERE role='user' AND status='active' AND id NOT IN (SELECT user_id FROM attendance WHERE attendance_date=?)
            DB-->>AttSvc: Array of Unrecorded Active User IDs
            loop For Each Missing User
                AttSvc->>DB: INSERT IGNORE INTO attendance (user_id, attendance_date, status) VALUES (?, ?, 'Absent')
                AttSvc->>DB: INSERT INTO alerts (user_id, type, title, message) VALUES (?, 'Absent', ...)
            end
            AttSvc-->>Script: Absence Generation Complete (Count N)
        end
    end
    Script-->>Trigger: Output Result Summary Log
```

---

## 5. Data Design & Database Schema

### 5.1 Entity-Relationship Diagram (ERD)

```mermaid
erDiagram
    users ||--o| face_data : "has baseline"
    users ||--o{ attendance : "records"
    users ||--o{ correction_requests : "submits"
    users ||--o{ alerts : "receives"
    attendance ||--o{ correction_requests : "has corrections"
    menu_items ||--o{ menu_items : "parent child"

    users {
        int id PK
        string name
        string email UK
        string password
        enum role "admin, user"
        string phone
        string photo
        string department
        string identifier UK
        enum status "active, inactive"
        datetime deleted_at
        datetime created_at
        datetime updated_at
    }

    face_data {
        int id PK
        int user_id FK, UK
        text face_descriptor
        datetime created_at
        datetime updated_at
    }

    attendance {
        int id PK
        int user_id FK
        date attendance_date
        time check_in
        time check_out
        enum status "Present, Late, Absent, Incomplete"
        string recognition_method
        datetime created_at
        datetime updated_at
    }

    correction_requests {
        int id PK
        int user_id FK
        int attendance_id FK
        time requested_check_in
        time requested_check_out
        text reason
        enum status "Pending, Approved, Rejected"
        text admin_note
        int reviewed_by FK
        datetime reviewed_at
        datetime created_at
        datetime updated_at
    }

    alerts {
        int id PK
        int user_id FK
        string type
        string title
        text message
        boolean is_read
        datetime created_at
    }

    settings {
        int id PK
        string key_name UK
        text value
        datetime updated_at
    }

    holidays {
        int id PK
        date date UK
        string name
    }

    login_attempts {
        int id PK
        string email
        string ip
        datetime attempted_at
    }

    menu_items {
        int id PK
        int parent_id FK
        string title
        string url
        string icon
        enum role "admin, user, both"
        int sort_order
        boolean is_active
    }
```

### 5.2 Complete Data Dictionary

#### Table: `users`
| Field | Type | Nullable | Key | Default | Description |
|---|---|---|---|---|---|
| `id` | `INT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Unique user identifier. |
| `name` | `VARCHAR(100)` | No | | | Full user name. |
| `email` | `VARCHAR(150)` | No | UK | | User email address (unique). |
| `password` | `VARCHAR(255)` | No | | | Bcrypt password hash. |
| `role` | `ENUM('admin','user')`| No | | `'user'` | User authorization role. |
| `phone` | `VARCHAR(20)` | Yes | | `NULL` | Contact phone number. |
| `photo` | `VARCHAR(255)` | Yes | | `NULL` | Profile photo path. |
| `department`| `VARCHAR(100)` | Yes | | `NULL` | Department / Academic major. |
| `identifier`| `VARCHAR(50)` | No | UK | | Academic / Employee ID number. |
| `status` | `ENUM('active','inactive')`| No | | `'active'` | Account status flag. |
| `deleted_at`| `DATETIME` | Yes | | `NULL` | Soft-delete timestamp. |
| `created_at`| `DATETIME` | No | | `CURRENT_TIMESTAMP` | Record creation timestamp. |
| `updated_at`| `DATETIME` | No | | `CURRENT_TIMESTAMP` | Last update timestamp. |

#### Table: `face_data`
| Field | Type | Nullable | Key | Default | Description |
|---|---|---|---|---|---|
| `id` | `INT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Descriptor record ID. |
| `user_id` | `INT UNSIGNED` | No | FK, UK | | Foreign key referencing `users(id)`. |
| `face_descriptor` | `LONGTEXT` | No | | | JSON array of 128 floats. |
| `created_at` | `DATETIME` | No | | `CURRENT_TIMESTAMP` | Enrollment timestamp. |
| `updated_at` | `DATETIME` | No | | `CURRENT_TIMESTAMP` | Last update timestamp. |

#### Table: `attendance`
| Field | Type | Nullable | Key | Default | Description |
|---|---|---|---|---|---|
| `id` | `INT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Attendance record ID. |
| `user_id` | `INT UNSIGNED` | No | FK | | Foreign key referencing `users(id)`. |
| `attendance_date` | `DATE` | No | Composite UK | | Calendar date of attendance. |
| `check_in` | `TIME` | Yes | | `NULL` | Check-in timestamp. |
| `check_out` | `TIME` | Yes | | `NULL` | Check-out timestamp. |
| `status` | `ENUM('Present','Late','Absent','Incomplete')` | No | | `'Present'` | Evaluated attendance status. |
| `recognition_method` | `VARCHAR(50)` | No | | `'Face Recognition'` | Verification method used. |
| `created_at` | `DATETIME` | No | | `CURRENT_TIMESTAMP` | Insert timestamp. |
| `updated_at` | `DATETIME` | No | | `CURRENT_TIMESTAMP` | Update timestamp. |

#### Table: `correction_requests`
| Field | Type | Nullable | Key | Default | Description |
|---|---|---|---|---|---|
| `id` | `INT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Request ID. |
| `user_id` | `INT UNSIGNED` | No | FK | | User requesting correction. |
| `attendance_id` | `INT UNSIGNED` | No | FK | | Target attendance record ID. |
| `requested_check_in` | `TIME` | Yes | | `NULL` | Proposed check-in time. |
| `requested_check_out`| `TIME` | Yes | | `NULL` | Proposed check-out time. |
| `reason` | `TEXT` | No | | | User justification reason. |
| `status` | `ENUM('Pending','Approved','Rejected')` | No | | `'Pending'` | Review workflow status. |
| `admin_note` | `TEXT` | Yes | | `NULL` | Administrator review note. |
| `reviewed_by` | `INT UNSIGNED` | Yes | FK | `NULL` | Admin ID reviewing request. |
| `reviewed_at` | `DATETIME` | Yes | | `NULL` | Timestamp of review. |
| `created_at` | `DATETIME` | No | | `CURRENT_TIMESTAMP` | Submission timestamp. |
| `updated_at` | `DATETIME` | No | | `CURRENT_TIMESTAMP` | Update timestamp. |

#### Table: `menu_items` (Self-Referencing Dynamic Menu)
| Field | Type | Nullable | Key | Default | Description |
|---|---|---|---|---|---|
| `id` | `INT UNSIGNED` | No | PK | `AUTO_INCREMENT` | Menu item ID. |
| `parent_id` | `INT UNSIGNED` | Yes | FK | `NULL` | Parent menu item ID (Self FK). |
| `title` | `VARCHAR(100)` | No | | | Display label. |
| `url` | `VARCHAR(150)` | No | | | Target routing URL. |
| `icon` | `VARCHAR(50)` | Yes | | `NULL` | SVG icon key string. |
| `role` | `ENUM('admin','user','both')` | No | | `'both'` | Permitted role view. |
| `sort_order` | `INT` | No | | `0` | Sequence ordering index. |
| `is_active` | `TINYINT(1)` | No | | `1` | Active visibility flag. |

---

## 6. Security Design & Safeguards

### 6.1 Authentication & Session Security
- **Passwords:** Hashed using `password_hash()` with `PASSWORD_BCRYPT` (work factor 10+). Plaintext passwords are NEVER saved, cached, or logged.
- **Session Tokens:** Configured with `session.cookie_httponly = 1`, `session.cookie_samesite = 'Lax'`, and `session_regenerate_id(true)` upon authentication changes. Idle sessions expire automatically after 30 minutes.
- **Rate Limiting:** `login_attempts` table records IP address and timestamp. 5 failed login attempts trigger a 15-minute IP lockout.

### 6.2 Authorization (RBAC) & CSRF Safeguards
- **Server Middleware:** Every protected route is passed through `AuthMiddleware` and `RoleMiddleware`. Hiding UI buttons/links is treated purely as a UI convenience; server enforcement returns HTTP 403 Access Denied if unauthorized.
- **CSRF Tokens:** All state-changing POST/PUT/DELETE endpoints validate cryptographically generated per-session tokens via `Csrf::verify()`.

### 6.3 Biometric Data Protection & Privacy
- **Zero Raw Images:** Camera streams operate strictly in browser memory. Video frames and raw facial images are NEVER sent over HTTP or stored on disk.
- **Biometric Vectors:** 128-dimensional floating-point arrays are stored in `face_data` and remain strictly private to the server. Biometric descriptors are never included in JSON API payloads returned to users.

---

## 7. Design Patterns & Implementation Strategy

1. **MVC Pattern:** Strictly separates core entities (`Models`), UI markup templates (`Views`), and workflow routing handlers (`Controllers`).
2. **Repository Pattern:** Encapsulates all SQL execution within dedicated Repository classes (`UserRepository`, `AttendanceRepository`, `FaceDataRepository`), keeping Controllers pure and database-agnostic.
3. **Singleton Pattern:** Used in `Database::getInstance()` to maintain a single reusable PDO connection handle across each request lifecycle.
4. **Service Layer Pattern:** Houses complex business logic (`AttendanceService`, `FaceRecognitionService`, `ReportService`) independently from controllers for clear testability.

---

## 8. UI Design & User Experience Specification

### 8.1 Page Inventory
The system provides a total of 18 distinct views across public, user, and administrative security scopes:

1. **Public Views:**
   - Landing Page (`/`): Public showcase featuring hero section, interactive face-scanning preview visual, system statistics counters, feature matrix, workflow guide, and security disclosures.
   - Login View (`/login`): Clean authentication form with password visibility toggle, rate-limit warnings, and CSRF protection.
   - Registration View (`/register`): Public self-registration form restricted to role `user`.
   - Error Views (`/403`, `/404`, `/500`): Custom styled error pages aligned with brand colors.

2. **User Views:**
   - User Dashboard (`/user/dashboard`): Personal status Overview, live digital clock, today's attendance badge, monthly attendance percentage meter, quick action buttons, and recent log feed.
   - Mark Attendance (`/attendance/scan`): Interactive showpiece camera view, corner bracket scanning UI, animated scan line, liveness challenge indicators, and result modal.
   - Register Face Baseline (`/face/register`): Multi-sample facial capture workflow with real-time feedback and sample progress bar.
   - My Attendance History (`/user/attendance`): Personal record list with search, status filters, date pickers, and pagination.
   - My Correction Requests (`/user/corrections`): List of submitted requests with live status badges and new request submission modal.
   - User Profile (`/user/profile`): Profile info editor, avatar picture upload, password change panel, and biometric status indicator.

3. **Admin Views:**
   - Admin Dashboard (`/admin/dashboard`): Animated system metrics counters, Chart.js graphs (daily, weekly, monthly, present vs absent), quick action bar, and real-time activity feed.
   - User Management (`/admin/users`): Paginated user table with search, role/department/status filters, CSV export, soft-delete confirmation modal, and reset face action.
   - User Form / Profile View (`/admin/users/create`, `/admin/users/edit?id={id}`): Detailed user account creation/editing modal and full attendance history.
   - CSV HR Import (`/admin/users/import-csv`): File upload interface with drag-and-drop zone and line-by-line validation log report.
   - Attendance Records (`/admin/attendance`): Master attendance view with advanced filtering, sorting, status badges, and CSV export button.
   - Correction Requests Review (`/admin/corrections`): Review queue with request inspection modal, admin note input, and Approve/Reject action triggers.
   - Reports & Analytics (`/admin/reports`): Interactive report generator with date range pickers, department breakdowns, late/absent summary tables, and UTF-8 BOM CSV exporter.
   - System Settings & Holidays (`/admin/settings`): Tabbed configuration form for working hours, late thresholds, recognition threshold, camera resolution, weekend selection, and academic holiday CRUD table.
   - Admin Profile (`/admin/profile`): Administrative account details and security password update form.

### 8.2 UI Navigation Map & Routing Tree

```
Public User
  ├── Landing Page (/)
  ├── Login (/login)
  └── Register (/register)
       │
 Authenticated User (Role: user)
  ├── User Dashboard (/user/dashboard)
  ├── Mark Attendance (/attendance/scan)
  ├── Register Face (/face/register)
  ├── My Attendance History (/user/attendance)
  ├── Correction Requests (/user/corrections)
  └── Profile Settings (/user/profile)
       │
 Authenticated Administrator (Role: admin)
  ├── Admin Dashboard (/admin/dashboard)
  ├── User Management (/admin/users)
  ├── HR CSV Import (/admin/users/import-csv)
  ├── Master Attendance Records (/admin/attendance)
  ├── Review Correction Requests (/admin/corrections)
  ├── Reports & Analytics (/admin/reports)
  ├── System Settings & Holidays (/admin/settings)
  └── Admin Profile (/admin/profile)
```

### 8.3 Scanning UI State Machine Specification

The showpiece facial scanning UI operates strictly through six deterministic states:

```
[State 1: Initializing Camera]
   │ (WebRTC MediaStream acquired & face-api.js models loaded)
   ▼
[State 2: Looking for Face]
   │ (Single face detected in video viewport)
   ▼
[State 3: Liveness Challenge Prompt]
   │ (User performs randomized head yaw turn - e.g. Left/Right)
   ▼
[State 4: Extracting Biometric Descriptor]
   │ (128-d vector generated from active canvas frame)
   ▼
[State 5: Server Verification & Distance Calculation]
   │ (Euclidean distance D calculated vs baseline)
   ▼
[State 6: Attendance Recorded Confirmation]
```

### 8.4 Application of Design System Tokens (SPEC §9)

All UI views strictly adhere to the brand design system defined in `SPEC.md` §9:

- **Primary Brand Colors:**
  - Ink (`--c-ink: #222831`): Sidebar background, primary action buttons, main heading text.
  - Slate (`--c-slate: #393E46`): Table headers, secondary text, component borders, hover states.
  - Stone (`--c-stone: #948979`): Muted text, structural dividers, visual icons, pending badges.
  - Sand (`--c-sand: #DFD0B8`): Main page background, soft container fills, contrast button text.
- **Derived Tints:** `--c-sand-light: #EFE6D5`, `--c-paper: #F7F1E5`, `--c-stone-soft: #C9BFAE`.
- **Semantic Status Indicators:**
  - `Present`: Soft Forest Green (`#4F7A5A`)
  - `Late`: Earthy Amber (`#B7791F`)
  - `Absent`: Muted Crimson (`#A8403A`)
  - `Pending`: Warm Stone (`#948979`)
- **Typography:** Self-hosted `.woff2` fonts (`Bricolage Grotesque` for headings 600–800, `Figtree` for body 400–600).
- **Animations:** CSS transitions on `transform` and `opacity` (stagger card reveal, scan line translation, modal scale-in, counter counter-up, pulsing alert indicator).

