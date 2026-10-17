-- FluentPath Schema Definition
-- Supports safe incremental execution (CREATE TABLE IF NOT EXISTS)

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lessons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `level` ENUM('A1', 'A2', 'B1', 'B2', 'C1', 'C2') NOT NULL,
  `skill` ENUM('Grammar', 'Vocabulary', 'Reading', 'Listening', 'Writing', 'Speaking') NOT NULL,
  `order_num` INT DEFAULT 1,
  `title` VARCHAR(255) NOT NULL,
  `learning_objectives` TEXT NOT NULL,
  `explanation` LONGTEXT NOT NULL,
  `examples` LONGTEXT NOT NULL,
  `notes` TEXT NULL,
  `practice_guide` TEXT NULL,
  `further_reading` TEXT NULL,
  `xp_reward` INT DEFAULT 25,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_lessons_level_skill` (`level`, `skill`, `order_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `challenges` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `lesson_id` INT NULL,
  `type` ENUM('multiple_choice', 'fill_blank', 'sentence_order', 'grammar_correction', 'reading_comprehension', 'vocab_match', 'listening_comprehension', 'short_answer') NOT NULL,
  `skill` VARCHAR(50) DEFAULT 'Grammar',
  `level` VARCHAR(10) DEFAULT 'A1',
  `topic` VARCHAR(100) DEFAULT 'General',
  `difficulty` ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
  `prompt` TEXT NOT NULL,
  `passage` TEXT NULL,
  `audio_text` TEXT NULL,
  `correct_answer` TEXT NOT NULL,
  `explanation` TEXT NOT NULL,
  `xp_reward` INT DEFAULT 10,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_challenges_lesson` (`lesson_id`),
  INDEX `idx_challenges_filter` (`skill`, `level`, `difficulty`),
  CONSTRAINT `fk_challenges_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `challenge_options` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `challenge_id` INT NOT NULL,
  `option_key` VARCHAR(10) NOT NULL,
  `option_text` TEXT NOT NULL,
  `is_correct` TINYINT(1) DEFAULT 0,
  INDEX `idx_challenge_options_cid` (`challenge_id`),
  CONSTRAINT `fk_options_challenge` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `challenge_attempts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `challenge_id` INT NOT NULL,
  `submitted_answer` TEXT NOT NULL,
  `is_correct` TINYINT(1) NOT NULL,
  `score` INT DEFAULT 0,
  `xp_awarded` INT DEFAULT 0,
  `attempted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_attempts_user` (`user_id`, `challenge_id`),
  INDEX `idx_attempts_date` (`attempted_at`),
  CONSTRAINT `fk_attempts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_attempts_challenge` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_lesson_progress` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `lesson_id` INT NOT NULL,
  `is_completed` TINYINT(1) DEFAULT 0,
  `completed_at` DATETIME NULL,
  UNIQUE KEY `uq_user_lesson` (`user_id`, `lesson_id`),
  CONSTRAINT `fk_progress_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_progress_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `xp_transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `amount` INT NOT NULL,
  `source_type` ENUM('lesson_completion', 'challenge_completion', 'academy_practice', 'exam_completion', 'achievement') NOT NULL,
  `source_id` INT NULL,
  `event_key` VARCHAR(120) NOT NULL UNIQUE,
  `description` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_xp_user_date` (`user_id`, `created_at`),
  CONSTRAINT `fk_xp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `exam_attempts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `exam_title` VARCHAR(255) NOT NULL,
  `reading_score` INT DEFAULT 0,
  `listening_score` INT DEFAULT 0,
  `structure_score` INT DEFAULT 0,
  `total_score` INT DEFAULT 0,
  `max_score` INT DEFAULT 100,
  `duration_seconds` INT DEFAULT 0,
  `answers_json` LONGTEXT NULL,
  `completed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_exams_user` (`user_id`, `completed_at`),
  CONSTRAINT `fk_exam_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `achievements` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `title` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `icon` VARCHAR(50) NOT NULL,
  `xp_reward` INT DEFAULT 50
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_achievements` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `achievement_id` INT NOT NULL,
  `unlocked_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_user_achievement` (`user_id`, `achievement_id`),
  CONSTRAINT `fk_ua_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ua_achievement` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
