-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Adds support for the redesigned "Submit an Article" page: a featured-image
-- column, and a 'draft' status so "Save as Draft" has somewhere to land.

ALTER TABLE articles
  ADD COLUMN image VARCHAR(255) NULL AFTER body,
  MODIFY COLUMN status ENUM('draft','pending','published') NOT NULL DEFAULT 'pending';
