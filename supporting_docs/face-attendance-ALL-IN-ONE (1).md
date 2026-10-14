# FACE ATTENDANCE — ALL-IN-ONE PROMPT PACK

## How to use
1. Copy **PART 0 (Rules)** into `.cursorrules` / `AGENTS.md`, and **PART 1 (Spec)** into `supporting_docs/SPEC.md` (also create an empty `supporting_docs/PROGRESS.md`).
2. Send **one phase at a time** (Part 2, Phase 01 → 25) to the agent. After each phase: do the manual checklist, then say "continue" and send the next phase.
3. If the agent asks a question, answer it before moving on.

Table of contents: PART 0 Rules · PART 1 Spec · PART 2 Phases 01–25

---

# PART 0

## PERMANENT RULES (save as `.cursorrules` / `AGENTS.md`)

You are an expert senior full-stack PHP developer, software architect, UI/UX designer, DB designer and QA engineer. You are building **Face Recognition Attendance System**, a university Software Engineering project (graded by a rubric, see `supporting_docs/SPEC.md` §10). You must build the **complete working project**, never mock-ups or snippets.

## How we work (read this every time)
1. I give you **one phase file at a time** (`01-...md`, `02-...md`, ...). Do ONLY that phase, then STOP and wait for me to say "continue".
2. Always read `supporting_docs/SPEC.md` (shared spec) and `supporting_docs/PROGRESS.md` (what is done) before starting a phase. Update `supporting_docs/PROGRESS.md` at the end of every phase (phase number, files created, decisions, open questions).
3. **If anything is unclear, ambiguous, or has more than one reasonable option: STOP and ASK ME immediately.** Do not assume. Do not silently pick the simplest solution. Ask short, specific questions (offer options). Wait for the answer.
4. Inspect the existing directory first. Never blindly overwrite useful work. Never delete files without asking.
5. The documents in `docs/` (SRS, SDD) are the source of truth. The code must conform to the design. If code needs to deviate, update the doc AND tell me.
6. **Phase boundaries:** do NOT build, stub, or start any part of a later phase, even if the current phase would benefit from it. If you need something from a later phase, tell me and stop. Do not leave TODO/placeholder code for the current phase.
7. At the end of every phase output: (a) files created/changed, (b) how you verified it — whichever apply: `php -l` on every changed PHP file, route test, DB query test, browser test, security test for that feature, (c) a **manual test checklist** for me (camera, browser, responsive, animations — things you cannot test), (d) open questions. Then stop.
8. Fix errors before finishing a phase. If something cannot be done exactly as requested, build the closest REAL working alternative and tell me. Never a fake button.

