ALTER TABLE alumni_roster ADD COLUMN matriculation_year INT NULL AFTER email;
ALTER TABLE alumni_roster MODIFY COLUMN graduation_year INT NULL;
ALTER TABLE users ADD COLUMN matriculation_year YEAR NULL AFTER graduation_year;
