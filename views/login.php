<?php

// Load configuration and helper functions
require_once __DIR__ . '/../config/config.php';

// Get flash message
$flash = getFlash();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title><?= e(APP_NAME) ?> - Login</title>

    <!-- Bootstrap -->
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


<body class="login-page">


<!-- ========================================================= -->
<!-- BACKGROUND DECORATION -->
<!-- ========================================================= -->

<div class="login-bg-shape login-bg-shape-one"></div>
<div class="login-bg-shape login-bg-shape-two"></div>


<!-- ========================================================= -->
<!-- MAIN LOGIN CONTAINER -->
<!-- ========================================================= -->

<main class="login-wrapper">

    <div class="login-container">


        <!-- ================================================= -->
        <!-- LEFT BRANDING PANEL -->
        <!-- ================================================= -->

        <section class="login-brand-panel">

            <div class="brand-content">

                <!-- Logo -->
                <div class="brand-logo">

                    <div class="brand-logo-inner">

                        <i class="bi bi-mortarboard-fill"></i>

                    </div>

                </div>


                <!-- Brand -->
                <div class="brand-label">
                    POLITEKNIK SEBERANG PERAI
                </div>


                <h1 class="brand-heading">

                    PSP Student
                    <span>Portal</span>

                </h1>


                <p class="brand-description">

                    Your centralized platform for
                    academic information, student
                    records and personal profile
                    management.

                </p>


                <!-- Feature list -->
                <div class="brand-features">

                    <div class="brand-feature">

                        <div class="brand-feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>

                        <div>
                            <strong>Secure Access</strong>
                            <span>Protected student account</span>
                        </div>

                    </div>


                    <div class="brand-feature">

                        <div class="brand-feature-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>

                        <div>
                            <strong>Student Profile</strong>
                            <span>Manage your personal information</span>
                        </div>

                    </div>


                    <div class="brand-feature">

                        <div class="brand-feature-icon">
                            <i class="bi bi-mortarboard"></i>
                        </div>

                        <div>
                            <strong>Academic Management</strong>
                            <span>Access your academic records</span>
                        </div>

                    </div>

                </div>

            </div>


            <!-- Branding footer -->
            <div class="brand-footer">

                <span>
                    <i class="bi bi-circle-fill"></i>
                    Student Academic Management System
                </span>

            </div>

        </section>


        <!-- ================================================= -->
        <!-- RIGHT LOGIN PANEL -->
        <!-- ================================================= -->

        <section class="login-form-panel">


            <!-- Mobile logo -->
            <div class="mobile-login-logo">

                <div class="mobile-logo-icon">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>

                <span>
                    PSP Student Portal
                </span>

            </div>


            <!-- Login heading -->
            <div class="login-heading">

                <span class="login-welcome">
                    WELCOME BACK
                </span>

                <h2>
                    Sign in to your account
                </h2>

                <p>
                    Enter your credentials to continue
                    to the student portal.
                </p>

            </div>


            <!-- ================================================= -->
            <!-- FLASH MESSAGE -->
            <!-- ================================================= -->

            <?php if ($flash): ?>

                <div
                    class="alert alert-<?= e($flash['type']) ?> login-alert"
                    role="alert"
                >

                    <i class="bi bi-info-circle-fill"></i>

                    <span>
                        <?= e($flash['message']) ?>
                    </span>

                </div>

            <?php endif; ?>


            <!-- ================================================= -->
            <!-- LOGIN FORM -->
            <!-- ================================================= -->

            <form
                action="index.php?action=doLogin"
                method="POST"
                class="login-form needs-validation"
                novalidate
            >


                <!-- IC NUMBER -->
                <div class="login-form-group">

                    <label
                        for="loginIc"
                        class="login-label"
                    >
                        IC Number
                    </label>

                    <div class="modern-input-wrapper">

                        <i class="bi bi-person-vcard input-icon"></i>

                        <input
                            type="text"
                            name="ic"
                            id="loginIc"
                            class="modern-input"
                            placeholder="010101-01-0101"
                            pattern="\d{6}-\d{2}-\d{4}"
                            autocomplete="username"
                            required
                        >

                    </div>

                    <div class="invalid-feedback">
                        Please enter a valid IC format.
                    </div>

                </div>


                <!-- PASSWORD -->
                <div class="login-form-group">

                    <div class="password-label-row">

                        <label
                            for="loginPassword"
                            class="login-label"
                        >
                            Password
                        </label>

                    </div>


                    <div class="modern-input-wrapper">

                        <i class="bi bi-lock input-icon"></i>

                        <input
                            type="password"
                            name="password"
                            id="loginPassword"
                            class="modern-input password-field"
                            placeholder="Enter your password"
                            minlength="8"
                            autocomplete="current-password"
                            required
                        >


                        <!-- Show / Hide -->
                        <button
                            type="button"
                            class="modern-password-toggle toggle-password"
                            data-target="loginPassword"
                            aria-label="Show password"
                        >

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                    <div
                        id="loginPasswordFeedback"
                        class="invalid-feedback"
                    >
                        Password must be at least 8 characters.
                    </div>

                </div>


                <!-- LOGIN BUTTON -->
                <button
                    class="modern-login-btn"
                    type="submit"
                >

                    <span>
                        Sign In
                    </span>

                    <i class="bi bi-arrow-right"></i>

                </button>


                <!-- Security note -->
                <div class="login-security-note">

                    <i class="bi bi-shield-lock-fill"></i>

                    <span>
                        Your login information is securely protected.
                    </span>

                </div>

            </form>


            <!-- ================================================= -->
            <!-- DEMO ACCOUNTS -->
            <!-- ================================================= -->

            <section class="demo-section">


                <div class="demo-section-heading">

                    <div class="demo-heading-icon">

                        <i class="bi bi-lightning-charge-fill"></i>

                    </div>

                    <div>

                        <strong>
                            Quick Access
                        </strong>

                        <span>
                            Demo accounts
                        </span>

                    </div>

                </div>


                <div class="demo-grid">


                    <!-- STUDENT 1 -->
                    <div class="demo-card">

                        <div class="demo-card-top">

                            <div class="demo-avatar student-avatar">
                                <i class="bi bi-person-fill"></i>
                            </div>

                            <div class="demo-user">

                                <strong>
                                    Student 1
                                </strong>

                                <span>
                                    Tang Jenny
                                </span>

                            </div>

                        </div>


                        <div class="demo-details">

                            <div>

                                <small>
                                    IC NUMBER
                                </small>

                                <strong>
                                    010101-01-0101
                                </strong>

                            </div>

                            <div>

                                <small>
                                    PASSWORD
                                </small>

                                <strong>
                                    password
                                </strong>

                            </div>

                        </div>

                    </div>


                    <!-- STUDENT 2 -->
                    <div class="demo-card">

                        <div class="demo-card-top">

                            <div class="demo-avatar student-avatar">
                                <i class="bi bi-person-fill"></i>
                            </div>

                            <div class="demo-user">

                                <strong>
                                    Student 2
                                </strong>

                                <span>
                                    Jacqueline Anak David
                                </span>

                            </div>

                        </div>


                        <div class="demo-details">

                            <div>

                                <small>
                                    IC NUMBER
                                </small>

                                <strong>
                                    020202-02-0202
                                </strong>

                            </div>

                            <div>

                                <small>
                                    PASSWORD
                                </small>

                                <strong>
                                    password
                                </strong>

                            </div>

                        </div>

                    </div>


                    <!-- STUDENT 3 -->
                    <div class="demo-card">

                        <div class="demo-card-top">

                            <div class="demo-avatar student-avatar">
                                <i class="bi bi-person-fill"></i>
                            </div>

                            <div class="demo-user">

                                <strong>
                                    Student 3
                                </strong>

                                <span>
                                    Zarifah Izyan Bt Shahreen
                                </span>

                            </div>

                        </div>


                        <div class="demo-details">

                            <div>

                                <small>
                                    IC NUMBER
                                </small>

                                <strong>
                                    030303-03-0303
                                </strong>

                            </div>

                            <div>

                                <small>
                                    PASSWORD
                                </small>

                                <strong>
                                    password
                                </strong>

                            </div>

                        </div>

                    </div>


                    <!-- ADMIN -->
                    <div class="demo-card admin-demo-card">

                        <div class="demo-card-top">

                            <div class="demo-avatar admin-avatar">
                                <i class="bi bi-shield-fill"></i>
                            </div>

                            <div class="demo-user">

                                <strong>
                                    Administrator
                                </strong>

                                <span>
                                    PSP Administrator
                                </span>

                            </div>

                        </div>


                        <div class="demo-details">

                            <div>

                                <small>
                                    IC NUMBER
                                </small>

                                <strong>
                                    900101-01-0001
                                </strong>

                            </div>

                            <div>

                                <small>
                                    PASSWORD
                                </small>

                                <strong>
                                    password
                                </strong>

                            </div>

                        </div>

                    </div>


                </div>

            </section>


            <!-- ================================================= -->
            <!-- FOOTER -->
            <!-- ================================================= -->

            <footer class="login-footer">

                <span>
                    © <?= date('Y') ?> PSP Student Portal
                </span>

                <span class="footer-dot">
                    •
                </span>

                <span>
                    Politeknik Seberang Perai
                </span>

            </footer>


        </section>

    </div>

</main>


<!-- Existing JavaScript -->
<script src="assets/script.js"></script>

</body>

</html>