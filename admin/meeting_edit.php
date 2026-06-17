<?php
/**
 * Meeting Edit Page
 * Edit meeting details and update status
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/classes/Admin.php';
require_once __DIR__ . '/../includes/classes/Meeting.php';
require_once __DIR__ . '/../includes/classes/Validator.php';

Admin::requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$meeting = Meeting::getById($id);

if (!$meeting) {
    $_SESSION['flash_message'] = 'Meeting not found.';
    $_SESSION['flash_type'] = 'error';
    header('Location: meetings.php');
    exit;
}

$errors = [];
$successMessage = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ---- Update Status Action ----
    if ($action === 'update_status') {
        $newStatus = $_POST['status'] ?? '';
        $allowedStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];

        if (in_array($newStatus, $allowedStatuses)) {
            $meeting->setStatus($newStatus);
            if ($meeting->save()) {
                $_SESSION['flash_message'] = 'Meeting status updated successfully.';
                $_SESSION['flash_type'] = 'success';
                header('Location: meeting_edit.php?id=' . $id);
                exit;
            }
        }
    }

    // ---- Update Details Action ----
    if ($action === 'update_details') {
        $formData = Validator::sanitizeArray($_POST);

        $validator = new Validator($formData);
        $validator->addRule('first_name', 'required');
        $validator->addRule('last_name', 'required');
        $validator->addRule('email', 'required');
        $validator->addRule('email', 'email');
        $validator->addRule('selected_date', 'required');
        $validator->addRule('selected_time', 'required');

        if ($validator->validate()) {
            // Update meeting properties
            $meeting->setFirstName($formData['first_name']);
            $meeting->setLastName($formData['last_name']);
            $meeting->setEmail($formData['email']);
            $meeting->setSelectedDate($formData['selected_date']);
            $meeting->setSelectedTime($formData['selected_time']);
            $meeting->setNotes($formData['notes'] ?? '');

            if (!empty($formData['status'])) {
                $meeting->setStatus($formData['status']);
            }

            if ($meeting->save()) {
                $_SESSION['flash_message'] = 'Meeting updated successfully.';
                $_SESSION['flash_type'] = 'success';
                header('Location: meeting_edit.php?id=' . $id);
                exit;
            } else {
                $errors[] = 'Failed to update meeting. Please try again.';
            }
        } else {
            $allErrors = $validator->getErrors();
            foreach ($allErrors as $fieldErrors) {
                foreach ($fieldErrors as $err) {
                    $errors[] = $err;
                }
            }
        }
    }
}

$pageTitle = 'Edit Meeting';
$currentPage = 'meeting_edit.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1, shrink-to-fit=no"> 
    <link rel="icon" type="image/ico" href="../images/favicon.ico">
    <title>Edit Meeting #<?= $meeting->getId() ?> - Admin | Book your Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../includes/partials/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Topbar -->
        <?php include __DIR__ . '/../includes/partials/admin_topbar.php'; ?>

        <!-- Content -->
        <div class="admin-content">

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="flash-message <?= htmlspecialchars($_SESSION['flash_type'] ?? 'info') ?>">
                    <?= htmlspecialchars($_SESSION['flash_message']) ?>
                </div>
                <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
            <?php endif; ?>

            <!-- Back Link -->
            <div style="margin-bottom: 20px;">
                <a href="meetings.php" style="display: inline-flex; align-items: center; gap: 6px; color: #3a5a78; text-decoration: none; font-size: 0.88rem; font-weight: 500;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Back to Meetings
                </a>
            </div>

            <!-- Quick Status Update -->
            <div class="meeting-detail-card" style="margin-bottom: 20px;">
                <div style="padding: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <strong>Meeting #<?= $meeting->getId() ?></strong>
                        <span style="color: #999; margin-left: 8px;"><?= htmlspecialchars($meeting->getFullName()) ?> - <?= htmlspecialchars($meeting->getFormattedDate()) ?></span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 0.85rem; font-weight: 500;">Status:</span>
                        <form method="POST" action="meeting_edit.php?id=<?= $meeting->getId() ?>" style="display: flex; gap: 8px; align-items: center;">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="meeting_id" value="<?= $meeting->getId() ?>">
                            <select name="status" class="status-update-select form-select" style="width: auto; padding: 6px 32px 6px 10px; font-size: 0.85rem;" data-meeting-id="<?= $meeting->getId() ?>">
                                <option value="pending" <?= $meeting->getStatus() === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="confirmed" <?= $meeting->getStatus() === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="completed" <?= $meeting->getStatus() === 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= $meeting->getStatus() === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="btn-filter" style="padding: 6px 14px; font-size: 0.82rem;">Update Status</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="meeting-detail-card">
                <div class="meeting-detail-header">
                    <h2>Edit Meeting Details</h2>
                </div>

                <!-- Error Messages -->
                <?php if (!empty($errors)): ?>
                    <div style="padding: 16px 24px 0;">
                        <div class="alert alert-danger" style="margin: 0;">
                            <strong>Please fix the following errors:</strong>
                            <ul style="margin: 6px 0 0 18px; padding: 0;">
                                <?php foreach ($errors as $err): ?>
                                    <li><?= htmlspecialchars($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="meeting-detail-body">
                    <form method="POST" action="meeting_edit.php?id=<?= $meeting->getId() ?>">
                        <input type="hidden" name="action" value="update_details">

                        <div class="detail-grid">
                            <div class="detail-field">
                                <label class="field-label" for="first_name">First Name *</label>
                                <input type="text" id="first_name" name="first_name" class="form-control" value="<?= htmlspecialchars($meeting->getFirstName()) ?>" required>
                            </div>
                            <div class="detail-field">
                                <label class="field-label" for="last_name">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" class="form-control" value="<?= htmlspecialchars($meeting->getLastName()) ?>" required>
                            </div>
                            <div class="detail-field">
                                <label class="field-label" for="email">Email *</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($meeting->getEmail()) ?>" required>
                            </div>
                            <div class="detail-field">
                                <label class="field-label" for="selected_date">Meeting Date *</label>
                                <input type="date" id="selected_date" name="selected_date" class="form-control" value="<?= htmlspecialchars($meeting->getSelectedDate()) ?>" required>
                            </div>
                            <div class="detail-field">
                                <label class="field-label" for="selected_time">Meeting Time *</label>
                                <select id="selected_time" name="selected_time" class="form-control" required>
                                    <?php foreach (TIME_SLOTS as $value => $label): ?>
                                        <option value="<?= htmlspecialchars($value) ?>" <?= $meeting->getSelectedTime() === $value ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="detail-field">
                                <label class="field-label" for="status">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="pending" <?= $meeting->getStatus() === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="confirmed" <?= $meeting->getStatus() === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="completed" <?= $meeting->getStatus() === 'completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= $meeting->getStatus() === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="detail-field full-width">
                                <label class="field-label" for="notes">Notes</label>
                                <textarea id="notes" name="notes" class="form-control" rows="4"><?= htmlspecialchars($meeting->getNotes()) ?></textarea>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                            <a href="meeting_view.php?id=<?= $meeting->getId() ?>" style="color: #666; text-decoration: none; font-size: 0.88rem;">
                                Cancel
                            </a>
                            <div style="display: flex; gap: 10px;">
                                <a href="meetings.php" class="btn-reset" style="text-decoration: none; padding: 10px 20px;">Discard</a>
                                <button type="submit" class="btn-filter" style="padding: 10px 28px;">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="../js/admin.js"></script>
</body>
</html>
