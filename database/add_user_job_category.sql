-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Adds alumni "job category interests" (selected at registration, editable from
-- their profile or by an admin) and powers the new "Jobs for Me" tab. Also
-- expands the job-category taxonomy and backfills the existing alumni roster's
-- categories from their profile's "industry" field.

CREATE TABLE IF NOT EXISTS user_job_category (
  user_id INT NOT NULL,
  category_id INT NOT NULL,
  PRIMARY KEY (user_id, category_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Lets admins toggle the new "Jobs for Me" tab like the other Job Board tabs.
INSERT IGNORE INTO page_tab_visibility (page_key, tab_key, label, is_visible)
VALUES ('jobs', 'forme', 'Jobs for Me', 1);

-- Remove leftover test data from earlier development.
DELETE FROM categories WHERE type = 'job' AND slug = 'multipart-sanity-test';

-- Expand the job-category taxonomy (the 5 existing categories - Banking & Finance,
-- Consulting, Marketing, Operations, Technology - are left untouched since live
-- job posts already reference their ids).
INSERT IGNORE INTO categories (type, name, slug) VALUES
  ('job', 'Agriculture & Agribusiness', 'agriculture-agribusiness'),
  ('job', 'Consumer Goods & Retail', 'consumer-goods-retail'),
  ('job', 'Energy, Oil & Gas', 'energy-oil-gas'),
  ('job', 'Healthcare & Pharmaceuticals', 'healthcare-pharmaceuticals'),
  ('job', 'Insurance', 'insurance'),
  ('job', 'Logistics & Supply Chain', 'logistics-supply-chain'),
  ('job', 'Manufacturing', 'manufacturing'),
  ('job', 'Media & Entertainment', 'media-entertainment'),
  ('job', 'Real Estate & Construction', 'real-estate-construction'),
  ('job', 'Legal & Professional Services', 'legal-professional-services'),
  ('job', 'Telecommunications', 'telecommunications'),
  ('job', 'Human Resources', 'human-resources'),
  ('job', 'Sales & Business Development', 'sales-business-development'),
  ('job', 'Education & Training', 'education-training'),
  ('job', 'Non-Profit & Public Sector', 'non-profit-public-sector'),
  ('job', 'Hospitality & Tourism', 'hospitality-tourism'),
  ('job', 'Entrepreneurship & Startups', 'entrepreneurship-startups');

-- Backfill existing alumni's job-category interests from their profile's
-- "industry" field - exact matches first, then a few synonym/near-miss passes.
-- Idempotent: INSERT IGNORE skips rows that already exist (composite PK).
INSERT IGNORE INTO user_job_category (user_id, category_id)
SELECT u.id, c.id FROM users u
JOIN categories c ON c.type = 'job' AND c.name = u.industry
WHERE u.role = 'alumni' AND u.industry IS NOT NULL AND u.industry != '';

INSERT IGNORE INTO user_job_category (user_id, category_id)
SELECT u.id, c.id FROM users u
JOIN categories c ON c.type = 'job' AND c.slug = 'consumer-goods-retail'
WHERE u.role = 'alumni' AND u.industry = 'Consumer Goods';

INSERT IGNORE INTO user_job_category (user_id, category_id)
SELECT u.id, c.id FROM users u
JOIN categories c ON c.type = 'job' AND c.slug = 'energy-oil-gas'
WHERE u.role = 'alumni' AND u.industry IN ('Energy', 'Oil & Gas');

INSERT IGNORE INTO user_job_category (user_id, category_id)
SELECT u.id, c.id FROM users u
JOIN categories c ON c.type = 'job' AND c.slug = 'healthcare-pharmaceuticals'
WHERE u.role = 'alumni' AND u.industry = 'Healthcare';

INSERT IGNORE INTO user_job_category (user_id, category_id)
SELECT u.id, c.id FROM users u
JOIN categories c ON c.type = 'job' AND c.slug = 'legal-professional-services'
WHERE u.role = 'alumni' AND u.industry = 'Professional Services';

INSERT IGNORE INTO user_job_category (user_id, category_id)
SELECT u.id, c.id FROM users u
JOIN categories c ON c.type = 'job' AND c.slug = 'real-estate-construction'
WHERE u.role = 'alumni' AND u.industry = 'Real Estate';

-- "Technology & Fintech" spans both domains, so it seeds both categories.
INSERT IGNORE INTO user_job_category (user_id, category_id)
SELECT u.id, c.id FROM users u
JOIN categories c ON c.type = 'job' AND c.slug = 'technology'
WHERE u.role = 'alumni' AND u.industry = 'Technology & Fintech';

INSERT IGNORE INTO user_job_category (user_id, category_id)
SELECT u.id, c.id FROM users u
JOIN categories c ON c.type = 'job' AND c.slug = 'banking-finance'
WHERE u.role = 'alumni' AND u.industry = 'Technology & Fintech';
