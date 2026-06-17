<?php
/**
 * Application Configuration File
 * Book Your Demo - Scheduling Application
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'book_demo');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application Settings
define('APP_NAME', 'Book your Demo');
define('APP_URL', 'http://localhost/book-demo-system');
define('TIMEZONE', 'UTC+06:00');

// Meeting Settings
define('MEETING_DURATION', 30); // minutes
define('MEETING_LOCATION', 'Google Meet');
define('MAX_GUESTS', 10);

// Available Time Slots (24h format)
define('TIME_SLOTS', [
    '14:30' => '2:30 pm',
    '15:00' => '3:00 pm',
    '17:00' => '5:00 pm',
    '17:30' => '5:30 pm',
    '18:00' => '6:00 pm',
    '18:30' => '6:30 pm',
]);

// Role Options
define('ROLE_OPTIONS', [
    '' => 'Select',
    'ceo' => 'CEO / Founder',
    'cto' => 'CTO / Technical Lead',
    'cmo' => 'CMO / Marketing Lead',
    'manager' => 'Manager',
    'developer' => 'Developer',
    'designer' => 'Designer',
    'analyst' => 'Analyst',
    'other' => 'Other',
]);

// Employee Count Options
define('EMPLOYEE_OPTIONS', [
    '' => 'Select',
    '1-10' => '1-10 employees',
    '11-50' => '11-50 employees',
    '51-200' => '51-200 employees',
    '201-500' => '201-500 employees',
    '501-1000' => '501-1000 employees',
    '1000+' => '1000+ employees',
]);

// AI Search Experience Options
define('AI_SEARCH_OPTIONS', [
    '' => 'Select',
    'beginner' => 'Beginner - Just getting started',
    'intermediate' => 'Intermediate - Some experience',
    'advanced' => 'Advanced - Extensive experience',
    'expert' => 'Expert - Building AI solutions',
]);

// Referral Options
define('REFERRAL_OPTIONS', [
    '' => 'Select',
    'google' => 'Google Search',
    'social' => 'Social Media',
    'referral' => 'Friend / Colleague Referral',
    'blog' => 'Blog Post / Article',
    'email' => 'Email Campaign',
    'conference' => 'Conference / Event',
    'other' => 'Other',
]);

// Admin Credentials (fallback when database is unavailable)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'password');

// Set timezone
date_default_timezone_set('Asia/Dhaka');
