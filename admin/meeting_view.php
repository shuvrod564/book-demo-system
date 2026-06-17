<?php
/**
 * Meeting View Page
 * Read-only detail view of a single meeting
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/classes/Admin.php';
require_once __DIR__ . '/../includes/classes/Meeting.php';

Admin::requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$meeting = Meeting::getById($id);

if (!$meeting) {
    $_SESSION['flash_message'] = 'Meeting not found.';
    $_SESSION['flash_type'] = 'error';
    header('Location: meetings.php');
    exit;
}

$pageTitle = 'Meeting Details';
$currentPage = 'meeting_view.php';

// Helper to display role/employee labels
$roleLabel = ROLE_OPTIONS[$meeting->getRole()] ?? ucfirst($meeting->getRole());
$empLabel = EMPLOYEE_OPTIONS[$meeting->getEmployees()] ?? $meeting->getEmployees();
$aiLabel = AI_SEARCH_OPTIONS[$meeting->getAiSearchExperience()] ?? ucfirst($meeting->getAiSearchExperience());
$refLabel = REFERRAL_OPTIONS[$meeting->getReferralSource()] ?? ucfirst($meeting->getReferralSource());
$guests = $meeting->getGuestEmailList();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1, shrink-to-fit=no"> 
    <link rel="icon" type="image/ico" href="../images/favicon.ico">
    <title>Meeting #<?= $meeting->getId() ?> - Admin | Book your Demo</title>
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

            <!-- Meeting Detail Card -->
            <div class="meeting-detail-card">
                <div class="meeting-detail-header">
                    <div>
                        <h2>Meeting #<?= $meeting->getId() ?></h2>
                        <div style="font-size: 0.85rem; color: #999; margin-top: 4px;">
                            Booked on <?= date('M j, Y g:i A', strtotime($meeting->getCreatedAt() ?? 'now')) ?>
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <a href="meeting_edit.php?id=<?= $meeting->getId() ?>" class="btn-action edit" style="padding: 8px 16px; width: auto; text-decoration: none; font-size: 0.85rem;" title="Edit">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                        </a>
                        <a href="meetings.php" class="btn-action" style="padding: 8px 16px; width: auto; text-decoration: none; font-size: 0.85rem;" title="Back">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                            Back
                        </a>
                    </div>
                </div>

                <div class="meeting-detail-body">
                    <!-- Status Banner -->
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding: 16px; background: #f8f9fa; border-radius: 10px;">
                        <span class="status-badge status-<?= $meeting->getStatus() ?>" style="font-size: 0.88rem; padding: 6px 18px;">
                            <?= htmlspecialchars($meeting->getStatusLabel()) ?>
                        </span>
                        <?php if ($meeting->isUpcoming()): ?>
                            <span style="font-size: 0.82rem; color: #43a047; font-weight: 500;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -2px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                Upcoming - <?= $meeting->getDaysUntil() ?> day(s) away
                            </span>
                        <?php elseif ($meeting->isToday()): ?>
                            <span style="font-size: 0.82rem; color: #ff7849; font-weight: 500;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -2px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                Today at <?= $meeting->getFormattedTime() ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Detail Grid -->
                    <div class="detail-grid">
                        <div class="detail-field">
                            <div class="field-label">Full Name</div>
                            <div class="field-value"><?= htmlspecialchars($meeting->getFullName()) ?></div>
                        </div>
                        <div class="detail-field">
                            <div class="field-label">Email</div>
                            <div class="field-value">
                                <a href="mailto:<?= htmlspecialchars($meeting->getEmail()) ?>"><?= htmlspecialchars($meeting->getEmail()) ?></a>
                            </div>
                        </div>
                        <div class="detail-field">
                            <div class="field-label">Meeting Date</div>
                            <div class="field-value"><?= htmlspecialchars($meeting->getFormattedDate()) ?></div>
                        </div>
                        <div class="detail-field">
                            <div class="field-label">Meeting Time</div>
                            <div class="field-value"><?= htmlspecialchars($meeting->getFormattedTime()) ?></div>
                        </div>
                        <div class="detail-field">
                            <div class="field-label">Location</div>
                            <div class="field-value">Google Meet</div>
                        </div>
                        <div class="detail-field">
                            <div class="field-label">Duration</div>
                            <div class="field-value"><?= MEETING_DURATION ?> minutes</div>
                        </div>
                        <div class="detail-field">
                            <div class="field-label">Role</div>
                            <div class="field-value"><?= htmlspecialchars($roleLabel) ?></div>
                        </div>
                        <div class="detail-field">
                            <div class="field-label">Company Size</div>
                            <div class="field-value"><?= htmlspecialchars($empLabel) ?></div>
                        </div>
                        <div class="detail-field">
                            <div class="field-label">Website</div>
                            <div class="field-value">
                                <?php if (!empty($meeting->getWebsiteUrl())): ?>
                                    <a href="<?= htmlspecialchars($meeting->getWebsiteUrl()) ?>" target="_blank"><?= htmlspecialchars($meeting->getWebsiteUrl()) ?></a>
                                <?php else: ?>
                                    <span style="color: #ccc;">Not provided</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="detail-field">
                            <div class="field-label">AI Search Experience</div>
                            <div class="field-value"><?= !empty($aiLabel) ? htmlspecialchars($aiLabel) : '<span style="color:#ccc;">Not provided</span>' ?></div>
                        </div>
                        <div class="detail-field">
                            <div class="field-label">Referral Source</div>
                            <div class="field-value"><?= !empty($refLabel) ? htmlspecialchars($refLabel) : '<span style="color:#ccc;">Not provided</span>' ?></div>
                        </div>
                        <div class="detail-field">
                            <div class="field-label">Guests (<?= $meeting->getGuestCount() ?>)</div>
                            <div class="field-value">
                                <?php if (!empty($guests)): ?>
                                    <?php foreach ($guests as $guest): ?>
                                        <span class="guest-tag" style="display: inline-block; margin: 2px 4px 2px 0; background: #e9ecef; padding: 3px 10px; border-radius: 50px; font-size: 0.82rem;"><?= htmlspecialchars($guest) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span style="color: #ccc;">None</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="detail-field full-width">
                            <div class="field-label">Notes</div>
                            <div class="field-value" style="white-space: pre-wrap;"><?= !empty($meeting->getNotes()) ? htmlspecialchars($meeting->getNotes()) : '<span style="color:#ccc;">No notes</span>' ?></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="../js/admin.js"></script>
</body>
</html>
