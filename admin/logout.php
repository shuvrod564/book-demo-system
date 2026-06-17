<?php
/**
 * Admin Logout
 * Destroys the admin session and redirects to login
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/classes/Admin.php';

Admin::logout();
header('Location: login.php');
exit;
