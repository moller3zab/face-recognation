# SPEC — Shared Specification

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
├── docs/ (PROJECT_PROPOSAL.md, SRS.md, SDD.md, PROJECT_PLAN.md, TESTING.md, REPORT.md, diagrams/)
├── supporting_docs/ (SPEC.md, PROGRESS.md, SECURITY_REVIEW.md, DEPLOYMENT.md, USER_MANUAL.md)
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
Admin `admin@gmail.com` / `admin123` + several realistic users with sample attendance/corrections/alerts. Never shown in the UI; documented in README (dev only).

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
