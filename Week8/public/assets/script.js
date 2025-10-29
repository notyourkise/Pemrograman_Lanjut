/**
 * Week 8: JavaScript utilities
 */

// Initialize tooltips and popovers
document.addEventListener("DOMContentLoaded", function () {
  // Initialize Bootstrap tooltips
  var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Initialize Bootstrap popovers
  var popoverTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="popover"]')
  );
  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
  });

  // Auto-hide alerts after 5 seconds
  setTimeout(function () {
    var alerts = document.querySelectorAll(".alert");
    alerts.forEach(function (alert) {
      var bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    });
  }, 5000);

  // Confirm delete actions
  document.querySelectorAll(".btn-delete").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      if (!confirm("Are you sure you want to delete this item?")) {
        e.preventDefault();
      }
    });
  });

  // Update clock in dashboard
  updateClock();
  setInterval(updateClock, 1000);
});

// Update clock function
function updateClock() {
  const clockElement = document.querySelector(".live-clock");
  if (clockElement) {
    const now = new Date();
    const time = now.toLocaleTimeString("en-US", { hour12: false });
    clockElement.textContent = time;
  }
}

// Form validation helper
function validateForm(formId) {
  const form = document.getElementById(formId);
  if (!form) return false;

  let isValid = true;
  const inputs = form.querySelectorAll(
    "input[required], select[required], textarea[required]"
  );

  inputs.forEach(function (input) {
    if (!input.value.trim()) {
      input.classList.add("is-invalid");
      isValid = false;
    } else {
      input.classList.remove("is-invalid");
      input.classList.add("is-valid");
    }
  });

  return isValid;
}

// Password strength indicator
function checkPasswordStrength(password) {
  let strength = 0;

  if (password.length >= 8) strength++;
  if (password.length >= 12) strength++;
  if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
  if (/\d/.test(password)) strength++;
  if (/[^a-zA-Z0-9]/.test(password)) strength++;

  return {
    score: strength,
    label: ["Very Weak", "Weak", "Fair", "Good", "Strong"][
      Math.min(strength, 4)
    ],
    color: ["danger", "danger", "warning", "info", "success"][
      Math.min(strength, 4)
    ],
  };
}

// Show password toggle
function togglePasswordVisibility(inputId, buttonId) {
  const input = document.getElementById(inputId);
  const button = document.getElementById(buttonId);

  if (input.type === "password") {
    input.type = "text";
    button.innerHTML = '<i class="bi bi-eye-slash"></i>';
  } else {
    input.type = "password";
    button.innerHTML = '<i class="bi bi-eye"></i>';
  }
}

// Debounce function for search
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// AJAX helper
function ajaxRequest(url, method, data, callback) {
  const xhr = new XMLHttpRequest();
  xhr.open(method, url, true);
  xhr.setRequestHeader("Content-Type", "application/json");

  xhr.onload = function () {
    if (xhr.status >= 200 && xhr.status < 300) {
      callback(null, JSON.parse(xhr.responseText));
    } else {
      callback(new Error("Request failed"), null);
    }
  };

  xhr.onerror = function () {
    callback(new Error("Network error"), null);
  };

  if (data) {
    xhr.send(JSON.stringify(data));
  } else {
    xhr.send();
  }
}
