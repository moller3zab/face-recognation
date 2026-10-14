# User Manual

## Open the application

Start Apache and MariaDB in XAMPP, open `http://localhost/project%20se/public/`, and sign in. Camera features require browser permission. For use from a phone over a network, configure HTTPS as described in `DEPLOYMENT.md`.

## User tasks

1. Sign in with the account created for you.
2. Open **Register Face** and allow camera access. Follow the on-screen prompts and confirm the captured samples. Complete enrollment before attendance.
3. Open **Mark Attendance**, allow camera access, center your face, and follow the head-turn prompt. Wait for the server result before retrying.
4. Open **My Attendance** to review records. Use **Correction Requests** when a recorded entry needs review.
5. Update personal information and password from **Profile**. Use **Logout** in the navigation menu to end the session.

## Administrator tasks

- **Users:** create, edit, inspect, deactivate, and import accounts from CSV. Use the Edit action to update account details; leave the password field blank to retain the current password.
- **Settings:** configure attendance rules, recognition threshold, camera preference, and resolution.
- **Attendance / Reports:** filter attendance and export CSV reports.
- **Correction Requests / Alerts:** review user requests and manage notifications.
- **Profile:** update the administrator profile and password.

## Troubleshooting

- If the camera does not start, allow browser camera permission, close other apps using the camera, and reload the page.
- If no face is recognized, check lighting and camera framing, then complete or repeat enrollment with a clear, front-facing face.
- If sign-in fails, verify that the account is active or ask the administrator to reset it.
- If a route returns 404, open the application through the `/public/` base URL and use the navigation links.

## Development account

The seeded administrator account is documented in the project README. Change its password before exposing the application outside a local development environment.

