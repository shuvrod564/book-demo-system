<?php
/**
 * Admin Dashboard
 * Overview with statistics, upcoming meetings, and quick actions
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/classes/Admin.php';
require_once __DIR__ . '/../includes/classes/Meeting.php';

Admin::requireLogin();

// Get statistics
$stats = Meeting::getStats();
$upcomingMeetings = Meeting::getUpcoming(5);
$todayMeetings = Meeting::getToday();
$statusBreakdown = Meeting::getStatusBreakdown();

// Page variables
$pageTitle = 'Dashboard';
$currentPage = 'dashboard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1, shrink-to-fit=no"> 
    <link rel="icon" type="image/ico" href="../images/favicon.ico">
    <title>Dashboard - Admin | Book your Demo</title>
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

            <!-- Stat Cards -->
            <div class="stat-cards">
                <!-- Total Bookings -->
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Total Bookings</h3>
                        <p class="stat-value"><?= $stats['total'] ?></p>
                    </div>
                    <div class="stat-icon icon-total">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                </div>

                <!-- Today's Meetings -->
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Today</h3>
                        <p class="stat-value"><?= $stats['today'] ?></p>
                    </div>
                    <div class="stat-icon icon-today">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                </div>

                <!-- Upcoming -->
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Upcoming</h3>
                        <p class="stat-value"><?= $stats['upcoming'] ?></p>
                    </div>
                    <div class="stat-icon icon-upcoming">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                </div>

                <!-- Pending -->
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Pending</h3>
                        <p class="stat-value"><?= $stats['pending'] ?></p>
                    </div>
                    <div class="stat-icon icon-pending">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                </div>
            </div>

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Upcoming Meetings Table -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>Upcoming Meetings</h3>
                        <a href="meetings.php?type=upcoming" class="header-link">View All</a>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <?php if (empty($upcomingMeetings)): ?>
                            <div class="empty-state">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <h3>No upcoming meetings</h3>
                                <p>Bookings will appear here when scheduled.</p>
                            </div>
                        <?php else: ?>
                            <table class="meetings-table">
                                <thead>
                                    <tr>
                                        <th>Person</th>
                                        <th>Date & Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcomingMeetings as $meeting): ?>
                                        <tr>
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
                                            <td>
                                                <span class="status-badge status-<?= $meeting->getStatus() ?>">
                                                    <?= htmlspecialchars($meeting->getStatusLabel()) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-btns">
                                                    <a href="meeting_view.php?id=<?= $meeting->getId() ?>" class="btn-action view" title="View">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                    </a>
                                                    <a href="meeting_edit.php?id=<?= $meeting->getId() ?>" class="btn-action edit" title="Edit">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column: Status Breakdown + Today -->
                <div>
                    <!-- Status Breakdown -->
                    <div class="content-card" style="margin-bottom: 20px;">
                        <div class="card-header">
                            <h3>Status Breakdown</h3>
                        </div>
                        <div class="card-body">
                            <ul class="quick-stats">
                                <li>
                                    <span class="qs-label"><span class="qs-dot" style="background: #43a047;"></span> Confirmed</span>
                                    <span class="qs-value"><?= $stats['confirmed'] ?></span>
                                </li>
                                <li>
                                    <span class="qs-label"><span class="qs-dot" style="background: #f9a825;"></span> Pending</span>
                                    <span class="qs-value"><?= $stats['pending'] ?></span>
                                </li>
                                <li>
                                    <span class="qs-label"><span class="qs-dot" style="background: #1565c0;"></span> Completed</span>
                                    <span class="qs-value"><?= $stats['completed'] ?></span>
                                </li>
                                <li>
                                    <span class="qs-label"><span class="qs-dot" style="background: #dc3545;"></span> Cancelled</span>
                                    <span class="qs-value"><?= $stats['cancelled'] ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Today's Meetings -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3>Today's Schedule</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($todayMeetings)): ?>
                                <div style="text-align: center; padding: 20px; color: #999; font-size: 0.88rem;">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ddd" stroke-width="2" style="margin-bottom: 8px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    <p>No meetings today</p>
                                </div>
                            <?php else: ?>
                                <ul class="quick-stats">
                                    <?php foreach ($todayMeetings as $meeting): ?>
                                        <li>
                                            <span class="qs-label">
                                                <span class="qs-dot" style="background: <?= $meeting->getStatus() === 'confirmed' ? '#43a047' : '#f9a825' ?>;"></span>
                                                <?= htmlspecialchars($meeting->getFullName()) ?>
                                            </span>
                                            <span class="qs-value" style="font-size: 0.85rem;"><?= htmlspecialchars($meeting->getFormattedTime()) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Monthly Summary -->
                    <div class="content-card" style="margin-top: 20px;">
                        <div class="card-header">
                            <h3>This Month</h3>
                        </div>
                        <div class="card-body" style="text-align: center; padding: 28px;">
                            <div style="font-size: 2.5rem; font-weight: 800; color: #3a5a78; line-height: 1;"><?= $stats['this_month'] ?></div>
                            <div style="font-size: 0.85rem; color: #999; margin-top: 6px;">bookings this month</div>
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
