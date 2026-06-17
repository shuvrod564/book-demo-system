/**
 * Book Your Demo - Admin Panel JavaScript
 * Sidebar toggle, confirmations, toasts, form handling
 */

document.addEventListener('DOMContentLoaded', function () {

  // ==================== Sidebar Toggle (Mobile) ====================
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const sidebar = document.getElementById('admin-sidebar');
  const overlay = document.getElementById('sidebar-overlay');

  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', function () {
      sidebar.classList.toggle('show');
      overlay.classList.toggle('show');
    });
  }

  if (overlay) {
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('show');
      overlay.classList.remove('show');
    });
  }

  // ==================== Flash Message Auto-Dismiss ====================
  const flashMessages = document.querySelectorAll('.flash-message');
  flashMessages.forEach(function (msg) {
    setTimeout(function () {
      msg.style.transition = 'opacity 0.3s ease';
      msg.style.opacity = '0';
      setTimeout(function () { msg.remove(); }, 300);
    }, 4000);
  });

  // ==================== Delete Confirmation ====================
  const deleteForms = document.querySelectorAll('.delete-form');
  deleteForms.forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!confirm('Are you sure you want to delete this meeting? This action cannot be undone.')) {
        e.preventDefault();
      }
    });
  });

  // ==================== Status Update ====================
  const statusSelects = document.querySelectorAll('.status-update-select');
  statusSelects.forEach(function (select) {
    select.addEventListener('change', function () {
      const meetingId = this.dataset.meetingId;
      const newStatus = this.value;
      if (meetingId && newStatus) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'meeting_edit.php?id=' + meetingId;
        form.innerHTML = '<input type="hidden" name="action" value="update_status">' +
                         '<input type="hidden" name="status" value="' + newStatus + '">' +
                         '<input type="hidden" name="meeting_id" value="' + meetingId + '">';
        document.body.appendChild(form);
        form.submit();
      }
    });
  });

  // ==================== Filter Form ====================
  const filterForm = document.getElementById('filter-form');
  if (filterForm) {
    const resetBtn = document.getElementById('filter-reset');
    if (resetBtn) {
      resetBtn.addEventListener('click', function (e) {
        e.preventDefault();
        window.location.href = window.location.pathname;
      });
    }
  }

  // ==================== Set Active Nav Link ====================
  const currentPath = window.location.pathname;
  const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
  navLinks.forEach(function (link) {
    if (currentPath.endsWith(link.getAttribute('href'))) {
      link.classList.add('active');
    }
  });
});
