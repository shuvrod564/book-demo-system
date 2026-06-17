<?php
/**
 * AJAX Endpoint - Get Available Time Slots
 * Returns time slots for a selected date
 */

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

$date = $_GET['date'] ?? '';

if (empty($date)) {
    echo json_encode(['success' => false, 'message' => 'Date is required']);
    exit;
}

// Validate date format
$dateObj = DateTime::createFromFormat('Y-m-d', $date);
if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
    exit;
}

// Check if date is in the past
$today = new DateTime('today');
if ($dateObj < $today) {
    echo json_encode(['success' => false, 'message' => 'Cannot book past dates']);
    exit;
}

// Get time slots from config
$timeSlots = TIME_SLOTS;

// Build response
$slots = [];
foreach ($timeSlots as $value => $label) {
    $slots[] = [
        'value' => $value,
        'label' => $label,
        'available' => true, // All slots are available by default
    ];
}

echo json_encode([
    'success' => true,
    'date' => $date,
    'slots' => $slots,
]);
