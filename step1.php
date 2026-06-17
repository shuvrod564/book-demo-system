<?php
/**
 * Step 1 - Choose Time
 * Calendar and time slot selection page
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/classes/Calendar.php';
require_once __DIR__ . '/includes/classes/Booking.php';

// Get calendar navigation parameters
$year = isset($_GET['year']) ? (int) $_GET['year'] : (int) date('Y');
$month = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('n');

// Validate month/year
if ($month < 1 || $month > 12) $month = (int) date('n');
if ($year < date('Y') || $year > date('Y') + 2) $year = (int) date('Y');

// Create calendar instance
$calendar = new Calendar($year, $month);

// Handle form submission (step 1)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step1_submit'])) {
    $validator = Booking::validateStep1($_POST);
    if ($validator->validate()) {
        // Store selection in session and redirect to step 2
        session_start();
        $_SESSION['selected_date'] = $_POST['selected_date'];
        $_SESSION['selected_time'] = $_POST['selected_time'];
        header('Location: index.php?step=2');
        exit;
    }
}

// Calendar data
$grid = $calendar->generateGrid();
$weekdays = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
$prevMonth = $calendar->getPrevMonth();
$nextMonth = $calendar->getNextMonth();
$today = $calendar->getToday();

// Time slots from config
$timeSlots = TIME_SLOTS;

// Selected values (from session if going back)
session_start();
$selectedDate = $_SESSION['selected_date'] ?? '';
$selectedTime = $_SESSION['selected_time'] ?? '';

// Set selected day on calendar
if (!empty($selectedDate)) {
    $dateParts = explode('-', $selectedDate);
    if ((int)$dateParts[0] === $year && (int)$dateParts[1] === $month) {
        $calendar->setSelectedDay((int)$dateParts[2]);
        $grid = $calendar->generateGrid();
    }
}

// Format selected date for display
$displayDate = '';
if (!empty($selectedDate)) {
    $displayDate = date('l, F j, Y', strtotime($selectedDate));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1, shrink-to-fit=no"> 
    <link rel="icon" type="image/ico" href="images/favicon.ico">
    <title>Book your Demo - Choose Time</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Progress Indicator -->
    <div class="progress-indicator">
        <div class="progress-step active">
            <div class="progress-circle">1</div>
            <span>Choose Time</span>
        </div>
        <div class="progress-line active"></div>
        <div class="progress-step">
            <div class="progress-circle">2</div>
            <span>Your Info</span>
        </div>
    </div>

    <!-- Main Booking Card -->
    <div class="booking-card">

        <!-- Left: Calendar Panel -->
        <div class="calendar-panel">
            <!-- Logo -->
            <div class="calendar-logo">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 3L4 14h7l-2 7 9-11h-7l2-7z" fill="#3a5a78" stroke="#3a5a78" stroke-width="1"/>
                </svg>
            </div>
            <div class="calendar-title">Book your Demo</div>

            <!-- Month Navigation -->
            <div class="calendar-nav">
                <button type="button" class="calendar-nav-btn" data-year="<?= $prevMonth['year'] ?>" data-month="<?= $prevMonth['month'] ?>" title="Previous Month">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M10 2L4 8l6 6" stroke="currentColor" stroke-width="2" fill="none"/></svg>
                </button>
                <span class="calendar-month-year"><?= $calendar->getMonthName() ?> <?= $calendar->getYear() ?></span>
                <button type="button" class="calendar-nav-btn" data-year="<?= $nextMonth['year'] ?>" data-month="<?= $nextMonth['month'] ?>" title="Next Month">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M6 2l6 6-6 6" stroke="currentColor" stroke-width="2" fill="none"/></svg>
                </button>
            </div>

            <!-- Calendar Grid -->
            <div class="calendar-grid">
                <div class="calendar-weekdays">
                    <?php foreach ($weekdays as $day): ?>
                        <div class="calendar-weekday"><?= $day ?></div>
                    <?php endforeach; ?>
                </div>
                <div class="calendar-days">
                    <?php foreach ($grid as $week): ?>
                        <?php foreach ($week as $dayInfo): ?>
                            <div class="calendar-day <?= !$dayInfo['currentMonth'] ? 'other-month' : 'current-month' ?><?= $dayInfo['disabled'] ? ' disabled' : '' ?><?= $dayInfo['selected'] ? ' selected' : '' ?><?= ($dayInfo['currentMonth'] && $dayInfo['day'] === $today) ? ' today' : '' ?>"
                                 <?= $dayInfo['currentMonth'] && !$dayInfo['disabled'] ? 'data-date="' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($dayInfo['day'], 2, '0', STR_PAD_LEFT) . '"' : '' ?>>
                                <?= $dayInfo['day'] ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Right: Form Panel -->
        <div class="form-panel">
            <!-- Meeting Info -->
            <div class="meeting-info">
                <div class="form-section-title">Meeting location</div>
                <div class="meeting-location">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Google Meet
                </div>

                <div class="form-section-title">Meeting duration</div>
                <div class="meeting-duration">
                    <span class="duration-bar"><?= MEETING_DURATION ?> mins</span>
                </div>
            </div>

            <!-- Time Selection -->
            <div class="form-section-title">What time works best?</div>
            <div class="form-section-subtitle" id="selected-date-display">
                <?php if (!empty($displayDate)): ?>
                    Showing times for <?= htmlspecialchars($displayDate) ?>
                <?php else: ?>
                    Select a date on the calendar
                <?php endif; ?>
            </div>

            <div class="timezone-selector">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                UTC +06:00 Dhaka
            </div>

            <!-- Time Slots -->
            <form method="POST" action="index.php?step=1" id="step1-form">
                <input type="hidden" name="selected_date" id="selected_date" value="<?= htmlspecialchars($selectedDate) ?>">
                <input type="hidden" name="selected_time" id="selected_time" value="<?= htmlspecialchars($selectedTime) ?>">

                <div class="time-slots row row-cols-2 g-3" id="time-slots-container">
                    <?php foreach ($timeSlots as $value => $label): ?>
                        <div class="col"> 
                            <button type="button" class="time-slot<?= $selectedTime === $value ? ' selected' : '' ?>"
                                    data-time="<?= htmlspecialchars($value) ?>">
                                <?= htmlspecialchars($label) ?>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="submit" name="step1_submit" class="btn-next" id="btn-next" <?= (empty($selectedDate) || empty($selectedTime)) ? 'disabled' : '' ?>>
                    Next
                </button>
            </form>
        </div>
    </div>

    <script src="js/app.js"></script>
</body>
</html>
