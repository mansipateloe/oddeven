# OECRM Tests

This directory contains a lightweight unit test harness for the procedural PHP helper layer.

Run:

```bash
php tests/run.php
```

Coverage focus:

- `security.php` pure and session-based helpers
- `foundation.php` helper and mapping functions
- `admin/functions.php` formatting and status-label helpers

Known gaps:

- Database-bound controller files and action scripts still need integration/manual testing.
- Some legacy helpers depend directly on `mysqli_*` and are difficult to unit test without refactoring.

