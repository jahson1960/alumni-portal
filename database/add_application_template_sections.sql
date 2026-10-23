-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Groups every application template's fields into three sections - Profile
-- Info, Education, and Experience - shown as headings on the candidate's
-- apply form.
--
-- Safe to re-run: ADD COLUMN is skipped (with an error you can ignore) if it
-- already exists; the two UPDATEs below are idempotent either way.

ALTER TABLE application_template_fields
  ADD COLUMN section ENUM('profile_info','education','experience') NOT NULL DEFAULT 'profile_info';

-- The ALTER above defaults every existing row (including any Education/
-- Experience field already created by an earlier visit to Post a Job) to
-- profile_info, since a column default backfills uniformly. Put those two
-- back in their real section based on the profile_field they're already
-- bound to.
UPDATE application_template_fields SET section = 'education' WHERE profile_field = 'education';
UPDATE application_template_fields SET section = 'experience' WHERE profile_field = 'experience';
