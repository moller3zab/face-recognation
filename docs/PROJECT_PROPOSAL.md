# Project Proposal — Smart Face Recognition Attendance System

**Prepared:** 3 October 2026  
**Course:** Software Engineering, Misr International University  
**Team size:** 5 students

## Problem and motivation

Manual attendance takes class time and makes proxy attendance difficult to detect. This project provides a web application for recording attendance using a live face descriptor comparison and a head-turn liveness prompt, with administrative reports and correction workflows.

## Objectives

- Provide role-based administrator and user workflows.
- Let each user enroll face samples and request attendance using the browser camera.
- Apply configurable attendance and grace-period rules.
- Provide searchable attendance records, reports, alerts, and correction requests.
- Keep face processing local to the browser until a numeric descriptor is sent for server-side comparison; do not persist camera frames.

## Scope

Included: PHP MVC application, MariaDB, authentication and roles, user CRUD, multi-sample face enrollment, live attendance verification, liveness prompt, attendance rules, admin settings, reports/CSV, alerts, corrections, responsive views, and local XAMPP deployment documentation.

HR data exchange is provided as CSV user import/export. A direct integration with a named third-party HR system is outside this version because no vendor/API was specified. Liveness is a basic head-turn challenge and should not be represented as certified anti-spoofing. Enrollment and live camera acceptance require a real camera and consenting users.

## Users

- **Administrator:** manage accounts and settings, review attendance and corrections, and view reports/alerts.
- **User:** enroll biometrics, check in/out, view personal records, and request corrections.

## Proposed technology

PHP 8.2, MVC/OOP, PDO, MariaDB/MySQL, HTML5, CSS, JavaScript, WebRTC camera access, and locally hosted face-api.js models. Development target: XAMPP on Windows.

## Deliverables

SRS, SDD and diagrams, static page prototype, project plan, PHP application, automated unit test suite, testing plan/results, deployment guide, user manual, and final report. Product Owner feedback is a course bonus and is not claimed without actual feedback.

## Team and responsibility allocation

| Student | Role | Main responsibility |
|---|---|---|
| Ahmed Mahmoud Azab | Team lead / backend | MVC, database, authentication, attendance services |
| Jana Mohamed Ashraf | UI/UX | UI system, prototype, responsive views and interactions |
| Mariam Tamer Ali | Biometrics | Face models, descriptor enrollment and verification |
| Jana Mohamed | QA / security | Validation, CSRF, security review and test cases |
| Reem Emad | Analysis / documentation | Requirements, reports, user manual and documentation |

Names and role mapping reflect the team information supplied for this project and can be corrected if a member confirms a different spelling or allocation.

## Assumptions and risks

- Camera access requires browser permission and a secure context; localhost is permitted for local testing, while phone/LAN use requires HTTPS.
- Accuracy depends on enrollment quality, lighting, camera, threshold, and model performance; no universal recognition rate is promised.
- Biometric data is sensitive. Users must enroll knowingly, and only the numeric descriptor is stored for matching.
- Seed accounts are development-only and must be changed before any public deployment.

