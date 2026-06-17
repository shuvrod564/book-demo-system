<?php
/**
 * Admin Sidebar Partial
 * Navigation sidebar for the admin panel
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/Meeting.php';

// Count pending meetings for badge
try {
    $pendingCount = Meeting::getStats()['pending'] ?? 0;
} catch (Exception $e) {
    $pendingCount = 0;
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>

<!-- Sidebar -->
<aside class="admin-sidebar" id="admin-sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-logo">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 3L4 14h7l-2 7 9-11h-7l2-7z" fill="#3a5a78" stroke="#3a5a78" stroke-width="1"/>
            </svg>
        </div>
        <div>
            <div class="brand-text">Book Demo</div>
            <div class="brand-sub">Admin Panel</div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <div class="nav-label">Main</div>
        <div class="nav-item">
            <a href="dashboard.php" class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
        </div>
        <div class="nav-item">
            <a href="meetings.php?type=upcoming" class="nav-link <?= $currentPage === 'meetings.php' && ($_GET['type'] ?? '') === 'upcoming' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Upcoming Meetings
            </a>
        </div>

        <div class="nav-label">Management</div>
        <div class="nav-item">
            <a href="meetings.php" class="nav-link <?= $currentPage === 'meetings.php' && !isset($_GET['type']) ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                All Meetings
                <?php if ($pendingCount > 0): ?>
                    <span class="badge bg-warning text-dark"><?= $pendingCount ?></span>
                <?php endif; ?>
            </a>
        </div>
        <div class="nav-item">
            <a href="meetings.php?status=pending" class="nav-link <?= $currentPage === 'meetings.php' && ($_GET['status'] ?? '') === 'pending' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Pending
            </a>
        </div>
        <div class="nav-item">
            <a href="meetings.php?status=confirmed" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Confirmed
            </a>
        </div>
        <div class="nav-item">
            <a href="meetings.php?status=cancelled" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                Cancelled
            </a>
        </div>

        <div class="nav-label">Quick Links</div>
        <div class="nav-item">
            <a href="../index.php" target="_blank" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                View Booking Page
            </a>
        </div>
    </nav>

    <!-- User Info -->
    <div class="sidebar-user">
        <div class="user-avatar"><?= strtoupper(substr(Admin::getCurrentName(), 0, 1)) ?></div>
        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars(Admin::getCurrentName()) ?></div>
            <div class="user-role"><?= htmlspecialchars(Admin::getCurrentRole()) ?></div>
        </div>
    </div>
</aside>
