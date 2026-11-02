-- Run this once in phpMyAdmin's SQL tab against your production database.
-- Adds self-hosted page-view tracking (visitors, page views, time on page/site), shown on the
-- new Admin > Analytics page. No IP addresses or other PII are stored - just an anonymous
-- per-browser id (a long-lived cookie) and, when the visitor happens to be logged in, their
-- user_id.

CREATE TABLE IF NOT EXISTS page_views (
  id INT AUTO_INCREMENT PRIMARY KEY,
  visitor_id VARCHAR(36) NOT NULL,
  user_id INT NULL,
  path VARCHAR(255) NOT NULL,
  referrer VARCHAR(255) NULL,
  duration_seconds INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_page_views_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_page_views_visitor (visitor_id),
  INDEX idx_page_views_created (created_at)
) ENGINE=InnoDB;
