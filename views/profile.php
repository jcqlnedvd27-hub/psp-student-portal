<?php

// Get flash message
$flash = getFlash();

// Normalize flash type
if ($flash) {

    $typeMap = [
        'error'  => 'danger',
        'ok'     => 'success',
        'notice' => 'info',
    ];

    $flash['type'] = $typeMap[$flash['type']] ?? $flash['type'];

    $allowedTypes = [
        'success',
        'danger',
        'warning',
        'info',
        'primary',
        'secondary',
        'dark',
        'light'
    ];

    if (!in_array($flash['type'], $allowedTypes, true)) {
        $flash['type'] = 'info';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Basic page settings -->
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <!-- Page title -->
    <title>
        Student Profile - <?= e(APP_NAME) ?>
    </title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <!-- Custom CSS -->
    <link
        rel="stylesheet"
        href="assets/style.css"
    >

</head>

<body class="profile-page">

<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg navbar-dark portal-nav profile-navbar">

    <div class="container profile-navbar-container">

        <!-- Portal logo and name -->
        <a
            class="navbar-brand profile-brand"
            href="index.php?action=profile"
        >

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

        <!-- Logout button -->
        <a
            class="btn profile-logout"
            href="index.php?action=logout"
        >

            <i class="bi bi-box-arrow-right"></i>
            Logout

        </a>

    </div>

</nav>


<!-- Main profile content -->
<main class="profile-main">

    <div class="profile-wrapper">

        <!-- Page header -->
        <div class="profile-header">

            <h1>
                Student Profile
            </h1>

            <p>
                Your profile is based on the current login session.
            </p>

        </div>


        <!-- Flash message -->
        <?php if ($flash): ?>

            <div
                class="alert alert-<?= e($flash['type']) ?> profile-alert"
                role="alert"
            >

                <?= e($flash['message']) ?>

            </div>

        <?php endif; ?>


        <!-- ===================================================== -->
        <!-- PROFILE PICTURE SECTION -->
        <!-- ===================================================== -->

        <div
            class="profile-box mb-4"
            style="width: 100%;"
        >

            <div class="profile-box-header">

                <div
                    class="profile-box-icon profile-user-icon"
                >

                    <i class="bi bi-camera-fill"></i>

                </div>

                <div>

                    <h2>
                        Profile Picture
                    </h2>

                    <p>
                        Upload a picture for your student profile
                    </p>

                </div>

            </div>


            <div
                class="row align-items-center g-4"
            >

                <!-- Profile picture preview -->
                <div
                    class="col-md-4 text-center"
                >

                    <?php if (
                        !empty($student['profile_picture'])
                    ): ?>

                        <img
                            src="uploads/profile/<?= e($student['profile_picture']) ?>"
                            alt="Student Profile Picture"
                            class="img-fluid rounded-circle border"
                            style="
                                width: 160px;
                                height: 160px;
                                object-fit: cover;
                            "
                        >

                    <?php else: ?>

                        <div
                            class="rounded-circle bg-light border d-flex align-items-center justify-content-center mx-auto"
                            style="
                                width: 160px;
                                height: 160px;
                            "
                        >

                            <i
                                class="bi bi-person-fill text-secondary"
                                style="font-size: 80px;"
                            ></i>

                        </div>

                    <?php endif; ?>

                </div>


                <!-- Upload form -->
                <div class="col-md-8">

                    <form
                        action="index.php?action=uploadProfilePicture"
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        <!-- CSRF token -->
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e(csrfToken()) ?>"
                        >


                        <div class="mb-3">

                            <label
                                for="profilePicture"
                                class="form-label fw-semibold"
                            >
                                Choose Profile Picture
                            </label>

                            <input
                                type="file"
                                class="form-control"
                                name="profile_picture"
                                id="profilePicture"
                                accept=".jpg,.jpeg,.png"
                                required
                            >

                        </div>


                        <!-- Upload requirements -->
                        <div class="alert alert-info">

                            <div class="fw-semibold mb-2">

                                <i class="bi bi-info-circle-fill"></i>
                                Upload Requirements

                            </div>

                            <div>
                                ✓ JPG, JPEG or PNG only
                            </div>

                            <div>
                                ✓ Maximum file size: 2 MB
                            </div>

                            <div>
                                ✓ Only valid image files are accepted
                            </div>

                        </div>


                        <!-- Upload button -->
                        <button
                            type="submit"
                            class="btn btn-primary"
                        >

                            <i class="bi bi-cloud-arrow-up-fill"></i>

                            Upload Profile Picture

                        </button>

                    </form>

                </div>

            </div>

        </div>


        <!-- ===================================================== -->
        <!-- EXISTING PROFILE + PASSWORD SECTION -->
        <!-- ===================================================== -->

        <div class="profile-layout">


            <!-- Left card for profile information -->
            <div class="profile-box">

                <!-- Card heading -->
                <div class="profile-box-header">

                    <div
                        class="profile-box-icon profile-user-icon"
                    >

                        <i class="bi bi-person-fill"></i>

                    </div>

                    <div>

                        <h2>
                            Profile Information
                        </h2>

                        <p>
                            Your personal and academic details
                        </p>

                    </div>

                </div>


                <!-- Student name -->
                <div class="student-info-row">

                    <div class="student-info-icon">

                        <i class="bi bi-person"></i>

                    </div>

                    <span class="student-info-label">
                        Name
                    </span>

                    <strong class="student-info-value">

                        <?= e($student['student_name']) ?>

                    </strong>

                </div>


                <!-- NRIC -->
                <div class="student-info-row">

                    <div class="student-info-icon">

                        <i class="bi bi-card-text"></i>

                    </div>

                    <span class="student-info-label">
                        NRIC
                    </span>

                    <strong class="student-info-value">

                        <?= e($student['student_ic']) ?>

                    </strong>

                </div>


                <!-- Program -->
                <div class="student-info-row">

                    <div class="student-info-icon">

                        <i class="bi bi-book"></i>

                    </div>

                    <span class="student-info-label">
                        Program
                    </span>

                    <strong class="student-info-value">

                        <?= e($student['program']) ?>

                    </strong>

                </div>

            </div>


            <!-- Right card for password exchange -->
            <div class="profile-box password-box">

                <!-- Card heading -->
                <div class="profile-box-header">

                    <div
                        class="profile-box-icon profile-lock-icon"
                    >

                        <i class="bi bi-lock-fill"></i>

                    </div>

                    <div>

                        <h2>
                            Password Exchange Module
                        </h2>

                        <p>
                            Update your password securely
                        </p>

                    </div>

                </div>


                <!-- Password form -->
                <form
                    action="index.php?action=changePassword"
                    method="POST"
                    class="profile-password-form needs-validation"
                    novalidate
                >

                    <!-- CSRF security token -->
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(csrfToken()) ?>"
                    >


                    <!-- Old password -->
                    <div class="password-form-group">

                        <label for="oldPassword">
                            Old Password
                        </label>

                        <div class="password-input">

                            <i class="bi bi-lock-fill"></i>

                            <input
                                type="password"
                                name="old_password"
                                id="oldPassword"
                                placeholder="Enter your current password"
                                required
                            >

                        </div>

                        <div class="invalid-feedback">
                            Please enter your old password.
                        </div>

                    </div>


                    <!-- New password -->
                    <div class="password-form-group">

                        <label for="newPassword">
                            New Password
                        </label>

                        <div class="password-input">

                            <i class="bi bi-lock-fill"></i>

                            <input
                                type="password"
                                name="new_password"
                                id="newPassword"
                                minlength="8"
                                placeholder="Enter new password"
                                required
                            >

                        </div>

                        <div class="invalid-feedback">
                            Password must be at least 8 characters.
                        </div>

                    </div>


                    <!-- Confirm password -->
                    <div class="password-form-group confirm-group">

                        <label for="confirmPassword">
                            Confirm Password
                        </label>

                        <div class="password-input">

                            <i class="bi bi-lock-fill"></i>

                            <input
                                type="password"
                                name="confirm_password"
                                id="confirmPassword"
                                minlength="8"
                                placeholder="Confirm new password"
                                required
                            >

                        </div>

                        <div
                            class="invalid-feedback"
                            id="confirmPasswordError"
                        >
                            Please confirm your password.
                        </div>

                        <div
                            id="passwordMatch"
                            class="password-match-message"
                        ></div>

                    </div>


                    <!-- Change password button -->
                    <button
                        type="submit"
                        class="change-password-btn"
                    >

                        <i class="bi bi-key-fill"></i>

                        Change Password

                    </button>

                </form>


                <!-- Password requirements -->
                <div class="password-requirements">

                    <div class="requirements-icon">

                        <i class="bi bi-info-circle-fill"></i>

                    </div>

                    <div class="requirements-content">

                        <strong>
                            Password Requirements
                        </strong>

                        <div class="requirement-item">
                            ✓ Minimum 8 characters
                        </div>

                        <div class="requirement-item">
                            ✓ At least one uppercase and one lowercase letter
                        </div>

                        <div class="requirement-item">
                            ✓ At least one special character
                        </div>

                        <div class="requirement-item">
                            ✓ Old password must be correct
                        </div>

                        <div class="requirement-item">
                            ✓ Confirm password must match
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>


<!-- Existing JavaScript -->
<script src="assets/script.js"></script>

</body>

</html>