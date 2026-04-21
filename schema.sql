-- ============================================================
--  DOCVAULT — Database Schema  (XAMPP / MySQL)
--
--  How to run this in XAMPP:
--  1. Start XAMPP Control Panel → Start Apache + MySQL
--  2. Visit http://localhost/phpmyadmin
--  3. Click "Import" tab → "Choose File" → select schema.sql → "Go"
--  OR paste the SQL directly into the SQL tab.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `docvault`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `docvault`;

CREATE TABLE IF NOT EXISTS `users` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `first_name`   VARCHAR(80)     NOT NULL,
  `last_name`    VARCHAR(80)     NOT NULL DEFAULT '',
  `email`        VARCHAR(255)    NOT NULL,
  `password`     VARCHAR(255)    NOT NULL COMMENT 'bcrypt hash — password_hash()',
  `storage_used` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'bytes used',
  `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Demo accounts ──────────────────────────────────────────
-- Passwords are bcrypt-hashed.  Plain-text: password123 / securepass
-- Regenerate with: php -r "echo password_hash('your_pass', PASSWORD_DEFAULT);"
--
-- NOTE: These hashes were generated at schema-creation time.
-- If you prefer, leave this block commented out and use the
-- "Create Account" form in the app — auth.php will hash on signup.
--
-- INSERT INTO `users` (`first_name`, `last_name`, `email`, `password`) VALUES
--   ('Alex',  'Johnson', 'alex@docvault.io',  '$2y$12$PASTE_HASH_HERE'),
--   ('Priya', 'Sharma',  'priya@docvault.io', '$2y$12$PASTE_HASH_HERE');

