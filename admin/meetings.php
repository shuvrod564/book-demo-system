<?php
/**
 * Meetings List Page
 * Displays all meetings with filtering, searching, and management actions
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/classes/Admin.php';
require_once __DIR__ . '/../includes/classes/Meeting.php';

Admin::requireLogin();

// Build filters from GET parameters
$filters = [];
$getStatus = $_GET['status'] ?? '';
$getSearch = $_GET['search'] ?? '';
$getDateFrom = $_GET['date_from'] ?? '';
$getDateTo = $_GET['date_to'] ?? '';
$getType = $_GET['type'] ?? '';

if (!empty($getStatus)) $filters['status'] = $getStatus;
if (!empty($getSearch)) $filters['search'] = $getSearch;
if (!empty($getDateFrom)) $filters['date_from'] = $getDateFrom;
if (!empty($getDateTo)) $filters['date_to'] = $getDateTo;

// For "upcoming" type, filter future dates
if ($getType === 'upcoming') {
    $filters['date_from'] = date('Y-m-d');
    if (empty($getStatus)) {
        // Exclude cancelled by default in upcoming view
    }
}

// Get meetings with filters
$meetings = Meeting::getAll($filters, 'selected_date', 'ASC');

// Determine page title
if ($getType === 'upcoming') {
    $pageTitle = 'Upcoming Meetings';
} elseif (!empty($getStatus)) {
    $pageTitle = ucfirst($getStatus) . ' Meetings';
} else {
    $pageTitle = 'All Meetings';
}

// Handle flash messages
$flashMessage = '';
$flashType = '';
if (isset($_SESSION['flash_message'])) {
    $flashMessage = $_SESSION['flash_message'];
    $flashType = $_SESSION['flash_type'] ?? 'info';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

$currentPage = 'meetings.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1, shrink-to-fit=no"> 
    <link rel="icon" type="image/ico" href="../images/favicon.ico">
    <title><?= htmlspecialchars($pageTitle) ?> - Admin | Book your Demo</title>
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

            <!-- Flash Message -->
            <?php if (!empty($flashMessage)): ?>
                <div class="flash-message <?= htmlspecialchars($flashType) ?>">
                    <?= htmlspecialchars($flashMessage) ?>
                </div>
            <?php endif; ?>

            <!-- Filter Bar -->
            <div class="filter-bar">
                <form method="GET" action="meetings.php" id="filter-form" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; flex: 1;">
                    <?php if (!empty($getType)): ?>
                        <input type="hidden" name="type" value="<?= htmlspecialchars($getType) ?>">
                    <?php endif; ?>

                    <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?= htmlspecialchars($getSearch) ?>" style="max-width: 260px;">

                    <select name="status" class="form-select" style="max-width: 160px;">
                        <option value="">All Status</option>
                        <option value="pending" <?= $getStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?= $getStatus === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="completed" <?= $getStatus === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= $getStatus === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>

                    <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($getDateFrom) ?>" style="max-width: 160px;" placeholder="From">
                    <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($getDateTo) ?>" style="max-width: 160px;" placeholder="To">

                    <button type="submit" class="btn-filter">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -2px;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        Filter
                    </button>
                    <button type="button" id="filter-reset" class="btn-reset">Reset</button>
                </form>
            </div>

            <!-- Meetings Table -->
            <div class="content-card">
                <div class="card-header">
                    <h3><?= htmlspecialchars($pageTitle) ?> <span style="font-weight: 400; font-size: 0.85rem; color: #999;">(<?= count($meetings) ?> results)</span></h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <?php if (empty($meetings)): ?>
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <h3>No meetings found</h3>
                            <p>Try adjusting your filters or wait for new bookings.</p>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table class="meetings-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Person</th>
                                        <th>Date & Time</th>
                                        <th>Role</th>
                                        <th>Company Size</th>
                                        <th>Guests</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($meetings as $index => $meeting): ?>
                                        <tr>
                                            <td style="color: #999; font-size: 0.82rem;"><?= $meeting->getId() ?></td>
                                            <td>
                                                <div class="meeting-person">
                                                    <div class="meeting-avatar" style="background: hsl(<?= crc32($meeting->getEmail()) % 360 ?>, 55%, 55%);">
                                                        <?= strtoupper(substr($meeting->getFirstName(), 0, 1) . substr($meeting->getLastName(), 0, 1)) ?>
                                                    </div>
                                                    <div class="meeting-person-info">
                                                        <div class="person-name"><?= htmlspecialchars($meeting->getFullName()) ?></div>
                                                        <div class="person-email"><?= htmlspecialchars($meeting->getEmail()) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="meeting-datetime">
                                                    <div class="meeting-date"><?= htmlspecialchars($meeting->getFormattedDate()) ?></div>
                                                    <div class="meeting-time"><?= htmlspecialchars($meeting->getFormattedTime()) ?></div>
                                                </div>
                                            </td>
                                            <td style="font-size: 0.85rem;"><?= htmlspecialchars(ROLE_OPTIONS[$meeting->getRole()] ?? ucfirst($meeting->getRole())) ?></td>
                                            <td style="font-size: 0.85rem;"><?= htmlspecialchars(EMPLOYEE_OPTIONS[$meeting->getEmployees()] ?? $meeting->getEmployees()) ?></td>
                                            <td style="font-size: 0.85rem;"><?= $meeting->getGuestCount() > 0 ? $meeting->getGuestCount() . ' guest(s)' : '<span style="color:#ccc;">None</span>' ?></td>
                                            <td>
                                                <span class="status-badge status-<?= $meeting->getStatus() ?>">
                                                    <?= htmlspecialchars($meeting->getStatusLabel()) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-btns">
                                                    <a href="meeting_view.php?id=<?= $meeting->getId() ?>" class="btn-action view" title="View Details">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                    </a>
                                                    <a href="meeting_edit.php?id=<?= $meeting->getId() ?>" class="btn-action edit" title="Edit Meeting">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                    </a>
                                                    <form method="POST" action="meeting_delete.php" class="delete-form" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?= $meeting->getId() ?>">
                                                        <button type="submit" class="btn-action delete" title="Delete Meeting">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="../js/admin.js"></script>
</body>
</html>
