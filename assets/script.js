document.addEventListener("DOMContentLoaded", function () {
  /* Password strength: same order as validatePasswordStrength() in config.php */
  function getPasswordStrengthError(value) {
    if (value.length < 8) return "Password must be at least 8 characters.";
    if (!/[A-Z]/.test(value))
      return "Password must contain at least one uppercase letter.";
    if (!/[a-z]/.test(value))
      return "Password must contain at least one lowercase letter.";
    if (!/[^A-Za-z0-9]/.test(value))
      return "Password must contain at least one special character.";
    return "";
  }

  /* Profile password validation */
  const passwordForm = document.querySelector(".profile-password-form");

  if (passwordForm) {
    const oldPassword = document.getElementById("oldPassword");
    const newPassword = document.getElementById("newPassword");
    const confirmPassword = document.getElementById("confirmPassword");
    const passwordMatch = document.getElementById("passwordMatch");

    function validateField(input, forceCheck = false) {
      if (!input) return;
      const group = input.closest(".password-form-group");
      const feedback = group ? group.querySelector(".invalid-feedback") : null;

      if (input.value === "") {
        if (forceCheck) {
          input.classList.remove("is-valid");
          input.classList.add("is-invalid");
        } else {
          input.classList.remove("is-invalid", "is-valid");
        }
        if (feedback) {
          feedback.classList.toggle(
            "d-block",
            input.classList.contains("is-invalid"),
          );
        }
        return;
      }

      const valid = input.checkValidity();
      input.classList.toggle("is-invalid", !valid);
      input.classList.toggle("is-valid", valid);
      if (feedback) feedback.classList.toggle("d-block", !valid);
    }

    function checkPasswordMatch() {
      if (!newPassword || !confirmPassword || !passwordMatch) return;

      if (newPassword.value === "" || confirmPassword.value === "") {
        passwordMatch.textContent = "";
        passwordMatch.classList.remove("is-match", "is-mismatch");
        confirmPassword.setCustomValidity("");
        return;
      }

      if (newPassword.value === confirmPassword.value) {
        passwordMatch.textContent = "Passwords match.";
        passwordMatch.classList.remove("is-mismatch");
        passwordMatch.classList.add("is-match");
        confirmPassword.setCustomValidity("");
      } else {
        passwordMatch.textContent = "Passwords do not match.";
        passwordMatch.classList.remove("is-match");
        passwordMatch.classList.add("is-mismatch");
        confirmPassword.setCustomValidity("Passwords do not match.");
      }
    }

    if (oldPassword) {
      oldPassword.addEventListener("input", function () {
        validateField(oldPassword);
      });
    }

    if (newPassword) {
      newPassword.addEventListener("input", function () {
        const strengthError = getPasswordStrengthError(newPassword.value);
        newPassword.setCustomValidity(
          newPassword.value === "" ? "" : strengthError,
        );

        const group = newPassword.closest(".password-form-group");
        const feedback = group
          ? group.querySelector(".invalid-feedback")
          : null;

        if (feedback && newPassword.value !== "" && strengthError) {
          feedback.textContent = strengthError;
        } else if (feedback && newPassword.value === "") {
          feedback.textContent = "Password must be at least 8 characters.";
        }

        validateField(newPassword);
        if (confirmPassword) validateField(confirmPassword);
        checkPasswordMatch();
      });
    }

    if (confirmPassword) {
      confirmPassword.addEventListener("input", function () {
        validateField(confirmPassword);
        checkPasswordMatch();
      });
    }

    passwordForm.addEventListener("submit", function (event) {
      validateField(oldPassword, true);
      validateField(newPassword, true);
      validateField(confirmPassword, true);
      checkPasswordMatch();

      if (!passwordForm.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }

      passwordForm.classList.add("was-validated");
    });
  }

  /* Login password validation */
  const loginPassword = document.getElementById("loginPassword");
  const loginPasswordFeedback = document.getElementById(
    "loginPasswordFeedback",
  );

  if (loginPassword && loginPasswordFeedback) {
    loginPassword.addEventListener("input", function () {
      if (loginPassword.value === "") {
        loginPasswordFeedback.textContent = "";
        loginPasswordFeedback.classList.remove("d-block");
      } else if (loginPassword.value.length < 8) {
        loginPasswordFeedback.textContent =
          "Password must be at least 8 characters.";
        loginPasswordFeedback.classList.add("d-block");
      } else {
        loginPasswordFeedback.textContent = "";
        loginPasswordFeedback.classList.remove("d-block");
      }
    });
  }

  /* Student password strength (admin create form) */
  const studentPassword = document.getElementById("studentPassword");
  const studentPasswordFeedback = document.getElementById(
    "studentPasswordFeedback",
  );

  if (studentPassword && studentPasswordFeedback) {
    studentPassword.addEventListener("input", function () {
      const error = getPasswordStrengthError(studentPassword.value);
      studentPassword.setCustomValidity(
        studentPassword.value === "" ? "" : error,
      );

      if (studentPassword.value === "") {
        studentPasswordFeedback.textContent =
          "Password must be at least 8 characters.";
        studentPasswordFeedback.classList.remove("d-block");
      } else if (error) {
        studentPasswordFeedback.textContent = error;
        studentPasswordFeedback.classList.add("d-block");
      } else {
        studentPasswordFeedback.classList.remove("d-block");
      }
    });
  }

  /* Password show and hide */
  document.querySelectorAll(".toggle-password").forEach(function (button) {
    button.addEventListener("click", function () {
      const targetId = button.dataset.target;
      const input = targetId ? document.getElementById(targetId) : null;
      if (!input) return;

      const isHidden = input.type === "password";
      input.type = isHidden ? "text" : "password";

      const icon = button.querySelector("i");
      if (icon) {
        icon.classList.toggle("bi-eye", !isHidden);
        icon.classList.toggle("bi-eye-slash", isHidden);
      } else {
        button.textContent = isHidden ? "Hide" : "Show";
      }

      button.setAttribute("aria-pressed", String(isHidden));
      button.setAttribute(
        "aria-label",
        (isHidden ? "Hide" : "Show") + " password",
      );
    });
  });

  /* Delete confirmation modal */
  const deleteModal = document.getElementById("deleteModal");
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

  if (deleteModal && confirmDeleteBtn) {
    let pendingForm = null;

    document.querySelectorAll(".delete-form").forEach(function (form) {
      form.addEventListener("submit", function (event) {
        event.preventDefault();
        pendingForm = form;
        deleteModal.classList.add("is-open");
        deleteModal.setAttribute("aria-hidden", "false");
        setTimeout(function () {
          confirmDeleteBtn.focus();
        }, 100);
      });
    });

    function closeModal() {
      deleteModal.classList.remove("is-open");
      deleteModal.setAttribute("aria-hidden", "true");
      pendingForm = null;
    }

    deleteModal.querySelectorAll("[data-modal-close]").forEach(function (el) {
      el.addEventListener("click", closeModal);
    });

    deleteModal.addEventListener("click", function (event) {
      if (event.target === deleteModal) closeModal();
    });

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && deleteModal.classList.contains("is-open")) {
        closeModal();
      }
    });

    confirmDeleteBtn.addEventListener("click", function () {
      if (pendingForm) pendingForm.submit();
    });
  }
});

