# Smart Face Recognition Attendance System

> A complete, production-grade PHP MVC web application built for a University Software Engineering Course (Graded by strict rubric).

---

## 🌟 Executive Summary
The **Smart Face Recognition Attendance System** eliminates traditional manual roll calls and proxy attendance ("buddy punching") by utilizing real-time deep neural network facial verification, head-turn liveness challenges, and dynamic business rule enforcement.

The system is **100% self-hosted and works fully offline** — all machine learning models (`face-api.js`), fonts (Bricolage Grotesque & Figtree), and libraries are stored locally in the project repository without any external CDN dependencies.

---

## 🛠️ Technology Stack
- **Backend Language**: PHP 8.2.12 (Strict Types `declare(strict_types=1);`, OOP, MVC Pattern).
- **Database**: MariaDB / MySQL 10.4+ (PDO prepared statements, Transactions, Foreign Keys).
- **Frontend Core**: Native HTML5, Vanilla CSS3 (Custom 4-Color Tokens System), Vanilla JavaScript (ES6+).
- **Machine Learning / AI**: `face-api.js` (SsdMobilenetv1, 68-Point FaceLandmarks, FaceRecognition 128D embedding vector).
- **Analytics & Charts**: Chart.js.
- **Web Server**: Apache 2.4 (XAMPP on Windows).

---

## 🎨 Design System (SPEC §9 Compliance)
The visual identity follows a curated 4-color brand palette built on light sand paper surfaces:

| Token | Color Code | Purpose |
|---|---|---|
| `--c-ink` | `#222831` | Headings, primary buttons, top navigation header |
| `--c-slate` | `#393E46` | Secondary text, table headers, hover states |
| `--c-stone` | `#948979` | Muted text, borders, icons, accent highlights |
| `--c-sand` | `#DFD0B8` | Main page background, contrast fills |
| `--c-status-present` | `#4F7A5A` | Present status badge / success indicators |
| `--c-status-late` | `#B7791F` | Late status badge |
| `--c-status-absent` | `#A8403A` | Absent status badge / danger buttons |

---

## 🔑 Default Credentials (Development & Testing)

| Role | Email Address | Password | Privileges |
|---|---|---|---|
| **Administrator** | `admin@gmail.com` | `admin123` | Full System Access, User CRUD, Reports, Settings, Correction Reviews |

---

## 🚀 Quick Setup & Installation Guide

### 1. Prerequisites
- Windows 10/11 with **XAMPP** installed at `D:\xampp` (or `C:\xampp`).
- Ensure Apache and MariaDB services are started.

### 2. Database Initialization
1. Open phpMyAdmin or MySQL client.
2. Execute `database/schema.sql` (Creates `face_attendance` database and tables).
3. Execute `database/seed.sql` (Inserts default settings, menu tree, and the development administrator account. It does not create fictitious users or attendance records.)

*Note: Database configuration is in `config/database.php` (default host `127.0.0.1`, port `3307`, user `root`, pass ``).*

### 3. Accessing the Application
Open your browser and navigate to:
```
http://localhost/project%20se/public/
```

---

## Biometric Face Recognition & Liveness Design

### 1. Facial Descriptors (128-D Math Vector)
During face enrollment (`/face/register`), the browser detects the user's face, captures 3–5 frames, and averages the resulting **128-dimensional floating point embedding vector**. This math vector is stored in `face_data`. **No raw face images or video frames are saved to disk.**

### 2. Server-Side Verification
During attendance marking (`/attendance/scan`), the browser sends the live descriptor to the server (`/attendance/verify`). The **server** computes the Euclidean distance ($D$) between the live descriptor and stored descriptor:
\[
D = \sqrt{\sum_{i=0}^{127} (A_i - B_i)^2}
\]
Identity is verified only if $D \le \text{Threshold}$ (default $D \le 0.60$).

### 3. Head-Turn Liveness Challenge
To prevent photo spoofing, the live scanner prompts the user with a random head turn challenge (*Turn head slowly to the LEFT / RIGHT*). The browser calculates the nose-to-jaw landmark distance ratio using 68-point face landmarks and requires a valid turn before enabling server verification.

---

## 🧪 Running Automated Unit Tests

Run the custom automated test suite via CLI:

```bash
php tests/run_tests.php
```

---

## 👥 Team Distribution & Rubric Compliance

| Team Member | Primary Role | Major Contributions |
|---|---|---|
| **Ahmed Mahmoud Azab** | Lead Architect & Backend Engineer | MVC Framework Core, Database Schema, Router, Face Verification Service, Unit Tests |
| **Jana Mohamed Ashraf** | UI/UX Lead | Design Tokens, UI Views, Responsive Layout, Prototype |
| **Jana Mohamed** | QA & Security Engineer | Validation, Security Audit, CSRF Controls, Test Cases |
| **Mariam Tamer Ali** | Biometrics & AI Specialist | `face-api.js` Model Integration, Descriptor Averaging, Distance Threshold Math |
| **Reem Emad** | System Analyst & Documentation | SRS Document, SDD Diagrams, Project Plan, Absence Cron Generator, User Manual |

---

## 📄 Documentation Index
- [Project Proposal](docs/PROJECT_PROPOSAL.md)
- [SRS Specification](docs/SRS.md)
- [System Design Document](docs/SDD.md)
- [Project Execution Plan](docs/PROJECT_PLAN.md)
- [Testing Plan and Results](docs/TESTING.md)
- [User Manual](supporting_docs/USER_MANUAL.md)
- [Security Review](supporting_docs/SECURITY_REVIEW.md)
- [Deployment and HTTPS Guide](supporting_docs/DEPLOYMENT.md)
- [Development Specification](supporting_docs/SPEC.md)
- [Development Progress Log](supporting_docs/PROGRESS.md)
- [Final Project Report](docs/REPORT.md)
