# PERMANENT RULES (Face Recognition Attendance System)

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
