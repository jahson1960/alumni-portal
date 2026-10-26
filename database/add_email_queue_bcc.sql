-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Lets an outbound email carry an optional BCC address - used so a staff member
-- can be copied on every broadcast email sent to applicants, for monitoring.

ALTER TABLE email_queue
  ADD COLUMN bcc_email VARCHAR(150) NULL AFTER to_name;
