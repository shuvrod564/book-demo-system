<?php
/**
 * Meeting Delete Handler
 * Processes meeting deletion requests with CSRF-style validation
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/classes/Admin.php';
require_once __DIR__ . '/../includes/classes/Meeting.php';

Admin::requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: meetings.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['flash_message'] = 'Invalid meeting ID.';
    $_SESSION['flash_type'] = 'error';
    header('Location: meetings.php');
    exit;
}

$meeting = Meeting::getById($id);

if (!$meeting) {
    $_SESSION['flash_message'] = 'Meeting not found.';
    $_SESSION['flash_type'] = 'error';
    header('Location: meetings.php');
    exit;
}

// Attempt deletion
if (Meeting::deleteById($id)) {
    $_SESSION['flash_message'] = 'Meeting #' . $id . ' (' . $meeting->getFullName() . ') has been deleted successfully.';
    $_SESSION['flash_type'] = 'success';
} else {
    $_SESSION['flash_message'] = 'Failed to delete meeting. Please try again.';
    $_SESSION['flash_type'] = 'error';
}

header('Location: meetings.php');
exit;
