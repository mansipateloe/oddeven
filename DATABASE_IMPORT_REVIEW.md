# Database import

Imported oddev213.sql into local oddeven on 2026-10-22.
Verified: 112 tables, 11 employee records, 60 foreign keys.
Original SQL file preserved. Target database had zero tables before import.

Windows name collision: original domainHostingTbl imported as domainhostingtbl_case_variant; original domainhostingtbl retained.
Source data includes 38 salary_structures rows without a matching employeestbl record. Foreign key checks were disabled while adding the remaining constraints to preserve source data.
Resumed connections produced a session-only charset restoration warning after COMMIT; all remaining constraints were applied.

Application root dbconnect.php still targets oddev213; dist/dbconnect.php targets oddeven. Application connection configuration was not changed.
