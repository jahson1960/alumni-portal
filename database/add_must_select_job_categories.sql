ALTER TABLE users ADD COLUMN must_select_job_categories TINYINT(1) NOT NULL DEFAULT 0 AFTER must_change_password;
