# Cleanup review

Deleted: output/deployment/bank-expense-fixes.zip (18,631 bytes). No ZIP files remain.

## Pending deletion approval

These directories have no matching path references in the scanned application PHP, JS, CSS, HTML and Markdown. Backup/archive copies may still be useful for recovery. No pending directory has been deleted.

- _codex_backup_20260720_152323 (26 files)
- _codex_client_backup_20260720_170153 (3 files)
- _codex_client_doc_backup_20260720_170539 (2 files)
- _codex_client_doc_backup_20260720_170833 (2 files)
- _codex_employee_continuation_backup_20260720_182315 (5 files)
- _codex_employee_continuation_design_backup_20260720_183014 (1 files)
- _codex_lead_backup_20260720_171716 (4 files)
- _codex_lead_backup_20260720_172035 (4 files)
- _codex_project_deadline_backup_20260720_173052 (3 files)
- _codex_task_attachment_backup_20260720_174140 (3 files)
- _security_backup_20260613 (3 files)
- storage\legacy_archive (32 files)
- dist\dist123 (4 files)

Other cleanup candidates: images/crossftp_1.98.9.deb, tmp/pdfs/letter-preview.html, desktop.ini files and empty error_log files.

Preserved: uploads, stored documents, SQL backups, migrations, dependencies, tests and application routes. Database-backed and external usage has not been audited.

Existing dist/dbconnect.php modification was preserved.

## Validation

Existing unit suite: 21 passed, 1 failed (test_oecrm_session_guard_rejects_expired_session). PHP deprecation output from admin/functions.php:59 precedes a session_regenerate_id warning. Failure was present before deletion.
