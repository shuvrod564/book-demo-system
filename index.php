<?php
/**
 * Book Your Demo - Main Entry Point
 * Routes between steps of the booking process
 */
 

// Determine current step
$step = isset($_GET['step']) ? (int) $_GET['step'] : 1;

// Validate step value
if ($step < 1 || $step > 3) {
    $step = 1;
}

// Route to the appropriate step file
switch ($step) {
    case 1:
        require_once __DIR__ . '/step1.php';
        break;
    case 2:
        require_once __DIR__ . '/step2.php';
        break;
    case 3:
        require_once __DIR__ . '/step3.php';
        break;
    default:
        require_once __DIR__ . '/step1.php';
        break;
} 