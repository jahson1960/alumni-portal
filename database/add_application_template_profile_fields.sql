-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Lets an application template field bind to the applicant's own profile
-- (name, email, phone, LinkedIn, resume) so the in-app application form can
-- prefill it automatically, LinkedIn-Easy-Apply style, instead of asking
-- the candidate to retype what's already on their profile.

ALTER TABLE application_template_fields
  ADD COLUMN profile_field VARCHAR(30) NULL;
