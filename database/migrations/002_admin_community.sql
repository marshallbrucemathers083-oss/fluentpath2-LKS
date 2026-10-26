-- Incremental FluentPath admin, learning workflow, forum, and moderation schema.
-- Back up the database and inspect this file before applying it.
ALTER TABLE users
  ADD COLUMN role ENUM('user','admin') NOT NULL DEFAULT 'user',
  ADD COLUMN account_status ENUM('active','suspended','banned') NOT NULL DEFAULT 'active',
  ADD COLUMN post_status ENUM('active','suspended') NOT NULL DEFAULT 'active',
  ADD COLUMN post_suspended_until DATETIME NULL,
  ADD COLUMN status_reason TEXT NULL;

ALTER TABLE lessons
  ADD COLUMN is_published TINYINT(1) NOT NULL DEFAULT 1,
  ADD COLUMN archived_at DATETIME NULL;

CREATE TABLE forum_posts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  thread_id BIGINT UNSIGNED NULL,
  title VARCHAR(180) NULL,
  body TEXT NOT NULL,
  category ENUM('Grammar','Vocabulary','Reading','Listening','Writing','Speaking','General') NOT NULL DEFAULT 'General',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  hidden_at DATETIME NULL,
  hidden_reason TEXT NULL,
  INDEX idx_forum_threads(thread_id,created_at),
  INDEX idx_forum_user(user_id,created_at),
  CONSTRAINT fk_forum_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_forum_thread FOREIGN KEY(thread_id) REFERENCES forum_posts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE forum_reports (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id BIGINT UNSIGNED NOT NULL,
  reporter_id BIGINT UNSIGNED NOT NULL,
  reason ENUM('harassment','spam','disruptive','other') NOT NULL,
  details TEXT NULL,
  status ENUM('pending','resolved','dismissed') NOT NULL DEFAULT 'pending',
  resolution_reason TEXT NULL,
  resolved_by BIGINT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  resolved_at DATETIME NULL,
  INDEX idx_reports_status(status,created_at),
  CONSTRAINT fk_report_post FOREIGN KEY(post_id) REFERENCES forum_posts(id) ON DELETE CASCADE,
  CONSTRAINT fk_reporter FOREIGN KEY(reporter_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_report_admin FOREIGN KEY(resolved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE moderation_history (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  admin_id BIGINT UNSIGNED NOT NULL,
  target_user_id BIGINT UNSIGNED NOT NULL,
  action_type VARCHAR(32) NOT NULL,
  reason TEXT NOT NULL,
  duration_days INT NULL,
  report_id BIGINT UNSIGNED NULL,
  content_id BIGINT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_mod_target(target_user_id,created_at),
  CONSTRAINT fk_mod_admin FOREIGN KEY(admin_id) REFERENCES users(id),
  CONSTRAINT fk_mod_target FOREIGN KEY(target_user_id) REFERENCES users(id),
  CONSTRAINT fk_mod_report FOREIGN KEY(report_id) REFERENCES forum_reports(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_appeals (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  moderation_id BIGINT UNSIGNED NULL,
  message TEXT NOT NULL,
  status ENUM('pending','reviewed') NOT NULL DEFAULT 'pending',
  admin_response TEXT NULL,
  reviewed_by BIGINT UNSIGNED NULL,
  reviewed_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_appeals_status(status,created_at),
  CONSTRAINT fk_appeal_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_appeal_action FOREIGN KEY(moderation_id) REFERENCES moderation_history(id) ON DELETE SET NULL,
  CONSTRAINT fk_appeal_admin FOREIGN KEY(reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
