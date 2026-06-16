ALTER TABLE attendance_events
    MODIFY event_type ENUM('sign_in','lunch_in','lunch_out','break_in','break_out','sign_out','manual_adjustment') NOT NULL;