/* =========================================================
   ADMIN PROFILE DROPDOWN
   Opened by onclick="toggleAdminDropdown(event)" on the button.
   This is the only handler — don't add another click listener
   to the button, or the two will cancel each other out.
   ========================================================= */

function closeAdminDropdown() {
  const menu = document.getElementById("adminProfileDropdownMenu");
  const button = document.getElementById("adminProfileDropdownBtn");
  if (!menu || !button) return;

  menu.classList.remove("show");
  button.classList.remove("active");
  button.setAttribute("aria-expanded", "false");
}

function toggleAdminDropdown(event) {
  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }

  const menu = document.getElementById("adminProfileDropdownMenu");
  const button = document.getElementById("adminProfileDropdownBtn");
  if (!menu || !button) return;

  if (menu.classList.contains("show")) {
    closeAdminDropdown();
  } else {
    menu.classList.add("show");
    button.classList.add("active");
    button.setAttribute("aria-expanded", "true");
  }
}

/* Close when clicking outside the dropdown */
document.addEventListener("click", function (event) {
  const dropdown = document.querySelector(".admin-dropdown");
  if (dropdown && !dropdown.contains(event.target)) {
    closeAdminDropdown();
  }
});

/* Close with Escape key */
document.addEventListener("keydown", function (event) {
  if (event.key === "Escape") closeAdminDropdown();
});
