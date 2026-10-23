-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Adds a free-text "application instructions" field posters can fill in when
-- creating a job, shown to candidates when they click Apply (e.g. "Send your
-- CV to careers@company.com using the job title as the subject").

ALTER TABLE jobs
  ADD COLUMN application_instructions TEXT NULL AFTER apply_value;
