<?php

// Check whether the form is in edit mode
$isEdit = ($mode === 'edit');

// Get flash message
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Set page title -->
    <title><?= $isEdit ? 'Edit Student' : 'Add Student' ?> - <?= e(APP_NAME) ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg navbar-dark portal-nav profile-navbar">
    <div class="container profile-navbar-container admin-navbar-container">

        <!-- Portal logo and name -->
        <a class="navbar-brand profile-brand" href="index.php?action=students">
            <span class="profile-logo">
                <i class="bi bi-mortarboard-fill"></i>
            </span>

            <span class="profile-brand-content">
                <span class="profile-brand-title">
                    PSP Student Portal
                </span>

                <span class="profile-brand-subtitle">
                    Learn • Grow • Succeed
                </span>
            </span>
        </a>


        <!-- Admin information and logout -->
        <div class="admin-navbar-right">
            <span class="admin-user">
                <i class="bi bi-person-circle"></i>
                Admin: <?= e($_SESSION['admin_name']) ?>
            </span>

            <a class="btn profile-logout" href="index.php?action=logout">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </a>
        </div>
    </div>
</nav>


<!-- Main content -->
<div class="container py-5">
    <div class="content-card form-card mx-auto">

        <!-- Form heading -->
        <h2 class="fw-bold mb-1">
            <?= $isEdit ? 'Edit Student' : 'Add Student' ?>
        </h2>

        <!-- Form description -->
        <p class="text-muted mb-4">
            <?= $isEdit ? 'Update student information and marks.' : 'Create a new student record.' ?>
        </p>

        <!-- Display flash message -->
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Student form -->
        <form action="index.php?action=<?= $isEdit ? 'update' : 'store' ?>"
              method="POST" class="needs-validation" novalidate>

            <!-- CSRF token -->
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <!-- Student ID for edit mode -->
            <?php if ($isEdit): ?>
                <input type="hidden" name="student_id"
                       value="<?= e($student['student_id']) ?>">
            <?php endif; ?>

            <!-- Student name -->
            <div class="mb-3">
                <label class="form-label">Student Name</label>

                <input type="text" name="student_name" class="form-control"
                       value="<?= e($student['student_name']) ?>"
                       pattern="[A-Za-zÀ-ÿ .'-]+" required>

                <div class="invalid-feedback">
                    Name must not contain numbers.
                </div>
            </div>

            <!-- Student NRIC -->
            <div class="mb-3">
                <label class="form-label">NRIC</label>
                <input type="text" name="student_ic" class="form-control"
                       value="<?= e($student['student_ic']) ?>"
                       pattern="\d{6}-\d{2}-\d{4}"
                       placeholder="010101-01-0101" required>

                <div class="invalid-feedback">
                    Use format XXXXXX-XX-XXXX.
                </div>
            </div>

            <!-- Student program -->
            <div class="mb-3">
                <label class="form-label">Program</label>
                <input type="text" name="program" class="form-control"
                       value="<?= e($student['program']) ?>" required>
            </div>

            <!-- Student marks -->
            <div class="mb-4">
                <label class="form-label">Marks</label>
                <input type="number" name="marks" class="form-control"
                       value="<?= e($student['marks']) ?>"
                       min="0" max="100" step="0.01" required>

                <div class="invalid-feedback">
                    Marks must be between 0 and 100.
                </div>
            </div>

            <!-- Password -->
            <?php if (!$isEdit): ?>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="studentPassword"
                            class="form-control" minlength="8" required>

                        <!-- Show or hide password -->
                        <button type="button"
                                class="btn btn-outline-secondary toggle-password"
                                data-target="studentPassword">
                            Show
                        </button>
                    </div>

                    <div id="studentPasswordFeedback" class="invalid-feedback">
                        Password must be at least 8 characters.
                    </div>

                    <div class="form-text">
                        Must include an uppercase letter, a lowercase letter, and a special character.
                    </div>
                </div>
            <?php endif; ?>


            <!-- Form buttons -->
            <div class="d-flex gap-2">

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Update Student' : 'Add Student' ?>
                </button>

                <!-- Cancel button -->
                <a href="index.php?action=students" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>

    </div>
</div>

<!-- Load custom JavaScript -->
<script src="assets/script.js"></script>

</body>
</html>