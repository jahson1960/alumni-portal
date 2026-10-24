-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Adds "Interview Scheduled" as a status a poster can set on an applicant,
-- between Shortlisted and Rejected in the triage flow.

ALTER TABLE job_applications
  MODIFY COLUMN status ENUM('new','reviewed','shortlisted','interview_scheduled','rejected') NOT NULL DEFAULT 'new';
