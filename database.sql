-- =====================================================
-- Book Your Demo - Database Schema
-- MySQL / MariaDB
-- =====================================================

-- Create the database
CREATE DATABASE IF NOT EXISTS `book_demo`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `book_demo`;

-- =====================================================
-- Bookings Table
-- =====================================================
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `role` VARCHAR(100) DEFAULT NULL,
  `employees` VARCHAR(50) DEFAULT NULL,
  `website_url` VARCHAR(500) DEFAULT NULL,
  `ai_search_experience` VARCHAR(100) DEFAULT NULL,
  `referral_source` VARCHAR(100) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `guest_emails` TEXT DEFAULT NULL,
  `selected_date` DATE NOT NULL,
  `selected_time` VARCHAR(10) NOT NULL,
  `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_date` (`selected_date`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Admins Table
-- =====================================================
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(80) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(150) NOT NULL,
  `role` ENUM('admin', 'super_admin') NOT NULL DEFAULT 'admin',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `last_login` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `idx_username` (`username`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Default Admin User
-- Username: admin | Password: admin123
-- =====================================================
INSERT INTO `admins` (`username`, `password`, `email`, `full_name`, `role`, `status`)
VALUES (
  'admin',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'admin@bookdemo.com',
  'Administrator',
  'super_admin',
  'active'
);

-- =====================================================
-- Sample Booking Data (optional - for testing)
-- =====================================================
INSERT INTO `bookings` (`first_name`, `last_name`, `email`, `role`, `employees`, `website_url`, `ai_search_experience`, `referral_source`, `notes`, `guest_emails`, `selected_date`, `selected_time`, `status`)
VALUES
  ('Alex', 'Martinez', 'alex.m@techvanguard.com', 'cto', '51-200', 'https://techvanguard.com', 'advanced', 'google', 'Interested in custom API integrations.', 'dev-team@techvanguard.com', '2026-06-23', '10:00', 'confirmed'),
  ('Jessica', 'Taylor', 'jessica@growthscale.io', 'ceo', '11-50', 'https://growthscale.io', 'intermediate', 'referral', 'Seeking scalability solutions.', '', '2026-06-23', '11:30', 'pending'),
  ('Brian', 'O\'Connor', 'brian@fintechly.net', 'manager', '201-500', 'https://fintechly.net', 'beginner', 'linkedin', 'Compliance and security questions.', 'legal@fintechly.net', '2026-06-23', '14:00', 'confirmed'),
  ('Sophia', 'Lin', 'sophia.l@creativecloud.co', 'designer', '1-10', 'https://creativecloud.co', 'intermediate', 'social', 'Ui/UX integration consultation.', '', '2026-06-24', '09:00', 'pending'),
  ('Marcus', 'Vance', 'marcus@globalcorp.com', 'cmo', '501-1000', 'https://globalcorp.com', 'expert', 'conference', 'Enterprise tier onboarding discussion.', 'marketing-leads@globalcorp.com', '2026-06-24', '16:00', 'confirmed'),
  ('Elena', 'Rostova', 'elena@cyberdefense.org', 'cto', '51-200', 'https://cyberdefense.org', 'advanced', 'email', 'Assessing data privacy architecture.', 'sec-ops@cyberdefense.org', '2026-06-25', '10:30', 'confirmed'),
  ('David', 'Kim', 'dkim@alphaventures.vc', 'partner', '11-50', 'https://alphaventures.vc', 'intermediate', 'referral', 'Evaluating for portfolio companies.', '', '2026-06-25', '13:00', 'confirmed'),
  ('Rachel', 'Green', 'rachel@modafit.com', 'founder', '1-10', 'https://modafit.com', 'beginner', 'google', 'E-commerce automation assessment.', '', '2026-06-25', '15:30', 'pending'),
  ('Tom', 'Hardy', 't.hardy@logisticsx.com', 'director', '501-1000', 'https://logisticsx.com', 'intermediate', 'conference', 'Route optimization workflows.', 'dispatch@logisticsx.com', '2026-06-26', '11:00', 'confirmed'),
  ('Aisha', 'Khan', 'aisha@healthtech.io', 'ceo', '11-50', 'https://healthtech.io', 'expert', 'linkedin', 'HIPAA compliant deployment query.', 'cto@healthtech.io', '2026-06-26', '14:30', 'confirmed'),
  ('Chris', 'Evans', 'c.evans@shieldmedia.com', 'manager', '201-500', 'https://shieldmedia.com', 'beginner', 'social', 'Media asset management workflows.', '', '2026-06-29', '10:00', 'pending'),
  ('Natalie', 'Portman', 'natalie@biolabs.edu', 'researcher', '51-200', 'https://biolabs.edu', 'advanced', 'google', 'Academic licensing models.', 'lab-group@biolabs.edu', '2026-06-29', '16:15', 'confirmed'),
  ('James', 'Holden', 'jholden@roci-transport.com', 'captain', '1-10', 'https://roci-transport.com', 'intermediate', 'referral', 'Fleet tracking automation.', 'naomi@roci-transport.com', '2026-06-30', '08:30', 'confirmed'),
  ('Amos', 'Burton', 'amos@mechanix.engineering', 'lead', '11-50', 'https://mechanix.engineering', 'beginner', 'email', 'Heavy machinery telemetry data.', '', '2026-06-30', '13:00', 'pending'),
  ('Robert', 'Downey', 'robert@starkind.com', 'ceo', '501-1000', 'https://starkind.com', 'expert', 'conference', 'Proprietary engine configurations.', 'pepper@starkind.com', '2026-07-01', '09:00', 'confirmed'),
  ('Oliver', 'Queen', 'oliver@queeninc.com', 'founder', '201-500', 'https://queeninc.com', 'intermediate', 'linkedin', 'Supply chain transparency tools.', '', '2026-07-01', '15:00', 'pending'),
  ('Barry', 'Allen', 'barry@star-labs.org', 'scientist', '1-10', 'https://star-labs.org', 'advanced', 'google', 'Real-time data processing speeds.', 'cisco@star-labs.org', '2026-07-02', '10:00', 'confirmed'),
  ('Diana', 'Prince', 'diana@themyscira.org', 'director', '11-50', 'https://themyscira.org', 'expert', 'referral', 'Archival preservation solutions.', '', '2026-07-02', '14:00', 'confirmed'),
  ('Bruce', 'Wayne', 'bruce@waynecorp.com', 'chairman', '501-1000', 'https://waynecorp.com', 'expert', 'conference', 'Satellite communication relays.', 'alfred@waynecorp.com', '2026-07-03', '11:00', 'confirmed'),
  ('Clark', 'Kent', 'clark@dailyplanet.press', 'journalist', '201-500', 'https://dailyplanet.press', 'beginner', 'email', 'Editorial system evaluation.', '', '2026-07-03', '16:30', 'pending');