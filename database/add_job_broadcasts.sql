-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Adds a log of per-job broadcast messages (poster/admin -> all or one status
-- group of applicants), so past broadcasts can be viewed and recalled.

CREATE TABLE IF NOT EXISTS job_broadcasts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  job_id INT NOT NULL,
  sender_id INT NOT NULL,
  status_filter VARCHAR(30) NOT NULL DEFAULT 'all',
  body TEXT NOT NULL,
  recipient_count INT NOT NULL DEFAULT 0,
  recalled_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_job_broadcasts_job FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
  CONSTRAINT fk_job_broadcasts_sender FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Links each broadcast to the individual message/notification it created per recipient, so
-- recalling a broadcast can remove exactly those rows without touching anything else in the
-- recipient's inbox.
CREATE TABLE IF NOT EXISTS job_broadcast_recipients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  broadcast_id INT NOT NULL,
  user_id INT NOT NULL,
  message_id INT NULL,
  notification_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_job_broadcast_recipients_broadcast FOREIGN KEY (broadcast_id) REFERENCES job_broadcasts(id) ON DELETE CASCADE,
  CONSTRAINT fk_job_broadcast_recipients_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_job_broadcast_recipients_message FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE SET NULL,
  CONSTRAINT fk_job_broadcast_recipients_notification FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE SET NULL
) ENGINE=InnoDB;
