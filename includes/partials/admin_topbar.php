<?php
/**
 * Admin Topbar Partial
 * Top navigation bar with breadcrumb, notifications, logout
 */

$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitles = [
    'dashboard.php' => 'Dashboard',
    'meetings.php' => 'Meetings',
    'meeting_edit.php' => 'Edit Meeting',
    'meeting_view.php' => 'Meeting Details',
];
$pageTitle = $pageTitles[$currentPage] ?? 'Admin';
?>
<!-- Top Navbar -->
<header class="admin-topbar">
    <div class="topbar-left">
        <button class="btn-sidebar-toggle" id="sidebar-toggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <div>
            <h2><?= htmlspecialchars($pageTitle) ?></h2>
            <div class="topbar-breadcrumb">Admin / <?= htmlspecialchars($pageTitle) ?></div>
        </div>
    </div>
    <div class="topbar-right">
        <div class="topbar-btn" title="Notifications">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="notif-badge"></span>
        </div>
        <a href="logout.php" class="btn-logout">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Logout
        </a>
    </div>
</header>
