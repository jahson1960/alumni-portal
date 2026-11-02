-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Adds "easy_apply" as a job's Apply Method, alongside email/link/form. A candidate applies
-- in one click using their profile info (name, email, phone, LinkedIn, resume) - no form, no
-- template needed.

ALTER TABLE jobs MODIFY COLUMN apply_type ENUM('link','email','form','easy_apply') NOT NULL DEFAULT 'email';
