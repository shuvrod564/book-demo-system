<?php
/**
 * Step 3 - Confirmation Page
 * Displayed after a successful booking
 */

require_once __DIR__ . '/includes/config.php';

session_start();

// Must have booking data
if (empty($_SESSION['booking_data'])) {
    header('Location: index.php?step=1');
    exit;
}

$booking = $_SESSION['booking_data'];

// Clear booking data from session
unset($_SESSION['booking_data'], $_SESSION['booking_id']);

// Format role display
$roleLabels = ROLE_OPTIONS;
$roleDisplay = $roleLabels[$booking['role']] ?? ucfirst($booking['role']);

// Format employee display
$empLabels = EMPLOYEE_OPTIONS;
$empDisplay = $empLabels[$booking['employees']] ?? $booking['employees'];

// Format AI experience
$aiLabels = AI_SEARCH_OPTIONS;
$aiDisplay = $aiLabels[$booking['ai_search_experience']] ?? ucfirst($booking['ai_search_experience'] ?? '');

// Format referral
$refLabels = REFERRAL_OPTIONS;
$refDisplay = $refLabels[$booking['referral_source']] ?? ucfirst($booking['referral_source'] ?? '');

// Guest list
$guestEmails = array_filter(array_map('trim', explode(',', $booking['guest_emails'] ?? '')));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1, shrink-to-fit=no"> 
    <link rel="icon" type="image/ico" href="images/favicon.ico">
    <title>Booking Confirmed - Book your Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Progress Indicator -->
    <div class="progress-indicator">
        <div class="progress-step completed">
            <div class="progress-circle">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>
            </div>
            <span>Choose Time</span>
        </div>
        <div class="progress-line active"></div>
        <div class="progress-step completed">
            <div class="progress-circle">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>
            </div>
            <span>Your Info</span>
        </div>
    </div>

    <!-- Confirmation Card -->
    <div class="booking-card step2">
        <div class="confirmation-container">
            <!-- Success Icon -->
            <div class="confirmation-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
            </div>

            <h1>Booking Confirmed!</h1>
            <p class="subtitle">Your demo has been scheduled. You'll receive a confirmation email shortly.</p>

            <!-- Booking Details -->
            <div class="confirmation-details">
                <div class="detail-row">
                    <span class="detail-label">Name</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['email']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date & Time</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['formatted_datetime']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Location</span>
                    <span class="detail-value">Google Meet</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Duration</span>
                    <span class="detail-value"><?= MEETING_DURATION ?> mins</span>
                </div>
                <?php if (!empty($roleDisplay)): ?>
                <div class="detail-row">
                    <span class="detail-label">Role</span>
                    <span class="detail-value"><?= htmlspecialchars($roleDisplay) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($empDisplay)): ?>
                <div class="detail-row">
                    <span class="detail-label">Company Size</span>
                    <span class="detail-value"><?= htmlspecialchars($empDisplay) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($booking['website_url'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Website</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['website_url']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($guestEmails)): ?>
                <div class="detail-row">
                    <span class="detail-label">Guests</span>
                    <span class="detail-value"><?= htmlspecialchars(implode(', ', $guestEmails)) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($booking['notes'])): ?>
                <div class="detail-row">
                    <span class="detail-label d-block">Notes</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['notes']) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <a href="index.php" class="btn-book-another">Book Another Demo</a>
        </div>
    </div>

    <script src="js/app.js"></script>
</body>
</html>