## Environment
- Windows + XAMPP (Apache, MySQL/**MariaDB**, **PHP 8.2.12** ZTS x64). Path: `D:\xampp\htdocs\face-attendance`. URL: `http://localhost/face-attendance/`.
- No features newer than PHP 8.2. Use `declare(strict_types=1)`, typed properties, enums, `readonly` where appropriate.
- MySQL/MariaDB-compatible SQL only (no MySQL-8-only features). DB name `face_attendance`. Test DB `face_attendance_test`.
- Extensions needed: `pdo_mysql`, `mbstring`, `openssl`. Timezone default `Africa/Cairo` (configurable).
- Works fully offline: every library, font and face model is stored locally in `assets/`. No CDN.

## Stack (minimal — do not add anything else without asking)
PHP + MySQL + HTML5 + CSS3 + vanilla JS. Allowed local libraries ONLY: `face-api.js` (+ models in `assets/models/`), `Chart.js`, PHPUnit via Composer **only if** `composer -V` works (otherwise a small custom test runner). Fonts: Bricolage Grotesque (headings) + Figtree (body), self-hosted `.woff2`.
No Docker, microservices, cloud, paid services, or unnecessary APIs. **Do NOT add any library, npm/Composer package, CDN link or external API that is not listed here. If you think you need one, ask me first.**

## Permissions & safety
- You may create/edit files inside the project folder. You must NOT: run destructive commands, delete unrelated files, change XAMPP/PHP/Apache/MySQL configuration globally (php.ini, httpd.conf, my.ini, vhosts), or touch data outside this project — unless I explicitly approve after you tell me the exact file and change.
- **Never modify, drop, reset or truncate an existing database without my explicit approval.** SQL files must not contain `DROP DATABASE`. Tests touch only `face_attendance_test`.
- Seed data must be idempotent (running `seed.sql` twice creates no duplicates: fixed IDs / `INSERT IGNORE` / `ON DUPLICATE KEY UPDATE`).

## Code rules
- MVC + OOP. No huge files. Separate Models, Views, Controllers, Services, Repositories, Config, Assets, Routes. Reusable components. Clean readable code, comments only where useful.
- Patterns (document in README/SDD): **MVC** + **Repository Pattern** (Repositories = SQL; Models = entities; Services = business logic; Controllers = coordination only). Optional: Singleton (Database), Strategy/Factory if useful.
- PDO prepared statements only. `password_hash()/password_verify()`. Secure sessions (httponly, samesite, regenerate on login, timeout). CSRF tokens on all state-changing POSTs. Escape all output (XSS). Login rate limiting.
- Authorization enforced **on the server** for every protected route. Never rely on hiding links.
- Validate on frontend AND backend.
- No dead buttons, no fake data where real is possible, no raw PHP errors shown to users (log to `storage/logs/`).
- Public registration creates role `user` only.

## Design system (applies to ALL UI work)
Follow `supporting_docs/SPEC.md` §9 exactly: palette `#222831 #393E46 #948979 #DFD0B8`, fonts, animations. It must look like a premium, human-designed product, NOT a Bootstrap template and NOT AI-generated. The approved prototype (Phase 05) is the **visual source of truth**: real views must reproduce it, and any visual change must be reported to me.

---

# PART 1

## SPEC — Shared specification (copy to `supporting_docs/SPEC.md` in the project)

## 1. Roles & features (from the course project description)
**Admin**: Manage Users (add/edit/remove profiles and photos) · Configure System (cameras and recognition parameters) · Generate Reports (attendance patterns/trends) · Manage Alerts (e.g. notifications for absent users).
**User**: Register Face · Mark Attendance (check in/out via face recognition) · View Records (own history) · Request Corrections.
System aims: accuracy, less manual time, prevent buddy punching, real-time recognition, HR-system integration (CSV import/export), detailed reporting.

## 2. Folder structure
```
face-attendance/
├── app/ controllers/ models/ repositories/ services/ views/(layouts,components,auth,user,admin,attendance,reports,corrections,errors) core/
├── config/ (database.php, app.php, constants.php)
├── public/ index.php (single front controller), .htaccess
├── assets/ css/ js/ images/ fonts/ models/
├── cron/ generate_absences.php
├── storage/ logs/ uploads/
├── database/ schema.sql, seed.sql
├── docs/ (SPEC.md, PROGRESS.md, SRS.md, SDD.md, PROJECT_PLAN.md, TESTING.md, REPORT.md)
├── prototype/ (static HTML prototype)
├── tests/
└── README.md
```
One entry point `public/index.php`. Root `index.php`/`.htaccess` only forward to it. Routing must work under `/face-attendance/`.

## 3. Database (`face_attendance`) — FKs, indexes, constraints
- **users**: id, name, email(unique), password, role(`admin`/`user`), phone, photo, department, identifier(unique), status(`active`/`inactive`), deleted_at, created_at, updated_at
- **face_data**: id, user_id(FK, unique), face_descriptor(128 floats JSON/TEXT), created_at, updated_at
- **attendance**: id, user_id, attendance_date, check_in, check_out, status(`Present`/`Late`/`Absent`/`Incomplete`), recognition_method, created_at, updated_at; unique(user_id, attendance_date). **Decision: exactly ONE check-in and ONE check-out per user per day** (no multiple sessions)
- **correction_requests**: id, user_id, attendance_id, requested_check_in, requested_check_out, reason, status(`Pending`/`Approved`/`Rejected`), admin_note, reviewed_by, reviewed_at, created_at, updated_at
- **alerts**: id, user_id, type, title, message, is_read, created_at
- **settings**: key/value (system name, working start/end, late threshold minutes, recognition threshold, weekend days, timezone, default camera, camera resolution, attendance rules)
- **holidays**: id, date, name
- **login_attempts**: id, email, ip, attempted_at
- **menu_items** (SELF-REFERENCING dynamic menu): id, parent_id (FK → menu_items.id, nullable), title, url, icon, role(`admin`/`user`/`both`), sort_order, is_active. The navigation is built **recursively from this table by the logged-in user's role**; supports sub-menus.

## 4. Face recognition design (real, server-verified)
- `face-api.js` in the browser, models local in `assets/models/`; produces a 128-d descriptor.
- Registration: capture 3–5 samples, require exactly one face, store averaged descriptor via CSRF-protected POST. Descriptor is biometric: never shown, never logged, never returned to other users.
- Attendance: browser sends live descriptor; **server** computes Euclidean distance vs the **logged-in user's** stored descriptor and compares with the configurable threshold. The server decides; never trust client JS.
- **Liveness (required, to prevent buddy punching) — DECIDED: head-turn challenge.** Each attempt asks for a random direction (left or right); use face-api.js 68-point landmarks to estimate head yaw and accept only after a clear turn then return to front. No blink detection. Document honestly in Known Limitations that this is client-side and can be defeated by a determined attacker.
- **Biometric storage — DECIDED:** store only the 128-d descriptor. NEVER save face images or video frames (frames stay in browser memory and are discarded). The optional profile photo is a separate upload and is never used for recognition.
- Camera works on `localhost`; phone via LAN IP needs HTTPS (document + set up local HTTPS in XAMPP).
- Handle: permission denied, no camera, no face, multiple faces, low light, model load failure, with clear messages. Admin can choose default camera/resolution in settings.

## 5. Attendance rules (all from DB settings, never hard-coded)
Start/end time, late threshold → `Late` else `Present`; check-in without check-out at end of day → `Incomplete`. **Absent**: `php cron/generate_absences.php` (manual or Windows Task Scheduler) + admin button "Generate absences for date": creates `Absent` + alert for active users with no record on working days (skip weekends/holidays). Prevent duplicate check-ins, inactive users, unauthorized attendance.

## 6. Feature list
Admin dashboard (animated counters: Total Users, Present/Late/Absent Today, Pending Corrections; real-data charts daily/weekly/monthly/present-vs-absent/late; recent activity) · User CRUD with search/filter/sort/pagination, soft delete · User dashboard (greeting, date, live time, today status, check-in/out, monthly %, recent, pending corrections) · Attendance records (user: own only, filters; admin: all, filters date/user/department/status) · Correction requests (approve updates attendance in a transaction, stores reviewer/date/note, alerts) · Alerts (Late, Absent, Correction Request/Approved/Rejected, System; animated indicator) · Reports (daily, weekly, monthly, user, late, absent; filters; CSV export; HR CSV import of users) · Settings in DB · Profiles (user + admin with password change) · Error pages 403/404/500 · Landing page.

## 7. Menus (rows of `menu_items`)
Admin: Dashboard, Users, Attendance, Reports, Correction Requests, Alerts, Settings, Profile, Logout. User: Dashboard, Mark Attendance, Register Face, My Attendance, Correction Requests, Profile, Logout.

## 8. Seed data
Admin `admin@faceattendance.local` / `Admin@12345` + several realistic users with sample attendance/corrections/alerts. Never shown in the UI; documented in README (dev only).

## 9. DESIGN SYSTEM (strict)
**Palette — use these four brand colors; everything else is a tint/shade of them or a semantic status color:**
| Token | Value | Use |
|---|---|---|
| `--c-ink` | `#222831` | text, primary buttons, sidebar/nav, headings |
| `--c-slate` | `#393E46` | secondary text, secondary buttons, hover states, table headers |
| `--c-stone` | `#948979` | muted text, borders, icons, dividers, accent highlights |
| `--c-sand` | `#DFD0B8` | main page background, button text on dark, soft fills |
Derived (allowed): `--c-sand-light #EFE6D5` and `--c-paper #F7F1E5` for cards/inputs/modals (lighter tints of sand), `--c-stone-soft #C9BFAE` for borders on light surfaces.
Semantic status (muted, earthy, harmonised with the palette): Present `#4F7A5A`, Late `#B7791F`, Absent `#A8403A`, Pending `#948979`.
Rules: the **page is light (sand)**; dark colors (`#222831`/`#393E46`) are used for text, sidebar, buttons, and small dark panels (e.g. camera frame), not for full-page dark backgrounds. No blue, no purple, no neon, no gradient text, no emoji icons. Gradients only within the palette and very subtle. Depth = soft layered shadows + 1px borders + whitespace. Blur/glass only on small overlays (camera overlay). Define everything as CSS variables in one `tokens.css`.

**Typography**: Bricolage Grotesque (headings, 600–800, tight tracking on large sizes) + Figtree (body/UI, 400–600). NO Inter/Roboto/Arial/Poppins/system-ui as visible font. `font-variant-numeric: tabular-nums` for numbers. Self-host `.woff2`, `font-display: swap`, real fallbacks. Clear type scale, body line-height 1.6, no ALL-CAPS everywhere.

**Human, non-generic feel**: vary layouts between sections (no endless identical card grids), asymmetric landing sections, consistent custom SVG line icons, real microcopy, a distinctive logo mark.

**Animations — a major requirement; implement lots, polished, not slow/annoying:**
scroll reveal (Intersection Observer: fade-up/down/left/right, scale-in) · staggered card entrances · animated counters · landing hero (soft drifting sand/stone blobs, floating particles, subtle grid, animated circles, face-scan visual with brackets, light movement) · hover (buttons: scale/glow/transition; cards: lift/shadow/border highlight; nav: smooth active indicator) · modals fade+scale in/out · loading states for face recognition/DB actions/reports/forms ("Scanning face…", "Analyzing…", "Verifying identity…", "Attendance confirmed") · page transitions, toast animations, notification-bell pulse, skeleton loaders. Animate only `transform`/`opacity`, keep 60fps. Respect `prefers-reduced-motion` (accessibility only; with it off, ALL animations run fully).

**Face scanning UI (the showpiece)**: rounded container, corner-bracket scanning frame, animated scan line, status indicator, face-detected indicator, processing animation. States with smooth transitions: Waiting for camera… → Looking for face… → Face detected → Verifying identity… → Identity verified → Attendance recorded.

**Landing page**: Hero "Smart Attendance. Powered by Your Face." + subtitle + CTAs "Get Started"/"Login"; Features (Face Recognition, Real-Time Attendance, Secure Authentication, Attendance Analytics, Smart Alerts, Correction Requests); How it works (Register Face → Scan → Verify → Attendance Recorded); animated statistics; Security section (authentication, role-based access, secure password storage, validation, protected records); final CTA.
**Responsive**: desktop/laptop/tablet/mobile; mobile sidebar; responsive tables; camera UI usable on mobile.

## 10. Grading rubric to satisfy (Software Engineering, MIU)
Phase 1 docs: Preface, Introduction, Similar Systems, Glossary, Functional User Requirements, Functional System Requirements, Non-functional Requirements, Prototype of ALL forms/pages, Scenarios, Use cases, Form-based & tabular specification.
Phase 2: Design viewpoints (sequence, use case, context, class, architecture diagrams), Data design, UI design, Project plan & task distribution, SDD, automated unit tests, MVC, data validation, implementation conforms to design, clean code, CRUD, OOP, ≥2 design patterns, dynamic menu (self reference), authentication with roles, full implementation.
Bonus: Machine Learning/AI (face-api.js deep model — document it), Product Owner feedback, deployment, HTTPS.

---

# PART 2 — PHASES

## PHASE 01 — Requirements document (SRS part 1)

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Write `docs/SRS.md` (part 1). No code in this phase.

## Tasks
1. Preface (purpose of document, intended readers, version history)
2. Introduction (purpose, scope, definitions of the system, intended users, product perspective)
3. Similar Systems: compare 3–4 REAL existing attendance/face-attendance products you know with certainty; table of similarities/differences. Mark any uncertain claim as 'to verify'
4. Glossary of all technical terms (descriptor, liveness, MVC, RBAC, CSRF, etc.)
5. Functional User Requirements in natural language, numbered UR-01..., grouped by Admin / User
6. Functional System Requirements in structured language, numbered FR-01..., each traceable to a UR
7. Non-functional Requirements: performance, security, usability, reliability, compatibility, maintainability, accessibility — each measurable (e.g. recognition < 3 s)

## Done when
- Every item in SPEC §1 and §6 is covered by at least one UR and FR
- Requirements are numbered and traceable (UR → FR)
- No vague words like 'fast' or 'easy' without a measure

## Manual checklist for me (you cannot test these)
- [ ] Read the SRS: does it match what the doctors asked for in the rubric?
- [ ] Any missing feature? Tell me before 'continue'

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 02 — Scenarios, use cases and form-based specification (SRS part 2)

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Complete `docs/SRS.md`. No code in this phase.

## Tasks
1. Scenarios (narrative, step by step) for: register face, check-in, check-out, late arrival, duplicate attempt, failed recognition, correction request + approval, admin generates absences, admin creates user, unauthorized access attempt
2. Use cases in tabular form (ID, name, actor, pre-conditions, main flow, alternative flows, post-conditions) for every major feature
3. Form-based specification AND tabular specification for the key functions (check-in, register face, approve correction, generate absences, login): description, inputs, source, outputs, destination, action, requires, pre/post-conditions, side effects
4. Add a traceability table: UR → FR → Use case

## Done when
- Every UR has at least one use case
- Alternative/error flows are included, not only the happy path

## Manual checklist for me (you cannot test these)
- [ ] Read two use cases: do they match how you imagine the system?

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 03 — System Design Document (SDD) + diagrams

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Write `docs/SDD.md`. Draw all diagrams as **Mermaid** (render-ready in Markdown) and also save each as a `.mmd` file in `docs/diagrams/`. No application code.

## Tasks
1. Context diagram
2. Use case diagram (admin + user)
3. Class diagram (models, repositories, services, controllers, core)
4. Sequence diagrams: login, face registration, check-in with face verification (browser → controller → service → repository → DB), correction approval, absence generation
5. Architecture diagram (MVC layers + Repository pattern + browser face-api component)
6. Data design: ERD (including self-referencing `menu_items`), data dictionary for every table/column, indexes/constraints
7. Security design (auth, sessions, CSRF, rate limit, biometric data handling)
8. Design patterns section: where MVC and Repository (and any extra) are used, with the reason

## Done when
- Every class/table in the SDD matches SPEC §2–§3
- Every diagram renders without syntax errors (check each Mermaid block)
- The SDD says which classes will exist so later code can conform to it

## Manual checklist for me (you cannot test these)
- [ ] Open the diagrams in a Mermaid viewer: do they look right and readable?

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 04 — UI design + project plan and task distribution

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Write the UI section of the SDD and `docs/PROJECT_PLAN.md`. No application code.

## Tasks
1. UI design in SDD: page inventory (every page for admin/user/public), navigation map, wireframe description for each page, state list for the scanning UI, application of the design system (SPEC §9)
2. Project plan: milestones aligned with the 25 phases, risks and mitigations, tools (Git/GitHub workflow, branches)
3. Task distribution table for the team (4–5 students): **ASK ME the member names and who is strong in what**, then assign modules/documents fairly
4. Definition of Done checklist for the whole project

## Done when
- Every page in the inventory will exist in the prototype (next phase)
- Task distribution covers code, docs, testing, and presentation

## Manual checklist for me (you cannot test these)
- [ ] Show the plan to the team: do they agree on the split?

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 05 — Static prototype of ALL pages

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Build a clickable static prototype in `prototype/` (HTML/CSS/JS only, no PHP, fake sample data is fine HERE because it is a prototype). It is NOT a disposable mock-up: it is the visual source of truth that the real views must reproduce.

## Tasks
1. `prototype/assets/css/tokens.css` with the full palette as CSS variables, plus base, components, and animation CSS; self-hosted fonts
2. Pages: landing, login, register, access-denied/404/500; admin: dashboard (with sample Chart.js charts), users list, user form, user profile, attendance, reports, corrections, alerts, settings, profile; user: dashboard, mark attendance (scanning UI with simulated states), register face, my attendance, corrections, profile
3. All animations from SPEC §9 working: scroll reveal, stagger, counters, hover, modals, loaders, scan line, bell pulse
4. Working sidebar (mobile-friendly), responsive tables, consistent components

## Done when
- Every page in the UI inventory exists and is linked
- Looks consistent, light sand theme, correct fonts, no blue/purple
- Responsive at 360px, 768px, 1280px

## Manual checklist for me (you cannot test these)
- [ ] Open `prototype/index.html` and click through EVERY page
- [ ] Resize the browser to phone width
- [ ] Tell me what you want changed in the look before 'continue' — it is cheaper to change now

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 06 — Project structure, config and routing

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Create the real project skeleton from SPEC §2 inside `D:\xampp\htdocs\face-attendance`. Do not break `prototype/` or `docs/`.

## Tasks
1. All folders from SPEC §2, with `config/app.php`, `config/database.php`, `config/constants.php`
2. `public/index.php` front controller, `public/.htaccess`, root forwarding so `http://localhost/face-attendance/` works
3. PSR-4-style autoloader (or Composer autoload if available)
4. Environment/config loading, error handling (log to `storage/logs/`, friendly 500 page), timezone set from config
5. A temporary 'it works' route to prove routing

## Done when
- Visiting `/face-attendance/` and a deep URL both reach `public/index.php`
- Wrong URL returns a styled 404 (placeholder style is OK now)
- No PHP warnings in logs

## Manual checklist for me (you cannot test these)
- [ ] Open the URL, a fake URL, and check `storage/logs/`

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 07 — Database schema, seed and connection

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Create `database/schema.sql` and `database/seed.sql` exactly per SPEC §3 and SDD data design.

## Tasks
1. All tables with FKs, indexes, unique keys, charset utf8mb4
2. `menu_items` self-referencing table + seed rows for admin and user menus (SPEC §7)
3. Seed: admin account (password hashed with `password_hash`), realistic users, sample attendance for the last 60 days, corrections, alerts, settings, holidays
4. `app/core/Database.php` (PDO, exceptions mode, utf8mb4, singleton or injected)
5. A DB check script/route that confirms the connection and table counts

## Done when
- Both SQL files import cleanly in phpMyAdmin on a fresh database; `seed.sql` can run twice without duplicates
- No MySQL-8-only syntax
- Seed passwords are hashes, never plaintext

## Manual checklist for me (you cannot test these)
- [ ] Import `schema.sql` then `seed.sql` in phpMyAdmin
- [ ] Check the tables and the `menu_items` tree visually

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 08 — MVC core and repository layer

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Build `app/core/*`: Router, base Controller, base Model, Validator, Csrf, View renderer, and the repository base + interfaces.

## Tasks
1. Router with route groups and middleware (auth, role)
2. Base Controller (render, redirect, json, flash messages)
3. Validator with reusable rules (required, email, min, max, unique, match, date, in)
4. Csrf helper (token generate/verify + form helper)
5. Models (User, Attendance, FaceData, CorrectionRequest, Alert, Setting, MenuItem) as entities; Repository interfaces + PDO implementations with the SQL
6. View layouts and shared components using the prototype HTML

## Done when
- No SQL outside repositories
- No business logic in controllers
- Matches the class diagram in the SDD

## Manual checklist for me (you cannot test these)
- [ ] Open a test route that reads from the DB through a repository and renders a view

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 09 — Authentication, sessions and rate limiting

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Implement login, logout, registration (role `user` only), secure sessions and brute-force protection.

## Tasks
1. Login page (from prototype) with validation and error messages
2. Registration with all fields (name, email, password, confirm, phone, department, identifier, optional photo upload with type/size checks)
3. `AuthService` + `Auth` core: password_hash/verify, session regeneration, idle timeout, secure cookie flags
4. Rate limiting via `login_attempts` (lockout window from settings)
5. Logout (POST + CSRF)
6. Alerts/flash messages for errors

## Done when
- No way to register as admin
- Wrong passwords are throttled
- CSRF on login/register/logout
- Passwords never stored or logged in plaintext

## Manual checklist for me (you cannot test these)
- [ ] Register a user, log in, log out
- [ ] Fail login 5+ times and confirm lockout
- [ ] Try registering with role=admin by editing the form — must not work

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 10 — Roles, dynamic self-reference menu and error pages

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Authorization on the server and the menu built from `menu_items`.

## Tasks
1. Role middleware: `/admin/*` admin only, `/user/*` user only; otherwise real 403 page
2. `MenuBuilder` service: loads `menu_items` by role and builds the tree **recursively via parent_id**; marks the active item; supports sub-menus
3. Sidebar/top-nav component rendered from the menu tree, mobile-friendly
4. Final styled 403, 404, 500 pages
5. Post-login redirect by role

## Done when
- A user typing an admin URL gets 403 (server-side)
- Adding a row to `menu_items` (with a child) appears in the UI without code changes
- Menu differs by role

## Manual checklist for me (you cannot test these)
- [ ] Insert a sub-menu row in phpMyAdmin and refresh
- [ ] Try admin URLs as a normal user

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 11 — System settings and attendance rules

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Admin settings page backed by the `settings` and `holidays` tables, and the rules engine used later.

## Tasks
1. Settings UI: system name, working start/end, late threshold, recognition threshold, weekends, timezone, holidays CRUD, **camera config (default camera, resolution)**, attendance rules
2. `Setting` repository + `SettingsService` with caching per request
3. `AttendanceRules` class (pure logic, unit-testable): decides Present/Late/Incomplete, working-day check
4. Validation + CSRF, success/error feedback

## Done when
- No attendance value is hard-coded anywhere
- Changing a setting immediately changes the rule result

## Manual checklist for me (you cannot test these)
- [ ] Change the late threshold and confirm it is saved
- [ ] Add a holiday

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 12 — User management (admin CRUD)

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Admin users module.

## Tasks
1. List with search, filters (role, status, department), sorting, pagination
2. Create / view profile (incl. attendance history + face registration status) / edit (name, email, phone, department, identifier, role, status, photo) / deactivate and soft-delete safely (cannot delete yourself or the last admin)
3. CSV import of users (HR integration) with row-level validation report
4. Photo upload handling (validation, safe filenames, stored in `storage/uploads`, served safely)
5. Delete/confirm modals with animation

## Done when
- All input validated both sides
- Unique email/identifier enforced with friendly errors
- Soft-deleted users cannot log in

## Manual checklist for me (you cannot test these)
- [ ] Create, edit, deactivate, search, sort, paginate
- [ ] Import a small CSV with one bad row

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 13 — User dashboard and profiles

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Normal-user dashboard and both profile pages.

## Tasks
1. User dashboard: greeting, date, live clock, today's status, check-in/out times, monthly attendance %, recent records, pending corrections, with counters and staggered animations
2. User profile (photo, name, email, phone, department, identifier, face status, account status) with allowed updates
3. Admin profile (name, email, photo, password change, account info)
4. Password change with current-password check

## Done when
- Monthly % computed from real data and working days/holidays
- User can only edit allowed fields

## Manual checklist for me (you cannot test these)
- [ ] Log in as a seed user and compare the percentage with the table by hand

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 14 — Face registration

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Real face registration with `face-api.js` (local).

## Tasks
1. Download/place `face-api.js` and its models locally in `assets/` (tell me exactly what to download if you cannot)
2. Registration page: camera preview, capture multiple samples, retake, status, instructions, scanning UI
3. Validate exactly one face; handle permission denied / no camera / low light / model failure
4. Server endpoint stores the averaged descriptor in `face_data` (no images saved anywhere) (CSRF, auth, validation of 128 floats)
5. Face status shown on profile/admin user view; admin can reset a user's face data

## Done when
- Descriptor never appears in any HTML/JSON visible to other users
- Works on `localhost`

## Manual checklist for me (you cannot test these)
- [ ] Register your face in Chrome and in a phone browser (note: phone needs HTTPS — phase 24)
- [ ] Cover the camera / use two faces to test error messages

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 15 — Face recognition and attendance (check-in / check-out)

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
The core feature. Server-side verification.

## Tasks
1. Mark Attendance page with the full scanning UI and all states (SPEC §9)
2. **Head-turn liveness challenge** (random left/right, per SPEC §4)
3. Endpoint receives the live descriptor, `FaceRecognitionService` computes Euclidean distance vs the logged-in user's descriptor, applies the threshold from settings
4. `AttendanceService`: check-in (Present/Late via rules), check-out, duplicate prevention, inactive/unregistered-face handling, DB transaction/unique key safety
5. Result card: name, date, time, status, recognition result; alert created on Late
6. Rate-limit failed recognition attempts

## Done when
- The accept/reject decision happens only on the server
- Another person's face is rejected
- Double check-in is blocked
- Tampering with the POST (fake descriptor) cannot create attendance for someone else

## Manual checklist for me (you cannot test these)
- [ ] Check in with your face; check in again (must be blocked)
- [ ] Try with another person or a photo on a phone and report what happens (so we document liveness limits honestly)

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 16 — Attendance records and absence generation

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Records pages and absent logic.

## Tasks
1. User 'My Attendance': search, date/month/status filters, pagination, columns Date/Check-in/Check-out/Status/Recognition Method
2. Admin attendance: filters date/user/department/status, pagination, sorting
3. `cron/generate_absences.php` (CLI) + admin button 'Generate absences for date'; skips weekends/holidays; idempotent; creates alerts
4. Mark `Incomplete` for missing check-out
5. Document Windows Task Scheduler command for the cron script

## Done when
- Running the absence generator twice creates no duplicates
- Weekends/holidays produce no absences

## Manual checklist for me (you cannot test these)
- [ ] Run `D:\xampp\php\php.exe cron\generate_absences.php` and check the table

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 17 — Correction requests

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Full correction workflow.

## Tasks
1. User: submit (select own attendance record, requested check-in/out, required reason), list with statuses
2. Admin: list/filter, review modal, approve/reject with admin note
3. Approval updates the attendance record inside a **transaction** and recalculates status via the rules engine; stores reviewer, review date, note
4. Alerts to the admin on submit and to the user on decision
5. Authorization: a user cannot request for someone else's record

## Done when
- Approving twice is impossible
- Reason is required (frontend + backend)

## Manual checklist for me (you cannot test these)
- [ ] Submit → approve → check the attendance row changed; submit → reject

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 18 — Alerts system

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Internal notifications.

## Tasks
1. Alert types: Late, Absent, Correction Request, Correction Approved, Correction Rejected, System Notification
2. Bell with animated unread indicator (pulse), dropdown, mark read/all read, alerts page with filters
3. Admin alerts management (view all, filter by type/user, delete/clear, send a system notification to a user or everyone)
4. Polling endpoint for the unread count (lightweight)

## Done when
- Users only see their own alerts
- Unread count is accurate

## Manual checklist for me (you cannot test these)
- [ ] Trigger a late check-in and a correction and watch the bell

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 19 — Admin dashboard, reports and exports

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Analytics for admin.

## Tasks
1. Dashboard: animated stat cards from real data, Chart.js charts (daily, weekly, monthly, present vs absent, late users), recent activity feed
2. Reports: Daily, Weekly, Monthly, User, Late users, Absent users — with date/user/department/status filters and loading states
3. `ReportService` with pure calculation methods (unit-testable)
4. CSV export for every report (UTF-8 BOM so Arabic opens in Excel); HR-friendly attendance export
5. PDF export only if it needs no new library — otherwise skip and tell me

## Done when
- All numbers come from the DB and agree with the attendance table
- Empty states handled

## Manual checklist for me (you cannot test these)
- [ ] Compare a report total against rows counted by hand
- [ ] Open the CSV in Excel

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 20 — Landing page

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Public landing page from the prototype, now real.

## Tasks
1. Hero, Features, How it works, animated statistics (real counts), Security section, final CTA — exactly as SPEC §9
2. Hero animation (soft blobs, particles, grid, circles, scan visual) and scroll reveals
3. Redirect logged-in users sensibly; links to login/register
4. SEO basics (title, meta, favicon) and a real footer

## Done when
- Matches the prototype look
- No console errors

## Manual checklist for me (you cannot test these)
- [ ] Scroll the page slowly and check each animation
- [ ] Check on phone width

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 21 — UI polish, animations and responsive pass

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Quality pass over the whole app. No new features.

## Tasks
1. Audit every page against SPEC §9: palette variables only (no hard-coded colors), fonts, spacing scale, consistent components
2. Add/finish all animations: reveals, stagger, hover, modal in/out, loaders, toasts, page transitions, skeleton loaders
3. Performance check: animate only transform/opacity; no layout jank
4. Responsive audit 360/768/1024/1440; tables → cards on mobile
5. Accessibility: focus states, labels, contrast, `prefers-reduced-motion` handling

## Done when
- No blue/purple/neon anywhere
- No dead buttons or broken links (crawl every route)

## Manual checklist for me (you cannot test these)
- [ ] Click every link and button in both roles
- [ ] Test on a real phone

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 22 — Validation and security review

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Verify, do not add features.

## Tasks
1. Review every form and endpoint: frontend + backend validation, CSRF, XSS escaping, SQL via PDO only, authorization on server, file upload safety, session settings
2. Try attacks yourself and report: SQLi in search/filter, XSS in names/reasons, CSRF without token, IDOR (user A reading user B's data/records/corrections), role bypass, upload of PHP file
3. Fix everything found; write a short `supporting_docs/SECURITY_REVIEW.md` (findings + fixes)
4. Check biometric data handling (never exposed or logged)

## Done when
- All attack attempts fail
- Security review doc exists

## Manual checklist for me (you cannot test these)
- [ ] Read the findings and confirm nothing is left open

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 23 — Automated unit tests

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Tests in `/tests` with a separate test DB.

## Tasks
1. Use PHPUnit if `composer -V` works, else a custom runner (`php tests/run.php`)
2. Test: authentication, password verification, Validator rules, attendance creation, duplicate prevention, late/present calculation, correction approval, role authorization, report calculations, face distance comparison
3. Separate `face_attendance_test` database, created/reset by the tests
4. Short instructions to run them; all tests green

## Done when
- `php tests/run.php` (or phpunit) passes
- Business logic is covered, not only trivial getters

## Manual checklist for me (you cannot test these)
- [ ] Run the tests yourself and show the green output

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 24 — HTTPS, deployment readiness and ML documentation (bonus)

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Bonus items from the rubric.

## Tasks
1. Local HTTPS in XAMPP (this changes Apache config: list the exact files/edits and WAIT for my approval before applying): create a self-signed certificate, enable `mod_ssl`, configure the vhost, step-by-step for Windows, and verify the camera works from a phone over `https://<LAN-IP>/face-attendance/`
2. Deployment readiness: production config separation (`config/app.php` env switch, debug off), `.htaccess` hardening, steps to deploy on a PHP shared host (document them; do not require an online service for local use)
3. ML/AI documentation: which model face-api.js uses, how descriptors and distance thresholds work, accuracy/limits

## Done when
- App still works locally on http and https
- Production mode hides errors

## Manual checklist for me (you cannot test these)
- [ ] Open the HTTPS URL on your phone and test the camera

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

## PHASE 25 — Testing document, README and final report

**Before starting:** read `00-RULES.md` (`.cursorrules`), `supporting_docs/SPEC.md` and `supporting_docs/PROGRESS.md`. Do ONLY this phase. If anything is unclear, ASK ME first.

## Goal
Finish all documents.

## Tasks
1. `docs/TESTING.md`: test plan, test cases (ID, feature, steps, expected, actual, pass/fail) for ALL features including manual camera tests, results, bug log
2. `README.md`: overview, features, technologies, architecture, patterns and where used, folder structure, DB setup (phpMyAdmin), XAMPP setup, installation, face recognition setup, HTTPS, absence cron/Task Scheduler, test accounts, running, testing, security considerations (biometric data), Known Limitations (liveness, browsers)
3. `docs/REPORT.md`: compile proposal summary, SRS, SDD, plan, testing, outcomes, lessons learned; add a **Product Owner Feedback** section (I will fill it after showing the doctor/TA — leave clear placeholders to fill)
4. Final QA: fresh install walk-through from README on a clean DB; list of any remaining issues; make sure `docs/` matches the final code

## Done when
- A new person can install and run the project only from the README
- Docs and code agree

## Manual checklist for me (you cannot test these)
- [ ] Do the fresh-install walk-through yourself
- [ ] Show the system to the doctor/TA and record feedback in the report

## End of phase
Update `supporting_docs/PROGRESS.md`, report files changed + how you verified + open questions, then **STOP and wait for me to say "continue"**.

---

