-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Adds "recruiter" as a user role, alongside admin/editor/alumni. Recruiters are
-- created directly by an admin (Admin > Create Staff Account), not via the alumni
-- roster, and are restricted to Jobs + Messages - no Alumni Directory, Connections,
-- Community Feed, or Mentorship.

ALTER TABLE users
  MODIFY COLUMN role ENUM('admin','editor','alumni','recruiter') NOT NULL DEFAULT 'alumni';
