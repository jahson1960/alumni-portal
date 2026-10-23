-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Adds the in-app job application form feature: reusable application
-- templates a poster builds once (a set of custom questions) and attaches
-- to any of their job posts, a third "form" apply method alongside the
-- existing email/link ones, and applicant tracking (submitted answers +
-- a new/reviewed/shortlisted/rejected status) on job_applications.

CREATE TABLE IF NOT EXISTS application_templates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS application_template_fields (
  id INT AUTO_INCREMENT PRIMARY KEY,
  template_id INT NOT NULL,
  label VARCHAR(200) NOT NULL,
  field_type ENUM('text','textarea','email','phone','number','url','select','file') NOT NULL DEFAULT 'text',
  options VARCHAR(500) NULL,
  is_required TINYINT(1) NOT NULL DEFAULT 0,
  sort_order INT NOT NULL DEFAULT 0,
  FOREIGN KEY (template_id) REFERENCES application_templates(id) ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE jobs
  MODIFY COLUMN apply_type ENUM('link','email','form') NOT NULL DEFAULT 'email',
  ADD COLUMN application_template_id INT NULL;

ALTER TABLE jobs
  ADD CONSTRAINT fk_jobs_application_template FOREIGN KEY (application_template_id) REFERENCES application_templates(id) ON DELETE SET NULL;

ALTER TABLE job_applications
  ADD COLUMN status ENUM('new','reviewed','shortlisted','rejected') NOT NULL DEFAULT 'new' AFTER job_id,
  ADD COLUMN responses MEDIUMTEXT NULL AFTER status;
