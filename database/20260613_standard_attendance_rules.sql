START TRANSACTION;

UPDATE shifts
SET required_minutes = 510
WHERE status = 1;

DELETE rules
FROM shift_weekly_off_rules rules
JOIN shifts ON shifts.id = rules.shift_id
WHERE shifts.status = 1;

INSERT INTO shift_weekly_off_rules (shift_id, weekday, week_of_month, is_off)
SELECT id, 0, 0, 1 FROM shifts WHERE status = 1
UNION ALL
SELECT id, 6, 1, 1 FROM shifts WHERE status = 1
UNION ALL
SELECT id, 6, 3, 1 FROM shifts WHERE status = 1
UNION ALL
SELECT id, 6, 5, 1 FROM shifts WHERE status = 1;

COMMIT;
