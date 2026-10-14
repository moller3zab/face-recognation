# Project Plan & Task Distribution
**Project:** Face Recognition Attendance System  
**Document Version:** 1.0.0  
**Date:** October 2026  
**Course:** Software Engineering (MIU)  

---

## 1. Project Overview & Milestones

The project development execution is divided into 26 structured, sequential phases:

| Milestone ID | Phase Range | Core Focus / Deliverables | Target Completion |
|---|---|---|---|
| **M1: Requirements & Design** | Phases 01–04 | SRS document, SDD with diagrams, UI design specification, project plan & team distribution. | Week 1–2 |
| **M2: Design Source of Truth** | Phase 05 | Clickable HTML static prototype featuring brand design system & scanning visual state machine. | Week 3 |
| **M3: Core Architecture & Auth** | Phases 06–10 | MVC skeleton, PDO database schema & seeds, router, base controllers, auth, RBAC, dynamic self-referencing menu. | Week 4–5 |
| **M4: Business Logic & Modules** | Phases 11–13 | Settings service, attendance rules engine, Admin user CRUD, HR CSV import, User & Admin dashboards. | Week 6–7 |
| **M5: Biometric Core & Logic** | Phases 14–16 | Local `face-api.js` baseline enrollment, server Euclidean distance verification, head-turn liveness, cron absence generator. | Week 8–9 |
| **M6: Workflow & Analytics** | Phases 17–20 | Correction requests workflow, internal alerts system, admin analytics & Chart.js reports, UTF-8 BOM CSV exports, landing page. | Week 10–11 |
| **M7: Verification & Final QA** | Phases 21–25 | UI/Animation polish, security review, automated unit tests, HTTPS setup, final report and documentation. | Week 12 |
| **M8: Deployment & Mobile Pass** | Phase 26 | Deployment configuration and mobile responsive adjustments. | Post-course extension |

---

## 2. Team Task Distribution Matrix

The project team consists of 5 members. Responsibilities are divided strictly according to technical strengths:

| Team Member | Primary Role | Assigned Functional Modules & Code Responsibilities | Assigned Documentation & QA Tasks |
|---|---|---|---|
| **Ahmed Mahmoud Azab** | Team Lead & Lead Full-Stack Architect | MVC Core framework (`Router`, `Controller`, `Model`), Database singleton (`Database.php`), Schema (`schema.sql`, `seed.sql`), `AuthService`, `UserService`, `AttendanceService`. | Architectural design, SRS/SDD co-author, DB Dictionary lead, Project Plan coordinator. |
| **Jana Mohamed Ashraf** | Lead UI/UX & Frontend Engineer | Design system (`tokens.css`), Typography (`Bricolage Grotesque`, `Figtree`), Static Prototype (`prototype/`), Base Layouts, Component views, Micro-animations, Scanning visual CSS. | UI Design Inventory, Wireframe specs, Usability testing checklist, UI Polish audit. |
| **Mariam Tamer Ali** | Biometrics & AI Specialist | Client-side `face-api.js` local model integration, Landmark 68 yaw liveness challenge algorithm, `FaceRecognitionService`, Euclidean distance math, `FaceDataRepository`. | Machine Learning documentation, Biometric Privacy Spec, Liveness Limitations analysis. |
| **Jana Mohamed** | QA & Security Engineer | Validation engine (`Validator.php`), CSRF module (`Csrf.php`), Security middleware, Rate-limiting, Automated unit test runner (`tests/run.php`), Security audit. | Security Review document (`SECURITY_REVIEW.md`), Test Cases matrix (`TESTING.md`), Threat Modeling. |
| **Reem Emad** | System Analyst & Documentation Lead | Navigation builder (`MenuBuilder.php`), Alerts system (`AlertController`), Report aggregation logic (`ReportService`), HR CSV Exporter with BOM header. | SRS Requirements Traceability, User Manual, Final Report (`REPORT.md`), Product Owner Feedback compiler. |

---

## 3. Risk Management & Mitigation Matrix

| Risk ID | Identified Risk | Impact | Likelihood | Mitigation Strategy |
|---|---|---|---|---|
| **R-01** | Client spoofing via static photographs or video playback ("buddy punching"). | High | High | Implement mandatory client-side head-turn yaw landmark challenge before sending descriptors; document limitations in known constraints. |
| **R-02** | Camera WebRTC access blocked in mobile browsers over LAN IP. | High | Medium | Provide local HTTPS setup using XAMPP Apache `mod_ssl` self-signed certificates for LAN testing. |
| **R-03** | Arabic character corruption in Microsoft Excel CSV exports. | Medium | High | Prefix all exported CSV data streams with UTF-8 Byte Order Mark (BOM: `\xEF\xBB\xBF`). |
| **R-04** | SQL Injection or XSS vulnerabilities in user search / correction inputs. | High | Low | Enforce 100% PDO prepared statement bindings and wrap all view outputs in `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`. |
| **R-05** | Browser offline performance failure due to external CDN dependencies. | High | Low | Store all JavaScript libraries (`face-api.js`, `Chart.js`), models, and `.woff2` font files locally in `assets/`. |

---

## 4. Development Tools & Git Workflow

- **Environment:** Windows 11 + XAMPP (PHP 8.2.12 ZTS x64, MariaDB 10.4, Apache 2.4).
- **IDE & Version Control:** VS Code / Antigravity IDE, Git version control.
- **Git Branching Strategy:**
  - `main`: Production-ready, fully verified stable releases.
  - `dev`: Active integration branch where phase code is merged after verification.
  - `feature/*`: Short-lived branches for specific phase tasks (e.g., `feature/phase-09-auth`).
- **Commit Convention:** `[Phase-XX] Short descriptive summary` (e.g., `[Phase-07] Create MariaDB schema.sql and idempotent seed.sql`).

---

## 5. Definition of Done (DoD) Checklist

A phase or feature is strictly considered **Done** only when all the following criteria are verified:

- [ ] **Code Quality & Integrity:** PHP code contains `declare(strict_types=1);`, typed properties, and passes `php -l` linting without warnings or syntax errors.
- [ ] **MVC Architecture:** Logic is cleanly separated into Controllers, Services, and Repositories. No raw SQL exists in Controllers or Views.
- [ ] **Security Compliance:** Inputs pass server validation; state POSTs include CSRF tokens; database calls use PDO prepared statements; output is XSS-escaped.
- [ ] **Biometric & Privacy Standards:** Raw camera images are NEVER saved or logged; only 128-d vectors are processed on the server.
- [ ] **Design System Fidelity:** Interface conforms to the 4-color palette (`#222831`, `#393E46`, `#948979`, `#DFD0B8`) and self-hosted fonts without unapproved external CDNs.
- [ ] **Empirical Verification:** Manual test checklist and automated test scripts report 100% green pass results.
- [ ] **Documentation Update:** `supporting_docs/PROGRESS.md` is updated with phase summary, files changed, verification steps, and open questions.
