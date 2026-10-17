-- FluentPath Database Initialization Script
-- Run this script as root/admin: sudo mariadb < database/setup.sql

CREATE DATABASE IF NOT EXISTS `fluentpath` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

-- Create dedicated application user for local connections
CREATE USER IF NOT EXISTS 'fluentpath_user'@'localhost' IDENTIFIED BY 'fluentpath_secret_2026';
CREATE USER IF NOT EXISTS 'fluentpath_user'@'127.0.0.1' IDENTIFIED BY 'fluentpath_secret_2026';

-- Grant required privileges on fluentpath database
GRANT ALL PRIVILEGES ON `fluentpath`.* TO 'fluentpath_user'@'localhost';
GRANT ALL PRIVILEGES ON `fluentpath`.* TO 'fluentpath_user'@'127.0.0.1';

-- Also grant user 'rezon' for frictionless CLI access if needed
CREATE USER IF NOT EXISTS 'rezon'@'localhost';
GRANT ALL PRIVILEGES ON `fluentpath`.* TO 'rezon'@'localhost';

FLUSH PRIVILEGES;

SELECT 'FluentPath database and user successfully configured!' AS status;
