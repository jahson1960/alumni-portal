-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Adds a 4th, optional "Additional Info" section (Salary Expectation, How
-- soon can you resume?) to the application template system, and backfills
-- those two questions onto any "Quick Apply" template that was already
-- created before this existed.
--
-- Safe to re-run: the MODIFY is harmless to repeat, and the two INSERTs are
-- guarded by NOT EXISTS so they never create duplicates.

ALTER TABLE application_template_fields
  MODIFY COLUMN section ENUM('profile_info','education','experience','additional_info') NOT NULL DEFAULT 'profile_info';

INSERT INTO application_template_fields (template_id, label, field_type, options, is_required, sort_order, profile_field, section)
SELECT t.id, 'Salary Expectation', 'text', NULL, 0, 7, NULL, 'additional_info'
FROM application_templates t
WHERE t.name = 'Quick Apply'
  AND NOT EXISTS (
    SELECT 1 FROM application_template_fields f WHERE f.template_id = t.id AND f.label = 'Salary Expectation'
  );

INSERT INTO application_template_fields (template_id, label, field_type, options, is_required, sort_order, profile_field, section)
SELECT t.id, 'How soon can you resume?', 'select', 'Immediately, 2 weeks notice, 1 month notice, More than 1 month', 0, 8, NULL, 'additional_info'
FROM application_templates t
WHERE t.name = 'Quick Apply'
  AND NOT EXISTS (
    SELECT 1 FROM application_template_fields f WHERE f.template_id = t.id AND f.label = 'How soon can you resume?'
  );
