/**
 * Book Your Demo - Application JavaScript
 * Handles calendar navigation, time slot selection, guest management
 */

document.addEventListener('DOMContentLoaded', function () {

  // ==================== Step 1: Calendar & Time Slots ====================

  const calendarNavBtns = document.querySelectorAll('.calendar-nav-btn');
  const calendarDays = document.querySelectorAll('.calendar-day.current-month:not(.disabled)');
  const timeSlots = document.querySelectorAll('.time-slot:not(.disabled)');
  const btnNext = document.getElementById('btn-next');
  const selectedDateInput = document.getElementById('selected_date');
  const selectedTimeInput = document.getElementById('selected_time');

  // Calendar day selection
  calendarDays.forEach(function (day) {
    day.addEventListener('click', function () {
      // Remove previous selection
      document.querySelectorAll('.calendar-day.selected').forEach(function (d) {
        d.classList.remove('selected');
      });
      // Mark current as selected
      this.classList.add('selected');
      // Update hidden input
      if (selectedDateInput) {
        selectedDateInput.value = this.dataset.date;
      }
      // Load time slots for selected date via AJAX
      loadTimeSlots(this.dataset.date);
      updateNextButton();
    });
  });

  // Time slot selection
  timeSlots.forEach(function (slot) {
    slot.addEventListener('click', function () {
      document.querySelectorAll('.time-slot.selected').forEach(function (s) {
        s.classList.remove('selected');
      });
      this.classList.add('selected');
      if (selectedTimeInput) {
        selectedTimeInput.value = this.dataset.time;
      }
      updateNextButton();
    });
  });

  // Enable/disable Next button based on selections
  function updateNextButton() {
    if (!btnNext) return;
    const hasDate = selectedDateInput && selectedDateInput.value;
    const hasTime = selectedTimeInput && selectedTimeInput.value;
    btnNext.disabled = !(hasDate && hasTime);
  }

  // Load time slots for a specific date (AJAX call)
  function loadTimeSlots(date) {
    const container = document.getElementById('time-slots-container');
    const dateDisplay = document.getElementById('selected-date-display');
    if (!container) return;

    // Update the display text
    if (dateDisplay) {
      const dateObj = new Date(date + 'T00:00:00');
      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      dateDisplay.textContent = 'Showing times for ' + dateObj.toLocaleDateString('en-US', options);
    }

    // Fetch time slots from server
    fetch('ajax/get_timeslots.php?date=' + encodeURIComponent(date))
      .then(function (response) { return response.json(); })
      .then(function (data) {
        if (data.success && data.slots) {
          renderTimeSlots(data.slots);
        }
      })
      .catch(function () {
        // If AJAX fails, keep existing slots
      });
  }

  // Render time slots from server response
  function renderTimeSlots(slots) {
    const container = document.getElementById('time-slots-container');
    if (!container) return;
    container.innerHTML = '';
    slots.forEach((slot) => {
      // Create the column wrapper
      const colDiv = document.createElement('div');
      colDiv.className = 'col';

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'time-slot' + (slot.available ? '' : ' disabled');
      btn.dataset.time = slot.value;
      btn.textContent = slot.label;
      if (slot.available) {
        btn.addEventListener('click', function () {
          document.querySelectorAll('.time-slot.selected').forEach((s) => {
            s.classList.remove('selected');
          });
          this.classList.add('selected');
          if (selectedTimeInput) {
            selectedTimeInput.value = this.dataset.time;
          }
          updateNextButton();
        });
      }
      
      // Append button to column, and column to container
      colDiv.appendChild(btn);
      container.appendChild(colDiv);
    });
  }

  // Calendar month navigation
  calendarNavBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const year = this.dataset.year;
      const month = this.dataset.month;
      if (year && month) {
        window.location.href = 'index.php?step=1&year=' + year + '&month=' + month;
      }
    });
  });

  // ==================== Step 2: Guest Management ====================

  const guestEmailInput = document.getElementById('guest_email_input');
  const btnAddGuest = document.getElementById('btn-add-guest');
  const guestListEl = document.getElementById('guest-list');
  const guestCounterEl = document.getElementById('guest-counter');
  const guestEmailsInput = document.getElementById('guest_emails');
  let guests = [];

  // Parse existing guests from hidden input
  if (guestEmailsInput && guestEmailsInput.value) {
    guests = guestEmailsInput.value.split(',').filter(function (e) { return e.trim() !== ''; });
    renderGuests();
  }

  // Add guest
  if (btnAddGuest) {
    btnAddGuest.addEventListener('click', addGuest);
  }

  if (guestEmailInput) {
    guestEmailInput.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        addGuest();
      }
    });
  }

  function addGuest() {
    if (!guestEmailInput) return;
    const email = guestEmailInput.value.trim();
    if (!email) return;

    // Validate email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      alert('Please enter a valid email address.');
      return;
    }

    // Check max guests
    if (guests.length >= 10) {
      alert('You can invite up to 10 guests only.');
      return;
    }

    // Check duplicate
    if (guests.indexOf(email) !== -1) {
      alert('This email has already been added.');
      return;
    }

    guests.push(email);
    guestEmailInput.value = '';
    updateGuestInput();
    renderGuests();
  }

  function removeGuest(email) {
    guests = guests.filter(function (g) { return g !== email; });
    updateGuestInput();
    renderGuests();
  }

  function updateGuestInput() {
    if (guestEmailsInput) {
      guestEmailsInput.value = guests.join(',');
    }
  }

  function renderGuests() {
    if (!guestListEl) return;
    guestListEl.innerHTML = '';

    if (guests.length === 0) {
      guestListEl.innerHTML = '<span class="guest-empty">Added guests will appear here.</span>';
    } else {
      guests.forEach(function (email) {
        const tag = document.createElement('span');
        tag.className = 'guest-tag';
        tag.innerHTML = email + ' <button type="button" class="remove-guest" data-email="' + email + '">&times;</button>';
        tag.querySelector('.remove-guest').addEventListener('click', function () {
          removeGuest(this.dataset.email);
        });
        guestListEl.appendChild(tag);
      });
    }

    // Update counter
    if (guestCounterEl) {
      guestCounterEl.textContent = guests.length + '/10 guests';
    }
  }

  // ==================== Form Validation (Step 2) ====================

  const step2Form = document.getElementById('step2-form');
  if (step2Form) {
    step2Form.addEventListener('submit', function (e) {
      let isValid = true;
      // Clear previous errors
      document.querySelectorAll('.is-invalid').forEach(function (el) {
        el.classList.remove('is-invalid');
      });
      document.querySelectorAll('.invalid-feedback').forEach(function (el) {
        el.remove();
      });

      // Required fields
      const requiredFields = ['first_name', 'last_name', 'email', 'role', 'employees'];
      requiredFields.forEach(function (fieldName) {
        const field = document.getElementById(fieldName);
        if (field && !field.value.trim()) {
          field.classList.add('is-invalid');
          const feedback = document.createElement('div');
          feedback.className = 'invalid-feedback';
          feedback.textContent = 'This field is required.';
          field.parentNode.appendChild(feedback);
          isValid = false;
        }
      });

      // Email validation
      const emailField = document.getElementById('email');
      if (emailField && emailField.value.trim()) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailField.value.trim())) {
          emailField.classList.add('is-invalid');
          const feedback = document.createElement('div');
          feedback.className = 'invalid-feedback';
          feedback.textContent = 'Please enter a valid email address.';
          emailField.parentNode.appendChild(feedback);
          isValid = false;
        }
      }

      // URL validation
      const urlField = document.getElementById('website_url');
      if (urlField && urlField.value.trim()) {
        try {
          new URL(urlField.value.trim());
        } catch (_) {
          urlField.classList.add('is-invalid');
          const feedback = document.createElement('div');
          feedback.className = 'invalid-feedback';
          feedback.textContent = 'Please enter a valid URL.';
          urlField.parentNode.appendChild(feedback);
          isValid = false;
        }
      }

      if (!isValid) {
        e.preventDefault();
      }
    });
  }

  // Initialize
  updateNextButton();
});
