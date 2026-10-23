-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Groups every application template's fields into three sections - Profile
-- Info, Education, and Experience - shown as headings on the candidate's
-- apply form. Existing fields default to profile_info, same as today.

ALTER TABLE application_template_fields
  ADD COLUMN section ENUM('profile_info','education','experience') NOT NULL DEFAULT 'profile_info';
