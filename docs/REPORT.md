# Smart Face Recognition Attendance System — Final Project Report

## 1. Project Information
- **Course**: Software Engineering (Graded Course Project)
- **Institution**: University Software Engineering Department
- **Project Title**: Smart Face Recognition Attendance System
- **Date**: October 2026
- **Repository Path**: `D:\xampp\htdocs\project se\`

---

## 2. Team Members & Task Allocation Matrix

| Student Name | Role | Module Responsibility | Documentation Contribution |
|---|---|---|---|
| **Ahmed Mahmoud Azab** | Lead Architect | MVC Core Framework, PDO Repositories, Attendance Engine, Database Schema | SDD Architecture & Class Diagrams, Unit Test Suite |
| **Jana Mohamed Ashraf** | UI/UX Lead | Design System Tokens, View Rendering, Responsive UI | UI prototype and usability checklist |
| **Jana Mohamed** | QA & Security Engineer | Validation, CSRF, Security Review, Test Cases | Security Review and Testing document |
| **Mariam Tamer Ali** | AI & Biometrics Specialist | `face-api.js` Neural Nets, 128D Embedding Averaging, Distance Verification | SDD Sequence Diagrams & Biometric Privacy Model |
| **Reem Emad** | System Analyst | Admin User Management, Reports CSV Exporter, Absence Cron Generator | SRS Document, Project Plan, Deployment Guide |

---

## 3. System Architecture & Design Patterns

### 3.1 Architecture Overview
The application follows a clean **Model-View-Controller (MVC)** pattern decoupled with the **Repository Pattern**:
- **Controllers** (`app/controllers/`): Handle HTTP request coordination, input extraction, and response rendering. Contain zero raw SQL or business rules.
- **Services** (`app/services/`): Pure business logic (e.g. `AttendanceRules`, `FaceRecognitionService`, `AttendanceService`, `SettingsService`, `ReportService`).
- **Repositories** (`app/repositories/`): Data access layer handling all database queries using PDO prepared statements.
- **Models** (`app/models/`): Strongly typed domain entity objects.
- **Views** (`app/views/`): Vanilla PHP templates utilizing design system tokens and HTML escaping helpers.

### 3.2 Design Patterns Utilized
1. **Repository Pattern**: Decouples data persistence from controllers and business services.
2. **Singleton Pattern**: Encapsulates PDO database connection in `App\Core\Database`.
3. **Strategy / Rules Pattern**: Isolated `AttendanceRules` engine for computing check-in status.
4. **Self-Referencing Tree**: Dynamic recursive menu rendering from `menu_items` DB table.

---

## 4. Feature Implementation & Verification Matrix

| Phase | Feature Description | Verification Method | Status |
|---|---|---|---|
| **Phase 01–02** | Software Requirements Specification (SRS) | Document Inspection | Completed |
| **Phase 03–04** | System Design Document (SDD) & Diagrams | Mermaid Diagram Rendering | Completed |
| **Phase 05** | Clickable Static Prototype | Browser Inspection | Completed |
| **Phase 06–07** | Project Skeleton, Config, Database Schema & Seed | PHP CLI & MariaDB Connection | Completed |
| **Phase 08–10** | MVC Core, Repositories, Auth, RBAC & Error Pages | Route Tests & HTTP Status Checks | Completed |
| **Phase 11–12** | Settings Engine & Admin User Management CRUD | Form Submissions & CSV Imports | Completed |
| **Phase 13** | User Dashboard & Profile Management | Monthly % Calculation Verification | Completed |
| **Phase 14** | Face Registration & Multi-Sample Averaging | `face-api.js` Vector Storage | Completed |
| **Phase 15** | Live Face Attendance & Liveness Challenge | Head-Turn Yaw Ratio & Euclidean Math | Completed |
| **Phase 16** | Attendance Records & CLI Absence Cron | `php cron/generate_absences.php` CLI | Completed |
| **Phase 17** | Correction Request Workflow & DB Transactions | PDO Transaction Approval Tests | Completed |
| **Phase 18** | Alerts & System Notifications | Unread Badge Polling & Broadcasts | Completed |
| **Phase 19** | Admin Dashboard & UTF-8 BOM CSV Exports | Chart.js & Excel CSV File Inspection | Completed |
| **Phase 20–21** | Landing Page & Design Tokens Polish | Responsive 360/768/1440px Audit | Completed |
| **Phase 22–24** | Security Audit, Unit Tests & HTTPS Guide | Automated Test Runner (8/8 Pass) | Completed |
| **Phase 25** | README, Final Report & Summary | Final Inspection | Completed |

---

## 5. Conclusion & Rubric Compliance
The project includes the documented course deliverables and a later deployment/mobile pass. The automated suite covers selected attendance and face-distance logic only. Browser camera, enrollment, live identity matching, complete database workflows, and mobile acceptance still require execution using the local app and a consenting enrolled user; these are tracked in `TESTING.md`. The local database had no enrolled face profiles at the last inspection. Course bonus feedback is not claimed because no Product Owner feedback was supplied.
