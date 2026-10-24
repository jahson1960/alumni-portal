-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Lets the Applicants page tell apart a candidate who filled out the full
-- application form from one who used "Quick Apply with My Profile" (whose
-- "responses" are just their profile restated, not anything they typed).

ALTER TABLE job_applications
  ADD COLUMN via_quick_apply TINYINT(1) NOT NULL DEFAULT 0 AFTER responses;
