# OECRM Tests

This directory contains a lightweight unit test harness for the procedural PHP helper layer.

Run:

```bash
php tests/run.php
```

Run the admin portal access regression checks:

```bash
php tests/portalAccess.php
```

This starts a temporary loopback PHP server with isolated test sessions. It checks
employee and mixed employee/admin sessions against copied admin URLs and POST
endpoints, along with login redirects, authorized session guards and no-store
headers. It does not log into real accounts or modify application records.

Coverage focus:

- `security.php` pure and session-based helpers
- `foundation.php` helper and mapping functions
- `admin/functions.php` formatting and status-label helpers

Known gaps:

- Database-bound controller files and action scripts still need integration/manual testing.
- Some legacy helpers depend directly on `mysqli_*` and are difficult to unit test without refactoring.

