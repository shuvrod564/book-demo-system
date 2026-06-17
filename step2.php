<?php
/**
 * Step 2 - Your Information
 * Personal details form page
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/classes/Booking.php';

session_start();

// Must have completed step 1
if (empty($_SESSION['selected_date']) || empty($_SESSION['selected_time'])) {
    header('Location: index.php?step=1');
    exit;
}

$selectedDate = $_SESSION['selected_date'];
$selectedTime = $_SESSION['selected_time'];
$displayDateTime = date('l, F j, Y g:i A', strtotime($selectedDate . ' ' . $selectedTime));
$displayTimeLabel = TIME_SLOTS[$selectedTime] ?? $selectedTime;

// Handle form submission
$errors = [];
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step2_submit'])) {
    $formData = Validator::sanitizeArray($_POST);

    $validator = Booking::validateStep2($formData);
    if ($validator->validate()) {
        // Merge with session data and save booking
        $formData['selected_date'] = $selectedDate;
        $formData['selected_time'] = $selectedTime;

        $booking = Booking::fromArray($formData);
        $booking->setStatus('confirmed');

        if ($booking->save()) {
            // Store booking ID in session for confirmation page
            $_SESSION['booking_id'] = $booking->getId();
            $_SESSION['booking_data'] = [
                'first_name' => $booking->getFirstName(),
                'last_name' => $booking->getLastName(),
                'email' => $booking->getEmail(),
                'role' => $booking->getRole(),
                'employees' => $booking->getEmployees(),
                'website_url' => $booking->getWebsiteUrl(),
                'ai_search_experience' => $booking->getAiSearchExperience(),
                'referral_source' => $booking->getReferralSource(),
                'notes' => $booking->getNotes(),
                'guest_emails' => $booking->getGuestEmails(),
                'selected_date' => $selectedDate,
                'selected_time' => $selectedTime,
                'formatted_datetime' => $displayDateTime,
            ];

            // Clear step data from session
            unset($_SESSION['selected_date'], $_SESSION['selected_time']);

            header('Location: index.php?step=3');
            exit;
        }
    } else {
        $errors = $validator->getErrors();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1, shrink-to-fit=no"> 
    <link rel="icon" type="image/ico" href="images/favicon.ico">
    <title>Book your Demo - Your Information</title>
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
        <div class="progress-step active">
            <div class="progress-circle">2</div>
            <span>Your Info</span>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="booking-card step2">
        <div style="padding: 32px;">

            <!-- Header -->
            <div class="step2-header">
                <h2>Your information</h2>
                <div class="step2-datetime">
                    <span><?= htmlspecialchars($displayDateTime) ?></span>
                    <a href="index.php?step=1" class="edit-link">Edit</a>
                </div>
                <div class="step2-location">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Google Meet
                </div>
            </div>

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin: 8px 0 0 20px; padding: 0;">
                        <?php foreach ($errors as $fieldErrors): ?>
                            <?php foreach ($fieldErrors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="index.php?step=2" id="step2-form" class="demo-form">
                <!-- First & Last Name -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First name <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" class="form-control<?= isset($errors['first_name']) ? ' is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($formData['first_name'] ?? '') ?>" required>
                        <?php if (isset($errors['first_name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['first_name'][0]) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last name <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" class="form-control<?= isset($errors['last_name']) ? ' is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($formData['last_name'] ?? '') ?>" required>
                        <?php if (isset($errors['last_name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['last_name'][0]) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Your email address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['email'][0]) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Role -->
                <div class="form-group">
                    <label for="role">Your Role <span class="required">*</span></label>
                    <select id="role" name="role" class="form-control<?= isset($errors['role']) ? ' is-invalid' : '' ?>" required>
                        <?php foreach (ROLE_OPTIONS as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= ($formData['role'] ?? '') === $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['role'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['role'][0]) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Number of Employees -->
                <div class="form-group">
                    <label for="employees">Number of employees <span class="required">*</span></label>
                    <select id="employees" name="employees" class="form-control<?= isset($errors['employees']) ? ' is-invalid' : '' ?>" required>
                        <?php foreach (EMPLOYEE_OPTIONS as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= ($formData['employees'] ?? '') === $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['employees'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['employees'][0]) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Website URL -->
                <div class="form-group">
                    <label for="website_url">Website URL</label>
                    <input type="url" id="website_url" name="website_url" class="form-control<?= isset($errors['website_url']) ? ' is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($formData['website_url'] ?? '') ?>" placeholder="https://example.com">
                    <?php if (isset($errors['website_url'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['website_url'][0]) ?></div>
                    <?php endif; ?>
                </div>

                <!-- AI Search Experience -->
                <div class="form-group">
                    <label for="ai_search_experience">AI Search Experience</label>
                    <select id="ai_search_experience" name="ai_search_experience" class="form-control">
                        <?php foreach (AI_SEARCH_OPTIONS as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= ($formData['ai_search_experience'] ?? '') === $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- How did you hear about us -->
                <div class="form-group">
                    <label for="referral_source">How did you hear about us?</label>
                    <select id="referral_source" name="referral_source" class="form-control">
                        <?php foreach (REFERRAL_OPTIONS as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= ($formData['referral_source'] ?? '') === $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Notes -->
                <div class="form-group">
                    <label for="notes">Anything else we should know?</label>
                    <textarea id="notes" name="notes" class="form-control" rows="3"><?= htmlspecialchars($formData['notes'] ?? '') ?></textarea>
                </div>

                <!-- Add Guests -->
                <div class="guests-section">
                    <div class="guests-header">
                        <h3>Add guests</h3>
                        <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>
                        </svg>
                    </div>
                    <div class="guests-subtitle">Invite up to 10 guests to attend the meeting.</div>
                    <div class="guest-input-row">
                        <input type="email" id="guest_email_input" class="form-control" placeholder="Add an email...">
                        <button type="button" id="btn-add-guest" class="btn-add-guest">Add</button>
                    </div>
                    <input type="hidden" name="guest_emails" id="guest_emails" value="<?= htmlspecialchars($formData['guest_emails'] ?? '') ?>">
                    <div class="guest-counter" id="guest-counter">0/10 guests</div>
                    <div class="guest-list" id="guest-list">
                        <span class="guest-empty">Added guests will appear here.</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-actions">
                    <a href="index.php?step=1" class="btn-back">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        Back
                    </a>
                    <button type="submit" name="step2_submit" class="btn-confirm">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/app.js"></script>
</body>
</html>
