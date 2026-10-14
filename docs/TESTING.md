# Testing Plan and Results

**Last reviewed:** 3 October 2026

## Automated tests

Run from the project root:

```sh
php tests/run_tests.php
```

The custom CLI suite covers attendance status/grace-period rules, weekends and holidays, face-vector distance, and threshold match/mismatch behavior. Record the actual output below after each run. It does not exercise HTTP routes, MariaDB CRUD, browser camera behavior, or biometric accuracy.

### Latest automated run

**Passed: 8/8, Failed: 0** using `php tests/run_tests.php` on 3 October 2026. PHP syntax checks also passed for the changed controllers, repositories, views, routes, and test classes. This result covers only the unit suite listed above.

## Manual acceptance checklist

| ID | Scenario | Expected result | Status |
|---|---|---|---|
| M-01 | Sign in as admin and user; visit a role-restricted route | Correct role access; unauthorized role receives a denial | Not run in this review |
| M-02 | Create, edit, deactivate, and inspect a user | Data persists; last active admin cannot be disabled | Not run in this review |
| M-03 | Import a valid and invalid CSV | Valid rows import; invalid rows are reported without exposing server errors | Not run in this review |
| M-04 | Save user/environment camera and resolution settings | Scan, enrollment, and landing identity check use selected constraints | Not run in this review |
| M-05 | Enroll face with camera and confirm multiple samples | Enrollment stores a descriptor only; no camera image is written | Requires consenting person and camera |
| M-06 | Match enrolled user and a different face; retry after failed liveness | Correct identity result; retry restarts the challenge | Requires enrolled profiles and camera |
| M-07 | Record check-in and check-out | Attendance follows configured work hours and prevents duplicate actions | Requires enrolled profile and database session |
| M-08 | Submit and approve/reject correction request | Ownership is enforced; approval updates record and creates alert | Not run in this review |
| M-09 | Filter dashboard/reports and export CSV | Filters and exported UTF-8 data match stored records | Not run in this review |
| M-10 | Use key pages at mobile and desktop widths | Controls remain visible, cards stack on narrow screens, no horizontal overflow | Not run in this review |
| M-11 | Submit POST without/with invalid CSRF token | Request is rejected without changing data | Not run in this review |

## Current environment notes

The local database had zero enrolled face profiles when inspected. Therefore a real identity match and end-to-end camera attendance cannot be claimed as verified until a consenting user enrolls and completes M-05 through M-07. The sample automated unit suite is not a substitute for these acceptance checks.

