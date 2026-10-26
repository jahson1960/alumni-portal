-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Lets a broadcast optionally also email its recipients (job_broadcasts.via_email),
-- and lets admin control email send rate (batch size + minutes between batches) so a
-- burst of broadcast emails doesn't trip spam filters. The rate-limit settings default
-- to 20 emails every 0 minutes (i.e. unthrottled, today's existing behavior) until an
-- admin sets them from Admin > Settings.

ALTER TABLE job_broadcasts
  ADD COLUMN via_email TINYINT(1) NOT NULL DEFAULT 0 AFTER recipient_count;
