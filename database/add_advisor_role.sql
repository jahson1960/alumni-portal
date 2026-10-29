-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Adds the 'advisor' staff role, plus the tables backing advisor-to-alumni assignments
-- and private advising notes.

ALTER TABLE users MODIFY COLUMN role ENUM('admin','editor','alumni','recruiter','advisor') NOT NULL DEFAULT 'alumni';

CREATE TABLE IF NOT EXISTS advisor_assignments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  advisor_id INT NOT NULL,
  alumni_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_advisor_alumni (advisor_id, alumni_id),
  CONSTRAINT fk_advisor_assignments_advisor FOREIGN KEY (advisor_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_advisor_assignments_alumni FOREIGN KEY (alumni_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS advisor_notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  advisor_id INT NOT NULL,
  alumni_id INT NOT NULL,
  note TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_advisor_notes_advisor FOREIGN KEY (advisor_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_advisor_notes_alumni FOREIGN KEY (alumni_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
